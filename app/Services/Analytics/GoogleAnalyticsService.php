<?php

namespace App\Services\Analytics;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;
use Google\Analytics\Data\V1beta\RunRealtimeReportRequest;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Thin wrapper around the official GA4 "Data API" client
 * (composer require google/analytics-data).
 *
 * Every public method fails soft: if GA4 isn't configured yet, or a
 * single call errors out (network blip, bad credentials, quota), it
 * returns an empty-but-shaped result instead of throwing — so a broken
 * GA4 integration can never take down the admin dashboard. Errors are
 * logged so you can still see what went wrong.
 *
 * All reads are cached for 30 minutes per property+period+report. GA4's
 * Data API has a real (and fairly low) daily quota per property, so
 * hammering it on every page load/poll will get you rate-limited. Only
 * the realtime methods bypass the cache, and even they're capped by
 * their own short TTL — see realtimeActiveUsers()/realtimeByPage().
 */
class GoogleAnalyticsService
{
    protected ?BetaAnalyticsDataClient $client = null;

    protected string $property;

    protected bool $configured = false;

    public function __construct()
    {
        $propertyId = config('services.google_analytics.property_id');
        $credentialsPath = config('services.google_analytics.credentials_path');

        $this->property = 'properties/' . $propertyId;

        if ($propertyId && $credentialsPath && is_readable($credentialsPath)) {
            try {
                $this->client = new BetaAnalyticsDataClient([
                    'credentials' => $credentialsPath,
                ]);
                $this->configured = true;
            } catch (Throwable $e) {
                Log::error('GA4: failed to initialize client', ['error' => $e->getMessage()]);
            }
        }
    }

    public function isConfigured(): bool
    {
        return $this->configured;
    }

    // ─── Public report methods ────────────────────────────────────────

    /**
     * Headline KPIs for the period: sessions, users, new users,
     * pageviews, bounce rate, engagement rate, avg session duration,
     * conversions.
     *
     * Bounce rate is deliberately derived as (1 - engagementRate) rather
     * than read from the `bounceRate` metric directly. Google's own docs
     * and community reports disagree on whether that metric is returned
     * as a 0–1 fraction or a 0–100 percentage, and getting it backwards
     * silently inverts "healthy" and "broken". `engagementRate` is
     * unambiguously documented as a 0–1 fraction, and bounce rate is by
     * definition its complement, so deriving it this way sidesteps the
     * ambiguity entirely.
     */
    public function overview(string $period = '30d'): array
    {
        if (!$this->configured) {
            return $this->emptyOverview();
        }

        return Cache::remember($this->cacheKey('overview', $period), now()->addMinutes(30), function () use ($period) {
            try {
                [$start, $end] = $this->rangeForPeriod($period);

                $response = $this->client->runReport(
                    (new RunReportRequest())
                        ->setProperty($this->property)
                        ->setDateRanges([$this->dateRange($start, $end)])
                        ->setMetrics([
                            $this->metric('sessions'),
                            $this->metric('activeUsers'),
                            $this->metric('newUsers'),
                            $this->metric('screenPageViews'),
                            $this->metric('engagementRate'),
                            $this->metric('averageSessionDuration'),
                            $this->metric('conversions'),
                        ])
                );

                $rows = $response->getRows();
                if (count($rows) === 0) {
                    return $this->emptyOverview();
                }

                $values = $this->rowMetricValues($rows[0]);
                $engagementRate = (float) ($values[4] ?? 0);

                return [
                    'sessions'             => (int) ($values[0] ?? 0),
                    'active_users'         => (int) ($values[1] ?? 0),
                    'new_users'            => (int) ($values[2] ?? 0),
                    'page_views'           => (int) ($values[3] ?? 0),
                    'engagement_rate'      => round($engagementRate * 100, 1),
                    'bounce_rate'          => round((1 - $engagementRate) * 100, 1),
                    'avg_session_duration' => $this->formatDuration((float) ($values[5] ?? 0)),
                    'conversions'          => (int) ($values[6] ?? 0),
                ];
            } catch (Throwable $e) {
                Log::error('GA4: overview report failed', ['error' => $e->getMessage()]);

                return $this->emptyOverview();
            }
        });
    }

    /**
     * Daily sessions / active users / pageviews for the period, shaped
     * for a multi-series line chart.
     */
    public function timeSeries(string $period = '30d'): array
    {
        $empty = ['labels' => ['No Data'], 'sessions' => [0], 'users' => [0], 'pageviews' => [0]];

        if (!$this->configured) {
            return $empty;
        }

        return Cache::remember($this->cacheKey('timeseries', $period), now()->addMinutes(30), function () use ($period, $empty) {
            try {
                [$start, $end] = $this->rangeForPeriod($period);

                $response = $this->client->runReport(
                    (new RunReportRequest())
                        ->setProperty($this->property)
                        ->setDateRanges([$this->dateRange($start, $end)])
                        ->setDimensions([$this->dimension('date')])
                        ->setMetrics([
                            $this->metric('sessions'),
                            $this->metric('activeUsers'),
                            $this->metric('screenPageViews'),
                        ])
                        ->setOrderBys([
                            (new OrderBy())->setDimension(
                                (new OrderBy\DimensionOrderBy())->setDimensionName('date')
                            ),
                        ])
                );

                $labels = $sessions = $users = $pageviews = [];

                foreach ($response->getRows() as $row) {
                    $rawDate = $row->getDimensionValues()[0]->getValue(); // YYYYMMDD
                    $labels[] = substr($rawDate, 0, 4) . '-' . substr($rawDate, 4, 2) . '-' . substr($rawDate, 6, 2);

                    $values = $this->rowMetricValues($row);
                    $sessions[] = (int) ($values[0] ?? 0);
                    $users[] = (int) ($values[1] ?? 0);
                    $pageviews[] = (int) ($values[2] ?? 0);
                }

                if (empty($labels)) {
                    return $empty;
                }

                return compact('labels', 'sessions', 'users', 'pageviews');
            } catch (Throwable $e) {
                Log::error('GA4: time series report failed', ['error' => $e->getMessage()]);

                return $empty;
            }
        });
    }

    /**
     * Sessions grouped by GA4's default channel grouping
     * (Organic Search, Direct, Paid Search, Social, Referral, Email...).
     */
    public function trafficSources(string $period = '30d'): array
    {
        return $this->groupedReport(
            cacheSuffix: 'traffic-sources',
            period: $period,
            dimensionName: 'sessionDefaultChannelGroup',
            metricName: 'sessions',
            limit: 8,
            palette: [
                '#0D99FF',
                '#3AC977',
                '#FF9F00',
                '#FF5E5E',
                '#8A5CF6',
                '#00C2CB',
                '#F472B6',
                '#6C757D',
            ],
        );
    }

    /** Sessions grouped by device category (desktop / mobile / tablet). */
    public function deviceBreakdown(string $period = '30d'): array
    {
        return $this->groupedReport(
            cacheSuffix: 'devices',
            period: $period,
            dimensionName: 'deviceCategory',
            metricName: 'sessions',
            limit: 5,
            palette: ['#0D99FF', '#3AC977', '#FF9F00', '#6C757D'],
        );
    }

    /** Sessions grouped by browser. */
    public function browserBreakdown(string $period = '30d'): array
    {
        return $this->groupedReport(
            cacheSuffix: 'browsers',
            period: $period,
            dimensionName: 'browser',
            metricName: 'sessions',
            limit: 6,
            palette: ['#0D99FF', '#3AC977', '#FF9F00', '#FF5E5E', '#8A5CF6', '#6C757D'],
        );
    }

    /** Top pages by pageviews, with average engagement time per page. */
    public function topPages(string $period = '30d', int $limit = 10): array
    {
        $key = $this->cacheKey('top-pages', $period . ':' . $limit);

        if (!$this->configured) {
            return [];
        }

        return Cache::remember($key, now()->addMinutes(30), function () use ($period, $limit) {
            try {
                [$start, $end] = $this->rangeForPeriod($period);

                $response = $this->client->runReport(
                    (new RunReportRequest())
                        ->setProperty($this->property)
                        ->setDateRanges([$this->dateRange($start, $end)])
                        ->setDimensions([$this->dimension('pagePath'), $this->dimension('pageTitle')])
                        ->setMetrics([
                            $this->metric('screenPageViews'),
                            $this->metric('averageSessionDuration'),
                        ])
                        ->setOrderBys([
                            (new OrderBy())
                                ->setMetric((new MetricOrderBy())->setMetricName('screenPageViews'))
                                ->setDesc(true),
                        ])
                        ->setLimit($limit)
                );

                $rows = [];
                foreach ($response->getRows() as $row) {
                    $dims = $row->getDimensionValues();
                    $values = $this->rowMetricValues($row);

                    $rows[] = [
                        'path'         => $dims[0]->getValue(),
                        'title'        => $dims[1]->getValue() ?: $dims[0]->getValue(),
                        'views'        => (int) ($values[0] ?? 0),
                        'avg_duration' => $this->formatDuration((float) ($values[1] ?? 0)),
                    ];
                }

                return $rows;
            } catch (Throwable $e) {
                Log::error('GA4: top pages report failed', ['error' => $e->getMessage()]);

                return [];
            }
        });
    }

    /** Top countries by active users. */
    public function topCountries(string $period = '30d', int $limit = 10): array
    {
        $key = $this->cacheKey('top-countries', $period . ':' . $limit);

        if (!$this->configured) {
            return [];
        }

        return Cache::remember($key, now()->addMinutes(30), function () use ($period, $limit) {
            try {
                [$start, $end] = $this->rangeForPeriod($period);

                $response = $this->client->runReport(
                    (new RunReportRequest())
                        ->setProperty($this->property)
                        ->setDateRanges([$this->dateRange($start, $end)])
                        ->setDimensions([$this->dimension('country')])
                        ->setMetrics([$this->metric('activeUsers'), $this->metric('sessions')])
                        ->setOrderBys([
                            (new OrderBy())
                                ->setMetric((new MetricOrderBy())->setMetricName('activeUsers'))
                                ->setDesc(true),
                        ])
                        ->setLimit($limit)
                );

                $rows = [];
                $total = 0;
                foreach ($response->getRows() as $row) {
                    $values = $this->rowMetricValues($row);
                    $users = (int) ($values[0] ?? 0);
                    $total += $users;

                    $rows[] = [
                        'country'  => $row->getDimensionValues()[0]->getValue(),
                        'users'    => $users,
                        'sessions' => (int) ($values[1] ?? 0),
                    ];
                }

                foreach ($rows as &$r) {
                    $r['share'] = $total > 0 ? round(($r['users'] / $total) * 100, 1) : 0;
                }

                return $rows;
            } catch (Throwable $e) {
                Log::error('GA4: top countries report failed', ['error' => $e->getMessage()]);

                return [];
            }
        });
    }

    /**
     * Users on the site RIGHT NOW. Realtime data is inherently volatile,
     * so this is cached for only 60 seconds — long enough to protect
     * quota if several admins have the dashboard open, short enough to
     * still feel "live".
     */
    public function realtimeActiveUsers(): int
    {
        if (!$this->configured) {
            return 0;
        }

        return Cache::remember($this->cacheKey('realtime', 'active-users'), now()->addSeconds(60), function () {
            try {
                $response = $this->client->runRealtimeReport(
                    (new RunRealtimeReportRequest())
                        ->setProperty($this->property)
                        ->setMetrics([$this->metric('activeUsers')])
                );

                $rows = $response->getRows();
                if (count($rows) === 0) {
                    return 0;
                }

                return (int) $rows[0]->getMetricValues()[0]->getValue();
            } catch (Throwable $e) {
                Log::error('GA4: realtime active users failed', ['error' => $e->getMessage()]);

                return 0;
            }
        });
    }

    /** Which pages the currently-active users are on, right now. */
    public function realtimeByPage(int $limit = 5): array
    {
        if (!$this->configured) {
            return [];
        }

        return Cache::remember($this->cacheKey('realtime', 'by-page:' . $limit), now()->addSeconds(60), function () use ($limit) {
            try {
                $response = $this->client->runRealtimeReport(
                    (new RunRealtimeReportRequest())
                        ->setProperty($this->property)
                        ->setDimensions([$this->dimension('unifiedScreenName')])
                        ->setMetrics([$this->metric('activeUsers')])
                        ->setOrderBys([
                            (new OrderBy())
                                ->setMetric((new MetricOrderBy())->setMetricName('activeUsers'))
                                ->setDesc(true),
                        ])
                        ->setLimit($limit)
                );

                $rows = [];
                foreach ($response->getRows() as $row) {
                    $rows[] = [
                        'page'  => $row->getDimensionValues()[0]->getValue(),
                        'users' => (int) $row->getMetricValues()[0]->getValue(),
                    ];
                }

                return $rows;
            } catch (Throwable $e) {
                Log::error('GA4: realtime by page failed', ['error' => $e->getMessage()]);

                return [];
            }
        });
    }

    /** Forget every cached report for the given period — call after big site changes if you want fresh numbers early. */
    public function flushCache(string $period = '30d'): void
    {
        foreach (['overview', 'timeseries', 'traffic-sources', 'devices', 'browsers'] as $suffix) {
            Cache::forget($this->cacheKey($suffix, $period));
        }
    }

    // ─── Shared helpers ────────────────────────────────────────────────

    /**
     * A single-dimension, single-metric "group by X, sum Y" report,
     * shaped for a doughnut/bar chart plus a legend list. Traffic
     * sources, devices, and browsers are all this same shape, so they
     * share one implementation.
     */
    protected function groupedReport(string $cacheSuffix, string $period, string $dimensionName, string $metricName, int $limit, array $palette): array
    {
        $empty = ['labels' => ['No Data'], 'data' => [0], 'colors' => ['#6C757D']];

        if (!$this->configured) {
            return $empty;
        }

        return Cache::remember($this->cacheKey($cacheSuffix, $period), now()->addMinutes(30), function () use ($period, $dimensionName, $metricName, $limit, $palette, $empty) {
            try {
                [$start, $end] = $this->rangeForPeriod($period);

                $response = $this->client->runReport(
                    (new RunReportRequest())
                        ->setProperty($this->property)
                        ->setDateRanges([$this->dateRange($start, $end)])
                        ->setDimensions([$this->dimension($dimensionName)])
                        ->setMetrics([$this->metric($metricName)])
                        ->setOrderBys([
                            (new OrderBy())
                                ->setMetric((new MetricOrderBy())->setMetricName($metricName))
                                ->setDesc(true),
                        ])
                        ->setLimit($limit)
                );

                $labels = $data = $colors = [];
                foreach ($response->getRows() as $i => $row) {
                    $label = $row->getDimensionValues()[0]->getValue();
                    $labels[] = $label !== '' ? $label : 'Unknown';
                    $data[] = (int) $row->getMetricValues()[0]->getValue();
                    $colors[] = $palette[$i % count($palette)];
                }

                if (empty($labels)) {
                    return $empty;
                }

                return compact('labels', 'data', 'colors');
            } catch (Throwable $e) {
                Log::error("GA4: grouped report [{$dimensionName}] failed", ['error' => $e->getMessage()]);

                return $empty;
            }
        });
    }

    protected function rowMetricValues($row): array
    {
        return array_map(fn($mv) => $mv->getValue(), iterator_to_array($row->getMetricValues()));
    }

    protected function dimension(string $name): Dimension
    {
        return (new Dimension())->setName($name);
    }

    protected function metric(string $name): Metric
    {
        return (new Metric())->setName($name);
    }

    protected function dateRange(string $start, string $end): DateRange
    {
        return (new DateRange())->setStartDate($start)->setEndDate($end);
    }

    protected function rangeForPeriod(string $period): array
    {
        return match ($period) {
            '7d'  => ['7daysAgo', 'today'],
            '30d' => ['30daysAgo', 'today'],
            '90d' => ['90daysAgo', 'today'],
            '12m' => ['365daysAgo', 'today'],
            default => ['30daysAgo', 'today'],
        };
    }

    protected function formatDuration(float $seconds): string
    {
        $seconds = max(0, (int) round($seconds));
        $minutes = intdiv($seconds, 60);
        $remaining = $seconds % 60;

        return sprintf('%d:%02d', $minutes, $remaining);
    }

    protected function cacheKey(string $report, string $suffix): string
    {
        $propertyId = config('services.google_analytics.property_id', 'unset');

        return "ga4:{$propertyId}:{$report}:{$suffix}";
    }

    protected function emptyOverview(): array
    {
        return [
            'sessions'             => 0,
            'active_users'         => 0,
            'new_users'            => 0,
            'page_views'           => 0,
            'engagement_rate'      => 0,
            'bounce_rate'          => 0,
            'avg_session_duration' => '0:00',
            'conversions'          => 0,
        ];
    }
}

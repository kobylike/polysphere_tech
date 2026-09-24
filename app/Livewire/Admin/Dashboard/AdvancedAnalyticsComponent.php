<?php

namespace App\Livewire\Admin\Dashboard;

use App\Services\Analytics\GoogleAnalyticsService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.users')]
class AdvancedAnalyticsComponent extends Component
{
    public string $period = '30d';

    // ─── Report data, stored as public props so Alpine/Chart.js can read
    // them via @json() on first paint and via the updateAnalyticsCharts
    // event on every period change, exactly like the existing KPI
    // dashboard's projectChartData/projectStatusData pattern. ──────────
    public array $overview = [];
    public array $timeSeries = [];
    public array $trafficSources = [];
    public array $deviceBreakdown = [];
    public array $browserBreakdown = [];
    public array $topPages = [];
    public array $topCountries = [];

    public bool $ga4Configured = false;

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->hasRole(['Super Admin', 'Admin'])) {
            return redirect()->route('dashboard.user')->with('error', 'You do not have access to the analytics dashboard.');
        }

        $this->refreshAnalytics();
    }

    public function refreshAnalytics(): void
    {
        $service = app(GoogleAnalyticsService::class);

        $this->ga4Configured = $service->isConfigured();

        $this->overview = $service->overview($this->period);
        $this->timeSeries = $service->timeSeries($this->period);
        $this->trafficSources = $service->trafficSources($this->period);
        $this->deviceBreakdown = $service->deviceBreakdown($this->period);
        $this->browserBreakdown = $service->browserBreakdown($this->period);
        $this->topPages = $service->topPages($this->period, 8);
        $this->topCountries = $service->topCountries($this->period, 8);
    }

    public function updatedPeriod(): void
    {
        $this->refreshAnalytics();

        $this->dispatch('updateAnalyticsCharts', [
            'timeSeries' => $this->timeSeries,
            'trafficSources' => $this->trafficSources,
            'deviceBreakdown' => $this->deviceBreakdown,
            'browserBreakdown' => $this->browserBreakdown,
        ]);
    }

    /**
     * Manual refresh button — also clears the 30-minute report cache for
     * the current period so the next load hits GA4 fresh, instead of
     * just re-reading the same cached values.
     */
    public function forceRefresh(): void
    {
        app(GoogleAnalyticsService::class)->flushCache($this->period);
        $this->refreshAnalytics();

        $this->dispatch('updateAnalyticsCharts', [
            'timeSeries' => $this->timeSeries,
            'trafficSources' => $this->trafficSources,
            'deviceBreakdown' => $this->deviceBreakdown,
            'browserBreakdown' => $this->browserBreakdown,
        ]);
    }

    // ─── Realtime widgets ──────────────────────────────────────────────
    // These are #[Computed] rather than mount()-populated properties:
    // Livewire recomputes them fresh on every request, including every
    // wire:poll tick from the view, without needing a dedicated polling
    // method. Each one is still cached for 60s server-side inside the
    // service, so multiple open dashboards / rapid polls don't multiply
    // GA4 API calls.
    #[Computed]
    public function realtimeActiveUsers()
    {
        if (!$this->ga4Configured) {
            return 0;
        }

        return app(GoogleAnalyticsService::class)->realtimeActiveUsers();
    }

    #[Computed]
    public function realtimeTopPages()
    {
        if (!$this->ga4Configured) {
            return [];
        }

        return app(GoogleAnalyticsService::class)->realtimeByPage(5);
    }

    public function render()
    {
        return view('livewire.admin.dashboard.advanced-analytics-component');
    }
}

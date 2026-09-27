<?php

namespace App\Services;

class LeadIntentDetector
{
    /* ──────────────────────────────────────────────────────────── */
    /*  Service keywords                                          */
    /* ──────────────────────────────────────────────────────────── */

    protected const SERVICE_KEYWORDS = [
        // Vertical / industry-specific systems
        'School Management'      => ['school management', 'school system', 'student management', 'education management', 'school portal', 'learning management', 'lms', 'school software'],
        'Hospital Management'    => ['hospital management', 'clinic management', 'patient management', 'healthcare system', 'medical system', 'hospital software'],
        'Hotel Management'       => ['hotel management', 'hotel software', 'hospitality system'],
        'Restaurant POS'         => ['restaurant pos', 'restaurant system', 'kitchen system'],
        'Real Estate Platform'   => ['real estate', 'property management', 'property listing', 'listing platform'],
        'Church Management'      => ['church management', 'church system'],

        // Business systems
        'ERP'                    => ['erp', 'enterprise resource', 'business management system'],
        'CRM'                    => ['crm', 'customer relationship', 'sales pipeline'],
        'POS System'             => ['pos system', 'point of sale', 'cash register', 'sales terminal'],
        'Inventory Management'   => ['inventory', 'stock management', 'warehouse system'],
        'Accounting & Invoicing' => ['accounting', 'bookkeeping', 'invoice system', 'invoicing', 'payroll'],
        'HR System'              => ['hr system', 'hr management', 'human resource', 'employee management'],
        'Booking System'         => ['booking system', 'reservation system', 'appointment system', 'scheduling system'],
        'Payment Platform'       => ['payment platform', 'payment system', 'payment gateway', 'fintech platform', 'vtu', 'mobile money'],

        // Technology categories
        'SaaS Platform'          => ['saas', 'subscription platform', 'multi-tenant'],
        'Web Application'        => ['web app', 'web application', 'web portal', 'web platform', 'customer portal'],
        'Website'                => ['website', 'landing page', 'corporate site', 'corporate website'],
        'Mobile App'             => ['mobile app', 'mobile application', 'android app', 'ios app', 'smartphone app'],
        'Custom Software'        => ['custom software', 'custom application', 'custom system', 'custom tool', 'custom platform'],
        'E-commerce'             => ['ecommerce', 'e-commerce', 'online store', 'online shop', 'marketplace', 'selling online'],
        'API & Integration'      => ['api', 'integration', 'third-party system', 'connect systems', 'connect apps'],
        'AI & Automation'        => ['ai', 'artificial intelligence', 'machine learning', 'automation', 'chatbot', 'gpt', 'llm'],
        'Cloud Solutions'        => ['cloud migration', 'aws', 'azure', 'cloud hosting', 'cloud solution', 'serverless'],
        'Cybersecurity'          => ['cybersecurity', 'cyber security', 'penetration test', 'pen test', 'security audit'],
        'Data & Analytics'       => ['analytics', 'dashboard', 'reporting system', 'business intelligence', 'data warehouse'],
        'Digital Transformation' => ['digital transformation', 'modernize', 'modernization', 'legacy system', 'digital strategy'],
        'IT Consulting'          => ['consulting', 'consultant', 'it advice', 'technology advice', 'advisory'],
    ];

    /* ──────────────────────────────────────────────────────────── */
    /*  Industry keywords                                         */
    /* ──────────────────────────────────────────────────────────── */

    protected const INDUSTRY_KEYWORDS = [
        'Fintech'       => ['fintech', 'financial technology', 'payment processing'],
        'Healthcare'    => ['healthcare', 'medical', 'hospital', 'clinic', 'patient care'],
        'Education'     => ['education', 'school', 'university', 'college', 'academy'],
        'E-commerce'    => ['e-commerce', 'ecommerce', 'online retail', 'online store'],
        'Real Estate'   => ['real estate', 'property', 'housing'],
        'Logistics'     => ['logistics', 'shipping', 'delivery', 'courier', 'freight'],
        'Banking'       => ['banking', 'bank', 'financial institution'],
        'Insurance'     => ['insurance', 'insurer'],
        'Hospitality'   => ['hospitality', 'hotel', 'restaurant', 'tourism'],
        'Manufacturing' => ['manufacturing', 'factory', 'production line'],
        'Retail'        => ['retail', 'retailer'],
        'Agriculture'   => ['agriculture', 'farming', 'agritech'],
        'Government'    => ['government', 'public sector', 'municipal', 'ministry'],
        'NGO'           => ['ngo', 'non-profit', 'nonprofit', 'charity'],
        'Telecom'       => ['telecom', 'telecommunications', 'isp'],
        'Energy'        => ['energy', 'oil and gas', 'solar', 'utility'],
    ];

    /* ──────────────────────────────────────────────────────────── */
    /*  Public API                                                 */
    /* ──────────────────────────────────────────────────────────── */

    /**
     * Analyse the visitor's full user-turn transcript.
     *
     * @return array{
     *   services_interested: array<string>,
     *   industry: ?string,
     *   budget_range: ?string,
     *   timeline: ?string,
     *   urgency: ?string,
     *   preferred_contact: ?string,
     *   company: ?string,
     * }
     */
    public function detect(string $transcript): array
    {
        return [
            'services_interested' => $this->detectServices($transcript),
            'industry'            => $this->detectIndustry($transcript),
            'budget_range'        => $this->detectBudget($transcript),
            'timeline'            => $this->detectTimeline($transcript),
            'urgency'             => $this->detectUrgency($transcript),
            'preferred_contact'   => $this->detectPreferredContact($transcript),
            'company'             => $this->detectCompany($transcript),
        ];
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Service detection                                          */
    /* ──────────────────────────────────────────────────────────── */

    protected function detectServices(string $text): array
    {
        $text    = $this->normalize($text);
        $matches = [];

        foreach (self::SERVICE_KEYWORDS as $label => $keywords) {
            foreach ($keywords as $kw) {
                if ($this->containsPhrase($text, $kw)) {
                    $matches[] = $label;
                    break;
                }
            }
        }

        return array_values(array_unique($matches));
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Industry detection                                         */
    /* ──────────────────────────────────────────────────────────── */

    protected function detectIndustry(string $text): ?string
    {
        $text = $this->normalize($text);

        foreach (self::INDUSTRY_KEYWORDS as $label => $keywords) {
            foreach ($keywords as $kw) {
                if ($this->containsPhrase($text, $kw)) {
                    return $label;
                }
            }
        }

        return null;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Budget detection                                           */
    /* ──────────────────────────────────────────────────────────── */

    protected function detectBudget(string $text): ?string
    {
        // Look at the original text for currency patterns
        $patterns = [
            // $5,000 | USD 5000 | GHS 10,000 | €3000 | £2,500
            '/(?:\$|usd|us\$|ghs|gh₵|₵|eur|€|gbp|£)\s*[0-9][0-9,\.]*(?:\s*(?:k|thousand|million))?/i',

            // "5000 dollars" | "5000 cedis" | "15k usd"
            '/\b[0-9][0-9,\.]*\s*(?:k\s*)?(?:dollars?|usd|ghs|cedis?|euros?|pounds?|thousand\s+dollars?|million)\b/i',

            // "budget is X" / "budget: X" / "budget of X"
            '/\bbudget(?:\s+is|\s+of|:)?\s+[^\.,\n;]{1,80}/i',

            // "around 5k" / "about $10k"
            '/\b(?:around|about|roughly|approximately|up to|max|max of|up to about)\s+[0-9][0-9,\.]*\s*(?:k|thousand|million|dollars?|usd|ghs|cedis?|euros?|pounds?)?/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $value = trim($m[0]);
                // Clean trailing punctuation
                $value = rtrim($value, '.,;:!? ');
                if (mb_strlen($value) >= 2 && mb_strlen($value) <= 100) {
                    return $value;
                }
            }
        }

        return null;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Timeline detection                                         */
    /* ──────────────────────────────────────────────────────────── */

    protected function detectTimeline(string $text): ?string
    {
        $patterns = [
            // "by March" / "by March 2026" / "by Q2 2026"
            '/\bby\s+(?:jan(?:uary)?|feb(?:ruary)?|mar(?:ch)?|apr(?:il)?|may|jun(?:e)?|jul(?:y)?|aug(?:ust)?|sep(?:tember)?|oct(?:ober)?|nov(?:ember)?|dec(?:ember)?)(?:\s+\d{4})?\b/i',
            '/\bby\s+q[1-4](?:\s+\d{4})?\b/i',

            // "in 2 weeks" / "in 3 months"
            '/\bin\s+\d+\s+(?:days?|weeks?|months?|years?)\b/i',

            // "next week/month/year/quarter" / "this week/month"
            '/\b(?:next|this|coming)\s+(?:week|month|quarter|year)\b/i',

            // "end of the month" / "end of Q3"
            '/\bend\s+of\s+(?:the\s+)?(?:week|month|quarter|year|q[1-4])\b/i',

            // "2-3 weeks" / "2 to 3 months"
            '/\b\d+\s*[-–to]+\s*\d+\s+(?:days?|weeks?|months?|years?)\b/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                return ucwords(mb_strtolower(trim($m[0])));
            }
        }

        return null;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Urgency detection                                          */
    /* ──────────────────────────────────────────────────────────── */

    protected function detectUrgency(string $text): ?string
    {
        $text = $this->normalize($text);

        $high = [
            'urgent',
            'urgently',
            'asap',
            'immediately',
            'right away',
            'as soon as possible',
            'this week',
            'next week',
            'deadline',
            'critical',
            'emergency',
            'rush',
            'rushing',
            'time sensitive',
            'time-sensitive',
            'priority',
        ];

        $medium = [
            'soon',
            'this month',
            'next month',
            'next quarter',
            'in a few weeks',
            'in a couple of weeks',
            'q1',
            'q2',
            'q3',
            'q4',
        ];

        foreach ($high as $kw) {
            if ($this->containsPhrase($text, $kw)) {
                return 'high';
            }
        }

        foreach ($medium as $kw) {
            if ($this->containsPhrase($text, $kw)) {
                return 'medium';
            }
        }

        return null;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Preferred contact                                          */
    /* ──────────────────────────────────────────────────────────── */

    protected function detectPreferredContact(string $text): ?string
    {
        $text = $this->normalize($text);

        if (preg_match('/\b(?:whatsapp|whats app)\b/i', $text)) {
            return 'whatsapp';
        }

        if (preg_match('/\b(?:call me|phone me|by phone|on the phone|give me a call|voice call)\b/i', $text)) {
            return 'phone';
        }

        if (preg_match('/\b(?:email me|by email|via email|send me an email)\b/i', $text)) {
            return 'email';
        }

        return null;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Company detection                                          */
    /* ──────────────────────────────────────────────────────────── */

    protected function detectCompany(string $text): ?string
    {
        $patterns = [
            '/\b(?:i\'m from|im from|i am from)\s+([A-Z][A-Za-z0-9&\'\.\-]*(?:\s+[A-Z][A-Za-z0-9&\'\.\-]*){0,3})/i',
            '/\b(?:i work at|i work for)\s+([A-Z][A-Za-z0-9&\'\.\-]*(?:\s+[A-Z][A-Za-z0-9&\'\.\-]*){0,3})/i',
            '/\b(?:my company is|our company is|company name is|company:)\s+([A-Z][A-Za-z0-9&\'\.\-]*(?:\s+[A-Z][A-Za-z0-9&\'\.\-]*){0,3})/i',
            '/\b(?:we\'re|we are)\s+(?:a|an)?\s*([A-Z][A-Za-z0-9&\'\.\-]*(?:\s+[A-Z][A-Za-z0-9&\'\.\-]*){0,3})\s+(?:company|firm|business|startup|agency)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                $company = trim($m[1]);

                $company = rtrim($company, '.,;:!?');
                $company = preg_replace('/\s+(and|or|but|so|because|and then|the|a|an)$/i', '', $company) ?? $company;
                $company = trim($company);

                if (mb_strlen($company) >= 2 && mb_strlen($company) <= 80) {
                    return $company;
                }
            }
        }

        return null;
    }

    /* ──────────────────────────────────────────────────────────── */
    /*  Helpers                                                    */
    /* ──────────────────────────────────────────────────────────── */

    protected function normalize(string $text): string
    {
        return mb_strtolower(trim($text));
    }

    /**
     * Word-boundary safe phrase match. Prevents "api" matching
     * inside "rapid" or "capable", etc.
     */
    protected function containsPhrase(string $haystack, string $needle): bool
    {
        $needle = mb_strtolower($needle);
        $pattern = '/(?<![a-z0-9])' . preg_quote($needle, '/') . '(?![a-z0-9])/i';

        return (bool) preg_match($pattern, $haystack);
    }
}

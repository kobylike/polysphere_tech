<?php

namespace App\Livewire\Main;

use Livewire\Component;

class FaqComponent extends Component
{
    // Public properties for search/filter
    public $search = '';
    public $category = 'all';

    // Define categories
    public $categories = ['all', 'General', 'Software Development', 'SaaS', 'Digital Transformation', 'Consulting', 'Support'];

    // FAQ data
    public $faqs = [
        [
            'id' => 1,
            'category' => 'General',
            'question' => 'What types of custom software do you build?',
            'answer' => 'We build web applications, mobile apps (iOS/Android), enterprise platforms, APIs, and custom internal tools tailored to your specific business needs.'
        ],
        [
            'id' => 2,
            'category' => 'Software Development',
            'question' => 'What technologies do you use?',
            'answer' => 'We use modern technologies including Laravel, React, Vue.js, Node.js, Python, .NET, and cloud platforms like AWS and Azure. We choose the best tech stack for each project.'
        ],
        [
            'id' => 3,
            'category' => 'SaaS',
            'question' => 'How long does it take to build a SaaS platform?',
            'answer' => 'A typical MVP takes 3-6 months. Full enterprise-grade platforms can take 6-12 months depending on complexity, integrations, and feature requirements.'
        ],
        [
            'id' => 4,
            'category' => 'Digital Transformation',
            'question' => 'What does digital transformation involve?',
            'answer' => 'It includes migrating legacy systems, automating workflows, implementing cloud solutions, adopting AI/ML, and transforming business processes for the digital age.'
        ],
        [
            'id' => 5,
            'category' => 'Consulting',
            'question' => 'Do you offer IT consulting services?',
            'answer' => 'Yes. We provide technology strategy consulting, architecture reviews, security audits, and digital roadmap planning to help you make informed decisions.'
        ],
        [
            'id' => 6,
            'category' => 'Support',
            'question' => 'What kind of support do you offer after launch?',
            'answer' => 'We offer ongoing maintenance, bug fixes, performance optimization, security updates, and 24/7 support for critical systems.'
        ],
        [
            'id' => 7,
            'category' => 'Software Development',
            'question' => 'How do you ensure the quality of the software?',
            'answer' => 'We follow rigorous testing practices including unit testing, integration testing, end-to-end testing, code reviews, and continuous integration/continuous deployment (CI/CD).'
        ],
        [
            'id' => 8,
            'category' => 'SaaS',
            'question' => 'Can you help with SaaS pricing and monetization?',
            'answer' => 'Yes. We advise on pricing models (subscription, usage-based, tiered), payment gateway integration (Stripe, Paddle), and monetization strategies for SaaS products.'
        ],
        [
            'id' => 9,
            'category' => 'Digital Transformation',
            'question' => 'How do you handle legacy system migration?',
            'answer' => 'We follow a phased approach: assessment, planning, data migration, parallel running, and finally cutover — ensuring minimal business disruption.'
        ],
        [
            'id' => 10,
            'category' => 'General',
            'question' => 'What is your typical project timeline?',
            'answer' => 'It varies by project complexity. A simple website may take 4-8 weeks, while a complex enterprise platform can take 6-18 months. We\'ll provide a detailed timeline during discovery.'
        ],
        [
            'id' => 11,
            'category' => 'Support',
            'question' => 'What is your response time for support tickets?',
            'answer' => 'We respond to critical issues within 1 hour, high-priority within 4 hours, and standard issues within 24 hours.'
        ],
        [
            'id' => 12,
            'category' => 'Consulting',
            'question' => 'Do you offer technology audit services?',
            'answer' => 'Yes. We conduct comprehensive technology audits covering architecture, security, performance, code quality, and scalability — with actionable recommendations.'
        ],
    ];

    // Computed property for filtered FAQs
    public function getFilteredFaqsProperty()
    {
        return collect($this->faqs)
            ->when($this->category !== 'all', fn($q) => $q->where('category', $this->category))
            ->when($this->search, function ($q) {
                return $q->filter(function ($faq) {
                    return stripos($faq['question'], $this->search) !== false
                        || stripos($faq['answer'], $this->search) !== false;
                });
            })
            ->values()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.main.faq-component', [
            'filteredFaqs' => $this->getFilteredFaqsProperty(),
        ]);
    }
}

<?php

namespace App\Livewire\Main;

use Livewire\Component;

class FaqComponent extends Component
{
    // Public properties for search/filter
    public $search = '';
    public $category = 'all';

    public $categories = [];

    public $faqs = [];

    public function mount(): void
    {
        $this->categories = config('faqs.categories');
        $this->faqs = config('faqs.items');
    }

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

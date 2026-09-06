<?php

namespace App\Livewire\Main\Partials;

use Livewire\Component;

class MainSearch extends Component
{
    public string $searchQuery = '';

    public function performSearch()
    {
        if (strlen(trim($this->searchQuery)) >= 2) {
            return redirect()->route('main.search', ['q' => $this->searchQuery])->with('navigate', true);
        }
        return null;
    }

    public function render()
    {
        return view('livewire.main.partials.main-search');
    }
}

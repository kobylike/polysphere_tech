<?php

namespace App\Livewire\Main\Partials;

use Livewire\Component;

class WhatsappButton extends Component
{
    /**
     * Rendering variant.
     *
     * Supported:
     *   'topbar'   → plain text link with WhatsApp icon (matches header top bar)
     *   'navbar'   → compact pill (green background) for the main nav row
     *   'footer'   → card block for the footer's brand column
     *   'floating' → floating circle button (opt-in)
     */
    public string $variant = 'footer';

    public ?string $message = null;

    public function mount(string $variant = 'footer', ?string $message = null): void
    {
        $this->variant = $variant;
        $this->message = $message;
    }

    public function render()
    {
        $number  = preg_replace('/\D+/', '', (string) config('services.whatsapp.number'));
        $message = $this->message
            ?: (string) config('services.whatsapp.default_message', 'Hi Polysphere Tech!');

        $url = $number
            ? 'https://wa.me/' . $number . '?text=' . rawurlencode($message)
            : null;

        return view('livewire.main.partials.whatsapp-button', [
            'url'     => $url,
            'number'  => $number,
            'message' => $message,
        ]);
    }
}

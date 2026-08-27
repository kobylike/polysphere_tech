<?php

namespace App\Livewire\Main;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\ContactMail;
use App\Mail\ContactConfirmationMail;

class ContactComponent extends Component
{
    #[Validate]
    public string $name = '';

    #[Validate]
    public string $email = '';

    #[Validate]
    public string $subject = '';

    #[Validate]
    public string $message = '';

    public string $category = 'General';

    // Honeypot: real users never see or fill this field (hidden off-screen).
    // Bots that blindly fill every input in the form will populate it.
    public string $website = '';

    // Timestamp (ms) the form was rendered, set client-side on load.
    // Bots that submit within ~1.5s of page load get silently rejected.
    public string $renderedAt = '';

    public bool $success = false;

    protected array $allowedCategories = ['General', 'Billing', 'Technical', 'Partnership'];

    protected function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'min:2', 'max:100'],
            'email'   => ['required', 'email:rfc,dns', 'max:255'],
            'subject' => ['required', 'string', 'min:3', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required'    => "We'd love to know who we're talking to — enter your name.",
            'name.min'         => 'That name looks a little short — mind double-checking it?',
            'name.max'         => 'That name is too long. Keep it under 100 characters.',

            'email.required'   => "We'll need an email so we can actually get back to you.",
            'email.email'      => "That email doesn't look quite right — mind checking for typos?",
            'email.max'        => 'That email address is too long.',

            'subject.required' => 'Give us a quick line on what this is about.',
            'subject.min'      => 'Just a bit more detail in the subject would help.',
            'subject.max'      => 'Keep the subject under 200 characters.',

            'message.required' => "Don't be shy — tell us what's on your mind.",
            'message.min'      => 'A few more details would help us help you (min. 10 characters).',
            'message.max'      => "That's a lot to say! Please keep it under 5000 characters.",
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'name'    => 'name',
            'email'   => 'email',
            'subject' => 'subject',
            'message' => 'message',
        ];
    }

    public function mount(): void
    {
        $this->success = false;
    }

    public function setCategory(string $value): void
    {
        if (in_array($value, $this->allowedCategories, true)) {
            $this->category = $value;
        }
    }

    public function submit(): void
    {


        if (filled($this->website)) {
            Log::info('Contact form honeypot triggered', ['ip' => request()->ip()]);
            $this->fakeSuccess();
            return;
        }

        if ($this->renderedAt !== '' && is_numeric($this->renderedAt)) {
            $elapsedMs = (now()->valueOf()) - (float) $this->renderedAt;
            if ($elapsedMs < 1500) {
                Log::info('Contact form submitted too fast, likely bot', [
                    'ip' => request()->ip(),
                    'elapsed_ms' => $elapsedMs,
                ]);
                $this->fakeSuccess();
                return;
            }
        }

        $this->validate();

        $ip  = request()->ip();
        $key = 'contact-form:' . Str::replace('.', '-', $ip);

        if (RateLimiter::tooManyAttempts($key, maxAttempts: 3)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = (int) ceil($seconds / 60);
            $this->addError(
                'message',
                $minutes > 1
                    ? "You've sent a few messages already. Please wait about {$minutes} minutes before trying again."
                    : "You've sent a few messages already. Please wait about a minute before trying again."
            );
            return;
        }

        RateLimiter::hit($key, decaySeconds: 600);

        Mail::to(config('mail.contact.to', 'contact@polyspheretech.com'))
            ->cc(config('mail.contact.cc', []))
            ->bcc(config('mail.contact.bcc', []))
            ->queue(new ContactMail(
                name: $this->name,
                email: $this->email,
                subject: $this->subject,
                message: $this->message,
                category: $this->category,
            ));

        Mail::to($this->email, $this->name)
            ->later(now()->addSeconds(10), new ContactConfirmationMail(
                name: $this->name,
                subject: $this->subject,
            ));

        Log::info('Contact form submitted', [
            'ip' => $ip,
            'category' => $this->category,
        ]);

        $this->reset(['name', 'email', 'subject', 'message', 'website', 'renderedAt']);
        $this->category = 'General';
        $this->success = true;
    }

    public function dismissSuccess(): void
    {
        $this->success = false;
    }


    protected function fakeSuccess(): void
    {
        $this->reset(['name', 'email', 'subject', 'message', 'website', 'renderedAt']);
        $this->category = 'General';
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.main.contact-component');
    }
}

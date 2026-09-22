<div>
    {{-- Hero --}}
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb"
            style="background-image: url('{{ asset('assets/main/imgs/resources/service.jpg') }}');"></div>
        <div class="breadcrumb__thumb_2"
            style="background-image: url('{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}');"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Apply — {{ $vacancy->title }}</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span><a wire:navigate.hover href="{{ route('vacancies') }}">Careers</a></span>
                                    </li>
                                    <li><span><a wire:navigate.hover
                                                href="{{ route('vacancy.details', $vacancy->slug) }}">{{ \Illuminate\Support\Str::limit($vacancy->title, 30) }}</a></span>
                                    </li>
                                    <li><span>Apply</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wizard --}}
    <section class="wizard-shell section-space">
        <div class="small-container">
            <div class="wizard-grid">
                {{-- Left rail: step indicator --}}
                <aside class="wizard-rail">
                    <div class="wizard-rail__head">
                        <span class="wizard-rail__eyebrow">Applying for</span>
                        <h4 class="wizard-rail__title">{{ $vacancy->title }}</h4>
                        <div class="wizard-rail__dept">
                            {{ $vacancy->department?->name ?? 'General' }}
                            @if($vacancy->location) · {{ $vacancy->location }} @endif
                        </div>
                    </div>

                    @php
                        $steps = [
                            1 => ['label' => 'Your details', 'icon' => 'fa-user'],
                            2 => ['label' => 'Links & work', 'icon' => 'fa-link'],
                            3 => ['label' => 'Experience', 'icon' => 'fa-briefcase'],
                            4 => ['label' => 'CV & letter', 'icon' => 'fa-file-arrow-up'],
                            5 => ['label' => 'Review & send', 'icon' => 'fa-paper-plane'],
                        ];
                    @endphp

                    <ol class="wizard-steps">
                        @foreach($steps as $n => $meta)
                            @php
                                $state = $n < $step ? 'done' : ($n === $step ? 'current' : 'pending');
                            @endphp
                            <li class="wizard-step wizard-step--{{ $state }}">
                                <button type="button" wire:click="goToStep({{ $n }})" @disabled($n > $step)
                                    class="wizard-step__btn">
                                    <span class="wizard-step__num">
                                        @if($state === 'done')
                                            <i class="fa-solid fa-check"></i>
                                        @else
                                            {{ $n }}
                                        @endif
                                    </span>
                                    <span class="wizard-step__label">
                                        <span class="wizard-step__title">{{ $meta['label'] }}</span>
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ol>

                    <div class="wizard-rail__foot">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Your data is stored securely and never shared. You can request deletion any time.</span>
                    </div>
                </aside>

                {{-- Right pane: form --}}
                <div class="wizard-main">

                    {{-- Progress bar --}}
                    <div class="wizard-progress" aria-hidden="true">
                        <div class="wizard-progress__bar"
                            style="width: {{ (($step - 1) / max($totalSteps - 1, 1)) * 100 }}%"></div>
                    </div>

                    {{-- ───────────────────────── STEP 1 ───────────────────────── --}}
                    @if($step === 1)
                        <div class="wizard-pane" wire:key="step-1">
                            <h3 class="wizard-pane__title">Let's start with the basics</h3>
                            <p class="wizard-pane__sub">So we can reach you and know who you are.</p>

                            <div class="wz-grid">
                                <label class="wz-field wz-field--full">
                                    <span>Full name <em>*</em></span>
                                    <input type="text" wire:model.blur="name" placeholder="e.g. Ama Mensah" autofocus>
                                    @error('name') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field">
                                    <span>Email <em>*</em></span>
                                    <input type="email" wire:model.blur="email" placeholder="you@email.com">
                                    @error('email') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field">
                                    <span>Phone <em>*</em></span>
                                    <input type="tel" wire:model.blur="phone" placeholder="+233 20 000 0000">
                                    @error('phone') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field">
                                    <span>City / town</span>
                                    <input type="text" wire:model.blur="location" placeholder="Accra">
                                    @error('location') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field">
                                    <span>Country</span>
                                    <input type="text" wire:model.blur="country" placeholder="Ghana">
                                    @error('country') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>
                            </div>
                        </div>
                    @endif

                    {{-- ───────────────────────── STEP 2 ───────────────────────── --}}
                    @if($step === 2)
                        <div class="wizard-pane" wire:key="step-2">
                            <h3 class="wizard-pane__title">Where can we see your work?</h3>
                            <p class="wizard-pane__sub">
                                @if($this->hasBranch('engineering'))
                                    We actually look at these — a working GitHub is worth more than a bullet point.
                                @elseif($this->hasBranch('design'))
                                    Portfolios tell us more than CVs. Show us what you've made.
                                @elseif($this->hasBranch('marketing'))
                                    We love reading good writing. Share what you're proud of.
                                @elseif($this->hasBranch('sales'))
                                    Numbers and CRM experience matter here — add anything relevant.
                                @else
                                    Any links that show us your work — portfolios, writing, projects.
                                @endif
                            </p>

                            <div class="wz-grid">
                                <label class="wz-field {{ $this->hasBranch('design') ? 'wz-field--required' : '' }}">
                                    <span>Portfolio URL @if($this->hasBranch('design')) <em>*</em> @endif</span>
                                    <input type="url" wire:model.blur="portfolio_url" placeholder="https://…">
                                    @error('portfolio_url') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field {{ $this->hasBranch('engineering') ? 'wz-field--required' : '' }}">
                                    <span>GitHub @if($this->hasBranch('engineering')) <em>*</em> @endif</span>
                                    <input type="url" wire:model.blur="github_url" placeholder="https://github.com/you">
                                    @error('github_url') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                @if($this->hasBranch('marketing'))
                                    <label class="wz-field wz-field--required">
                                        <span>Writing samples <em>*</em></span>
                                        <input type="url" wire:model.blur="writing_samples_url"
                                            placeholder="https://medium.com/…  or a Google Drive link">
                                        @error('writing_samples_url') <small class="wz-error">{{ $message }}</small> @enderror
                                    </label>
                                @endif

                                @if($this->hasBranch('design'))
                                    <label class="wz-field">
                                        <span>Behance / Dribbble</span>
                                        <input type="url" wire:model.blur="behance_url" placeholder="https://behance.net/you">
                                        @error('behance_url') <small class="wz-error">{{ $message }}</small> @enderror
                                    </label>
                                @endif

                                <label class="wz-field">
                                    <span>LinkedIn</span>
                                    <input type="url" wire:model.blur="linkedin_url"
                                        placeholder="https://linkedin.com/in/you">
                                    @error('linkedin_url') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field">
                                    <span>Personal site</span>
                                    <input type="url" wire:model.blur="personal_site_url"
                                        placeholder="https://yoursite.com">
                                    @error('personal_site_url') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>
                            </div>
                        </div>
                    @endif

                    {{-- ───────────────────────── STEP 3 ───────────────────────── --}}
                    @if($step === 3)
                        <div class="wizard-pane" wire:key="step-3">
                            <h3 class="wizard-pane__title">A little about your experience</h3>
                            <p class="wizard-pane__sub">Keep it short — we'll go into depth at the interview.</p>

                            <div class="wz-grid">
                                <label class="wz-field">
                                    <span>Current / last role</span>
                                    <input type="text" wire:model.blur="current_role" placeholder="e.g. Backend Engineer">
                                    @error('current_role') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field">
                                    <span>Company</span>
                                    <input type="text" wire:model.blur="current_company" placeholder="e.g. Acme Ltd">
                                    @error('current_company') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field">
                                    <span>Years of experience <em>*</em></span>
                                    <input type="number" min="0" max="60" wire:model.blur="years_experience"
                                        placeholder="3">
                                    @error('years_experience') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field">
                                    <span>Availability <em>*</em></span>
                                    <select wire:model.blur="availability">
                                        <option value="">Pick one…</option>
                                        <option value="Immediate">I can start immediately</option>
                                        <option value="2 weeks notice">2 weeks notice</option>
                                        <option value="1 month notice">1 month notice</option>
                                        <option value="2 months notice">2 months notice</option>
                                        <option value="Just exploring">Just exploring — no rush</option>
                                    </select>
                                    @error('availability') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field wz-field--full">
                                    <span>Salary expectation (optional)</span>
                                    <input type="text" wire:model.blur="salary_expectation"
                                        placeholder="e.g. $60,000–$75,000 or 'open to discussion'">
                                    @error('salary_expectation') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                @if($this->hasBranch('remote'))
                                    <div class="wz-divider">
                                        <span>Since this role is remote…</span>
                                    </div>

                                    <label class="wz-field">
                                        <span>Your timezone <em>*</em></span>
                                        <input type="text" wire:model.blur="timezone"
                                            placeholder="e.g. GMT (Accra), GMT+1 (Lagos), EST (New York)">
                                        @error('timezone') <small class="wz-error">{{ $message }}</small> @enderror
                                    </label>

                                    <label class="wz-field">
                                        <span>Work authorization <em>*</em></span>
                                        <input type="text" wire:model.blur="work_authorization"
                                            placeholder="e.g. Ghana (citizen), EU (permit), open to relocation…">
                                        @error('work_authorization') <small class="wz-error">{{ $message }}</small> @enderror
                                    </label>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- ───────────────────────── STEP 4 ───────────────────────── --}}
                    @if($step === 4)
                        <div class="wizard-pane" wire:key="step-4">
                            <h3 class="wizard-pane__title">Your CV & a short note</h3>
                            <p class="wizard-pane__sub">PDF or Word, up to 10 MB. Your file is only seen by our hiring team.
                            </p>

                            <div class="wz-grid">
                                <label class="wz-field wz-field--full wz-upload {{ $cv ? 'wz-upload--has-file' : '' }}">
                                    <span>Upload CV <em>*</em></span>
                                    <div class="wz-upload__box">
                                        <i class="fa-solid fa-cloud-arrow-up"></i>
                                        <div class="wz-upload__text">
                                            <strong>{{ $cv ? 'Ready to send' : 'Click to choose a file' }}</strong>
                                            <small>{{ $cv ? $cv->getClientOriginalName() : 'PDF, DOC, or DOCX · 10 MB max' }}</small>
                                        </div>
                                        <input type="file" accept=".pdf,.doc,.docx" wire:model="cv">
                                    </div>
                                    <div wire:loading wire:target="cv" class="wz-upload__loading">
                                        Uploading…
                                    </div>
                                    @error('cv') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>

                                <label class="wz-field wz-field--full">
                                    <span>Cover note (optional — but we read them)</span>
                                    <textarea wire:model.blur="cover_letter" rows="6"
                                        placeholder="Tell us briefly why this role caught your eye. Anything specific you'd want us to know?"></textarea>
                                    @error('cover_letter') <small class="wz-error">{{ $message }}</small> @enderror
                                </label>
                            </div>
                        </div>
                    @endif

                    {{-- ───────────────────────── STEP 5 ───────────────────────── --}}
                    @if($step === 5)
                        <div class="wizard-pane" wire:key="step-5">
                            <h3 class="wizard-pane__title">One last thing, then we'll review</h3>
                            <p class="wizard-pane__sub">You'll get a private link by email to track your application.</p>

                            <div class="wz-grid">
                                <label class="wz-field">
                                    <span>How did you hear about us?</span>
                                    <select wire:model.blur="source">
                                        <option value="">Pick one…</option>
                                        <option value="LinkedIn">LinkedIn</option>
                                        <option value="Google">Google search</option>
                                        <option value="Referral">Referred by someone</option>
                                        <option value="Twitter / X">Twitter / X</option>
                                        <option value="Instagram">Instagram</option>
                                        <option value="Job board">A job board</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </label>

                                <label class="wz-field">
                                    <span>Referrer's name (if any)</span>
                                    <input type="text" wire:model.blur="referrer_name" placeholder="Who told you about us?">
                                </label>

                                <label class="wz-consent wz-field--full">
                                    <input type="checkbox" wire:model.live="gdpr_consent">
                                    <span>
                                        I'm happy for Polysphere Tech to store and process my application.
                                        I understand I can request deletion at any time.
                                    </span>
                                </label>
                                @error('gdpr_consent') <small class="wz-error">{{ $message }}</small> @enderror
                            </div>

                            {{-- Summary --}}
                            <div class="wz-summary">
                                <h5>Quick review</h5>
                                <dl>
                                    <div>
                                        <dt>Name</dt>
                                        <dd>{{ $name ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Email</dt>
                                        <dd>{{ $email ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Phone</dt>
                                        <dd>{{ $phone ?: '—' }}</dd>
                                    </div>
                                    <div>
                                        <dt>CV</dt>
                                        <dd>{{ $cv ? $cv->getClientOriginalName() : '—' }}</dd>
                                    </div>
                                    @if($github_url)
                                        <div>
                                            <dt>GitHub</dt>
                                            <dd>{{ $github_url }}</dd>
                                        </div>
                                    @endif
                                    @if($portfolio_url)
                                        <div>
                                            <dt>Portfolio</dt>
                                            <dd>{{ $portfolio_url }}</dd>
                                        </div>
                                    @endif
                                    <div>
                                        <dt>Experience</dt>
                                        <dd>{{ $years_experience !== null ? $years_experience . ' years' : '—' }}</dd>
                                    </div>
                                </dl>
                                <p class="wz-summary__hint">Need to change something? Click any step on the left.</p>
                            </div>
                        </div>
                    @endif

                    {{-- Footer nav --}}
                    <div class="wizard-nav">
                        <button type="button" class="wizard-nav__back" wire:click="prevStep" @disabled($step === 1)>
                            <i class="fa-solid fa-arrow-left"></i> Back
                        </button>

                        <div class="wizard-nav__hint">
                            Step {{ $step }} of {{ $totalSteps }}
                        </div>

                        @if($step < $totalSteps)
                            <button type="button" class="wizard-nav__next" wire:click="nextStep"
                                wire:loading.attr="disabled" wire:target="nextStep">
                                <span wire:loading.remove wire:target="nextStep">
                                    Continue <i class="fa-solid fa-arrow-right"></i>
                                </span>
                                <span wire:loading wire:target="nextStep">
                                    Checking… <i class="fa-solid fa-circle-notch fa-spin"></i>
                                </span>
                            </button>
                        @else
                            <button type="button" class="wizard-nav__submit" wire:click="submit"
                                wire:loading.attr="disabled" wire:target="submit">
                                <span wire:loading.remove wire:target="submit">
                                    Send application <i class="fa-solid fa-paper-plane"></i>
                                </span>
                                <span wire:loading wire:target="submit">
                                    Sending… <i class="fa-solid fa-circle-notch fa-spin"></i>
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@push('styles')
    <style>
        /* ─── Layout ─── */
        .wizard-shell {
            background: #f6f7fb;
        }

        .wizard-grid {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 28px;
            align-items: start;
        }

        /* ─── Left rail ─── */
        .wizard-rail {
            background: #fff;
            border: 1px solid #e7e9f2;
            border-radius: 18px;
            padding: 26px 22px;
            position: sticky;
            top: 20px;
        }

        .wizard-rail__eyebrow {
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 700;
        }

        .wizard-rail__title {
            font-size: 18px;
            font-weight: 700;
            color: #0d1b2e;
            margin: 6px 0 4px;
            line-height: 1.3;
            letter-spacing: -.2px;
        }

        .wizard-rail__dept {
            font-size: 13px;
            color: #667085;
            margin-bottom: 22px;
        }

        .wizard-steps {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .wizard-step__btn {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            border: none;
            background: transparent;
            padding: 10px 8px;
            border-radius: 10px;
            text-align: left;
            transition: background .15s ease;
        }

        .wizard-step__btn:disabled {
            cursor: not-allowed;
        }

        .wizard-step__num {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #64748b;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
            transition: all .2s ease;
        }

        .wizard-step__title {
            font-size: 14px;
            font-weight: 600;
            color: #475467;
        }

        .wizard-step--current .wizard-step__btn {
            background: rgba(47, 111, 237, .08);
        }

        .wizard-step--current .wizard-step__num {
            background: #2f6fed;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(47, 111, 237, .15);
        }

        .wizard-step--current .wizard-step__title {
            color: #0d1b2e;
        }

        .wizard-step--done .wizard-step__num {
            background: #10b981;
            color: #fff;
        }

        .wizard-step--done .wizard-step__title {
            color: #0d1b2e;
        }

        .wizard-step:not(.wizard-step--current):not(.wizard-step--done) .wizard-step__btn:hover:not(:disabled) {
            background: #f8fafc;
        }

        .wizard-rail__foot {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            margin-top: 22px;
            padding-top: 18px;
            border-top: 1px dashed #e7e9f2;
            font-size: 12px;
            color: #667085;
            line-height: 1.55;
        }

        .wizard-rail__foot i {
            color: #10b981;
            margin-top: 2px;
            flex-shrink: 0;
        }

        /* ─── Main pane ─── */
        .wizard-main {
            background: #fff;
            border: 1px solid #e7e9f2;
            border-radius: 18px;
            padding: 32px 34px 28px;
            min-height: 420px;
            display: flex;
            flex-direction: column;
        }

        .wizard-progress {
            height: 4px;
            background: #eef2f6;
            border-radius: 3px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .wizard-progress__bar {
            height: 100%;
            background: linear-gradient(90deg, #2f6fed, #6366f1);
            border-radius: 3px;
            transition: width .3s ease;
        }

        .wizard-pane__title {
            font-size: 22px;
            font-weight: 700;
            color: #0d1b2e;
            margin: 0 0 6px;
            letter-spacing: -.3px;
        }

        .wizard-pane__sub {
            color: #667085;
            font-size: 14.5px;
            margin: 0 0 26px;
            line-height: 1.6;
        }

        /* ─── Fields ─── */
        .wz-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .wz-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .wz-field--full {
            grid-column: 1 / -1;
        }

        .wz-field>span {
            font-size: 13px;
            font-weight: 600;
            color: #344054;
        }

        .wz-field>span em {
            color: #ef4444;
            font-style: normal;
        }

        .wz-field input[type="text"],
        .wz-field input[type="email"],
        .wz-field input[type="tel"],
        .wz-field input[type="url"],
        .wz-field input[type="number"],
        .wz-field select,
        .wz-field textarea {
            width: 100%;
            border: 1px solid #e7e9f2;
            background: #f8fafc;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 14.5px;
            color: #101828;
            outline: none;
            transition: border-color .15s ease, background .15s ease;
            font-family: inherit;
        }

        .wz-field textarea {
            resize: vertical;
            min-height: 120px;
            line-height: 1.6;
        }

        .wz-field input:focus,
        .wz-field select:focus,
        .wz-field textarea:focus {
            border-color: #2f6fed;
            background: #fff;
        }

        .wz-field input::placeholder,
        .wz-field textarea::placeholder {
            color: #a0a6b8;
        }

        .wz-error {
            color: #ef4444;
            font-size: 12.5px;
            margin-top: 2px;
        }

        .wz-divider {
            grid-column: 1 / -1;
            display: flex;
            align-items: center;
            gap: 14px;
            color: #667085;
            font-size: 12.5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin: 8px 0 0;
        }

        .wz-divider::before,
        .wz-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e7e9f2;
        }

        .wz-divider span {
            flex-shrink: 0;
        }

        /* ─── Consent ─── */
        .wz-consent {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 14px 16px;
            background: #f8fafc;
            border: 1px solid #e7e9f2;
            border-radius: 12px;
        }

        .wz-consent input {
            margin-top: 3px;
            width: 16px;
            height: 16px;
        }

        .wz-consent span {
            font-size: 13.5px;
            color: #475467;
            line-height: 1.55;
        }

        /* ─── Upload ─── */
        .wz-upload__box {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 20px;
            border: 2px dashed #d5d9e8;
            border-radius: 12px;
            background: #fbfcfe;
            position: relative;
            cursor: pointer;
            transition: border-color .15s ease, background .15s ease;
        }

        .wz-upload__box:hover {
            border-color: #2f6fed;
            background: rgba(47, 111, 237, .03);
        }

        .wz-upload__box>i {
            font-size: 26px;
            color: #2f6fed;
            flex-shrink: 0;
        }

        .wz-upload__text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            flex: 1;
            min-width: 0;
        }

        .wz-upload__text strong {
            font-size: 14.5px;
            color: #0d1b2e;
            font-weight: 600;
        }

        .wz-upload__text small {
            font-size: 12.5px;
            color: #94a3b8;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .wz-upload__box input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .wz-upload--has-file .wz-upload__box {
            border-style: solid;
            border-color: #10b981;
            background: rgba(16, 185, 129, .04);
        }

        .wz-upload--has-file .wz-upload__box>i {
            color: #10b981;
        }

        .wz-upload__loading {
            font-size: 12.5px;
            color: #2f6fed;
            margin-top: 6px;
        }

        /* ─── Review summary ─── */
        .wz-summary {
            margin-top: 26px;
            padding: 22px 24px;
            background: #f8fafc;
            border: 1px solid #e7e9f2;
            border-radius: 14px;
        }

        .wz-summary h5 {
            font-size: 13px;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #667085;
            font-weight: 700;
            margin: 0 0 14px;
        }

        .wz-summary dl {
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        .wz-summary dl>div {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 8px 0;
            border-bottom: 1px dashed #e7e9f2;
            font-size: 14px;
        }

        .wz-summary dl>div:last-child {
            border-bottom: none;
        }

        .wz-summary dt {
            color: #667085;
            font-weight: 500;
            margin: 0;
        }

        .wz-summary dd {
            color: #0d1b2e;
            font-weight: 600;
            margin: 0;
            text-align: right;
            max-width: 60%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .wz-summary__hint {
            font-size: 12.5px;
            color: #94a3b8;
            margin: 12px 0 0;
        }

        /* ─── Footer nav ─── */
        .wizard-nav {
            margin-top: auto;
            padding-top: 28px;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .wizard-nav__back,
        .wizard-nav__next,
        .wizard-nav__submit {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            border-radius: 12px;
            padding: 12px 22px;
            font-weight: 600;
            font-size: 14.5px;
            cursor: pointer;
            transition: transform .15s ease, background .15s ease;
        }

        .wizard-nav__back {
            background: transparent;
            color: #64748b;
        }

        .wizard-nav__back:hover:not(:disabled) {
            color: #0d1b2e;
            background: #f8fafc;
        }

        .wizard-nav__back:disabled {
            opacity: .4;
            cursor: not-allowed;
        }

        .wizard-nav__next {
            background: #0d1b2e;
            color: #fff;
            margin-left: auto;
        }

        .wizard-nav__next:hover:not(:disabled) {
            transform: translateY(-1px);
            background: #17263f;
        }

        .wizard-nav__submit {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff;
            margin-left: auto;
            box-shadow: 0 12px 28px rgba(16, 185, 129, .3);
        }

        .wizard-nav__submit:hover:not(:disabled) {
            transform: translateY(-2px);
        }

        .wizard-nav__hint {
            font-size: 13px;
            color: #94a3b8;
        }

        /* ─── Responsive ─── */
        @media (max-width: 991.98px) {
            .wizard-grid {
                grid-template-columns: 1fr;
            }

            .wizard-rail {
                position: static;
            }

            .wizard-main {
                padding: 26px 22px;
            }
        }

        @media (max-width: 575.98px) {
            .wz-grid {
                grid-template-columns: 1fr;
            }

            .wizard-nav__hint {
                display: none;
            }
        }
    </style>
@endpush
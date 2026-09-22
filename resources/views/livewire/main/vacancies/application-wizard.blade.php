<div>
    {{-- ─── Hero ─────────────────────────────────────────────── --}}
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

    {{-- ─── Wizard / Blocked ─────────────────────────────────── --}}
    <section class="wizard-shell section-space">
        <div class="small-container">
            <div class="wizard-grid">

                {{-- ═══════════ BLOCKED: already applied ═══════════ --}}
                @if($showDuplicateBlock && $existingApplication)
                    <div class="wizard-blocked" wire:key="blocked-{{ $existingApplication->id }}">
                        <div class="wizard-blocked__card">
                            <div class="wizard-blocked__icon">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>

                            <span class="wizard-blocked__eyebrow">Application already on file</span>
                            <h3 class="wizard-blocked__title">You've already applied for this role</h3>

                            <p class="wizard-blocked__lede">
                                We received your application for <strong>{{ $vacancy->title }}</strong>
                                on <strong>{{ $existingApplication->created_at->format('F j, Y') }}</strong>.
                                Our team reads every application personally — you'll hear back within a few working days.
                            </p>

                            <dl class="wizard-blocked__facts">
                                <div>
                                    <dt>Role</dt>
                                    <dd>{{ $vacancy->title }}</dd>
                                </div>
                                <div>
                                    <dt>Department</dt>
                                    <dd>{{ $vacancy->department?->name ?? 'General' }}</dd>
                                </div>
                                <div>
                                    <dt>Status</dt>
                                    <dd>
                                        <span
                                            class="badge badge-{{ $existingApplication->statusEnum()->color() }} light border-0">
                                            {{ $existingApplication->statusEnum()->label() }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt>Submitted</dt>
                                    <dd>{{ $existingApplication->created_at->diffForHumans() }}</dd>
                                </div>
                                <div>
                                    <dt>Reference</dt>
                                    <dd><code>{{ strtoupper(substr($existingApplication->tracking_token, 0, 10)) }}</code>
                                    </dd>
                                </div>
                            </dl>

                            <div class="wizard-blocked__actions">
                                <button type="button" class="wz-btn wz-btn--primary" wire:click="resendTrackingLink"
                                    wire:loading.attr="disabled" wire:target="resendTrackingLink" @if($trackingLinkResent)
                                    disabled @endif>
                                    <span wire:loading.remove wire:target="resendTrackingLink">
                                        @if($trackingLinkResent)
                                            <i class="fa-solid fa-check"></i> Tracking link sent
                                        @else
                                            <i class="fa-solid fa-paper-plane"></i> Email me the tracking link
                                        @endif
                                    </span>
                                    <span wire:loading wire:target="resendTrackingLink">
                                        <i class="fa-solid fa-circle-notch fa-spin"></i> Sending…
                                    </span>
                                </button>

                                <a wire:navigate.hover href="{{ route('vacancy.details', $vacancy->slug) }}"
                                    class="wz-btn wz-btn--ghost">
                                    <i class="fa-solid fa-arrow-left"></i> Back to role
                                </a>
                            </div>

                            <p class="wizard-blocked__hint">
                                <i class="fa-regular fa-envelope"></i>
                                We originally sent the tracking link to
                                <strong>{{ $existingApplication->email }}</strong>
                                — bookmark it, it's private to you.
                            </p>

                            <hr class="wizard-blocked__rule">

                            <div class="wizard-blocked__foot">
                                <p>
                                    <strong>Need to update something?</strong>
                                    Reply to your confirmation email or write to
                                    <a href="mailto:careers@polyspheretech.com">careers@polyspheretech.com</a>
                                    — a real person will get back to you.
                                </p>
                                <p>
                                    <strong>Applied with a different email?</strong>
                                    <button type="button" class="wizard-blocked__link" wire:click="resetWizard">
                                        Start a fresh application
                                    </button>
                                </p>
                            </div>
                        </div>
                    </div>
                @else

                    {{-- ═══════════ NORMAL WIZARD ═══════════ --}}

                    {{-- LEFT RAIL: step indicator --}}
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
                                @php $state = $n < $step ? 'done' : ($n === $step ? 'current' : 'pending'); @endphp
                                <li class="wizard-step wizard-step--{{ $state }}">
                                    <button type="button" wire:click="goToStep({{ $n }})" @disabled($n > $step)
                                        class="wizard-step__btn">
                                        <span class="wizard-step__num">
                                            @if($state === 'done') <i class="fa-solid fa-check"></i>
                                            @else {{ $n }} @endif
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

                    {{-- RIGHT PANE: form --}}
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

                                    <label class="wz-field wz-field--full">
                                        <span>Email <em>*</em></span>
                                        <input type="email" wire:model.blur="email" placeholder="you@email.com">
                                        @error('email') <small class="wz-error">{{ $message }}</small> @enderror
                                    </label>

                                    {{-- Phone picker --}}
                                    <div class="wz-field wz-field--full" wire:key="wz-phone-field">
                                        <span>Phone <em>*</em></span>
                                        <div class="wz-phone-wrapper">
                                            <div class="wz-phone-row">
                                                <button type="button" wire:click="toggleCountryDropdown"
                                                    class="wz-phone-country-btn">
                                                    <img src="{{ asset('flags/' . $selectedFlag) }}" alt="">
                                                    <span>{{ $countryCode }}</span>
                                                    <i class="fa-solid fa-chevron-down"></i>
                                                </button>
                                                <input type="tel" inputmode="numeric" wire:model.defer="phone"
                                                    placeholder="{{ $phoneExample ? 'e.g. ' . $phoneExample : 'Phone number' }}"
                                                    maxlength="{{ $countryInfo['maxLength'] ?? 15 }}"
                                                    class="wz-phone-input @error('phone') has-error @enderror" x-data
                                                    x-on:input="
                                                                let v = $el.value.replace(/[^0-9]/g, '');
                                                                let max = {{ $countryInfo['maxLength'] ?? 15 }};
                                                                if (v.length > max) v = v.substring(0, max);
                                                                $el.value = v;
                                                                $wire.setPhone(v);
                                                            ">
                                            </div>

                                            @if($showCountryDropdown)
                                                <div class="wz-country-dropdown" x-data
                                                    x-on:click.outside="$wire.closeCountryDropdown()">
                                                    <div class="wz-country-dropdown__search">
                                                        <input type="text" wire:model.live.debounce.200ms="countrySearch"
                                                            placeholder="Search country…" autofocus>
                                                    </div>
                                                    <div>
                                                        @forelse($filteredCountries as $c)
                                                            <button type="button"
                                                                wire:click="selectPhoneCountry('{{ $c['code'] }}', '{{ $c['flag'] }}')"
                                                                class="wz-country-option {{ $countryCode === $c['code'] ? 'is-selected' : '' }}">
                                                                <img src="{{ asset('flags/' . $c['flag']) }}" alt="">
                                                                <span class="wz-country-option__name">{{ $c['name'] }}</span>
                                                                <span class="wz-country-option__code">{{ $c['code'] }}</span>
                                                            </button>
                                                        @empty
                                                            <div class="wz-country-empty">No countries found</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                        @error('phone') <small class="wz-error">{{ $message }}</small> @enderror
                                        @if($phone)
                                            <small class="wz-hint">
                                                Will be saved as <strong>{{ $countryCode }}{{ $phone }}</strong>
                                            </small>
                                        @endif
                                    </div>

                                    <label class="wz-field">
                                        <span>City / town</span>
                                        <input type="text" wire:model.blur="location" placeholder="Accra">
                                        @error('location') <small class="wz-error">{{ $message }}</small> @enderror
                                    </label>

                                    <label class="wz-field">
                                        <span>Country</span>
                                        <select wire:model.blur="country">
                                            <option value="">Select country…</option>
                                            @foreach($countries as $c)
                                                <option value="{{ $c['name'] }}">{{ $c['name'] }}</option>
                                            @endforeach
                                        </select>
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
                                            <select wire:model.blur="timezone">
                                                <option value="">Pick your timezone…</option>
                                                @foreach($timezoneGroups as $region => $zones)
                                                    <optgroup label="{{ $region }}">
                                                        @foreach($zones as $zone)
                                                            <option value="{{ $zone['value'] }}">{{ $zone['label'] }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                            @error('timezone') <small class="wz-error">{{ $message }}</small> @enderror
                                        </label>

                                        <label class="wz-field">
                                            <span>Work authorization <em>*</em></span>
                                            <select wire:model.blur="work_authorization">
                                                <option value="">Pick one…</option>
                                                <option value="Citizen — I don't need a permit">Citizen — I don't need a permit
                                                </option>
                                                <option value="Permanent resident">Permanent resident</option>
                                                <option value="Have a valid work visa / permit">Have a valid work visa / permit
                                                </option>
                                                <option value="Student visa with work rights">Student visa with work rights</option>
                                                <option value="Would require visa sponsorship">Would require visa sponsorship
                                                </option>
                                                <option value="Open to relocation — no sponsorship needed">Open to relocation — no
                                                    sponsorship needed</option>
                                                <option value="Other / prefer to explain">Other / prefer to explain</option>
                                            </select>
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
                                        <div wire:loading wire:target="cv" class="wz-upload__loading">Uploading…</div>
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
                                            <dd>{{ $phone ? $countryCode . $phone : '—' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Country</dt>
                                            <dd>{{ $country ?: '—' }}</dd>
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
                                        @if($timezone)
                                            <div>
                                                <dt>Timezone</dt>
                                                <dd>{{ $timezone }}</dd>
                                            </div>
                                        @endif
                                        @if($work_authorization)
                                            <div>
                                                <dt>Work auth</dt>
                                                <dd>{{ $work_authorization }}</dd>
                                            </div>
                                        @endif
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
                @endif
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

        .wz-field select {
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12'><path d='M2 4l4 4 4-4' fill='none' stroke='%2394a3b8' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/></svg>");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 12px;
            padding-right: 38px;
        }

        .wz-error {
            color: #ef4444;
            font-size: 12.5px;
            margin-top: 2px;
        }

        .wz-hint {
            color: #667085;
            font-size: 12.5px;
            margin-top: 4px;
        }

        .wz-hint strong {
            color: #2f6fed;
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

        /* ─── Phone picker ─── */
        .wz-phone-wrapper {
            position: relative;
        }

        .wz-phone-row {
            display: flex;
            align-items: stretch;
        }

        .wz-phone-country-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 12px;
            border: 1px solid #e7e9f2;
            border-right: none;
            border-radius: 10px 0 0 10px;
            background: #f8fafc;
            cursor: pointer;
            flex-shrink: 0;
            transition: background .15s ease;
        }

        .wz-phone-country-btn:hover {
            background: #f1f5f9;
        }

        .wz-phone-country-btn img {
            width: 20px;
            height: 14px;
            border-radius: 2px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .wz-phone-country-btn span {
            font-weight: 600;
            font-size: 13.5px;
            color: #344054;
            white-space: nowrap;
        }

        .wz-phone-country-btn i {
            font-size: 10px;
            color: #94a3b8;
            margin-left: 2px;
        }

        .wz-phone-input {
            flex: 1;
            border: 1px solid #e7e9f2;
            border-radius: 0 10px 10px 0;
            background: #f8fafc;
            padding: 11px 14px;
            font-size: 14.5px;
            outline: none;
            transition: border-color .15s ease, background .15s ease;
            min-width: 0;
        }

        .wz-phone-input:focus {
            border-color: #2f6fed;
            background: #fff;
        }

        .wz-phone-input.has-error {
            border-color: #ef4444;
        }

        .wz-country-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            right: 0;
            max-height: 300px;
            background: #fff;
            border: 1px solid #e7e9f2;
            border-radius: 12px;
            box-shadow: 0 16px 40px rgba(13, 27, 46, .12);
            overflow: hidden;
            z-index: 200;
            display: flex;
            flex-direction: column;
        }

        .wz-country-dropdown__search {
            padding: 10px;
            border-bottom: 1px solid #f1f5f9;
            background: #fff;
            flex-shrink: 0;
        }

        .wz-country-dropdown__search input {
            width: 100%;
            padding: 9px 12px;
            border: 1px solid #e7e9f2;
            border-radius: 8px;
            font-size: 13.5px;
            outline: none;
            background: #f8fafc;
        }

        .wz-country-dropdown__search input:focus {
            border-color: #2f6fed;
            background: #fff;
        }

        .wz-country-dropdown>div:last-child {
            overflow-y: auto;
            flex: 1;
        }

        .wz-country-option {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 14px;
            border: none;
            background: transparent;
            font-size: 13.5px;
            text-align: left;
            cursor: pointer;
            transition: background .12s ease;
        }

        .wz-country-option:hover {
            background: #f8fafc;
        }

        .wz-country-option.is-selected {
            background: rgba(47, 111, 237, .08);
            color: #2f6fed;
            font-weight: 600;
        }

        .wz-country-option img {
            width: 20px;
            height: 14px;
            border-radius: 2px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .wz-country-option__name {
            flex: 1;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .wz-country-option__code {
            color: #94a3b8;
            font-size: 12.5px;
            flex-shrink: 0;
        }

        .wz-country-empty {
            padding: 14px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
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

        /* ─── Blocked state ─── */
        .wizard-blocked {
            grid-column: 1 / -1;
            display: flex;
            justify-content: center;
            padding: 0 12px;
        }

        .wizard-blocked__card {
            background: #fff;
            border: 1px solid #e7e9f2;
            border-radius: 20px;
            padding: 44px 48px;
            max-width: 640px;
            width: 100%;
            text-align: center;
            box-shadow: 0 24px 56px rgba(13, 27, 46, .08);
        }

        .wizard-blocked__icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(16, 185, 129, .15), rgba(5, 150, 105, .15));
            color: #10b981;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            margin-bottom: 20px;
        }

        .wizard-blocked__eyebrow {
            display: inline-block;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: #10b981;
            margin-bottom: 10px;
        }

        .wizard-blocked__title {
            font-size: 26px;
            font-weight: 700;
            color: #0d1b2e;
            letter-spacing: -.4px;
            margin: 0 0 14px;
            line-height: 1.25;
        }

        .wizard-blocked__lede {
            font-size: 15px;
            color: #475467;
            line-height: 1.65;
            margin: 0 auto 28px;
            max-width: 520px;
        }

        .wizard-blocked__facts {
            background: #f8fafc;
            border: 1px solid #e7e9f2;
            border-radius: 14px;
            padding: 6px 20px;
            margin: 0 0 26px;
            text-align: left;
        }

        .wizard-blocked__facts>div {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            padding: 12px 0;
            border-bottom: 1px dashed #e7e9f2;
            font-size: 14px;
        }

        .wizard-blocked__facts>div:last-child {
            border-bottom: none;
        }

        .wizard-blocked__facts dt {
            color: #667085;
            font-weight: 500;
            margin: 0;
        }

        .wizard-blocked__facts dd {
            color: #0d1b2e;
            font-weight: 600;
            margin: 0;
            text-align: right;
        }

        .wizard-blocked__facts code {
            font-family: 'SF Mono', Menlo, Consolas, monospace;
            font-size: 12.5px;
            background: #eef2f6;
            padding: 2px 8px;
            border-radius: 6px;
            color: #4338ca;
            letter-spacing: .04em;
        }

        .wizard-blocked__actions {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .wz-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 22px;
            border-radius: 12px;
            font-size: 14.5px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            text-decoration: none;
            transition: transform .15s ease, background .15s ease;
            font-family: inherit;
        }

        .wz-btn--primary {
            background: linear-gradient(135deg, #2f6fed, #4f46e5);
            color: #fff;
            box-shadow: 0 12px 28px rgba(47, 111, 237, .25);
        }

        .wz-btn--primary:hover:not(:disabled) {
            transform: translateY(-1px);
        }

        .wz-btn--primary:disabled {
            opacity: .65;
            cursor: default;
            box-shadow: none;
        }

        .wz-btn--ghost {
            background: #f8fafc;
            color: #475467;
            border: 1px solid #e7e9f2;
        }

        .wz-btn--ghost:hover {
            background: #f1f5f9;
            color: #0d1b2e;
        }

        .wizard-blocked__hint {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #78350f;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 10px 16px;
            margin: 0 0 26px;
            line-height: 1.5;
            text-align: left;
        }

        .wizard-blocked__hint i {
            color: #f59e0b;
            flex-shrink: 0;
        }

        .wizard-blocked__rule {
            border: none;
            border-top: 1px solid #eef2f6;
            margin: 0 0 22px;
        }

        .wizard-blocked__foot {
            text-align: left;
            font-size: 13.5px;
            color: #667085;
            line-height: 1.65;
        }

        .wizard-blocked__foot p {
            margin: 0 0 10px;
        }

        .wizard-blocked__foot p:last-child {
            margin-bottom: 0;
        }

        .wizard-blocked__foot strong {
            color: #0d1b2e;
        }

        .wizard-blocked__foot a {
            color: #2f6fed;
            text-decoration: none;
        }

        .wizard-blocked__foot a:hover {
            text-decoration: underline;
        }

        .wizard-blocked__link {
            background: transparent;
            border: none;
            padding: 0;
            color: #2f6fed;
            font-weight: 600;
            font-size: 13.5px;
            cursor: pointer;
            font-family: inherit;
            text-decoration: underline;
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

        @media (max-width: 767.98px) {
            .wizard-blocked__card {
                padding: 32px 24px;
            }

            .wizard-blocked__title {
                font-size: 22px;
            }

            .wizard-blocked__actions {
                flex-direction: column;
            }

            .wz-btn {
                justify-content: center;
            }
        }

        @media (max-width: 575.98px) {
            .wz-grid {
                grid-template-columns: 1fr;
            }

            .wizard-nav__hint {
                display: none;
            }

            .wz-phone-country-btn {
                padding: 0 10px;
            }

            .wz-phone-country-btn span {
                font-size: 12.5px;
            }
        }
    </style>
@endpush
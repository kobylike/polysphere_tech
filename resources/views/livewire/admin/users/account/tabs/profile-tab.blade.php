<div x-data="profileHandler()" @notify.window="showToast($event.detail)" class="position-relative">

    {{-- Toast --}}
    <div x-show="toastVisible" x-cloak x-transition:enter.duration.300ms.opacity.scale
         x-transition:leave.duration.200ms.opacity.scale
         class="position-fixed top-0 end-0 p-3" style="z-index: 9999; max-width: 420px; width: 100%;">
        <div class="d-flex align-items-center p-3 rounded-4 shadow-lg border-0 text-white gap-3"
             :class="toastType === 'success' ? 'bg-gradient-success' : 'bg-gradient-danger'"
             style="backdrop-filter: blur(8px); background: linear-gradient(135deg, #10b981, #059669);">
            <div class="flex-shrink-0">
                <i class="fas fa-2x" :class="toastType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold" style="color: #ffffff;" x-text="toastTitle"></h6>
                <p class="mb-0 small" style="color: #ffffff; opacity: 0.9;" x-text="toastMessage"></p>
            </div>
            <button @click="dismissToast()" class="btn btn-sm btn-link text-white p-0 opacity-75">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- Avatar Delete Confirmation Modal --}}
    <div x-data="{ open: @entangle('confirmingAvatarDelete') }" x-show="open" x-cloak
         @keydown.escape.window="open = false; $wire.cancelAvatarDelete()">
        <div class="modal-backdrop fade show" x-show="open" x-transition:enter.duration.200ms.opacity
             x-transition:leave.duration.150ms.opacity style="background: rgba(0,0,0,0.5); z-index: 1050;"
             @click="open = false; $wire.cancelAvatarDelete()"></div>
        <div class="modal fade show d-block" x-show="open" x-transition:enter.duration.200ms.opacity.scale
             x-transition:leave.duration.150ms.opacity.scale tabindex="-1" style="z-index: 1060;"
             @click.self="open = false; $wire.cancelAvatarDelete()">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">Remove Avatar?</h5>
                        <button type="button" class="btn-close" @click="open = false; $wire.cancelAvatarDelete()"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash text-danger" style="font-size: 3rem;"></i>
                            <p class="mt-3 text-secondary">Are you sure you want to remove your profile picture?</p>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 justify-content-center gap-3">
                        <button type="button" class="btn btn-light px-4 rounded-pill" @click="open = false; $wire.cancelAvatarDelete()">Cancel</button>
                        <button type="button" class="btn btn-danger px-4 rounded-pill" @click="$wire.deleteAvatar()">Yes, Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row g-4">
        <div class="col-xl-8 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title fw-bold mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save" class="mt-2">

                        {{-- Avatar --}}
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-3 col-12">
                                <label class="form-label fw-semibold mb-md-0">Avatar</label>
                            </div>
                            <div class="col-md-9 col-12">
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <div class="position-relative d-inline-block">
                                        @if($avatar)
                                            <img src="{{ $avatar->temporaryUrl() }}" alt="New Avatar"
                                                 class="rounded-4 border border-2 border-primary"
                                                 style="width: 70px; height: 70px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                        @else
                                            <img src="{{ $user->avatar_url }}" alt="Avatar"
                                                 class="rounded-4 border border-2 border-light"
                                                 style="width: 70px; height: 70px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                        @endif
                                        @if($user->avatar && !$avatar)
                                            <button type="button" wire:click="confirmAvatarDelete"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0"
                                                    style="width: 22px; height: 22px; font-size: 10px; line-height: 22px; transform: translate(30%, -30%);">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control form-control-sm" wire:model="avatar" accept="image/*">
                                        @error('avatar') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                        @if($avatar)
                                            <div class="mt-1"><span class="badge bg-success">New avatar selected</span></div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3 col-12">
                                <label class="form-label fw-semibold mb-md-0">Full Name</label>
                            </div>
                            <div class="col-md-9 col-12">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name" placeholder="Your full name">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-3 col-12">
                                <label class="form-label fw-semibold mb-md-0">Email</label>
                            </div>
                            <div class="col-md-9 col-12">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       wire:model="email" placeholder="Your email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="row mb-3 align-items-start">
                            <div class="col-md-3 col-12">
                                <label class="form-label fw-semibold mb-md-0">Phone</label>
                            </div>
                            <div class="col-md-9 col-12">
                                <div class="phone-wrapper position-relative" wire:key="phone-field">
                                    <div class="input-group">
                                        <button type="button" wire:click="toggleCountryDropdown"
                                                class="btn btn-outline-secondary d-flex align-items-center gap-1 phone-country-btn"
                                                style="border-radius: 0.5rem 0 0 0.5rem; border-right: none; background: #f8fafc; padding: 0.35rem 0.5rem; white-space: nowrap;">
                                            <img src="{{ asset('flags/' . $selectedFlag) }}" class="rounded-1"
                                                 style="width: 18px; height: 12px; object-fit: cover; flex-shrink: 0;">
                                            <span class="fw-semibold text-dark" style="font-size: 0.7rem;">{{ $countryCode }}</span>
                                            <i class="fas fa-chevron-down text-muted" style="font-size: 0.5rem;"></i>
                                        </button>
                                        <input type="tel" inputmode="numeric" wire:model.defer="phone_local"
                                               placeholder="{{ $phoneExample ? 'e.g. ' . $phoneExample : 'Phone number' }}"
                                               maxlength="{{ $countryInfo['maxLength'] ?? 15 }}"
                                               class="form-control phone-number-input @error('phone_local') is-invalid @enderror"
                                               style="border-radius: 0 0.5rem 0.5rem 0; font-size: 0.85rem; padding: 0.35rem 0.65rem;"
                                               x-data x-on:input="
                                                   let v = $el.value.replace(/[^0-9]/g, '');
                                                   let max = {{ $countryInfo['maxLength'] ?? 15 }};
                                                   if (v.length > max) v = v.substring(0, max);
                                                   $el.value = v;
                                                   $wire.setPhone(v);
                                               ">
                                    </div>
                                    @if($showCountryDropdown)
                                        <div class="dropdown-menu show p-0 mt-1 shadow-lg position-absolute phone-country-dropdown"
                                             x-data x-on:click.away="$wire.closeCountryDropdown()">
                                            <div class="sticky-top bg-white p-2 border-bottom">
                                                <input type="text" wire:model.live.debounce.200ms="search"
                                                       placeholder="Search country…" class="form-control form-control-sm"
                                                       style="border-radius: 0.4rem; font-size: 0.75rem; padding: 0.2rem 0.4rem 0.2rem 1.6rem;">
                                                <i class="fas fa-search position-absolute text-muted"
                                                   style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.65rem;"></i>
                                            </div>
                                            <div class="p-1">
                                                @forelse($filteredCountries as $country)
                                                    <button type="button"
                                                            wire:click="selectCountry('{{ $country['code'] }}', '{{ $country['flag'] }}')"
                                                            class="dropdown-item d-flex align-items-center gap-2 py-1 px-2 rounded {{ $countryCode === $country['code'] ? 'active' : '' }}"
                                                            style="font-size: 0.75rem;">
                                                        <img src="{{ asset('flags/' . $country['flag']) }}" class="rounded-1"
                                                             style="width: 18px; height: 12px; object-fit: cover; flex-shrink: 0;">
                                                        <span class="flex-grow-1 text-truncate text-start">{{ $country['name'] }}</span>
                                                        <span class="text-muted flex-shrink-0" style="font-size: 0.65rem;">{{ $country['code'] }}</span>
                                                    </button>
                                                @empty
                                                    <div class="px-2 py-2 text-muted text-center" style="font-size: 0.75rem;">No countries found</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
                                    @if($phone_local)
                                        <div class="form-text text-primary" style="font-size: 0.65rem; margin-top: 0.15rem;">
                                            <i class="fas fa-info-circle me-1"></i> Saved as: <strong>{{ $countryCode }}{{ $phone_local }}</strong>
                                        </div>
                                    @endif
                                    @error('phone_local')
                                        <div class="invalid-feedback d-block" style="font-size: 0.7rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Employee Details --}}
                        @if($user->profile && $user->profile->is_employee)
                            <hr class="my-3">
                            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-briefcase me-2"></i>Employee Details</h6>

                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3 col-12">
                                    <label class="form-label fw-semibold mb-md-0">Gender</label>
                                </div>
                                <div class="col-md-9 col-12">
                                    <select class="form-select @error('gender') is-invalid @enderror" wire:model="gender">
                                        <option value="">Select gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3 col-12">
                                    <label class="form-label fw-semibold mb-md-0">Date of Birth</label>
                                </div>
                                <div class="col-md-9 col-12">
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                           wire:model="date_of_birth" max="{{ now()->subYears(18)->format('Y-m-d') }}">
                                    @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3 col-12">
                                    <label class="form-label fw-semibold mb-md-0">Country</label>
                                </div>
                                <div class="col-md-9 col-12">
                                    <select class="form-select @error('country_code') is-invalid @enderror" wire:model="country_code">
                                        <option value="">Select country</option>
                                        @foreach($countries as $country)
                                            <option value="{{ $country['code'] }}">{{ $country['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('country_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3 align-items-center">
                                <div class="col-md-3 col-12">
                                    <label class="form-label fw-semibold mb-md-0">City</label>
                                </div>
                                <div class="col-md-9 col-12">
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                           wire:model="city" placeholder="e.g. Accra">
                                    @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="row mb-3 align-items-start">
                                <div class="col-md-3 col-12">
                                    <label class="form-label fw-semibold mb-md-0">Emergency Contact</label>
                                </div>
                                <div class="col-md-9 col-12">
                                    <input type="text" class="form-control form-control-sm mb-2 @error('emergency_contact_name') is-invalid @enderror"
                                           wire:model="emergency_contact_name" placeholder="Contact name">
                                    @error('emergency_contact_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    <div class="phone-wrapper position-relative" wire:key="emergency-phone-field">
                                        <div class="input-group">
                                            <button type="button" wire:click="emergency_toggleCountryDropdown"
                                                    class="btn btn-outline-secondary d-flex align-items-center gap-1 phone-country-btn"
                                                    style="border-radius: 0.5rem 0 0 0.5rem; border-right: none; background: #f8fafc; padding: 0.35rem 0.5rem; white-space: nowrap;">
                                                <img src="{{ asset('flags/' . $emergency_selectedFlag) }}" class="rounded-1"
                                                     style="width: 18px; height: 12px; object-fit: cover; flex-shrink: 0;">
                                                <span class="fw-semibold text-dark" style="font-size: 0.7rem;">{{ $emergency_countryCode }}</span>
                                                <i class="fas fa-chevron-down text-muted" style="font-size: 0.5rem;"></i>
                                            </button>
                                            <input type="tel" inputmode="numeric" wire:model.defer="emergency_contact_phone_local"
                                                   placeholder="{{ $emergency_phoneExample ? 'e.g. ' . $emergency_phoneExample : 'Phone number' }}"
                                                   maxlength="{{ $emergency_countryInfo['maxLength'] ?? 15 }}"
                                                   class="form-control phone-number-input @error('emergency_contact_phone_local') is-invalid @enderror"
                                                   style="border-radius: 0 0.5rem 0.5rem 0; font-size: 0.85rem; padding: 0.35rem 0.65rem;"
                                                   x-data x-on:input="
                                                       let v = $el.value.replace(/[^0-9]/g, '');
                                                       let max = {{ $emergency_countryInfo['maxLength'] ?? 15 }};
                                                       if (v.length > max) v = v.substring(0, max);
                                                       $el.value = v;
                                                       $wire.emergency_setPhone(v);
                                                   ">
                                        </div>
                                        @if($emergency_showCountryDropdown)
                                            <div class="dropdown-menu show p-0 mt-1 shadow-lg position-absolute phone-country-dropdown"
                                                 x-data x-on:click.away="$wire.emergency_closeCountryDropdown()">
                                                <div class="sticky-top bg-white p-2 border-bottom">
                                                    <input type="text" wire:model.live.debounce.200ms="emergency_search"
                                                           placeholder="Search country…" class="form-control form-control-sm"
                                                           style="border-radius: 0.4rem; font-size: 0.75rem; padding: 0.2rem 0.4rem 0.2rem 1.6rem;">
                                                    <i class="fas fa-search position-absolute text-muted"
                                                       style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.65rem;"></i>
                                                </div>
                                                <div class="p-1">
                                                    @forelse($emergency_filteredCountries as $country)
                                                        <button type="button"
                                                                wire:click="emergency_selectCountry('{{ $country['code'] }}', '{{ $country['flag'] }}')"
                                                                class="dropdown-item d-flex align-items-center gap-2 py-1 px-2 rounded {{ $emergency_countryCode === $country['code'] ? 'active' : '' }}"
                                                                style="font-size: 0.75rem;">
                                                            <img src="{{ asset('flags/' . $country['flag']) }}" class="rounded-1"
                                                                 style="width: 18px; height: 12px; object-fit: cover; flex-shrink: 0;">
                                                            <span class="flex-grow-1 text-truncate text-start">{{ $country['name'] }}</span>
                                                            <span class="text-muted flex-shrink-0" style="font-size: 0.65rem;">{{ $country['code'] }}</span>
                                                        </button>
                                                    @empty
                                                        <div class="px-2 py-2 text-muted text-center" style="font-size: 0.75rem;">No countries found</div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        @endif
                                        @if($emergency_contact_phone_local)
                                            <div class="form-text text-primary" style="font-size: 0.65rem; margin-top: 0.15rem;">
                                                <i class="fas fa-info-circle me-1"></i> Saved as: <strong>{{ $emergency_countryCode }}{{ $emergency_contact_phone_local }}</strong>
                                            </div>
                                        @endif
                                        @error('emergency_contact_phone_local')
                                            <div class="invalid-feedback d-block" style="font-size: 0.7rem;">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- About Me --}}
                        <div class="row mb-3 align-items-start">
                            <div class="col-md-3 col-12">
                                <label class="form-label fw-semibold mb-md-0">About Me</label>
                            </div>
                            <div class="col-md-9 col-12">
                                <textarea class="form-control @error('about_me') is-invalid @enderror"
                                          wire:model="about_me" rows="4" placeholder="Tell us about yourself…"></textarea>
                                @error('about_me') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Skills --}}
                        <div class="row mb-3 align-items-start">
                            <div class="col-md-3 col-12">
                                <label class="form-label fw-semibold mb-md-0">Skills</label>
                            </div>
                            <div class="col-md-9 col-12">
                                @foreach($skills as $index => $skill)
                                    <div class="row g-1 mb-2 align-items-center" wire:key="skill-{{ $index }}">
                                        <div class="col-sm-4 col-6">
                                            <input type="text" class="form-control form-control-sm" wire:model="skills.{{ $index }}.name" placeholder="Skill name">
                                        </div>
                                        <div class="col-sm-5 col-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="range" class="form-range flex-grow-1" min="1" max="100" step="1"
                                                       wire:model.live="skills.{{ $index }}.level" style="cursor: pointer; padding: 0;">
                                                <span class="badge bg-primary" style="min-width: 40px; font-size: 0.7rem;">{{ $skill['level'] ?? 50 }}%</span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 col-2 text-end">
                                            <button type="button" class="btn btn-sm btn-danger" wire:click="removeSkill({{ $index }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" class="btn btn-sm btn-primary" wire:click="addSkill">
                                    <i class="fas fa-plus me-1"></i> Add Skill
                                </button>
                                @error('skills.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Education --}}
                        <div class="row mb-3 align-items-start">
                            <div class="col-md-3 col-12">
                                <label class="form-label fw-semibold mb-md-0">Education</label>
                            </div>
                            <div class="col-md-9 col-12">
                                @php $yearOptions = range(date('Y') + 5, 1950); @endphp
                                @foreach($education as $index => $edu)
                                    <div class="row g-1 mb-2 align-items-end" wire:key="edu-{{ $index }}" x-data="{
                                        startYear: @entangle('education.' . $index . '.start_year'),
                                        get endYearOptions() {
                                            if (!this.startYear) return @js($yearOptions);
                                            return @js($yearOptions).filter(y => y >= this.startYear);
                                        }
                                    }">
                                        <div class="col-sm-3 col-6">
                                            <input type="text" class="form-control form-control-sm" wire:model="education.{{ $index }}.institution" placeholder="Institution">
                                        </div>
                                        <div class="col-sm-2 col-6">
                                            <input type="text" class="form-control form-control-sm" wire:model="education.{{ $index }}.degree" placeholder="Degree">
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <select class="form-select form-select-sm" wire:model="education.{{ $index }}.start_year" x-model="startYear">
                                                <option value="">Start</option>
                                                @foreach($yearOptions as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-2 col-4">
                                            <select class="form-select form-select-sm" wire:model="education.{{ $index }}.end_year" @if($edu['currently_studying'] ?? false) disabled @endif>
                                                <option value="">End</option>
                                                <template x-for="year in endYearOptions" :key="year">
                                                    <option :value="year" x-text="year"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="col-sm-2 col-4 d-flex align-items-center gap-1">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:change="toggleCurrentlyStudying({{ $index }})" @if($edu['currently_studying'] ?? false) checked @endif>
                                                <label class="form-check-label small">Studying</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 col-2 text-end">
                                            <button type="button" class="btn btn-sm btn-danger" wire:click="removeEducation({{ $index }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                                <button type="button" class="btn btn-sm btn-primary" wire:click="addEducation">
                                    <i class="fas fa-plus me-1"></i> Add Education
                                </button>
                                @error('education.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Social Links --}}
                        <div class="row mb-3 align-items-start">
                            <div class="col-md-3 col-12">
                                <label class="form-label fw-semibold mb-md-0">Social Links</label>
                            </div>
                            <div class="col-md-9 col-12">
                                @foreach($social_links as $platform => $url)
                                    <div class="row g-1 mb-2" wire:key="social-{{ $platform }}">
                                        <div class="col-sm-3 col-4">
                                            <span class="badge bg-secondary">{{ ucfirst($platform) }}</span>
                                        </div>
                                        <div class="col-sm-9 col-8">
                                            <input type="url" class="form-control form-control-sm" wire:model="social_links.{{ $platform }}" placeholder="https://{{ $platform }}.com/your-profile">
                                        </div>
                                    </div>
                                @endforeach
                                @error('social_links.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="row">
                            <div class="col-md-9 offset-md-3 col-12">
                                <button type="submit" class="btn btn-primary btn-sm w-100 w-sm-auto px-4" wire:loading.attr="disabled">
                                    <span wire:loading.remove><i class="fas fa-save me-1"></i> Save Changes</span>
                                    <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Saving…</span>
                                </button>
                                <a href="{{ route('account', ['tab' => 'overview']) }}" wire:navigate class="btn btn-white btn-sm ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-xl-4 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent border-0">
                    <h6 class="card-title fw-bold">Profile Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Add a professional avatar</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Write a compelling "About Me"</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> List your top skills</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Share your education background</li>
                        <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Connect your professional social profiles</li>
                    </ul>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle me-1"></i> Your profile is visible to other team members.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
    @media (max-width: 575.98px) {
        .form-control-sm { font-size: 0.8rem; padding: 0.25rem 0.4rem; }
        .btn-sm { font-size: 0.75rem; padding: 0.25rem 0.5rem; }
        .badge { font-size: 0.65rem; }
        .form-range { height: 4px; }
        .form-range::-webkit-slider-thumb { width: 14px; height: 14px; }
    }
    @media (min-width: 576px) and (max-width: 991.98px) {
        .col-md-3 { width: 25%; }
        .col-md-9 { width: 75%; }
    }
</style>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('profileHandler', () => ({
            toastVisible: false,
            toastType: 'success',
            toastTitle: '',
            toastMessage: '',
            toastTimeout: null,

            init() {
                window.addEventListener('livewire:navigate', () => {
                    this.toastVisible = false;
                    clearTimeout(this.toastTimeout);
                });
            },

            showToast(detail) {
                this.toastType = detail.type || 'success';
                this.toastTitle = detail.title || (this.toastType === 'success' ? 'Success!' : 'Error!');
                this.toastMessage = detail.message || '';
                this.toastVisible = true;
                clearTimeout(this.toastTimeout);
                this.toastTimeout = setTimeout(() => {
                    this.dismissToast();
                }, 4000);
            },

            dismissToast() {
                this.toastVisible = false;
                clearTimeout(this.toastTimeout);
            }
        }));
    });

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            document.querySelectorAll('[x-data]').forEach((el) => {
                if (el.__x && 'toastVisible' in el.__x.$data) {
                    el.__x.$data.toastVisible = false;
                    clearTimeout(el.__x.$data.toastTimeout);
                }
            });
        }
    });
</script>
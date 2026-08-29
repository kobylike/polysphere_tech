<div x-data="profileHandler()" @notify.window="showToast($event.detail)" class="position-relative">


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

    {{-- ─── Avatar Delete Confirmation Modal (Livewire-controlled) ─────── --}}
    {{-- FIX: merged backdrop + modal into ONE Alpine scope (was two separate
         x-data blocks each independently entangled to the same Livewire
         property — that duplication was part of what caused desync/stale
         "still open" state). Backdrop click and click-outside-dialog now
         both close the modal AND notify the server via cancelAvatarDelete(). --}}
    <div x-data="{ open: @entangle('confirmingAvatarDelete') }"
         x-show="open"
         x-cloak
         @keydown.escape.window="open = false; $wire.cancelAvatarDelete()">

        <div class="modal-backdrop fade show"
             x-show="open"
             x-transition:enter.duration.200ms.opacity
             x-transition:leave.duration.150ms.opacity
             style="background: rgba(0,0,0,0.5); z-index: 1050;"
             @click="open = false; $wire.cancelAvatarDelete()"></div>

        <div class="modal fade show d-block"
             x-show="open"
             x-transition:enter.duration.200ms.opacity.scale
             x-transition:leave.duration.150ms.opacity.scale
             tabindex="-1"
             style="z-index: 1060;"
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
                            <p class="mt-3 text-secondary">Are you sure you want to remove your profile picture? This action cannot be undone.</p>
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

    {{-- ─── Main Content ─────────────────────────────────────────────────── --}}
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save" class="mt-2">

                        {{-- Avatar --}}
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold mb-md-0">Avatar</label>
                            </div>
                            <div class="col-md-9">
                                <div class="d-flex flex-wrap align-items-center gap-3">
                                    <div class="position-relative d-inline-block">
                                        @if($avatar)
                                            <img src="{{ $avatar->temporaryUrl() }}" alt="New Avatar"
                                                 class="rounded-4 border border-2 border-primary"
                                                 style="width: 80px; height: 80px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                        @else
                                            <img src="{{ $user->avatar_url }}" alt="Avatar"
                                                 class="rounded-4 border border-2 border-light"
                                                 style="width: 80px; height: 80px; object-fit: cover; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
                                        @endif

                                        @if($user->avatar && !$avatar)
                                            <button type="button" wire:click="confirmAvatarDelete"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 rounded-circle p-0"
                                                    style="width: 24px; height: 24px; font-size: 12px; line-height: 24px; transform: translate(30%, -30%);">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <input type="file" class="form-control" wire:model="avatar" accept="image/*">
                                        @error('avatar') <span class="text-danger small d-block mt-1">{{ $message }}</span> @enderror
                                        @if($avatar)
                                            <div class="mt-1">
                                                <span class="badge bg-success">New avatar selected (save to apply)</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Name --}}
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold mb-md-0">Full Name</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name" placeholder="Your full name">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="row mb-4 align-items-center">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold mb-md-0">Email</label>
                            </div>
                            <div class="col-md-9">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       wire:model="email" placeholder="Your email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="row mb-4 align-items-start">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold mb-md-0">Phone</label>
                            </div>
                            <div class="col-md-9">
                                <div class="phone-wrapper position-relative" wire:key="phone-field">
                                    <div class="input-group">
                                        <button type="button" wire:click="toggleCountryDropdown"
                                                class="btn btn-outline-secondary d-flex align-items-center gap-2 phone-country-btn"
                                                style="border-radius: 0.5rem 0 0 0.5rem; border-right: none; background: #f8fafc; padding: 0.45rem 0.65rem; white-space: nowrap;">
                                            <img src="{{ asset('flags/' . $selectedFlag) }}" class="rounded-1"
                                                 style="width: 22px; height: 15px; object-fit: cover; flex-shrink: 0;">
                                            <span class="fw-semibold text-dark" style="font-size: 0.82rem;">{{ $countryCode }}</span>
                                            <i class="fas fa-chevron-down text-muted" style="font-size: 0.6rem;"></i>
                                        </button>

                                        <input type="tel" inputmode="numeric" wire:model.defer="phone_local"
                                               placeholder="{{ $phoneExample ? 'e.g. ' . $phoneExample : 'Phone number' }}"
                                               maxlength="{{ $countryInfo['maxLength'] ?? 15 }}"
                                               class="form-control phone-number-input @error('phone_local') is-invalid @enderror"
                                               style="border-radius: 0 0.5rem 0.5rem 0; font-size: 0.9rem; padding: 0.45rem 0.75rem;"
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
                                                <div class="position-relative">
                                                    <i class="fas fa-search position-absolute text-muted"
                                                       style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;"></i>
                                                    <input type="text" wire:model.live.debounce.200ms="search"
                                                           placeholder="Search country…" class="form-control form-control-sm"
                                                           style="border-radius: 0.4rem; font-size: 0.8rem; padding: 0.3rem 0.5rem 0.3rem 1.8rem;">
                                                </div>
                                            </div>
                                            <div class="p-1">
                                                @forelse($filteredCountries as $country)
                                                    <button type="button"
                                                            wire:click="selectCountry('{{ $country['code'] }}', '{{ $country['flag'] }}')"
                                                            class="dropdown-item d-flex align-items-center gap-2 py-1 px-2 rounded
                                                                   {{ $countryCode === $country['code'] ? 'active' : '' }}"
                                                            style="font-size: 0.8rem;">
                                                        <img src="{{ asset('flags/' . $country['flag']) }}" class="rounded-1"
                                                             style="width: 20px; height: 14px; object-fit: cover; flex-shrink: 0;">
                                                        <span class="flex-grow-1 text-truncate text-start">{{ $country['name'] }}</span>
                                                        <span class="text-muted flex-shrink-0" style="font-size: 0.7rem;">{{ $country['code'] }}</span>
                                                    </button>
                                                @empty
                                                    <div class="px-2 py-2 text-muted text-center" style="font-size: 0.8rem;">No countries found</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif

                                    @if($phone_local)
                                        <div class="form-text text-primary" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Will be saved as: <strong>{{ $countryCode }}{{ $phone_local }}</strong>
                                        </div>
                                    @endif
                                    @error('phone_local')
                                        <div class="invalid-feedback d-block" style="font-size: 0.75rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <style>
                            @media (max-width: 575.98px) {
                                .phone-country-btn span { font-size: 0.75rem; }
                                .phone-country-btn img { width: 18px; height: 12px; }
                                .phone-number-input { font-size: 0.85rem; padding: 0.4rem 0.5rem; }
                                .phone-country-dropdown { width: calc(100vw - 2.5rem) !important; left: 0 !important; right: auto !important; max-height: 200px; }
                            }
                            @media (min-width: 576px) {
                                .phone-country-dropdown { width: 280px; max-width: 280px; max-height: 240px; }
                            }
                            .phone-country-dropdown { border-radius: 0.6rem; border: 1px solid #e2e8f0; z-index: 1050; overflow-y: auto; }
                            .phone-country-dropdown .dropdown-item:hover { background-color: #f1f5f9; }
                            .phone-country-dropdown .dropdown-item.active { background-color: #e0e7ff; color: #1e293b; }
                        </style>

                        {{-- About Me --}}
                        <div class="row mb-4 align-items-start">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold mb-md-0">About Me</label>
                            </div>
                            <div class="col-md-9">
                                <textarea class="form-control @error('about_me') is-invalid @enderror"
                                          wire:model="about_me" rows="4" placeholder="Tell us about yourself…"></textarea>
                                @error('about_me') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Skills --}}
                        <div class="row mb-4 align-items-start">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold mb-md-0">Skills</label>
                            </div>
                            <div class="col-md-9">
                                @foreach($skills as $index => $skill)
                                    <div class="row g-2 mb-2 align-items-center" wire:key="skill-{{ $index }}">
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control form-control-sm"
                                                   wire:model="skills.{{ $index }}.name" placeholder="Skill name">
                                        </div>
                                        <div class="col-sm-5">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="range" class="form-range flex-grow-1"
                                                       min="1" max="100" step="1"
                                                       wire:model.live="skills.{{ $index }}.level"
                                                       style="cursor: pointer;">
                                                <span class="badge bg-primary" style="min-width: 45px; font-size: 0.85rem;">
                                                    {{ $skill['level'] ?? 50 }}%
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-sm-3 text-end">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="removeSkill({{ $index }})">
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
                        <div class="row mb-4 align-items-start">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold mb-md-0">Education</label>
                            </div>
                            <div class="col-md-9">
                                @php
                                    $currentYear = date('Y');
                                    $yearOptions = range($currentYear + 5, 1950);
                                @endphp

                                @foreach($education as $index => $edu)
                                    <div class="row g-2 mb-3 align-items-end" wire:key="edu-{{ $index }}" x-data="{
                                        startYear: @entangle('education.' . $index . '.start_year'),
                                        get endYearOptions() {
                                            if (!this.startYear) return @js($yearOptions);
                                            return @js($yearOptions).filter(y => y >= this.startYear);
                                        }
                                    }">
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control form-control-sm"
                                                   wire:model="education.{{ $index }}.institution"
                                                   placeholder="Institution">
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" class="form-control form-control-sm"
                                                   wire:model="education.{{ $index }}.degree"
                                                   placeholder="Degree">
                                        </div>
                                        <div class="col-sm-2">
                                            <select class="form-select form-select-sm"
                                                    wire:model="education.{{ $index }}.start_year"
                                                    x-model="startYear">
                                                <option value="">Start</option>
                                                @foreach($yearOptions as $year)
                                                    <option value="{{ $year }}">{{ $year }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-2">
                                            <select class="form-select form-select-sm"
                                                    wire:model="education.{{ $index }}.end_year"
                                                    @if($edu['currently_studying'] ?? false) disabled @endif>
                                                <option value="">End</option>
                                                <template x-for="year in endYearOptions" :key="year">
                                                    <option :value="year" x-text="year"></option>
                                                </template>
                                            </select>
                                        </div>
                                        <div class="col-sm-3 d-flex align-items-center gap-2">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input"
                                                       wire:change="toggleCurrentlyStudying({{ $index }})"
                                                       @if($edu['currently_studying'] ?? false) checked @endif>
                                                <label class="form-check-label small">Currently studying</label>
                                            </div>
                                        </div>
                                        <div class="col-sm-1 text-end">
                                            <button type="button" class="btn btn-sm btn-danger"
                                                    wire:click="removeEducation({{ $index }})">
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
                        <div class="row mb-4 align-items-start">
                            <div class="col-md-3">
                                <label class="form-label fw-semibold mb-md-0">Social Links</label>
                            </div>
                            <div class="col-md-9">
                                @foreach($social_links as $platform => $url)
                                    <div class="row g-2 mb-2" wire:key="social-{{ $platform }}">
                                        <div class="col-sm-3">
                                            <span class="badge bg-secondary">{{ ucfirst($platform) }}</span>
                                        </div>
                                        <div class="col-sm-9">
                                            <input type="url" class="form-control form-control-sm"
                                                   wire:model="social_links.{{ $platform }}"
                                                   placeholder="https://{{ $platform }}.com/your-profile">
                                        </div>
                                    </div>
                                @endforeach
                                @error('social_links.*') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Submit --}}
                        <div class="row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" class="btn btn-primary px-5" wire:loading.attr="disabled">
                                    <span wire:loading.remove><i class="fas fa-save me-1"></i> Save Changes</span>
                                    <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Saving…</span>
                                </button>
                                <a href="{{ route('account', ['tab' => 'overview']) }}" wire:navigate
                                   class="btn btn-white ms-2">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar – Tips --}}
        <div class="col-xl-4">
            <div class="card h-100">
                <div class="card-header">
                    <h6 class="card-title">Profile Tips</h6>
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
</style>

{{-- ─── Alpine Handler ────────────────────────────────────────────────────── --}}
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

            // ── Toast ──
            showToast(detail) {
                this.toastType = detail.type || 'success';
                this.toastTitle = detail.title || (this.toastType === 'success' ? 'Success!' : 'Error!');
                this.toastMessage = detail.message || '';
                this.toastVisible = true;
                clearTimeout(this.toastTimeout);
                this.toastTimeout = setTimeout(() => {
                    this.dismissToast();
                }, 2500);
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
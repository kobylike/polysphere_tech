<div class="position-relative profile-page">

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- PRINT-ONLY LETTERHEAD --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @php
        $ref = 'USR-' . str_pad((string) $user->id, 5, '0', STR_PAD_LEFT);
    @endphp
    <div class="print-only print-letterhead">
        <div class="print-letterhead__brand">
            <div class="print-letterhead__logo">PT</div>
            <div>
                <div class="print-letterhead__name">POLYSPHERE TECH</div>
                <div class="print-letterhead__doc">Employee / User Profile</div>
            </div>
        </div>
        <div class="print-letterhead__meta">
            <div><span>Reference:</span> {{ $ref }}</div>
            <div><span>Username:</span> {{ $user->route_identifier }}</div>
            <div><span>Generated:</span> {{ now()->format('M d, Y · H:i') }}</div>
            <div><span>Printed by:</span> {{ Auth::user()?->name ?? 'System' }}</div>
        </div>
    </div>



    {{-- PAGE TITLES --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">User Details</h5>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M2.125 6.375L8.5 1.41667L14.875 6.375V14.1667C14.875 14.5424 14.7257 14.9027 14.4601 15.1684C14.1944 15.4341 13.8341 15.5833 13.4583 15.5833H3.54167C3.16594 15.5833 2.80561 15.4341 2.53993 15.1684C2.27426 14.9027 2.125 14.5424 2.125 14.1667V6.375Z"
                            stroke="#2C2C2C" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6.375 15.5833V8.5H10.625V15.5833" stroke="#2C2C2C" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Home
                </a>
            </li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">{{ $user->route_identifier }}</a></li>
        </ol>
        <div class="d-flex gap-2">
            @can('update', $user)

                <a href="{{ route('users', ['edit' => $user->route_identifier]) }}" class="btn btn-warning btn-sm"
                    wire:navigate.hover>
                    <i class="fa-solid fa-pen me-1"></i> Edit
                </a>

                <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                    <i class="fa-solid fa-print me-1"></i> Print Profile
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm" x-data="{ copied: false }" x-on:click="
                                            navigator.clipboard.writeText(@js($shareUrl));
                                            copied = true;
                                            setTimeout(() => copied = false, 2000);
                                        ">
                    <span x-show="!copied"><i class="fa-solid fa-link me-1"></i> Copy Link</span>
                    <span x-show="copied" x-cloak><i class="fa-solid fa-check me-1"></i> Copied!</span>
                </button>
            @endcan
            <button class="btn btn-outline-secondary btn-sm" wire:click="resetFilters">
                <i class="fa-solid fa-undo me-1"></i> Reset
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- PROFILE CONTENT (VISIBLE ON PRINT) --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    <div class="container-fluid profile-content">

        {{-- ─── HERO / HEADER ─── --}}
        <section class="profile-hero">
            <div class="profile-hero__bg"></div>
            <div class="profile-hero__inner">
                <div class="profile-hero__avatar-wrap">
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="profile-hero__avatar">
                    @if($user->status === 'active')
                        <span class="profile-hero__status profile-hero__status--active" title="Active"></span>
                    @else
                        <span class="profile-hero__status profile-hero__status--inactive"
                            title="{{ ucfirst($user->status) }}"></span>
                    @endif
                </div>

                <div class="profile-hero__meta">
                    <h1 class="profile-hero__name">{{ $user->name }}</h1>
                    <div class="profile-hero__username">
                        <i class="fa-solid fa-at" style="font-size:11px; opacity:.7;"></i>
                        {{ $user->route_identifier }}
                    </div>
                    <div class="profile-hero__role">
                        {{ $user->profile?->position ?: 'Team Member' }}
                        @if($user->profile?->department?->name)
                            · {{ $user->profile->department->name }}
                        @endif
                    </div>
                    <div class="profile-hero__chips">
                        <span class="ph-chip">
                            <i class="fa-solid fa-envelope"></i> {{ $user->email }}
                        </span>
                        @if($user->phone)
                            <span class="ph-chip">
                                <i class="fa-solid fa-phone"></i> {{ $user->phone }}
                            </span>
                        @endif
                        @if($user->status === 'active')
                            <span class="ph-chip ph-chip--green"><i class="fa-solid fa-circle-check"></i> Active</span>
                        @elseif($user->status === 'suspended')
                            <span class="ph-chip ph-chip--red"><i class="fa-solid fa-ban"></i> Suspended</span>
                        @else
                            <span class="ph-chip ph-chip--gray"><i class="fa-solid fa-circle"></i>
                                {{ ucfirst($user->status ?? 'unknown') }}</span>
                        @endif

                        @if($user->hasVerifiedEmail())
                            <span class="ph-chip ph-chip--blue"><i class="fa-solid fa-shield-check"></i> Verified</span>
                        @else
                            <span class="ph-chip ph-chip--gray"><i class="fa-solid fa-shield-halved"></i> Unverified</span>
                        @endif

                        @if($user->profile?->is_employee)
                            <span class="ph-chip ph-chip--indigo"><i class="fa-solid fa-briefcase"></i> Employee</span>
                        @endif
                        @if($user->profile?->is_spotlight)
                            <span class="ph-chip ph-chip--amber"><i class="fa-solid fa-star"></i> Spotlight</span>
                        @endif
                        @if($user->profile?->is_featured_team)
                            <span class="ph-chip ph-chip--purple"><i class="fa-solid fa-user-group"></i> Featured
                                Team</span>
                        @endif
                        @if($user->two_factor_confirmed_at)
                            <span class="ph-chip ph-chip--blue"><i class="fa-solid fa-lock"></i> 2FA on</span>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        {{-- ─── GRID OF SECTIONS ─── --}}
        <div class="profile-grid">

            {{-- ── Personal Information ── --}}
            <section class="pf-card">
                <h3 class="pf-card__title">
                    <span class="pf-card__icon"><i class="fa-solid fa-user"></i></span>
                    Personal Information
                </h3>
                <dl class="pf-list">
                    <div>
                        <dt>Full name</dt>
                        <dd>{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt>Username</dt>
                        <dd><code class="pf-code">{{ $user->route_identifier }}</code></dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd class="text-break">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt>Phone</dt>
                        <dd>{{ $user->phone ?: '—' }}</dd>
                    </div>
                    @if($user->profile)
                        <div>
                            <dt>Date of birth</dt>
                            <dd>{{ $user->profile->date_of_birth?->format('M d, Y') ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt>Gender</dt>
                            <dd>{{ $user->profile->gender ? ucfirst($user->profile->gender) : '—' }}</dd>
                        </div>
                        <div>
                            <dt>Country</dt>
                            <dd>{{ $user->profile->country_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt>City</dt>
                            <dd>{{ $user->profile->city ?? '—' }}</dd>
                        </div>
                    @endif
                </dl>
            </section>

            {{-- ── Employment Details (employees only) ── --}}
            @if($user->profile?->is_employee)
                <section class="pf-card">
                    <h3 class="pf-card__title">
                        <span class="pf-card__icon pf-card__icon--indigo"><i class="fa-solid fa-briefcase"></i></span>
                        Employment Details
                    </h3>
                    <dl class="pf-list">
                        <div>
                            <dt>Employee ID</dt>
                            <dd><code class="pf-code">{{ $user->profile->employee_id ?? '—' }}</code></dd>
                        </div>
                        <div>
                            <dt>Department</dt>
                            <dd>{{ $user->profile->department?->name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt>Position</dt>
                            <dd>{{ $user->profile->position ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt>Employment type</dt>
                            <dd>{{ $user->profile->employment_type ? ucfirst(str_replace('-', ' ', $user->profile->employment_type)) : '—' }}
                            </dd>
                        </div>
                        <div>
                            <dt>Hire date</dt>
                            <dd>{{ $user->profile->hire_date?->format('M d, Y') ?? '—' }}</dd>
                        </div>
                    </dl>
                </section>
            @endif

            {{-- ── Emergency Contact ── --}}
            @if($user->profile && ($user->profile->emergency_contact_name || $user->profile->emergency_contact_phone))
                <section class="pf-card">
                    <h3 class="pf-card__title">
                        <span class="pf-card__icon pf-card__icon--rose"><i class="fa-solid fa-phone-volume"></i></span>
                        Emergency Contact
                    </h3>
                    <dl class="pf-list">
                        <div>
                            <dt>Name</dt>
                            <dd>{{ $user->profile->emergency_contact_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt>Phone</dt>
                            <dd>{{ $user->profile->emergency_contact_phone ?? '—' }}</dd>
                        </div>
                    </dl>
                </section>
            @endif

            {{-- ── Account & Security ── --}}
            <section class="pf-card">
                <h3 class="pf-card__title">
                    <span class="pf-card__icon pf-card__icon--emerald"><i class="fa-solid fa-shield-halved"></i></span>
                    Account & Security
                </h3>
                <dl class="pf-list">
                    <div>
                        <dt>Account created</dt>
                        <dd>{{ $user->created_at->format('M d, Y · H:i') }}</dd>
                    </div>
                    <div>
                        <dt>Email verified</dt>
                        <dd>
                            @if($user->email_verified_at)
                                {{ $user->email_verified_at->format('M d, Y · H:i') }}
                            @else
                                <span class="text-danger">Not verified</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt>Two-factor auth</dt>
                        <dd>
                            @if($user->two_factor_confirmed_at)
                                Enabled · {{ $user->two_factor_confirmed_at->format('M d, Y') }}
                            @else
                                <span class="text-muted">Disabled</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt>Must change password</dt>
                        <dd>{{ $user->must_change_password ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt>Last login</dt>
                        <dd>{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</dd>
                    </div>
                    @if($user->last_login_ip)
                        <div>
                            <dt>Last login IP</dt>
                            <dd><code class="pf-code">{{ $user->last_login_ip }}</code></dd>
                        </div>
                    @endif
                </dl>
            </section>

            {{-- ── About / Bio ── --}}
            @if($user->profile?->about_me)
                <section class="pf-card pf-card--wide">
                    <h3 class="pf-card__title">
                        <span class="pf-card__icon"><i class="fa-solid fa-quote-left"></i></span>
                        About
                    </h3>
                    <p class="pf-about">{{ $user->profile->about_me }}</p>
                </section>
            @endif

            {{-- ── Skills ── --}}
            @php
                $skills = $user->profile?->skills ?? [];
                $cleanSkills = [];
                foreach ($skills as $s) {
                    $name = is_array($s) ? ($s['name'] ?? null) : $s;
                    $level = is_array($s) ? ($s['level'] ?? null) : null;
                    if ($name)
                        $cleanSkills[] = ['name' => $name, 'level' => $level];
                }
            @endphp
            @if(!empty($cleanSkills))
                <section class="pf-card pf-card--wide">
                    <h3 class="pf-card__title">
                        <span class="pf-card__icon pf-card__icon--amber"><i class="fa-solid fa-lightbulb"></i></span>
                        Skills & Proficiency
                    </h3>
                    <div class="pf-skills">
                        @foreach($cleanSkills as $skill)
                            @php
                                $rawLevel = (int) ($skill['level'] ?? 0);
                                $percent = $rawLevel <= 0 ? 0 : ($rawLevel <= 5 ? $rawLevel * 20 : min(100, $rawLevel));
                            @endphp
                            <div class="pf-skill">
                                <div class="pf-skill__head">
                                    <span class="pf-skill__name">{{ $skill['name'] }}</span>
                                    @if($rawLevel > 0)
                                        <span class="pf-skill__level">{{ $percent }}%</span>
                                    @endif
                                </div>
                                <div class="pf-skill__bar">
                                    <div class="pf-skill__fill" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- ── Education ── --}}
            @php
                $education = $user->profile?->education ?? [];
                $cleanEdu = [];
                foreach ($education as $e) {
                    if (is_array($e)) {
                        $cleanEdu[] = [
                            'title' => $e['degree'] ?? $e['title'] ?? '',
                            'institution' => $e['institution'] ?? $e['school'] ?? null,
                            'start_year' => $e['start_year'] ?? null,
                            'end_year' => $e['end_year'] ?? $e['year'] ?? null,
                        ];
                    } elseif (is_string($e)) {
                        $cleanEdu[] = ['title' => $e, 'institution' => null, 'start_year' => null, 'end_year' => null];
                    }
                }
            @endphp
            @if(!empty($cleanEdu))
                <section class="pf-card">
                    <h3 class="pf-card__title">
                        <span class="pf-card__icon pf-card__icon--sky"><i class="fa-solid fa-graduation-cap"></i></span>
                        Education
                    </h3>
                    <ul class="pf-edu">
                        @foreach($cleanEdu as $edu)
                            @if($edu['title'])
                                <li>
                                    <div class="pf-edu__title">{{ $edu['title'] }}</div>
                                    @if($edu['institution'])
                                        <div class="pf-edu__sub">{{ $edu['institution'] }}</div>
                                    @endif
                                    @if($edu['start_year'] || $edu['end_year'])
                                        <div class="pf-edu__meta">
                                            {{ $edu['start_year'] ?? '?' }} – {{ $edu['end_year'] ?? 'Present' }}
                                        </div>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- ── Links ── --}}
            @php
                $allLinks = array_filter([
                    'LinkedIn' => $user->profile?->linkedin_url,
                    'GitHub' => $user->profile?->github_url,
                    'Portfolio' => $user->profile?->portfolio_url,
                    'Website' => $user->profile?->personal_site_url,
                    'Behance' => $user->profile?->behance_url,
                    'Dribbble' => $user->profile?->dribbble_url,
                ]);
            @endphp
            @if(!empty($allLinks))
                <section class="pf-card">
                    <h3 class="pf-card__title">
                        <span class="pf-card__icon pf-card__icon--sky"><i class="fa-solid fa-link"></i></span>
                        Links
                    </h3>
                    <ul class="pf-links">
                        @foreach($allLinks as $label => $url)
                            <li>
                                <a href="{{ $url }}" target="_blank" rel="noopener">
                                    <span class="pf-links__label">{{ $label }}</span>
                                    <span class="pf-links__url">{{ $url }}</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square pf-links__icon no-print"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            {{-- ── Roles ── --}}
            <section class="pf-card pf-card--wide">
                <h3 class="pf-card__title">
                    <span class="pf-card__icon pf-card__icon--purple"><i class="fa-solid fa-user-shield"></i></span>
                    Roles
                </h3>
                <div class="pf-roles">
                    @forelse($user->roles as $role)
                        <span class="pf-role">{{ $role->name }}</span>
                    @empty
                        <span class="text-muted">No roles assigned</span>
                    @endforelse
                </div>
            </section>

            {{-- ── Recent Activity (screen only) ── --}}
            <section class="pf-card pf-card--wide pf-card--activity no-print">
                <h3 class="pf-card__title">
                    <span class="pf-card__icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                    Recent Activity
                </h3>
                @if($activities->count() > 0)
                    <ul class="pf-activity">
                        @foreach($activities as $activity)
                            <li>
                                <span class="pf-activity__dot"></span>
                                <div class="pf-activity__body">
                                    <div class="pf-activity__desc">{{ $activity->description ?? '—' }}</div>
                                    <div class="pf-activity__meta">
                                        <span>{{ ucfirst(str_replace('_', ' ', (string) $activity->action)) }}</span>
                                        <span>·</span>
                                        <span>{{ $activity->created_at->format('M d, Y · H:i') }}</span>
                                        <span>·</span>
                                        <span>{{ $activity->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fa-solid fa-inbox fs-3 d-block mb-2 opacity-50"></i>
                        No activity recorded yet.
                    </div>
                @endif
            </section>

        </div>

        {{-- ═══════════════════════════════════════════════════════════ --}}
        {{-- PRINT-ONLY SIGNATURE BLOCK --}}
        {{-- ═══════════════════════════════════════════════════════════ --}}
        <div class="print-only print-signature">
            <div class="print-signature__grid">
                <div class="print-signature__line">
                    <div class="print-signature__rule"></div>
                    <div class="print-signature__label">Printed by</div>
                    <div class="print-signature__name">{{ Auth::user()?->name ?? '' }}</div>
                </div>
                <div class="print-signature__line">
                    <div class="print-signature__rule"></div>
                    <div class="print-signature__label">Received / Reviewed by</div>
                    <div class="print-signature__name">&nbsp;</div>
                </div>
                <div class="print-signature__line">
                    <div class="print-signature__rule"></div>
                    <div class="print-signature__label">Date</div>
                    <div class="print-signature__name">&nbsp;</div>
                </div>
            </div>
            <div class="print-signature__foot">
                © {{ date('Y') }} Polysphere Tech · {{ $ref }} · Confidential
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- SCREEN STYLES --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<style>
    .profile-page .no-print {}

    /* ─── Hero ─── */
    .profile-hero {
        position: relative;
        border-radius: 18px;
        overflow: hidden;
        margin-bottom: 22px;
        box-shadow: 0 12px 32px -14px rgba(13, 27, 46, .18);
        border: 1px solid #e7e9f2;
    }

    .profile-hero__bg {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #0F172A 0%, #1E293B 45%, #312E81 100%);
        z-index: 0;
    }

    .profile-hero__bg::after {
        content: '';
        position: absolute;
        inset: 0;
        background:
            radial-gradient(circle at 15% 20%, rgba(99, 102, 241, .35), transparent 55%),
            radial-gradient(circle at 85% 70%, rgba(168, 85, 247, .25), transparent 55%);
    }

    .profile-hero__inner {
        position: relative;
        z-index: 1;
        padding: 32px 34px;
        display: flex;
        align-items: center;
        gap: 22px;
        flex-wrap: wrap;
    }

    .profile-hero__avatar-wrap {
        position: relative;
        flex-shrink: 0;
    }

    .profile-hero__avatar {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid rgba(255, 255, 255, .35);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .3);
    }

    .profile-hero__status {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 3px solid #0F172A;
        box-shadow: 0 0 0 2px rgba(255, 255, 255, .15);
    }

    .profile-hero__status--active {
        background: #10b981;
    }

    .profile-hero__status--inactive {
        background: #ef4444;
    }

    .profile-hero__meta {
        flex: 1;
        min-width: 260px;
    }

    .profile-hero__name {
        color: #fff;
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -.6px;
        margin: 0 0 4px;
        line-height: 1.15;
    }

    .profile-hero__username {
        color: rgba(255, 255, 255, .55);
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: .02em;
        margin-bottom: 6px;
        font-family: 'SF Mono', Menlo, Consolas, monospace;
    }

    .profile-hero__role {
        color: rgba(255, 255, 255, .7);
        font-size: 14.5px;
        font-weight: 500;
        margin-bottom: 14px;
    }

    .profile-hero__chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    .ph-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 11px;
        font-size: 12px;
        font-weight: 600;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        color: rgba(255, 255, 255, .9);
        border: 1px solid rgba(255, 255, 255, .15);
        backdrop-filter: blur(6px);
    }

    .ph-chip i {
        font-size: 10.5px;
        opacity: .85;
    }

    .ph-chip--green {
        background: rgba(16, 185, 129, .18);
        border-color: rgba(16, 185, 129, .35);
        color: #a7f3d0;
    }

    .ph-chip--red {
        background: rgba(239, 68, 68, .18);
        border-color: rgba(239, 68, 68, .35);
        color: #fecaca;
    }

    .ph-chip--blue {
        background: rgba(56, 189, 248, .18);
        border-color: rgba(56, 189, 248, .35);
        color: #bae6fd;
    }

    .ph-chip--indigo {
        background: rgba(99, 102, 241, .2);
        border-color: rgba(99, 102, 241, .4);
        color: #c7d2fe;
    }

    .ph-chip--amber {
        background: rgba(245, 158, 11, .2);
        border-color: rgba(245, 158, 11, .4);
        color: #fde68a;
    }

    .ph-chip--purple {
        background: rgba(168, 85, 247, .2);
        border-color: rgba(168, 85, 247, .4);
        color: #e9d5ff;
    }

    .ph-chip--gray {
        background: rgba(148, 163, 184, .15);
        border-color: rgba(148, 163, 184, .3);
        color: #e2e8f0;
    }

    /* ─── Grid ─── */
    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .pf-card--wide {
        grid-column: 1 / -1;
    }

    @media (max-width: 991.98px) {
        .profile-grid {
            grid-template-columns: 1fr;
        }
    }

    /* ─── Card ─── */
    .pf-card {
        background: #fff;
        border: 1px solid #e7e9f2;
        border-radius: 16px;
        padding: 22px 24px;
        box-shadow: 0 1px 3px rgba(13, 27, 46, .03), 0 8px 24px -12px rgba(13, 27, 46, .06);
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .pf-card:hover {
        border-color: #d5d9e8;
        box-shadow: 0 4px 8px rgba(13, 27, 46, .04), 0 16px 32px -16px rgba(13, 27, 46, .1);
    }

    .pf-card__title {
        font-size: 14px;
        font-weight: 700;
        color: #0d1b2e;
        margin: 0 0 16px;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -.1px;
    }

    .pf-card__icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 13px;
        flex-shrink: 0;
    }

    .pf-card__icon--indigo {
        background: #eef2ff;
        color: #4338ca;
    }

    .pf-card__icon--emerald {
        background: #ecfdf5;
        color: #047857;
    }

    .pf-card__icon--rose {
        background: #fff1f2;
        color: #be123c;
    }

    .pf-card__icon--amber {
        background: #fffbeb;
        color: #b45309;
    }

    .pf-card__icon--sky {
        background: #f0f9ff;
        color: #0369a1;
    }

    .pf-card__icon--purple {
        background: #faf5ff;
        color: #7e22ce;
    }

    /* ─── Definition lists ─── */
    .pf-list {
        display: flex;
        flex-direction: column;
        margin: 0;
    }

    .pf-list>div {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 16px;
        padding: 9px 0;
        border-bottom: 1px dashed #eef2f6;
    }

    .pf-list>div:first-child {
        padding-top: 0;
    }

    .pf-list>div:last-child {
        padding-bottom: 0;
        border-bottom: none;
    }

    .pf-list dt {
        color: #667085;
        font-size: 12.5px;
        font-weight: 500;
        margin: 0;
        flex-shrink: 0;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .pf-list dd {
        color: #0d1b2e;
        font-size: 14px;
        font-weight: 600;
        margin: 0;
        text-align: right;
        word-break: break-word;
    }

    .pf-code {
        font-family: 'SF Mono', Menlo, Consolas, monospace;
        font-size: 12.5px;
        background: #eef2f6;
        padding: 2px 7px;
        border-radius: 5px;
        color: #4338ca;
    }

    /* ─── About ─── */
    .pf-about {
        color: #344054;
        font-size: 14.5px;
        line-height: 1.7;
        margin: 0;
        white-space: pre-line;
    }

    /* ─── Skills ─── */
    .pf-skills {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px 28px;
    }

    @media (max-width: 575.98px) {
        .pf-skills {
            grid-template-columns: 1fr;
        }
    }

    .pf-skill__head {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 6px;
    }

    .pf-skill__name {
        font-size: 13.5px;
        font-weight: 600;
        color: #0d1b2e;
    }

    .pf-skill__level {
        font-size: 11.5px;
        font-weight: 700;
        color: #4338ca;
    }

    .pf-skill__bar {
        height: 6px;
        background: #f1f5f9;
        border-radius: 999px;
        overflow: hidden;
    }

    .pf-skill__fill {
        height: 100%;
        background: linear-gradient(90deg, #6366f1, #4338ca);
        border-radius: inherit;
        transition: width .3s ease;
    }

    /* ─── Education ─── */
    .pf-edu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pf-edu li {
        padding: 12px 0;
        border-bottom: 1px dashed #eef2f6;
    }

    .pf-edu li:first-child {
        padding-top: 0;
    }

    .pf-edu li:last-child {
        padding-bottom: 0;
        border-bottom: none;
    }

    .pf-edu__title {
        font-size: 14px;
        font-weight: 600;
        color: #0d1b2e;
        margin-bottom: 2px;
    }

    .pf-edu__sub {
        font-size: 13px;
        color: #475467;
        margin-bottom: 2px;
    }

    .pf-edu__meta {
        font-size: 12px;
        color: #94a3b8;
        font-weight: 500;
    }

    /* ─── Links ─── */
    .pf-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pf-links li {
        border-bottom: 1px dashed #eef2f6;
    }

    .pf-links li:last-child {
        border-bottom: none;
    }

    .pf-links a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 6px;
        margin: 0 -6px;
        border-radius: 8px;
        text-decoration: none;
        transition: background .12s ease;
        color: inherit;
    }

    .pf-links a:hover {
        background: #f8fafc;
    }

    .pf-links__label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #64748b;
        flex-shrink: 0;
        min-width: 76px;
    }

    .pf-links__url {
        flex: 1;
        min-width: 0;
        font-size: 13.5px;
        font-weight: 500;
        color: #4338ca;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .pf-links__icon {
        font-size: 11px;
        color: #cbd5e1;
        flex-shrink: 0;
    }

    .pf-links a:hover .pf-links__icon {
        color: #4338ca;
    }

    /* ─── Roles ─── */
    .pf-roles {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pf-role {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 999px;
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        color: #3730a3;
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .02em;
        border: 1px solid rgba(99, 102, 241, .15);
    }

    /* ─── Activity ─── */
    .pf-activity {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 420px;
        overflow-y: auto;
    }

    .pf-activity li {
        display: flex;
        gap: 14px;
        align-items: flex-start;
        padding: 10px 0;
        border-bottom: 1px dashed #eef2f6;
    }

    .pf-activity li:last-child {
        border-bottom: none;
    }

    .pf-activity__dot {
        flex-shrink: 0;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #6366f1;
        margin-top: 7px;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, .12);
    }

    .pf-activity__body {
        flex: 1;
        min-width: 0;
    }

    .pf-activity__desc {
        font-size: 13.5px;
        color: #0d1b2e;
        margin-bottom: 3px;
        line-height: 1.5;
    }

    .pf-activity__meta {
        font-size: 11.5px;
        color: #94a3b8;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }

    /* ─── Screen-only helpers ─── */
    .print-only {
        display: none;
    }

    [x-cloak] {
        display: none !important;
    }
</style>

{{-- ═══════════════════════════════════════════════════════════════════ --}}
{{-- PRINT STYLES --}}
{{-- ═══════════════════════════════════════════════════════════════════ --}}
<style>
    @media print {
        @page {
            size: A4;
            margin: 14mm 12mm 18mm 12mm;
        }

        html,
        body {
            background: #fff !important;
            color: #000 !important;
            font-size: 11pt;
            margin: 0 !important;
            padding: 0 !important;
        }

        body * {
            visibility: hidden;
        }

        .profile-content,
        .profile-content * {
            visibility: visible;
        }

        .profile-content {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            max-width: 100%;
            padding: 0 !important;
            margin: 0 !important;
        }

        .no-print,
        .no-print * {
            display: none !important;
            visibility: hidden !important;
        }

        .print-only {
            display: block !important;
            visibility: visible !important;
        }

        .pf-card,
        .pf-card__icon,
        .pf-role,
        .ph-chip,
        .pf-skill__bar,
        .pf-skill__fill,
        .pf-activity__dot {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .print-letterhead {
            display: flex !important;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 12px;
            border-bottom: 2px solid #0F172A;
            margin-bottom: 18px;
        }

        .print-letterhead__brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .print-letterhead__logo {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            background: #0F172A;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: -.5px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .print-letterhead__name {
            font-size: 16pt;
            font-weight: 800;
            letter-spacing: -.4px;
            line-height: 1.1;
            color: #0F172A;
        }

        .print-letterhead__doc {
            font-size: 10.5pt;
            color: #475467;
            margin-top: 2px;
        }

        .print-letterhead__meta {
            text-align: right;
            font-size: 9.5pt;
            color: #475467;
            line-height: 1.6;
        }

        .print-letterhead__meta span {
            color: #94a3b8;
            margin-right: 4px;
        }

        .profile-hero {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            box-shadow: none !important;
            margin-bottom: 14px !important;
            page-break-inside: avoid;
        }

        .profile-hero__bg {
            background: #0F172A !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .profile-hero__bg::after {
            display: none;
        }

        .profile-hero__inner {
            padding: 16px 18px !important;
            gap: 14px !important;
        }

        .profile-hero__avatar {
            width: 64px !important;
            height: 64px !important;
            border: 2px solid #fff !important;
        }

        .profile-hero__status {
            display: none;
        }

        .profile-hero__name {
            font-size: 18pt !important;
            margin-bottom: 2px !important;
        }

        .profile-hero__username {
            font-size: 9pt !important;
            margin-bottom: 4px !important;
        }

        .profile-hero__role {
            font-size: 10.5pt !important;
            margin-bottom: 8px !important;
        }

        .ph-chip {
            background: rgba(255, 255, 255, .15) !important;
            border-color: rgba(255, 255, 255, .25) !important;
            color: #fff !important;
            font-size: 9pt !important;
            padding: 2px 8px !important;
        }

        .profile-grid {
            display: block !important;
            column-count: 1;
        }

        .pf-card {
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            box-shadow: none !important;
            padding: 12px 14px !important;
            margin-bottom: 10px !important;
            page-break-inside: avoid;
        }

        .pf-card__title {
            font-size: 10.5pt !important;
            margin-bottom: 10px !important;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 8px;
        }

        .pf-card__icon {
            width: 22px !important;
            height: 22px !important;
            border-radius: 5px !important;
            font-size: 10px !important;
            background: #e2e8f0 !important;
            color: #0F172A !important;
        }

        .pf-list>div {
            padding: 5px 0 !important;
        }

        .pf-list dt {
            font-size: 9pt !important;
            color: #64748b !important;
        }

        .pf-list dd {
            font-size: 10.5pt !important;
            font-weight: 600;
        }

        .pf-code {
            background: #f1f5f9 !important;
            color: #334155 !important;
            font-size: 9.5pt !important;
        }

        .pf-about {
            font-size: 10.5pt !important;
            line-height: 1.55 !important;
        }

        .pf-skills {
            grid-template-columns: repeat(2, 1fr) !important;
            gap: 10px 22px !important;
        }

        .pf-skill__name {
            font-size: 10pt !important;
        }

        .pf-skill__level {
            font-size: 8.5pt !important;
            color: #4338ca !important;
        }

        .pf-skill__bar {
            height: 4px !important;
            background: #e2e8f0 !important;
        }

        .pf-skill__fill {
            background: #4338ca !important;
        }

        .pf-edu__title {
            font-size: 10.5pt !important;
        }

        .pf-edu__sub {
            font-size: 9.5pt !important;
        }

        .pf-edu__meta {
            font-size: 8.5pt !important;
        }

        .pf-links a {
            padding: 5px 0 !important;
            color: #0F172A !important;
        }

        .pf-links__label {
            font-size: 8.5pt !important;
            min-width: 68px !important;
            color: #64748b !important;
        }

        .pf-links__url {
            color: #0F172A !important;
            font-size: 10pt !important;
            white-space: normal !important;
            word-break: break-all;
        }

        .pf-role {
            background: #e2e8f0 !important;
            color: #0F172A !important;
            font-size: 9.5pt !important;
            padding: 4px 10px !important;
            border: 1px solid #cbd5e1 !important;
        }

        .print-signature {
            margin-top: 26px;
            page-break-inside: avoid;
        }

        .print-signature__grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 24px;
            margin-bottom: 18px;
        }

        .print-signature__rule {
            border-bottom: 1px solid #475467;
            height: 34px;
            margin-bottom: 6px;
        }

        .print-signature__label {
            font-size: 8.5pt;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: 700;
        }

        .print-signature__name {
            font-size: 10pt;
            color: #0F172A;
            font-weight: 600;
            margin-top: 2px;
        }

        .print-signature__foot {
            text-align: center;
            font-size: 8.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 12px;
        }
    }
</style>
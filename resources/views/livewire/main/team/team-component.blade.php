<div>
    <!-- Breadcrumb area start -->
    <div class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/team.jpg') }}"></div>
        <div class="breadcrumb__thumb_2" data-background="assets/imgs/resources/page-title-bg-2.png"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Our Team</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Team</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb area end -->

    <!-- Team area start -->
    <section class="team-section p-relative section-space">
        <div class="small-container">
            <div class="row g-4">
                @forelse($teamMembers as $member)
                    <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-6 mb-15">
                        <div class="team-area-box p-relative mb-60 wow fadeInLeft" data-wow-delay=".7s">
                            <figure class="image w-img p-relative">
                                {{-- Image container with fixed aspect ratio 370x451 --}}
                                <div class="team-image-wrapper">
                                    <img src="{{ $member->avatar_url }}" alt="{{ $member->name }}" class="team-member-img">
                                </div>
                            </figure>
                            <div class="content">
                                <div class="author-info">
                                    <h5 class="mb-5">
                                        <a wire:navigate href="{{ route('team.details', ['slug' => $member->username]) }}">
                                            {{ $member->name }}
                                        </a>
                                    </h5>
                                    <span>{{ $member->position ?? 'Team Member' }}</span>
                                </div>
                                <div class="social-links p-relative">
                                    <span><i class="icon-share"></i></span>
                                    <ul>
                                        @if($member->social_links['linkedin'] ?? false)
                                            <li><a href="{{ $member->social_links['linkedin'] }}" target="_blank"><i
                                                        class="fab fa-linkedin-in"></i></a></li>
                                        @endif
                                        @if($member->social_links['github'] ?? false)
                                            <li><a href="{{ $member->social_links['github'] }}" target="_blank"><i
                                                        class="fab fa-github"></i></a></li>
                                        @endif
                                        @if($member->social_links['twitter'] ?? false)
                                            <li><a href="{{ $member->social_links['twitter'] }}" target="_blank"><i
                                                        class="fab fa-twitter"></i></a></li>
                                        @endif
                                        @if($member->social_links['youtube'] ?? false)
                                            <li><a href="{{ $member->social_links['youtube'] }}" target="_blank"><i
                                                        class="fab fa-youtube"></i></a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <h4>No team members found.</h4>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
    <!-- Team area end -->

    <style>
        .team-image-wrapper {
            position: relative;
            width: 100%;
            aspect-ratio: 370 / 400;
            /* reduced from 451 → 400 */
            overflow: hidden;
            border-radius: 12px 12px 0 0;
            background: #e2e8f0;
        }
    </style>
</div>
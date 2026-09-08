<!-- Header area start -->
<header>
    <!-- Top bar -->
    <div class="container-fluid bg-color-1">
        <div class="header-top">
            <div class="header-top-contact-info">
                <span class="email p-relative">
                    <a href="mailto:contact@polyspheretech.com">contact@polyspheretech.com</a>
                </span>
                <span class="time p-relative">Hours: Mon - Fri: 9.00 AM - 6.00 PM</span>
            </div>
            <div class="header-top-socials">
                <span><a href="https://web.facebook.com/polyspheretech" target="_blank" rel="noopener noreferrer"><i
                            class="fab fa-facebook-f"></i></a></span>
                <span><a href="https://x.com/polyspheretech" target="_blank" rel="noopener noreferrer"><i
                            class="fab fa-twitter"></i></a></span>
                <span><a href="https://www.linkedin.com/company/polysphere-tech/" target="_blank"
                        rel="noopener noreferrer"><i class="fab fa-linkedin-in"></i></a></span>
                <span><a href="https://www.instagram.com/polyspheretech" target="_blank" rel="noopener noreferrer"><i
                            class="fab fa-instagram"></i></a></span>
                <span><a href="https://www.youtube.com/@polyspheretech" target="_blank" rel="noopener noreferrer"><i
                            class="fab fa-youtube"></i></a></span>
            </div>
        </div>
    </div>

    <!-- Main navigation -->
    <div id="header-sticky" class="header-area">
        <div class="large-container">
            <div class="mega-menu-wrapper">
                <div class="header-main">
                    <div class="header-left">
                        <div class="header-logo">
                            <a wire:navigate.hover href="{{ route('index') }}">
                                <img src="{{ asset('assets/main/imgs/logo/logo.png') }}" alt="Polysphere Tech logo">
                            </a>
                        </div>
                    </div>
                    <div class="header-right d-flex justify-content-end">
                        <!-- Main menu -->
                        <div class="mean__menu-wrapper d-none d-lg-block">
                            <div class="main-menu">
                                <nav id="mobile-menu">
                                    <ul>
                                        <!-- Home -->
                                        <li class="{{ request()->routeIs('index') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('index') }}">Home</a>
                                        </li>

                                        <!-- About -->
                                        <li class="{{ request()->routeIs('about') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('about') }}">About</a>
                                        </li>

                                        <!-- Services -->
                                        <li class="has-dropdown {{ request()->routeIs('services*') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('services') }}">Services</a>
                                        </li>

                                        <!-- Explore (Projects, Team, FAQ) -->
                                        <li
                                            class="has-dropdown {{ request()->routeIs('projects*') || request()->routeIs('team*') || request()->routeIs('faq') ? 'active' : '' }}">
                                            <a href="#">Explore</a>
                                            <ul class="submenu">
                                                <li><a wire:navigate.hover href="{{ route('projects') }}">Projects</a>
                                                </li>
                                                <li><a wire:navigate.hover href="{{ route('team') }}">Team</a></li>
                                                <li><a wire:navigate.hover href="{{ route('faq') }}">FAQ</a></li>
                                            </ul>
                                        </li>

                                        <!-- Blog -->
                                        <li
                                            class="{{ request()->routeIs('posts*') || request()->routeIs('blog.details') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('posts') }}">Blog</a>
                                        </li>

                                        <!-- Contact -->
                                        <li class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('contact') }}">Contact</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>

                        <!-- Search toggle -->
                        <div class="search-toggle-open header-search my-auto"
                            x-on:click="$dispatch('open-main-search')">
                            <div class="search-icon">
                                <i class="icon-search"></i>
                            </div>
                        </div>

                        <!-- Shopping cart (optional) -->
                        <div class="header-shopping-cart my-auto">
                            <div class="cart-icon">
                                <a href="#"><i class="icon-shopping-cart"></i></a>
                            </div>
                        </div>

                        <!-- Get a Quote button -->
                        <div class="header-action d-none d-xl-inline-flex gap-5">
                            <div class="header-link">
                                <a class="primary-btn-1 btn-hover" href="{{ route('contact') }}">
                                    GET A QUOTE &nbsp; | <i class="icon-right-arrow"></i>
                                    <span style="top: 147.172px; left: 108.5px;"></span>
                                </a>
                            </div>
                        </div>

                        <!-- Call Us -->
                        <div class="header-action">
                            <div class="header-link-1">
                                <div class="icon">
                                    <i class="fal fa-phone-volume"></i>
                                </div>
                                <div class="content">
                                    <span>Call Us Now</span>
                                    <h6><a href="tel:+1234567890">+1 (234) 567-8900</a></h6>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile hamburger -->
                        <div class="header__hamburger d-xl-none my-auto">
                            <div class="sidebar__toggle">
                                <a class="bar-icon" href="javascript:void(0)">
                                    <i class="fa-light fa-bars-sort"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- Header area end -->
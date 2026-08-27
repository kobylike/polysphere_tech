<!-- Header area start -->
<header>
    <!-- Top bar -->
    <div class="container-fluid bg-color-1">
        <div class="header-top">
            <div class="header-top-contact-info">
                <span class="email p-relative"><a
                        href="mailto:info@polyspheretech.com">info@polyspheretech.com</a></span>
                <span class="time p-relative">Hours: Mon - Fri: 9.00 AM - 6.00 PM</span>
            </div>
            <div class="header-top-socials">
                <span><a href="#"><i class="fab fa-facebook-f"></i></a></span>
                <span><a href="#"><i class="fab fa-twitter"></i></a></span>
                <span><a href="#"><i class="fab fa-linkedin-in"></i></a></span>
                <span><a href="#"><i class="fab fa-youtube"></i></a></span>
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
                                        <li class="has-dropdown {{ request()->routeIs('index') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('index') }}">Home</a>
                                            {{-- <ul class="submenu">
                                                <li><a href="{{ route('index') }}">Home One</a></li>
                                                {{-- <li><a href="index-2.html">Home Two</a></li>
                                                <li><a href="index-3.html">Home Three</a></li>
                                                <li><a href="dark-home.html">Home Dark</a></li>
                                            </ul> --}}
                                        </li>

                                        <!-- About -->
                                        <li class="{{ request()->routeIs('about') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('about') }}">About</a>
                                        </li>

                                        <!-- Services -->
                                        <li class="has-dropdown {{ request()->routeIs('services*') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('services') }}">Services</a>
                                            {{-- <ul class="submenu">
                                                <li><a href="services.html">Services</a></li>
                                                <li><a href="service-details.html">Service Details</a></li>
                                            </ul> --}}
                                        </li>

                                        <!-- Pages -->
                                        <li
                                            class="has-dropdown {{ request()->routeIs('projects*') || request()->routeIs('team*') || request()->routeIs('faq') ? 'active' : '' }}">
                                            <a href="#">Explore</a>
                                            <ul class="submenu">
                                                <li class="has-dropdown"><a wire:navigate.hover
                                                        href="{{ route('projects') }}">Projects</a>
                                                    {{-- <ul class="submenu">
                                                        <li><a href="projects.html">Projects</a></li>
                                                        <li><a href="project-details.html">Projects Details</a></li>
                                                    </ul> --}}
                                                </li>
                                                <li><a wire:navigate.hover href="{{ route('team') }}">Team</a></li>

                                                <li><a wire:navigate.hover href="{{ route('faq') }}">Faq's</a></li>

                                            </ul>
                                        </li>

                                        <!-- Blog -->
                                        <li class="has-dropdown {{ request()->routeIs('blog*') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('posts') }}">Blog</a>
                                            {{-- <ul class="submenu">
                                                <li><a href="blog-standard.html">Blog Default</a></li>
                                                <li><a href="blog-grid.html">Blog Grid</a></li>
                                                <li><a href="blog-details.html">Blog Details</a></li>
                                            </ul> --}}
                                        </li>

                                        <!-- Contact -->
                                        <li class="{{ request()->routeIs('contact') ? 'active' : '' }}">
                                            <a wire:navigate.hover href="{{ route('contact') }}">Contact</a>
                                        </li>
                                    </ul>
                                </nav>
                                <!-- for wp (hidden) -->
                                <div class="header__hamburger ml-50 d-none">
                                    <button type="button" class="hamburger-btn offcanvas-open-btn">
                                        <span>01</span>
                                        <span>01</span>
                                        <span>01</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Search icon -->
                        <div class="search-toggle-open header-search my-auto">
                            <div class="search-icon">
                                <i class="icon-search"></i>
                            </div>
                        </div>

                        <!-- Shopping cart (optional – you can remove if not needed) -->
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
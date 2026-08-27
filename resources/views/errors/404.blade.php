@extends('layouts.app')

@section('content')
    <div>
        <!-- Breadcrumb area start -->
        <div class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
            <div class="breadcrumb__thumb" data-background="{{ asset('assets/main/imgs/resources/404.jpg') }}">
            </div>
            <div class="breadcrumb__thumb_2"
                data-background="{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}"></div>
            <div class="small-container">
                <div class="row justify-content-center">
                    <div class="col-xxl-12">
                        <div class="breadcrumb__wrapper p-relative">
                            <h2 class="breadcrumb__title">Page Not Found</h2>
                            <div class="breadcrumb__menu">
                                <nav>
                                    <ul>
                                        <li><span><a href="{{ route('index') }}">Home</a></span></li>
                                        <li><span>404</span></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Breadcrumb area end -->

        <!-- Error Section -->
        <section class="error-page section-space text-center">
            <div class="small-container">
                <div class="row justify-content-center">
                    <div class="col-xxl-8 col-xl-8 col-lg-10">

                        <!-- Error Image -->
                        <div class="error-image">
                            <img src="{{ asset('assets/main/imgs/resources/error.jpg') }}" alt="404 - Page Not Found">
                        </div>

                        {{-- <!-- Error Code -->
                        <div class="error-code mt-40 mb-15">
                            <span
                                style="font-size: 120px; font-weight: 900; line-height: 1; background: linear-gradient(135deg, #3b82f6, #06b6d4); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; letter-spacing: -4px;">
                                404
                            </span>
                        </div> --}}

                        <!-- Error Title -->
                        <h4 class="mt-20 mb-20" style="font-size: 28px; font-weight: 700; color: #0a0a0a;">
                            Whoops! This Page Got Lost in Conversation
                        </h4>

                        <!-- Error Description -->
                        <p
                            style="font-size: 18px; color: #6c757d; max-width: 500px; margin: 0 auto 30px; line-height: 1.8;">
                            The page you're looking for doesn't exist or has been moved. But don't worry — we're here to get
                            you back on track.
                        </p>

                        <!-- Action Buttons -->
                        <div class="error-btn-box d-flex flex-wrap justify-content-center gap-3">
                            <a class="primary-btn-1 btn-hover" wire:navigate.hover href="{{ route('index') }}">
                                Go back to Home &nbsp; | <i class="icon-right-arrow"></i>
                                <span style="top: 147.172px; left: 108.5px;"></span>
                            </a>
                            <a class="primary-btn-1 btn-hover" wire:navigate.hover href="{{ route('contact') }}"
                                style="background: #1e293b;">
                                Contact Support &nbsp; | <i class="icon-right-arrow"></i>
                                <span style="top: 147.172px; left: 108.5px;"></span>
                            </a>
                        </div>

                        <!-- Quick Links -->
                        <div class="mt-40 pt-30" style="border-top: 1px solid #eef2f6;">
                            <p style="font-size: 14px; color: #94a3b8; margin-bottom: 12px;">
                                <i class="fas fa-compass" style="margin-right: 6px;"></i> Try these pages instead:
                            </p>
                            <div class="d-flex flex-wrap justify-content-center gap-3" style="font-size: 14px;">
                                <a wire:navigate.hover href="{{ route('index') }}"
                                    style="color: #3b82f6; text-decoration: none;">Home</a>
                                <span style="color: #e2e8f0;">|</span>
                                <a wire:navigate.hover href="{{ route('about') }}"
                                    style="color: #3b82f6; text-decoration: none;">About</a>
                                <span style="color: #e2e8f0;">|</span>
                                <a wire:navigate.hover href="{{ route('faq') }}"
                                    style="color: #3b82f6; text-decoration: none;">FAQ</a>
                                <span style="color: #e2e8f0;">|</span>
                                <a wire:navigate.hover href="" style="color: #3b82f6; text-decoration: none;">Services</a>
                                <span style="color: #e2e8f0;">|</span>
                                <a wire:navigate.hover href="{{ route('contact') }}"
                                    style="color: #3b82f6; text-decoration: none;">Contact</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

    </div>
@endsection
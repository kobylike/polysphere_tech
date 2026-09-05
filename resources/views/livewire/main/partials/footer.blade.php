<!-- Footer area start -->
<footer>
    <div class="footer-main bg-color-1">
        <div class="footer-top section-space-medium">
            <div class="small-container">
                <div class="row g-4">
                    <!-- Column 1: Brand Info -->
                    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                        <div class="footer-widget-1">
                            <figure class="image">
                                <img src="{{ asset('assets/main/imgs/logo/logo-white.png') }}" alt="Polysphere Tech">
                            </figure>
                            <p class="mt-40 mb-40" style="font-size: 15px; line-height: 1.8;">
                                Polysphere Tech delivers custom software development, SaaS platforms, and digital
                                transformation solutions that drive business growth.
                            </p>
                            <div class="footer-socials">
                                <span><a href="#"><i class="fab fa-facebook-f"></i></a></span>
                                <span><a href="#"><i class="fab fa-twitter"></i></a></span>
                                <span><a href="#"><i class="fab fa-linkedin-in"></i></a></span>
                                <span><a href="#"><i class="fab fa-youtube"></i></a></span>
                            </div>
                        </div>
                    </div>

                    <!-- Column 2: Services -->
                    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                        <div class="footer-widget-2 pl-50">
                            <h4 class="mb-20 footer-title">Our Services</h4>
                            <ul class="service-list">
                                <li><a href="service-details.html">Custom Software Development</a></li>
                                <li><a href="service-details.html">SaaS Platform Engineering</a></li>
                                <li><a href="service-details.html">Digital Transformation</a></li>
                                <li><a href="service-details.html">IT Consulting</a></li>
                                <li><a href="service-details.html">Cloud & Cyber (Coming Soon)</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Column 3: Latest Blog Posts -->
                    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                        <div class="footer-widget-3">
                            <h4 class="mb-20 footer-title">Latest Insights</h4>
                            <ul class="blog-list">
                                <li>
                                    <div class="footer-blog-post-box mb-15">
                                        <figure class="thumb">
                                            <img src="{{ asset('assets/main/imgs/blog/blog-s-1.jpg') }}" alt="Blog">
                                        </figure>
                                        <div class="content">
                                            <span class="date"><a href="news-details.html">15 Jan, 2026</a></span>
                                            <h6><a href="news-details.html">Top 5 SaaS Trends Shaping 2026</a></h6>
                                        </div>
                                    </div>
                                </li>
                                <li>
                                    <div class="footer-blog-post-box mb-15">
                                        <figure class="thumb">
                                            <img src="{{ asset('assets/main/imgs/blog/blog-s-2.jpg') }}" alt="Blog">
                                        </figure>
                                        <div class="content">
                                            <span class="date"><a href="news-details.html">10 Jan, 2026</a></span>
                                            <h6><a href="news-details.html">A Practical Guide to Digital
                                                    Transformation</a></h6>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Column 4: Newsletter -->
                    <div class="col-xxl-3 col-xl-3 col-lg-3 col-md-6">
                        <div class="footer-widget-4 pr-30">
                            <h4 class="mb-20 footer-title">Newsletter</h4>
                            <p style="font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
                                Subscribe for exclusive insights, industry trends, and special offers.
                            </p>
                            <div class="footer-subscribe">
                                <form action="#">
                                    <input type="email" name="email" placeholder="Your email address" required
                                        style="width: 100%; padding: 12px 16px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #fff; border-radius: 8px; font-size: 14px; margin-bottom: 12px;">
                                    <button type="submit" class="primary-btn-1 btn-hover"
                                        style="width: 100%; padding: 12px; text-align: center; font-size: 13px; letter-spacing: 1px;">
                                        SUBSCRIBE NOW
                                        <span style="top: 147.172px; left: 108.5px;"></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="small-container">
            <div class="footer-bottom pt-30 pb-30" style="border-top: 1px solid rgba(255,255,255,0.06);">
                <div class="left-area" style="text-align: left;">
                    <span style="font-size: 14px; color: rgba(255,255,255,0.6);">
                        © {{ now()->year }}
                        <a href="#" style="color: #fff; text-decoration: none;">PolySphere Tech</a>.
                        All rights reserved.
                    </span>
                </div>
                <div class="right-area" style="text-align: right;">
                    <span style="font-size: 14px; color: rgba(255,255,255,0.6);"><a href="#"
                            style="color: rgba(255,255,255,0.6); text-decoration: none; transition: color 0.3s;">Terms &
                            Conditions</a></span>
                    <span style="font-size: 14px; color: rgba(255,255,255,0.6); margin-left: 20px;"><a href="#"
                            style="color: rgba(255,255,255,0.6); text-decoration: none; transition: color 0.3s;">Privacy
                            Policy</a></span>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- Footer area end -->

<!-- ========== MOBILE OPTIMIZATION STYLES ========== -->
<style>
    /* Mobile-first responsive improvements */
    @media (max-width: 767.98px) {

        /* Give the container breathing room from screen edges */
        .footer-main .small-container {
            padding-left: 24px !important;
            padding-right: 24px !important;
        }

        /* Column spacing */
        .footer-widget-1,
        .footer-widget-2,
        .footer-widget-3,
        .footer-widget-4 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }

        /* Remove left padding on services column */
        .footer-widget-2.pl-50 {
            padding-left: 0 !important;
        }

        /* Add a divider between stacked sections instead of relying on gap alone */
        .row.g-4>[class*="col-"] {
            padding-top: 28px !important;
            padding-bottom: 28px !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .row.g-4>[class*="col-"]:last-child {
            border-bottom: none;
            padding-bottom: 0 !important;
        }

        .row.g-4>[class*="col-"]:first-child {
            padding-top: 0 !important;
        }

        /* Center the brand column for a cleaner mobile look */
        .footer-widget-1 {
            text-align: center;
        }

        .footer-widget-1 .image {
            display: flex;
            justify-content: center;
        }

        .footer-widget-1 img {
            max-width: 160px;
            height: auto;
        }

        .footer-widget-1 p {
            margin-top: 20px !important;
            margin-bottom: 20px !important;
            font-size: 14px !important;
        }

        .footer-socials {
            display: flex;
            justify-content: center;
        }

        /* Blog post thumbnails - smaller on mobile */
        .footer-blog-post-box .thumb {
            width: 60px !important;
            height: 60px !important;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.05);
        }

        .footer-blog-post-box .thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }

        /* Blog post content spacing */
        .footer-blog-post-box {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }

        .footer-blog-post-box .content h6 {
            font-size: 13px !important;
            line-height: 1.4 !important;
            margin-bottom: 2px !important;
        }

        .footer-blog-post-box .content .date {
            font-size: 11px !important;
        }

        /* Newsletter - full width */
        .footer-subscribe input,
        .footer-subscribe button {
            width: 100% !important;
            font-size: 14px !important;
        }

        /* Footer bottom - stack vertically on mobile */
        .footer-bottom {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            text-align: center !important;
            gap: 10px !important;
            padding-left: 24px !important;
            padding-right: 24px !important;
        }

        .footer-bottom .left-area,
        .footer-bottom .right-area {
            text-align: center !important;
            width: 100% !important;
        }

        .footer-bottom .right-area span {
            display: inline-block;
            margin: 0 10px !important;
        }

        .footer-bottom .right-area span:first-child {
            margin-left: 0 !important;
        }

        .footer-bottom .right-area span:last-child {
            margin-right: 0 !important;
        }

        /* Reduce top padding on mobile */
        .section-space-medium {
            padding-top: 40px !important;
            padding-bottom: 32px !important;
        }

        /* Make title smaller on mobile */
        .footer-title {
            font-size: 18px !important;
            margin-bottom: 15px !important;
        }

        /* Service list - better spacing */
        .service-list li {
            margin-bottom: 8px !important;
        }

        .service-list li a {
            font-size: 14px !important;
        }

        /* Social icons - adjust size */
        .footer-socials span {
            margin: 0 5px !important;
        }

        .footer-socials span a {
            width: 36px !important;
            height: 36px !important;
            line-height: 36px !important;
            font-size: 14px !important;
        }
    }

    /* Tablet adjustments (768px - 991px) */
    @media (min-width: 768px) and (max-width: 991.98px) {
        .footer-widget-2.pl-50 {
            padding-left: 20px !important;
        }

        .footer-widget-4.pr-30 {
            padding-right: 0 !important;
        }

        /* Constrain the logo so it doesn't dominate a narrow column */
        .footer-widget-1 .image img {
            max-width: 140px !important;
            height: auto !important;
        }

        .footer-widget-1 p {
            font-size: 14px !important;
            margin-top: 24px !important;
            margin-bottom: 24px !important;
        }

        /* Two-column grid reads better with a bit more row spacing */
        .row.g-4 {
            row-gap: 32px !important;
        }

        .footer-title {
            font-size: 17px !important;
            margin-bottom: 16px !important;
        }

        .footer-blog-post-box .thumb {
            width: 64px !important;
            height: 64px !important;
        }

        .footer-blog-post-box .content h6 {
            font-size: 13px !important;
            line-height: 1.4 !important;
        }

        .footer-blog-post-box .content .date {
            font-size: 11px !important;
        }

        .service-list li a {
            font-size: 14px !important;
        }
    }

    /* Small desktop (992px - 1199px) */
    @media (min-width: 992px) and (max-width: 1199.98px) {
        .footer-widget-2.pl-50 {
            padding-left: 30px !important;
        }
    }

    /* Hover effect for footer links */
    .service-list li a:hover,
    .footer-blog-post-box .content h6 a:hover,
    .footer-bottom .right-area span a:hover,
    .footer-bottom .left-area span a:hover {
        color: #fff !important;
        opacity: 1 !important;
        transition: all 0.3s ease;
    }

    /* Better spacing for blog posts */
    .footer-blog-post-box {
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }

    .footer-blog-post-box .thumb {
        flex-shrink: 0;
        width: 80px;
        height: 80px;
        overflow: hidden;
        border-radius: 8px;
    }

    .footer-blog-post-box .thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .footer-blog-post-box:hover .thumb img {
        transform: scale(1.05);
    }

    .footer-blog-post-box .content h6 {
        font-size: 14px;
        line-height: 1.5;
        margin-bottom: 2px;
    }

    .footer-blog-post-box .content h6 a {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-blog-post-box .content h6 a:hover {
        color: #fff;
    }

    .footer-blog-post-box .content .date a {
        color: rgba(255, 255, 255, 0.5);
        font-size: 12px;
        text-decoration: none;
    }
</style>
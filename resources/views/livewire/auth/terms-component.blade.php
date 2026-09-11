<div x-data="legalPage()" x-init="init()" @scroll.window="onScroll()">

    {{-- Reading Progress Bar --}}
    <div class="legal-progress" :style="`width: ${progress}%`"></div>

    {{-- Breadcrumb --}}
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb"
            style="background-image: url('{{ asset('assets/main/imgs/resources/terms.jpg') }}');">
        </div>
        <div class="breadcrumb__thumb_2"
            style="background-image: url('{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}');"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Terms & Conditions</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Terms & Conditions</span></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Meta Strip --}}
    <div class="legal-meta-strip">
        <div class="container">
            <div class="legal-meta-inner">
                <div class="legal-meta-item">
                    <i class="fal fa-calendar-alt"></i>
                    <span>Effective: <strong>{{ $lastUpdated }}</strong></span>
                </div>
                <div class="legal-meta-item">
                    <i class="fal fa-code-branch"></i>
                    <span>Version: <strong>{{ $version }}</strong></span>
                </div>
                <div class="legal-meta-item">
                    <i class="fal fa-clock"></i>
                    <span>~14 min read</span>
                </div>
                <div class="legal-meta-item legal-meta-actions">
                    <button type="button" @click="toggleToc()" class="legal-btn-toggle d-lg-none">
                        <i class="fal fa-list"></i> Contents
                    </button>
                    <button type="button" onclick="window.print()" class="legal-btn-icon" title="Print">
                        <i class="fal fa-print"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Section --}}
    <section class="legal-section">
        <div class="container">
            <div class="row g-4">

                {{-- TOC Sidebar (Desktop) --}}
                <aside class="col-lg-4 col-xl-3 d-none d-lg-block">
                    <div class="legal-toc">
                        <div class="legal-toc-header">
                            <h6>On this page</h6>
                        </div>
                        <nav class="legal-toc-nav">
                            <ul>
                                <li><a href="#intro" @click.prevent="scrollTo('intro')" :class="active === 'intro' ? 'active' : ''">1. Introduction</a></li>
                                <li><a href="#definitions" @click.prevent="scrollTo('definitions')" :class="active === 'definitions' ? 'active' : ''">2. Definitions</a></li>
                                <li><a href="#eligibility" @click.prevent="scrollTo('eligibility')" :class="active === 'eligibility' ? 'active' : ''">3. Eligibility</a></li>
                                <li><a href="#accounts" @click.prevent="scrollTo('accounts')" :class="active === 'accounts' ? 'active' : ''">4. Accounts & Security</a></li>
                                <li><a href="#use" @click.prevent="scrollTo('use')" :class="active === 'use' ? 'active' : ''">5. Acceptable Use</a></li>
                                <li><a href="#ip" @click.prevent="scrollTo('ip')" :class="active === 'ip' ? 'active' : ''">6. Intellectual Property</a></li>
                                <li><a href="#ugc" @click.prevent="scrollTo('ugc')" :class="active === 'ugc' ? 'active' : ''">7. User Content</a></li>
                                <li><a href="#payments" @click.prevent="scrollTo('payments')" :class="active === 'payments' ? 'active' : ''">8. Payments (Paystack)</a></li>
                                <li><a href="#refunds" @click.prevent="scrollTo('refunds')" :class="active === 'refunds' ? 'active' : ''">9. Refunds</a></li>
                                <li><a href="#sla" @click.prevent="scrollTo('sla')" :class="active === 'sla' ? 'active' : ''">10. Service Availability</a></li>
                                <li><a href="#thirdparty" @click.prevent="scrollTo('thirdparty')" :class="active === 'thirdparty' ? 'active' : ''">11. Third-Party Services</a></li>
                                <li><a href="#confidentiality" @click.prevent="scrollTo('confidentiality')" :class="active === 'confidentiality' ? 'active' : ''">12. Confidentiality</a></li>
                                <li><a href="#warranties" @click.prevent="scrollTo('warranties')" :class="active === 'warranties' ? 'active' : ''">13. Warranties</a></li>
                                <li><a href="#liability" @click.prevent="scrollTo('liability')" :class="active === 'liability' ? 'active' : ''">14. Liability</a></li>
                                <li><a href="#indemnity" @click.prevent="scrollTo('indemnity')" :class="active === 'indemnity' ? 'active' : ''">15. Indemnification</a></li>
                                <li><a href="#termination" @click.prevent="scrollTo('termination')" :class="active === 'termination' ? 'active' : ''">16. Termination</a></li>
                                <li><a href="#law" @click.prevent="scrollTo('law')" :class="active === 'law' ? 'active' : ''">17. Governing Law</a></li>
                                <li><a href="#disputes" @click.prevent="scrollTo('disputes')" :class="active === 'disputes' ? 'active' : ''">18. Disputes</a></li>
                                <li><a href="#changes" @click.prevent="scrollTo('changes')" :class="active === 'changes' ? 'active' : ''">19. Changes</a></li>
                                <li><a href="#contact" @click.prevent="scrollTo('contact')" :class="active === 'contact' ? 'active' : ''">20. Contact</a></li>
                            </ul>
                        </nav>
                        <div class="legal-toc-footer">
                            <a href="{{ route('privacy') }}" wire:navigate.hover class="legal-toc-link">
                                <i class="fal fa-shield-alt"></i>
                                Privacy Policy
                            </a>
                        </div>
                    </div>
                </aside>

                {{-- Mobile TOC Drawer --}}
                <div class="legal-toc-mobile d-lg-none" :class="{ 'open': tocOpen }">
                    <div class="legal-toc-mobile-backdrop" @click="toggleToc()"></div>
                    <div class="legal-toc-mobile-panel">
                        <div class="legal-toc-mobile-header">
                            <h6>On this page</h6>
                            <button @click="toggleToc()"><i class="fal fa-times"></i></button>
                        </div>
                        <nav class="legal-toc-nav">
                            <ul>
                                <li><a href="#intro" @click.prevent="scrollTo('intro'); toggleToc()">1. Introduction</a></li>
                                <li><a href="#definitions" @click.prevent="scrollTo('definitions'); toggleToc()">2. Definitions</a></li>
                                <li><a href="#eligibility" @click.prevent="scrollTo('eligibility'); toggleToc()">3. Eligibility</a></li>
                                <li><a href="#accounts" @click.prevent="scrollTo('accounts'); toggleToc()">4. Accounts & Security</a></li>
                                <li><a href="#use" @click.prevent="scrollTo('use'); toggleToc()">5. Acceptable Use</a></li>
                                <li><a href="#ip" @click.prevent="scrollTo('ip'); toggleToc()">6. Intellectual Property</a></li>
                                <li><a href="#ugc" @click.prevent="scrollTo('ugc'); toggleToc()">7. User Content</a></li>
                                <li><a href="#payments" @click.prevent="scrollTo('payments'); toggleToc()">8. Payments (Paystack)</a></li>
                                <li><a href="#refunds" @click.prevent="scrollTo('refunds'); toggleToc()">9. Refunds</a></li>
                                <li><a href="#sla" @click.prevent="scrollTo('sla'); toggleToc()">10. Service Availability</a></li>
                                <li><a href="#thirdparty" @click.prevent="scrollTo('thirdparty'); toggleToc()">11. Third-Party Services</a></li>
                                <li><a href="#confidentiality" @click.prevent="scrollTo('confidentiality'); toggleToc()">12. Confidentiality</a></li>
                                <li><a href="#warranties" @click.prevent="scrollTo('warranties'); toggleToc()">13. Warranties</a></li>
                                <li><a href="#liability" @click.prevent="scrollTo('liability'); toggleToc()">14. Liability</a></li>
                                <li><a href="#indemnity" @click.prevent="scrollTo('indemnity'); toggleToc()">15. Indemnification</a></li>
                                <li><a href="#termination" @click.prevent="scrollTo('termination'); toggleToc()">16. Termination</a></li>
                                <li><a href="#law" @click.prevent="scrollTo('law'); toggleToc()">17. Governing Law</a></li>
                                <li><a href="#disputes" @click.prevent="scrollTo('disputes'); toggleToc()">18. Disputes</a></li>
                                <li><a href="#changes" @click.prevent="scrollTo('changes'); toggleToc()">19. Changes</a></li>
                                <li><a href="#contact" @click.prevent="scrollTo('contact'); toggleToc()">20. Contact</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="col-lg-8 col-xl-9">
                    <article class="legal-article">

                        {{-- Notice --}}
                        <div class="legal-notice">
                            <div class="legal-notice-icon"><i class="fal fa-info-circle"></i></div>
                            <div>
                                <strong>Important</strong>
                                <p class="mb-0">Please read these Terms & Conditions carefully before using our website or services. By accessing or using any part of the platform, you agree to be bound by these Terms.</p>
                            </div>
                        </div>

                        {{-- 1 --}}
                        <section id="intro" class="legal-section-block">
                            <div class="legal-section-number">01</div>
                            <h2>Introduction</h2>
                            <p>These Terms & Conditions ("Terms") govern your access to and use of the website, applications, software, SaaS platforms, and IT services (collectively, the "Services") provided by <strong>Polysphere Tech</strong>, a company duly registered under the laws of the Republic of Ghana ("Company", "we", "us", "our").</p>
                            <p>By accessing, browsing, registering, or using our Services in any manner, you ("User", "you") acknowledge that you have read, understood, and agree to be bound by these Terms, together with our <a href="{{ route('privacy') }}" wire:navigate.hover>Privacy Policy</a> and any other applicable policies referenced herein.</p>
                            <p>If you do not agree with these Terms, you must immediately cease all use of our Services.</p>
                        </section>

                        {{-- 2 --}}
                        <section id="definitions" class="legal-section-block">
                            <div class="legal-section-number">02</div>
                            <h2>Definitions</h2>
                            <ul>
                                <li><strong>"Account"</strong> – a personal or organizational profile created to access specific Services.</li>
                                <li><strong>"Content"</strong> – any text, images, code, data, files, or materials submitted to or displayed on the Services.</li>
                                <li><strong>"Client"</strong> – an individual or organization that has entered into a separate agreement with us for professional services.</li>
                                <li><strong>"Subscription"</strong> – a paid, recurring plan granting access to premium features.</li>
                                <li><strong>"Paystack"</strong> – our designated third-party payment processor for card and mobile money transactions.</li>
                                <li><strong>"Personal Data"</strong> – has the meaning ascribed under the Ghana Data Protection Act, 2012 (Act 843).</li>
                            </ul>
                        </section>

                        {{-- 3 --}}
                        <section id="eligibility" class="legal-section-block">
                            <div class="legal-section-number">03</div>
                            <h2>Eligibility</h2>
                            <p>You must be at least <strong>18 years of age</strong> and legally competent to enter into binding contracts under Ghanaian law. By using the Services, you represent and warrant that:</p>
                            <ul>
                                <li>You have the legal capacity to enter into these Terms;</li>
                                <li>You are not barred from using the Services under any applicable law; and</li>
                                <li>All information you provide is accurate, current, and complete.</li>
                            </ul>
                            <div class="legal-highlight">
                                <i class="fal fa-user-lock"></i>
                                <span>Access to our platform is currently <strong>by invitation only</strong>. Public registration is disabled.</span>
                            </div>
                        </section>

                        {{-- 4 --}}
                        <section id="accounts" class="legal-section-block">
                            <div class="legal-section-number">04</div>
                            <h2>Accounts & Security</h2>
                            <p>When you create an Account, you agree to:</p>
                            <ul>
                                <li>Provide accurate, current, and complete information;</li>
                                <li>Maintain the confidentiality of your login credentials;</li>
                                <li>Immediately notify us of any unauthorized access or security breach;</li>
                                <li>Accept full responsibility for all activities under your Account.</li>
                            </ul>
                            <p>We strongly recommend enabling <strong>Two-Factor Authentication (2FA)</strong> on your Account. We reserve the right to suspend or terminate any Account that appears compromised or is used in violation of these Terms.</p>
                        </section>

                        {{-- 5 --}}
                        <section id="use" class="legal-section-block">
                            <div class="legal-section-number">05</div>
                            <h2>Acceptable Use</h2>
                            <p>You agree <strong>not</strong> to:</p>
                            <ul>
                                <li>Use the Services for any unlawful purpose or in violation of Ghanaian law;</li>
                                <li>Attempt to gain unauthorized access to any part of the Services;</li>
                                <li>Introduce viruses, malware, or any harmful code;</li>
                                <li>Interfere with or disrupt the integrity or performance of the Services;</li>
                                <li>Harvest or collect user data without consent;</li>
                                <li>Impersonate any person or entity;</li>
                                <li>Post abusive, defamatory, obscene, or infringing content;</li>
                                <li>Use automated systems (bots, scrapers) without express written permission.</li>
                            </ul>
                            <p>Violation of this section may result in immediate termination and may be reported to the appropriate authorities.</p>
                        </section>

                        {{-- 6 --}}
                        <section id="ip" class="legal-section-block">
                            <div class="legal-section-number">06</div>
                            <h2>Intellectual Property</h2>
                            <p>All intellectual property rights in the Services – including but not limited to source code, designs, logos, trademarks, documentation, and content – are owned by Polysphere Tech or its licensors and are protected by Ghanaian and international copyright laws.</p>
                            <p>You are granted a limited, non-exclusive, non-transferable, revocable license to use the Services strictly for their intended purpose. You may <strong>not</strong> copy, modify, distribute, reverse-engineer, or create derivative works without express written permission.</p>
                        </section>

                        {{-- 7 --}}
                        <section id="ugc" class="legal-section-block">
                            <div class="legal-section-number">07</div>
                            <h2>User-Generated Content</h2>
                            <p>You retain ownership of any Content you submit to the Services (such as blog comments, chat messages, or uploaded files). However, by submitting Content, you grant us a worldwide, royalty-free, non-exclusive license to store, display, and process that Content as necessary to operate the Services.</p>
                            <p>You represent and warrant that you own or have the necessary rights to any Content you submit and that it does not violate any third-party rights or applicable law.</p>
                            <p>We reserve the right to remove any Content that we deem, in our sole discretion, to be unlawful, offensive, or in violation of these Terms.</p>
                        </section>

                        {{-- 8 --}}
                        <section id="payments" class="legal-section-block">
                            <div class="legal-section-number">08</div>
                            <h2>Payments & Billing (Paystack)</h2>
                            <p>All payments for our Services are processed securely through <strong>Paystack</strong>, a licensed payment service provider approved by the Bank of Ghana under the Payment Systems and Services Act, 2019 (Act 987).</p>
                            <p>By making a payment, you agree to Paystack's <a href="https://paystack.com/terms" target="_blank" rel="noopener">Terms of Service</a> and acknowledge that:</p>
                            <ul>
                                <li>Polysphere Tech does <strong>not</strong> store, process, or have access to your full card details;</li>
                                <li>All card data is handled exclusively by Paystack in compliance with PCI-DSS;</li>
                                <li>We accept payment via card, bank transfer, USSD, and mobile money as supported by Paystack;</li>
                                <li>All prices are quoted in the currency shown at checkout and may be subject to applicable taxes and fees.</li>
                            </ul>
                            <p><strong>Subscriptions:</strong> Recurring payments will be automatically charged on the renewal date. You may cancel at any time via your Account settings, and cancellation takes effect at the end of the current billing period.</p>
                            <p><strong>Failed Payments:</strong> If a payment fails, we may suspend access to paid features until the outstanding balance is settled.</p>
                        </section>

                        {{-- 9 --}}
                        <section id="refunds" class="legal-section-block">
                            <div class="legal-section-number">09</div>
                            <h2>Refunds & Cancellations</h2>
                            <p>Because our Services involve digital delivery of software and content, refunds are subject to the following policy:</p>
                            <ul>
                                <li><strong>Subscriptions:</strong> Refunds are not provided for partial billing periods. Cancellations stop future renewals but do not refund the current period.</li>
                                <li><strong>One-time Services:</strong> Refunds may be granted at our discretion if requested within <strong>7 days</strong> of purchase, provided the Service has not been substantially used.</li>
                                <li><strong>Custom Development Projects:</strong> Refunds are governed by the specific project agreement signed between you and Polysphere Tech.</li>
                            </ul>
                            <p>To request a refund, contact us at <a href="mailto:contact@polyspheretech.com">contact@polyspheretech.com</a> with your order details.</p>
                        </section>

                        {{-- 10 --}}
                        <section id="sla" class="legal-section-block">
                            <div class="legal-section-number">10</div>
                            <h2>Service Availability</h2>
                            <p>We strive to maintain <strong>99.5% uptime</strong> for our hosted Services, excluding scheduled maintenance, force majeure events, or third-party outages. However, we do not guarantee uninterrupted or error-free operation.</p>
                            <p>Scheduled maintenance will be communicated in advance where possible. Emergency maintenance may occur without notice.</p>
                        </section>

                        {{-- 11 --}}
                        <section id="thirdparty" class="legal-section-block">
                            <div class="legal-section-number">11</div>
                            <h2>Third-Party Services</h2>
                            <p>Our Services may integrate with third-party platforms, including but not limited to:</p>
                            <ul>
                                <li><strong>Paystack</strong> – payment processing</li>
                                <li><strong>Google</strong> – analytics, authentication, cloud services</li>
                                <li><strong>Cloud hosting providers</strong> – infrastructure</li>
                                <li><strong>Email service providers</strong> – transactional emails</li>
                                <li><strong>Communication APIs</strong> – SMS and messaging</li>
                            </ul>
                            <p>Your use of such third-party services is subject to their respective terms and privacy policies. We are not responsible for the acts or omissions of any third-party provider.</p>
                        </section>

                        {{-- 12 --}}
                        <section id="confidentiality" class="legal-section-block">
                            <div class="legal-section-number">12</div>
                            <h2>Confidentiality</h2>
                            <p>Each party agrees to keep confidential any non-public information disclosed by the other party in connection with the Services. This obligation survives termination of these Terms for a period of <strong>five (5) years</strong>.</p>
                            <p>Confidential information does not include information that: (a) is or becomes publicly available without breach; (b) was already known to the receiving party; or (c) is required to be disclosed by law.</p>
                        </section>

                        {{-- 13 --}}
                        <section id="warranties" class="legal-section-block">
                            <div class="legal-section-number">13</div>
                            <h2>Warranties & Disclaimers</h2>
                            <div class="legal-highlight">
                                <i class="fal fa-exclamation-triangle"></i>
                                <span><strong>THE SERVICES ARE PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND</strong>, whether express, implied, or statutory, including but not limited to warranties of merchantability, fitness for a particular purpose, and non-infringement.</span>
                            </div>
                            <p>We do not warrant that: (a) the Services will meet your requirements; (b) the Services will be uninterrupted, timely, secure, or error-free; or (c) any errors will be corrected.</p>
                        </section>

                        {{-- 14 --}}
                        <section id="liability" class="legal-section-block">
                            <div class="legal-section-number">14</div>
                            <h2>Limitation of Liability</h2>
                            <p>To the maximum extent permitted by Ghanaian law, Polysphere Tech shall <strong>not</strong> be liable for any indirect, incidental, special, consequential, or punitive damages, including loss of profits, data, or goodwill, arising out of or related to your use of the Services.</p>
                            <p>Our total aggregate liability for any claim arising under these Terms shall not exceed the amount you paid to us in the <strong>twelve (12) months</strong> preceding the claim.</p>
                        </section>

                        {{-- 15 --}}
                        <section id="indemnity" class="legal-section-block">
                            <div class="legal-section-number">15</div>
                            <h2>Indemnification</h2>
                            <p>You agree to indemnify, defend, and hold harmless Polysphere Tech, its directors, employees, and agents from and against any claims, damages, losses, liabilities, and expenses arising from: (a) your use of the Services; (b) your violation of these Terms; or (c) your violation of any third-party right.</p>
                        </section>

                        {{-- 16 --}}
                        <section id="termination" class="legal-section-block">
                            <div class="legal-section-number">16</div>
                            <h2>Termination</h2>
                            <p>We may suspend or terminate your access to the Services at any time, with or without notice, if you breach these Terms or if we reasonably believe your use poses a risk to us or other users.</p>
                            <p>Upon termination: (a) your license to use the Services ends immediately; (b) you must cease all use; and (c) any provisions that by their nature should survive will survive, including IP, liability, indemnity, and governing law.</p>
                        </section>

                        {{-- 17 --}}
                        <section id="law" class="legal-section-block">
                            <div class="legal-section-number">17</div>
                            <h2>Governing Law</h2>
                            <p>These Terms shall be governed by and construed in accordance with the laws of the <strong>Republic of Ghana</strong>, without regard to its conflict of law principles. This includes, but is not limited to:</p>
                            <ul>
                                <li>Companies Act, 2019 (Act 992)</li>
                                <li>Data Protection Act, 2012 (Act 843)</li>
                                <li>Electronic Transactions Act, 2008 (Act 772)</li>
                                <li>Payment Systems and Services Act, 2019 (Act 987)</li>
                            </ul>
                        </section>

                        {{-- 18 --}}
                        <section id="disputes" class="legal-section-block">
                            <div class="legal-section-number">18</div>
                            <h2>Dispute Resolution</h2>
                            <p>The parties agree to first attempt to resolve any dispute amicably through good-faith negotiations. If unresolved within <strong>30 days</strong>, the dispute shall be referred to mediation in Accra, Ghana. If mediation fails, the dispute shall be resolved by the courts of competent jurisdiction in Accra, Ghana.</p>
                        </section>

                        {{-- 19 --}}
                        <section id="changes" class="legal-section-block">
                            <div class="legal-section-number">19</div>
                            <h2>Changes to These Terms</h2>
                            <p>We reserve the right to modify these Terms at any time. Material changes will be communicated via email or a prominent notice on the website at least <strong>14 days</strong> before they take effect.</p>
                            <p>Your continued use of the Services after the effective date constitutes acceptance of the revised Terms.</p>
                        </section>

                        {{-- 20 --}}
                        <section id="contact" class="legal-section-block">
                            <div class="legal-section-number">20</div>
                            <h2>Contact Information</h2>
                            <div class="legal-info-card">
                                <h6>Polysphere Tech</h6>
                                <ul>
                                    <li><i class="fal fa-map-marker-alt"></i> Accra, Ghana</li>
                                    <li><i class="fal fa-envelope"></i> <a href="mailto:contact@polyspheretech.com">contact@polyspheretech.com</a></li>
                                    <li><i class="fal fa-phone"></i> <a href="tel:+233597563427">+233 (59) 756-3427</a></li>
                                    <li><i class="fal fa-globe"></i> <a href="{{ url('/') }}">{{ url('/') }}</a></li>
                                </ul>
                            </div>
                        </section>

                        {{-- Footer Note --}}
                        <div class="legal-footer-note">
                            <div>
                                <strong>Last Updated:</strong> {{ $lastUpdated }} · <strong>Version:</strong> {{ $version }}
                            </div>
                            <div class="mt-2">© {{ date('Y') }} Polysphere Tech. All rights reserved.</div>
                        </div>

                    </article>
                </div>

            </div>
        </div>
    </section>

    {{-- Back to top --}}
    <button type="button" class="legal-back-to-top" x-show="showTop" x-transition @click="window.scrollTo({ top: 0, behavior: 'smooth' })" style="display:none;">
        <i class="fal fa-arrow-up"></i>
    </button>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
    Shared Styles for Legal Pages — responsive, mobile-first
    ═══════════════════════════════════════════════════════════════════════ --}}
<style>
    /* ─── Reading Progress Bar ─── */
    .legal-progress {
        position: fixed;
        top: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, #3b82f6, #06b6d4);
        z-index: 9999;
        transition: width 0.1s ease;
        width: 0;
    }

    /* ─── Meta Strip ─── */
    .legal-meta-strip {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        z-index: 100;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .legal-meta-inner {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 14px 0;
        flex-wrap: wrap;
        font-size: 14px;
        color: #475569;
    }
    .legal-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legal-meta-item i {
        color: #3b82f6;
        font-size: 14px;
    }
    .legal-meta-actions {
        margin-left: auto;
        gap: 10px;
    }
    .legal-btn-toggle {
        background: #3b82f6;
        color: #fff;
        border: 0;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }
    .legal-btn-icon {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    .legal-btn-icon:hover {
        background: #3b82f6;
        color: #fff;
        border-color: #3b82f6;
    }

    /* ─── Main Section ─── */
    .legal-section {
        background: #f8fafc;
        padding: 60px 0 80px;
    }
    @media (max-width: 991px) {
        .legal-section { padding: 40px 0 60px; }
        .legal-meta-inner { gap: 14px; padding: 12px 0; }
        .legal-meta-item { font-size: 13px; }
        .legal-meta-actions { width: 100%; margin-left: 0; justify-content: space-between; }
    }

    /* ─── TOC Sidebar (Desktop) ─── */
    .legal-toc {
        position: sticky;
        top: 90px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .legal-toc-header {
        padding: 18px 22px;
        border-bottom: 1px solid #f1f5f9;
    }
    .legal-toc-header h6 {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        color: #64748b;
    }
    .legal-toc-nav {
        max-height: 60vh;
        overflow-y: auto;
        padding: 12px 8px;
    }
    .legal-toc-nav ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .legal-toc-nav li a {
        display: block;
        padding: 7px 14px;
        font-size: 13.5px;
        color: #64748b;
        text-decoration: none;
        border-radius: 8px;
        border-left: 3px solid transparent;
        transition: all 0.15s;
    }
    .legal-toc-nav li a:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .legal-toc-nav li a.active {
        color: #3b82f6;
        background: #eff6ff;
        border-left-color: #3b82f6;
        font-weight: 600;
    }
    .legal-toc-footer {
        padding: 14px 22px;
        border-top: 1px solid #f1f5f9;
    }
    .legal-toc-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 500;
        color: #3b82f6;
        text-decoration: none;
    }
    .legal-toc-link:hover { color: #1d4ed8; }

    /* ─── Mobile TOC Drawer ─── */
    .legal-toc-mobile {
        position: fixed;
        inset: 0;
        z-index: 9998;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .legal-toc-mobile.open {
        pointer-events: auto;
        opacity: 1;
    }
    .legal-toc-mobile-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.5);
    }
    .legal-toc-mobile-panel {
        position: absolute;
        right: 0;
        top: 0;
        bottom: 0;
        width: 85%;
        max-width: 340px;
        background: #ffffff;
        transform: translateX(100%);
        transition: transform 0.25s ease;
        display: flex;
        flex-direction: column;
        box-shadow: -10px 0 30px rgba(15, 23, 42, 0.15);
    }
    .legal-toc-mobile.open .legal-toc-mobile-panel {
        transform: translateX(0);
    }
    .legal-toc-mobile-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 22px;
        border-bottom: 1px solid #e2e8f0;
    }
    .legal-toc-mobile-header h6 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
    }
    .legal-toc-mobile-header button {
        background: transparent;
        border: 0;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        color: #475569;
        font-size: 16px;
        cursor: pointer;
    }
    .legal-toc-mobile-header button:hover { background: #f1f5f9; }
    .legal-toc-mobile-panel .legal-toc-nav {
        flex: 1;
        max-height: none;
        padding: 12px 10px;
    }

    /* ─── Article / Content ─── */
    .legal-article {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 48px 52px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        font-size: 15.5px;
        line-height: 1.85;
        color: #334155;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    @media (max-width: 991px) {
        .legal-article { padding: 32px 24px; border-radius: 14px; }
    }
    @media (max-width: 576px) {
        .legal-article { padding: 24px 18px; font-size: 15px; border-radius: 12px; }
    }

    /* ─── Notice ─── */
    .legal-notice {
        display: flex;
        gap: 16px;
        padding: 20px;
        background: #eff6ff;
        border-left: 4px solid #3b82f6;
        border-radius: 12px;
        margin-bottom: 40px;
    }
    .legal-notice-icon {
        flex-shrink: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #3b82f6;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
    }
    .legal-notice strong {
        display: block;
        color: #0f172a;
        margin-bottom: 4px;
        font-size: 15px;
    }
    .legal-notice p {
        margin: 0;
        color: #475569;
        font-size: 14.5px;
    }
    @media (max-width: 576px) {
        .legal-notice { padding: 16px; gap: 12px; }
        .legal-notice-icon { width: 34px; height: 34px; font-size: 14px; }
    }

    /* ─── Section Blocks ─── */
    .legal-section-block {
        position: relative;
        padding: 40px 0;
        border-top: 1px solid #f1f5f9;
        scroll-margin-top: 100px;
    }
    .legal-section-block:first-of-type { border-top: 0; padding-top: 0; }
    .legal-section-number {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 2px;
        color: #3b82f6;
        margin-bottom: 6px;
    }
    .legal-section-block h2 {
        font-size: 24px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 20px;
        line-height: 1.3;
    }
    @media (max-width: 576px) {
        .legal-section-block { padding: 30px 0; }
        .legal-section-block h2 { font-size: 20px; }
    }

    .legal-subhead {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 22px;
        margin-bottom: 8px;
    }

    .legal-article p { margin-bottom: 14px; }
    .legal-article ul {
        margin: 0 0 18px;
        padding-left: 22px;
    }
    .legal-article ul li {
        margin-bottom: 8px;
        line-height: 1.75;
    }
    .legal-article a {
        color: #3b82f6;
        text-decoration: none;
        border-bottom: 1px solid rgba(59, 130, 246, 0.3);
        transition: all 0.15s;
    }
    .legal-article a:hover {
        color: #1d4ed8;
        border-bottom-color: #1d4ed8;
    }

    /* ─── Info Card ─── */
    .legal-info-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px 22px;
        margin: 18px 0;
    }
    .legal-info-card h6 {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
    }
    .legal-info-card ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .legal-info-card ul li {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        margin-bottom: 8px;
        color: #475569;
    }
    .legal-info-card ul li:last-child { margin-bottom: 0; }
    .legal-info-card ul li i {
        color: #3b82f6;
        width: 16px;
        text-align: center;
    }

    /* ─── Highlight Box ─── */
    .legal-highlight {
        display: flex;
        gap: 12px;
        padding: 16px 18px;
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        border-radius: 10px;
        margin: 20px 0;
        font-size: 14.5px;
        color: #78350f;
    }
    .legal-highlight i { color: #f59e0b; font-size: 16px; margin-top: 2px; flex-shrink: 0; }
    .legal-highlight-success {
        background: #ecfdf5;
        border-left-color: #10b981;
        color: #065f46;
    }
    .legal-highlight-success i { color: #10b981; }

    /* ─── Table ─── */
    .legal-table-wrap {
        margin: 18px 0;
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        -webkit-overflow-scrolling: touch;
    }
    .legal-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        min-width: 560px;
    }
    .legal-table thead {
        background: #f8fafc;
    }
    .legal-table th {
        text-align: left;
        padding: 14px 16px;
        font-weight: 700;
        color: #0f172a;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #e2e8f0;
        white-space: nowrap;
    }
    .legal-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
        vertical-align: top;
        line-height: 1.6;
    }
    .legal-table tbody tr:last-child td { border-bottom: 0; }
    .legal-table tbody tr:hover { background: #f8fafc; }

    /* Mobile card-style table */
    @media (max-width: 767px) {
        .legal-table-wrap {
            border: 0;
            overflow: visible;
        }
        .legal-table {
            min-width: 0;
            display: block;
        }
        .legal-table thead { display: none; }
        .legal-table tbody,
        .legal-table tr,
        .legal-table td {
            display: block;
            width: 100%;
        }
        .legal-table tr {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            margin-bottom: 12px;
            padding: 12px 14px;
        }
        .legal-table td {
            border: 0;
            padding: 6px 0;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            font-size: 14px;
        }
        .legal-table td::before {
            content: attr(data-label);
            font-weight: 700;
            color: #0f172a;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            flex-shrink: 0;
            min-width: 90px;
        }
    }

    /* ─── Footer Note ─── */
    .legal-footer-note {
        margin-top: 40px;
        padding-top: 24px;
        border-top: 1px solid #f1f5f9;
        font-size: 13px;
        color: #94a3b8;
        text-align: center;
    }

    /* ─── Back to Top ─── */
    .legal-back-to-top {
        position: fixed;
        bottom: 28px;
        right: 28px;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #3b82f6;
        color: #fff;
        border: 0;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.35);
        font-size: 16px;
        z-index: 999;
        transition: all 0.2s;
    }
    .legal-back-to-top:hover {
        background: #1d4ed8;
        transform: translateY(-2px);
    }
    @media (max-width: 576px) {
        .legal-back-to-top {
            width: 42px;
            height: 42px;
            bottom: 20px;
            right: 20px;
            font-size: 14px;
        }
    }

    /* ─── Print ─── */
    @media print {
        .legal-progress,
        .legal-meta-strip,
        .legal-toc,
        .legal-toc-mobile,
        .legal-back-to-top,
        .breadcrumb__area,
        button {
            display: none !important;
        }
        .legal-section { padding: 0; background: #fff; }
        .legal-article {
            border: 0;
            box-shadow: none;
            padding: 0;
            font-size: 12pt;
        }
        .legal-section-block { page-break-inside: avoid; }
        .legal-section-block h2 { page-break-after: avoid; }
    }
</style>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('legalPage', () => ({
            active: 'intro',
            tocOpen: false,
            progress: 0,
            showTop: false,

            init() {
                this.$nextTick(() => {
                    this.handleScroll();
                });
            },

            onScroll() {
                this.handleScroll();
            },

            handleScroll() {
                const scrollTop = window.scrollY;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                this.progress = docHeight > 0 ? Math.min((scrollTop / docHeight) * 100, 100) : 0;
                this.showTop = scrollTop > 600;

                const sections = document.querySelectorAll('section[id]');
                let current = 'intro';
                sections.forEach(section => {
                    const top = section.offsetTop - 140;
                    if (scrollTop >= top) {
                        current = section.id;
                    }
                });
                this.active = current;
            },

            scrollTo(id) {
                const el = document.getElementById(id);
                if (el) {
                    const top = el.offsetTop - 90;
                    window.scrollTo({ top, behavior: 'smooth' });
                }
            },

            toggleToc() {
                this.tocOpen = !this.tocOpen;
                document.body.style.overflow = this.tocOpen ? 'hidden' : '';
            }
        }));
    });
</script>
@endpush
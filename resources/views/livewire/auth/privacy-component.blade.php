<div x-data="legalPage()" x-init="init()" @scroll.window="onScroll()">

    {{-- Reading Progress Bar --}}
    <div class="legal-progress" :style="`width: ${progress}%`"></div>

    {{-- Breadcrumb --}}
    <div wire:ignore class="breadcrumb__area theme-bg-1 p-relative pt-160 pb-160">
        <div class="breadcrumb__thumb"
            style="background-image: url('{{ asset('assets/main/imgs/resources/privacy.jpg') }}');">
        </div>
        <div class="breadcrumb__thumb_2"
            style="background-image: url('{{ asset('assets/main/imgs/resources/page-title-bg-2.png') }}');"></div>
        <div class="small-container">
            <div class="row justify-content-center">
                <div class="col-xxl-12">
                    <div class="breadcrumb__wrapper p-relative">
                        <h2 class="breadcrumb__title">Privacy Policy</h2>
                        <div class="breadcrumb__menu">
                            <nav>
                                <ul>
                                    <li><span><a wire:navigate.hover href="{{ route('index') }}">Home</a></span></li>
                                    <li><span>Privacy Policy</span></li>
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
                    <span>~18 min read</span>
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
                                <li><a href="#intro" @click.prevent="scrollTo('intro')"
                                        :class="active === 'intro' ? 'active' : ''">1. Introduction</a></li>
                                <li><a href="#controller" @click.prevent="scrollTo('controller')"
                                        :class="active === 'controller' ? 'active' : ''">2. Data Controller</a></li>
                                <li><a href="#collect" @click.prevent="scrollTo('collect')"
                                        @click.prevent="scrollTo('collect')"
                                        :class="active === 'collect' ? 'active' : ''">3. Data We Collect</a></li>
                                <li><a href="#how" @click.prevent="scrollTo('how')"
                                        :class="active === 'how' ? 'active' : ''">4. How We Collect</a></li>
                                <li><a href="#basis" @click.prevent="scrollTo('basis')"
                                        :class="active === 'basis' ? 'active' : ''">5. Legal Basis</a></li>
                                <li><a href="#use" @click.prevent="scrollTo('use')"
                                        :class="active === 'use' ? 'active' : ''">6. How We Use Data</a></li>
                                <li><a href="#ai" @click.prevent="scrollTo('ai')"
                                        :class="active === 'ai' ? 'active' : ''">7. AI Chat Assistant</a></li>
                                <li><a href="#recruitment" @click.prevent="scrollTo('recruitment')"
                                        :class="active === 'recruitment' ? 'active' : ''">8. Recruitment Data</a></li>
                                <li><a href="#cookies" @click.prevent="scrollTo('cookies')"
                                        :class="active === 'cookies' ? 'active' : ''">9. Cookies & Tracking</a></li>
                                <li><a href="#thirdparty" @click.prevent="scrollTo('thirdparty')"
                                        :class="active === 'thirdparty' ? 'active' : ''">10. Third-Party Processors</a>
                                </li>
                                <li><a href="#sharing" @click.prevent="scrollTo('sharing')"
                                        :class="active === 'sharing' ? 'active' : ''">11. Data Sharing</a></li>
                                <li><a href="#transfers" @click.prevent="scrollTo('transfers')"
                                        :class="active === 'transfers' ? 'active' : ''">12. International Transfers</a>
                                </li>
                                <li><a href="#retention" @click.prevent="scrollTo('retention')"
                                        :class="active === 'retention' ? 'active' : ''">13. Retention</a></li>
                                <li><a href="#security" @click.prevent="scrollTo('security')"
                                        :class="active === 'security' ? 'active' : ''">14. Security</a></li>
                                <li><a href="#rights" @click.prevent="scrollTo('rights')"
                                        :class="active === 'rights' ? 'active' : ''">15. Your Rights</a></li>
                                <li><a href="#children" @click.prevent="scrollTo('children')"
                                        :class="active === 'children' ? 'active' : ''">16. Children</a></li>
                                <li><a href="#marketing" @click.prevent="scrollTo('marketing')"
                                        :class="active === 'marketing' ? 'active' : ''">17. Marketing</a></li>
                                <li><a href="#automated" @click.prevent="scrollTo('automated')"
                                        :class="active === 'automated' ? 'active' : ''">18. Automated Processing</a>
                                </li>
                                <li><a href="#breach" @click.prevent="scrollTo('breach')"
                                        :class="active === 'breach' ? 'active' : ''">19. Breach Notification</a></li>
                                <li><a href="#changes" @click.prevent="scrollTo('changes')"
                                        :class="active === 'changes' ? 'active' : ''">20. Changes</a></li>
                                <li><a href="#contact" @click.prevent="scrollTo('contact')"
                                        :class="active === 'contact' ? 'active' : ''">21. Contact / DPO</a></li>
                            </ul>
                        </nav>
                        <div class="legal-toc-footer">
                            <a href="{{ route('terms') }}" wire:navigate.hover class="legal-toc-link">
                                <i class="fal fa-file-contract"></i>
                                Terms & Conditions
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
                                <li><a href="#intro" @click.prevent="scrollTo('intro'); toggleToc()">1. Introduction</a>
                                </li>
                                <li><a href="#controller" @click.prevent="scrollTo('controller'); toggleToc()">2. Data
                                        Controller</a></li>
                                <li><a href="#collect" @click.prevent="scrollTo('collect'); toggleToc()">3. Data We
                                        Collect</a></li>
                                <li><a href="#how" @click.prevent="scrollTo('how'); toggleToc()">4. How We Collect</a>
                                </li>
                                <li><a href="#basis" @click.prevent="scrollTo('basis'); toggleToc()">5. Legal Basis</a>
                                </li>
                                <li><a href="#use" @click.prevent="scrollTo('use'); toggleToc()">6. How We Use Data</a>
                                </li>
                                <li><a href="#ai" @click.prevent="scrollTo('ai'); toggleToc()">7. AI Chat Assistant</a>
                                </li>
                                <li><a href="#recruitment" @click.prevent="scrollTo('recruitment'); toggleToc()">8.
                                        Recruitment Data</a></li>
                                <li><a href="#cookies" @click.prevent="scrollTo('cookies'); toggleToc()">9. Cookies &
                                        Tracking</a></li>
                                <li><a href="#thirdparty" @click.prevent="scrollTo('thirdparty'); toggleToc()">10.
                                        Third-Party Processors</a></li>
                                <li><a href="#sharing" @click.prevent="scrollTo('sharing'); toggleToc()">11. Data
                                        Sharing</a></li>
                                <li><a href="#transfers" @click.prevent="scrollTo('transfers'); toggleToc()">12.
                                        International Transfers</a></li>
                                <li><a href="#retention" @click.prevent="scrollTo('retention'); toggleToc()">13.
                                        Retention</a></li>
                                <li><a href="#security" @click.prevent="scrollTo('security'); toggleToc()">14.
                                        Security</a></li>
                                <li><a href="#rights" @click.prevent="scrollTo('rights'); toggleToc()">15. Your
                                        Rights</a></li>
                                <li><a href="#children" @click.prevent="scrollTo('children'); toggleToc()">16.
                                        Children</a></li>
                                <li><a href="#marketing" @click.prevent="scrollTo('marketing'); toggleToc()">17.
                                        Marketing</a></li>
                                <li><a href="#automated" @click.prevent="scrollTo('automated'); toggleToc()">18.
                                        Automated Processing</a></li>
                                <li><a href="#breach" @click.prevent="scrollTo('breach'); toggleToc()">19. Breach
                                        Notification</a></li>
                                <li><a href="#changes" @click.prevent="scrollTo('changes'); toggleToc()">20. Changes</a>
                                </li>
                                <li><a href="#contact" @click.prevent="scrollTo('contact'); toggleToc()">21. Contact /
                                        DPO</a></li>
                            </ul>
                        </nav>
                    </div>
                </div>

                {{-- Main Content --}}
                <div class="col-lg-8 col-xl-9">
                    <article class="legal-article">

                        {{-- Notice --}}
                        <div class="legal-notice">
                            <div class="legal-notice-icon"><i class="fal fa-shield-alt"></i></div>
                            <div>
                                <strong>Your Privacy Matters</strong>
                                <p class="mb-0">This Privacy Policy explains how Polysphere Tech collects, uses, stores,
                                    and protects your personal data across our website, applications, AI chat
                                    assistant, careers portal, and client platforms, in accordance with the
                                    <strong>Ghana Data Protection Act, 2012 (Act 843)</strong>.
                                </p>
                            </div>
                        </div>

                        {{-- 1 --}}
                        <section id="intro" class="legal-section-block">
                            <div class="legal-section-number">01</div>
                            <h2>Introduction</h2>
                            <p>Polysphere Tech ("we", "us", "our") is committed to protecting your privacy. This Privacy
                                Policy explains what personal data we collect, why we collect it, how we use it, how
                                long
                                we keep it, who we share it with, and your rights over it.</p>
                            <p>This policy applies to all visitors, registered users, invited users, blog commenters,
                                chat widget users, job applicants, employees, messenger users, newsletter subscribers,
                                and clients interacting with our website, applications, and Services.</p>
                            <p>By using any part of our Services — including our public website, our AI chat assistant
                                ("Sphere"), our careers portal, or our client platforms — you acknowledge that you have
                                read and understood this Privacy Policy.</p>
                        </section>

                        {{-- 2 --}}
                        <section id="controller" class="legal-section-block">
                            <div class="legal-section-number">02</div>
                            <h2>Data Controller</h2>
                            <p>The <strong>Data Controller</strong> responsible for your personal data is:</p>
                            <div class="legal-info-card">
                                <h6>Polysphere Tech</h6>
                                <ul>
                                    <li><i class="fal fa-map-marker-alt"></i> Accra, Ghana</li>
                                    <li><i class="fal fa-envelope"></i>
                                        <a href="mailto:contact@polyspheretech.com">contact@polyspheretech.com</a>
                                    </li>
                                    <li><i class="fal fa-phone"></i> <a href="tel:+233597563427">+233 (59) 756-3427</a>
                                    </li>
                                </ul>
                            </div>
                            <p>Polysphere Tech is committed to complying with the <strong>Ghana Data Protection Act,
                                    2012 (Act 843)</strong>. We are currently in the process of completing our formal
                                registration with the <strong>Data Protection Commission of Ghana</strong> as a Data
                                Controller.</p>
                        </section>

                        {{-- 3 --}}
                        <section id="collect" class="legal-section-block">
                            <div class="legal-section-number">03</div>
                            <h2>Data We Collect</h2>
                            <p>We collect the following categories of personal data:</p>

                            <h6 class="legal-subhead">A. Identity & Contact Data</h6>
                            <ul>
                                <li>Full name, username, and any preferred display name</li>
                                <li>Email address (personal and/or work)</li>
                                <li>Phone number (with country code)</li>
                                <li>Country, city, date of birth, gender</li>
                                <li>Profile picture, position/job title, department</li>
                                <li>Company or organization name (for B2B interactions)</li>
                            </ul>

                            <h6 class="legal-subhead">B. Account & Authentication Data</h6>
                            <ul>
                                <li>Hashed passwords (never stored in plain text)</li>
                                <li>Two-Factor Authentication (2FA) secrets — encrypted at rest</li>
                                <li>2FA recovery codes — encrypted at rest</li>
                                <li>Two-factor confirmation timestamps</li>
                                <li>Failed login attempts, account lock status</li>
                                <li>Last login time, last login IP, last seen timestamp</li>
                                <li>Google Account identifier (only if you sign in with Google)</li>
                            </ul>

                            <h6 class="legal-subhead">C. Technical & Usage Data</h6>
                            <ul>
                                <li>IP address, browser type and version, device information</li>
                                <li>User agent string, referring URL, and query parameters</li>
                                <li>Pages visited, time on page, clickstream data</li>
                                <li>Session identifiers, CSRF tokens, and authentication cookies</li>
                                <li>Error and performance telemetry (via our error-monitoring provider)</li>
                            </ul>

                            <h6 class="legal-subhead">D. AI Chat Assistant Data ("Sphere")</h6>
                            <ul>
                                <li>Messages you send to our AI assistant, including any personal data you voluntarily
                                    type into the chat</li>
                                <li>AI responses generated during your session</li>
                                <li>Session identifier and conversation timestamps</li>
                                <li>The page you were on when you opened the chat</li>
                                <li>If you share an email address: your name (if provided), email, and any phone
                                    number you voluntarily include</li>
                                <li>A scoring value generated internally to help us prioritise responses</li>
                            </ul>

                            <h6 class="legal-subhead">E. Recruitment & Job Application Data</h6>
                            <ul>
                                <li>Your full name, email, phone number, and location</li>
                                <li>CV / résumé, cover letter, and any documents or portfolios you upload</li>
                                <li>Employment history, education, qualifications, and references</li>
                                <li>Right-to-work information (where legally required)</li>
                                <li>Answers to screening questions specific to the vacancy</li>
                                <li>Recruitment status, interview notes, and internal assessments</li>
                                <li>Any additional information you voluntarily provide in the application wizard</li>
                            </ul>

                            <h6 class="legal-subhead">F. Employment & HR Data (for our team members)</h6>
                            <ul>
                                <li>Employee ID, department, position, hire date, employment type</li>
                                <li>Emergency contact name and phone number</li>
                                <li>Employment status, skills, education, and profile information</li>
                                <li>Internal HR activity such as role changes, status changes, and audit logs</li>
                            </ul>

                            <h6 class="legal-subhead">G. Communication & Content Data</h6>
                            <ul>
                                <li>Blog comments (both registered and guest comments, including verification tokens)
                                </li>
                                <li>Messages sent through our internal Messenger feature between users</li>
                                <li>Contact form submissions (name, email, subject, message, category)</li>
                                <li>Newsletter subscription data (email, verification status, subscription timestamps)
                                </li>
                                <li>Files and content you upload to the platform</li>
                                <li>Notification and system messages sent to your account</li>
                            </ul>

                            <h6 class="legal-subhead">H. Payment Data (via Paystack)</h6>
                            <ul>
                                <li>Transaction ID, amount, currency, and payment status</li>
                                <li>Last 4 digits of the card used, card brand, and expiry month/year</li>
                                <li>Billing country (for tax and compliance purposes)</li>
                                <li>Receipts and invoice records</li>
                            </ul>

                            <div class="legal-highlight">
                                <i class="fal fa-lock"></i>
                                <span><strong>We never store your full card number, CVV, or PIN.</strong> All sensitive
                                    payment data is handled exclusively by Paystack, a PCI-DSS compliant processor
                                    licensed by the Bank of Ghana.</span>
                            </div>
                        </section>

                        {{-- 4 --}}
                        <section id="how" class="legal-section-block">
                            <div class="legal-section-number">04</div>
                            <h2>How We Collect Data</h2>
                            <ul>
                                <li><strong>Directly from you:</strong> when you register, apply for a job, complete
                                    forms, post a comment, use the chat assistant, subscribe to our newsletter, or
                                    contact us.</li>
                                <li><strong>Automatically:</strong> through cookies, server logs, and analytics tools
                                    when you browse the site.</li>
                                <li><strong>From third parties:</strong> Paystack (payment confirmations), Google
                                    (if you sign in with Google), and error-monitoring providers.</li>
                                <li><strong>From invited users:</strong> your inviter may provide basic information
                                    (name, email, position) when they invite you to the platform.</li>
                                <li><strong>From your browsing behaviour:</strong> aggregated analytics data about
                                    which pages you view and how you interact with the site.</li>
                            </ul>
                        </section>

                        {{-- 5 --}}
                        <section id="basis" class="legal-section-block">
                            <div class="legal-section-number">05</div>
                            <h2>Legal Basis for Processing</h2>
                            <p>Under the Ghana Data Protection Act, 2012 (Act 843), we rely on the following legal
                                grounds:</p>
                            <ul>
                                <li><strong>Consent:</strong> You have given clear consent (e.g., for marketing emails,
                                    AI chat interactions where you voluntarily share data).</li>
                                <li><strong>Contract:</strong> Processing is necessary to fulfil our contract with you
                                    (e.g., delivering the Services, processing a job application you submitted).</li>
                                <li><strong>Legal Obligation:</strong> We must comply with Ghanaian law (e.g., tax
                                    records, employment law, court orders).</li>
                                <li><strong>Vital Interests:</strong> To protect someone's life or safety (e.g., in
                                    an emergency involving an employee).</li>
                                <li><strong>Public Interest:</strong> For tasks carried out in the public interest.</li>
                                <li><strong>Legitimate Interests:</strong> For fraud prevention, security, improving
                                    our Services, and responding to enquiries, provided these do not override your
                                    rights.</li>
                            </ul>
                        </section>

                        {{-- 6 --}}
                        <section id="use" class="legal-section-block">
                            <div class="legal-section-number">06</div>
                            <h2>How We Use Your Data</h2>
                            <ul>
                                <li>To create, secure, and manage your Account</li>
                                <li>To provide and improve the Services</li>
                                <li>To process payments, invoices, and prevent fraud</li>
                                <li>To send transactional emails (verification, password reset, receipts)</li>
                                <li>To send notifications and security alerts</li>
                                <li>To respond to your enquiries through our contact forms or AI chat</li>
                                <li>To operate our AI chat assistant and provide useful, context-aware answers</li>
                                <li>To capture and respond to business enquiries submitted through the chat widget</li>
                                <li>To assess and process job applications for vacancies you have applied to</li>
                                <li>To manage our team, departments, and internal HR workflows</li>
                                <li>To operate our internal Messenger and notification systems</li>
                                <li>To deliver our newsletter if you have opted in</li>
                                <li>To analyse usage and improve user experience</li>
                                <li>To comply with legal and regulatory obligations</li>
                                <li>To enforce our Terms & Conditions and protect our legal rights</li>
                            </ul>
                            <div class="legal-highlight legal-highlight-success">
                                <i class="fal fa-check-circle"></i>
                                <span>We do <strong>not</strong> sell your personal data to third parties. Ever.</span>
                            </div>
                        </section>

                        {{-- 7 --}}
                        <section id="ai" class="legal-section-block">
                            <div class="legal-section-number">07</div>
                            <h2>AI Chat Assistant ("Sphere")</h2>
                            <p>Our website includes an AI-powered chat assistant named <strong>Sphere</strong>. Because
                                this is a newer technology, we want to be especially transparent about how it works and
                                what data it processes.</p>

                            <h6 class="legal-subhead">A. Who processes your messages</h6>
                            <ul>
                                <li>Every message you send to Sphere is transmitted to <strong>Google's Gemini
                                        API</strong>
                                    so that an AI-generated reply can be produced.</li>
                                <li>Your message is processed by Google according to Google's API terms and data-use
                                    policies. Google does <strong>not</strong> use API data to train its public models.
                                </li>
                                <li>We do not send any information to Sphere that you have not typed into the chat.</li>
                            </ul>

                            <h6 class="legal-subhead">B. When we store a conversation</h6>
                            <ul>
                                <li>Anonymous chat sessions are held in your browser session only and are not
                                    permanently stored on our servers.</li>
                                <li>If you share an <strong>email address</strong> during the chat, we may save a
                                    "lead" record containing your email, name (if provided), phone (if provided), the
                                    full conversation transcript, the page you were on, your IP, and your user agent.
                                </li>
                                <li>That record is used to follow up on your enquiry and is subject to the retention
                                    rules in Section 13.</li>
                            </ul>

                            <h6 class="legal-subhead">C. Lead scoring</h6>
                            <ul>
                                <li>We compute an internal score for each captured lead based on signals such as the
                                    detail of your message, whether you provided a phone number, and which page you
                                    were on.</li>
                                <li>This score is used only to help our team prioritise responses. It has no legal
                                    effect and you are not subject to any decision based solely on it.</li>
                            </ul>

                            <h6 class="legal-subhead">D. What not to share with Sphere</h6>
                            <div class="legal-highlight">
                                <i class="fal fa-exclamation-triangle"></i>
                                <span>Please do <strong>not</strong> share passwords, payment card numbers, national
                                    IDs, health information, or any other sensitive personal data with Sphere. The
                                    assistant is designed for business enquiries only and should not be used to
                                    transmit confidential or high-risk information.</span>
                            </div>

                            <h6 class="legal-subhead">E. Automated processing</h6>
                            <ul>
                                <li>Sphere generates replies automatically. It does not make decisions that produce
                                    legal effects on you.</li>
                                <li>All commercial decisions (quotes, contracts, hiring) are made by a human team
                                    member.</li>
                            </ul>
                        </section>

                        {{-- 8 --}}
                        <section id="recruitment" class="legal-section-block">
                            <div class="legal-section-number">08</div>
                            <h2>Recruitment & Job Applications</h2>
                            <p>If you apply for a vacancy through our careers portal, we collect and process additional
                                data as part of our hiring process.</p>

                            <h6 class="legal-subhead">A. What we collect</h6>
                            <ul>
                                <li>Your application details (name, email, phone, location, CV, cover letter, and any
                                    supporting documents)</li>
                                <li>Answers to vacancy-specific questions</li>
                                <li>Any notes our team adds during the screening and interview process</li>
                            </ul>

                            <h6 class="legal-subhead">B. Why we process it</h6>
                            <ul>
                                <li>To assess your suitability for the role you applied to</li>
                                <li>To communicate with you about your application</li>
                                <li>To comply with Ghanaian employment and anti-discrimination law</li>
                                <li>To keep a record of our hiring decisions</li>
                            </ul>

                            <h6 class="legal-subhead">C. How long we keep it</h6>
                            <ul>
                                <li><strong>Unsuccessful applicants:</strong> We retain application data for up to
                                    <strong>12 months</strong> after the role is filled, unless you ask us to delete
                                    it sooner or consent to us keeping it longer for future opportunities.
                                </li>
                                <li><strong>Successful applicants:</strong> Your application becomes part of your
                                    employee HR record and is subject to our employment data-retention practices.</li>
                            </ul>

                            <h6 class="legal-subhead">D. Your rights as an applicant</h6>
                            <ul>
                                <li>You can request access to the data we hold about your application.</li>
                                <li>You can request correction of inaccurate information.</li>
                                <li>You can withdraw your application and request deletion at any time.</li>
                                <li>You can withdraw consent for us to keep your data on file for future roles.</li>
                            </ul>
                        </section>

                        {{-- 9 --}}
                        <section id="cookies" class="legal-section-block">
                            <div class="legal-section-number">09</div>
                            <h2>Cookies & Tracking</h2>
                            <p>We use cookies and similar technologies to:</p>
                            <ul>
                                <li><strong>Essential cookies:</strong> Required for authentication, session management,
                                    CSRF protection, and security. These cannot be disabled without breaking the
                                    Services.</li>
                                <li><strong>Preference cookies:</strong> Remember your settings and preferences.</li>
                                <li><strong>Analytics cookies:</strong> Help us understand how visitors use the site
                                    (e.g., Google Analytics 4). These are anonymised where possible.</li>
                                <li><strong>Marketing cookies:</strong> Used only with your explicit consent.</li>
                            </ul>
                            <p>You can control cookies through your browser settings. Disabling essential cookies may
                                prevent the Services from working properly.</p>
                        </section>

                        {{-- 10 --}}
                        <section id="thirdparty" class="legal-section-block">
                            <div class="legal-section-number">10</div>
                            <h2>Third-Party Data Processors</h2>
                            <p>We share your data with the following trusted processors, each bound by data protection
                                agreements:</p>

                            <div class="legal-table-wrap">
                                <table class="legal-table">
                                    <thead>
                                        <tr>
                                            <th>Provider</th>
                                            <th>Purpose</th>
                                            <th>Data Shared</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td data-label="Provider"><strong>Paystack</strong></td>
                                            <td data-label="Purpose">Payment processing (card, mobile money, bank
                                                transfer)</td>
                                            <td data-label="Data Shared">Name, email, phone, transaction amount, IP</td>
                                        </tr>
                                        <tr>
                                            <td data-label="Provider"><strong>Google Gemini API</strong></td>
                                            <td data-label="Purpose">AI chat assistant responses</td>
                                            <td data-label="Data Shared">Messages you send to the AI chat, session
                                                context</td>
                                        </tr>
                                        <tr>
                                            <td data-label="Provider"><strong>Google Analytics 4</strong></td>
                                            <td data-label="Purpose">Website traffic analysis</td>
                                            <td data-label="Data Shared">Anonymised IP, device, browsing behaviour</td>
                                        </tr>
                                        <tr>
                                            <td data-label="Provider"><strong>Google OAuth</strong></td>
                                            <td data-label="Purpose">"Sign in with Google" authentication</td>
                                            <td data-label="Data Shared">Name, email, Google account identifier</td>
                                        </tr>
                                        <tr>
                                            <td data-label="Provider"><strong>Sentry</strong></td>
                                            <td data-label="Purpose">Error monitoring and performance diagnostics</td>
                                            <td data-label="Data Shared">Error traces, stack details, user ID, IP,
                                                browser metadata</td>
                                        </tr>
                                        <tr>
                                            <td data-label="Provider"><strong>Pusher</strong></td>
                                            <td data-label="Purpose">Realtime messaging and notifications</td>
                                            <td data-label="Data Shared">User ID, event metadata (message content is
                                                not stored on Pusher)</td>
                                        </tr>
                                        <tr>
                                            <td data-label="Provider"><strong>Cloud Hosting Provider</strong></td>
                                            <td data-label="Purpose">Server infrastructure</td>
                                            <td data-label="Data Shared">All data stored on our servers</td>
                                        </tr>
                                        <tr>
                                            <td data-label="Provider"><strong>Email Service Provider</strong></td>
                                            <td data-label="Purpose">Transactional emails (verification, receipts,
                                                notifications)</td>
                                            <td data-label="Data Shared">Name, email address, message content</td>
                                        </tr>
                                        <tr>
                                            <td data-label="Provider"><strong>SMS Gateway</strong> (if used)</td>
                                            <td data-label="Purpose">OTP and notification delivery</td>
                                            <td data-label="Data Shared">Phone number, message content</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <p class="mt-3">We do not share more data than is necessary for each service, and each
                                provider is contractually required to protect your information.</p>
                        </section>

                        {{-- 11 --}}
                        <section id="sharing" class="legal-section-block">
                            <div class="legal-section-number">11</div>
                            <h2>Data Sharing & Disclosure</h2>
                            <p>We will <strong>never sell</strong> your personal data. We may disclose your data only:
                            </p>
                            <ul>
                                <li>To service providers acting on our behalf (see Section 10);</li>
                                <li>To comply with legal obligations (court orders, tax authorities, the Data Protection
                                    Commission, or employment regulators);</li>
                                <li>To protect our rights, property, or safety, or that of our users or employees;</li>
                                <li>To prospective employers or partners strictly in connection with your own
                                    application or enquiry;</li>
                                <li>In connection with a merger, acquisition, or sale of assets (with notice to you).
                                </li>
                            </ul>
                        </section>

                        {{-- 12 --}}
                        <section id="transfers" class="legal-section-block">
                            <div class="legal-section-number">12</div>
                            <h2>International Data Transfers</h2>
                            <p>Some of our processors (e.g., Google, Paystack, Sentry, Pusher) may store data outside
                                Ghana. When we transfer personal data internationally, we ensure appropriate safeguards
                                are in place, including:</p>
                            <ul>
                                <li>Standard Contractual Clauses approved by the Data Protection Commission;</li>
                                <li>Verification that the recipient country provides adequate protection;</li>
                                <li>Explicit consent from you where required.</li>
                            </ul>
                        </section>

                        {{-- 13 --}}
                        <section id="retention" class="legal-section-block">
                            <div class="legal-section-number">13</div>
                            <h2>Data Retention</h2>
                            <p>We retain personal data only as long as necessary:</p>
                            <ul>
                                <li><strong>Active accounts:</strong> Kept while your account is active.</li>
                                <li><strong>Inactive accounts:</strong> Deleted after 24 months of inactivity (with
                                    prior notice).</li>
                                <li><strong>Chat transcripts (leads):</strong> Retained for up to 24 months unless
                                    converted to a client relationship.</li>
                                <li><strong>Job applications (unsuccessful):</strong> Up to 12 months, unless you ask
                                    us to delete them sooner.</li>
                                <li><strong>Employee HR records:</strong> Retained for the duration of employment plus
                                    any legal minimum required by Ghanaian employment law.</li>
                                <li><strong>Transaction records:</strong> Retained for 6 years as required by Ghanaian
                                    tax law.</li>
                                <li><strong>Security logs and audit trails:</strong> Retained for 12 months.</li>
                                <li><strong>Newsletter subscriptions:</strong> Retained until you withdraw consent.</li>
                                <li><strong>Marketing data:</strong> Retained until you withdraw consent.</li>
                            </ul>
                        </section>

                        {{-- 14 --}}
                        <section id="security" class="legal-section-block">
                            <div class="legal-section-number">14</div>
                            <h2>Data Security</h2>
                            <p>We implement industry-standard security measures to protect your data:</p>
                            <ul>
                                <li><strong>Encryption in transit:</strong> HTTPS/TLS for all communication.</li>
                                <li><strong>Password hashing:</strong> Using bcrypt/Argon2 – never stored in plain text.
                                </li>
                                <li><strong>Two-Factor Authentication</strong> available on all accounts.</li>
                                <li><strong>Encrypted secrets:</strong> 2FA secrets and recovery codes encrypted at
                                    rest.</li>
                                <li><strong>Access controls:</strong> Role-based permissions limit internal access.</li>
                                <li><strong>Audit logging:</strong> All sensitive actions are logged.</li>
                                <li><strong>Regular backups:</strong> With encryption and access controls.</li>
                                <li><strong>Error monitoring:</strong> Sentry tracks issues so we can respond quickly
                                    to incidents.</li>
                            </ul>
                            <p>While no system is 100% impenetrable, we continuously monitor and improve our security
                                posture.</p>
                        </section>

                        {{-- 15 --}}
                        <section id="rights" class="legal-section-block">
                            <div class="legal-section-number">15</div>
                            <h2>Your Rights</h2>
                            <p>Under the Ghana Data Protection Act, 2012 (Act 843), you have the right to:</p>
                            <ul>
                                <li><strong>Access:</strong> Request a copy of the data we hold about you.</li>
                                <li><strong>Correction:</strong> Request that we correct inaccurate data.</li>
                                <li><strong>Deletion:</strong> Request that we delete your data (subject to legal
                                    obligations).</li>
                                <li><strong>Objection:</strong> Object to processing based on legitimate interests.</li>
                                <li><strong>Restriction:</strong> Request that we restrict processing.</li>
                                <li><strong>Portability:</strong> Receive your data in a portable format.</li>
                                <li><strong>Withdraw Consent:</strong> Withdraw consent at any time where processing is
                                    based on consent.</li>
                                <li><strong>Complaint:</strong> Lodge a complaint with the Data Protection Commission of
                                    Ghana.</li>
                            </ul>
                            <p>To exercise any of these rights, email us at
                                <a href="mailto:contact@polyspheretech.com">contact@polyspheretech.com</a>. We will
                                respond within <strong>30 days</strong>.
                            </p>
                        </section>

                        {{-- 16 --}}
                        <section id="children" class="legal-section-block">
                            <div class="legal-section-number">16</div>
                            <h2>Children's Privacy</h2>
                            <p>Our Services are not directed to individuals under the age of <strong>18</strong>. We do
                                not knowingly collect personal data from children. If we become aware that we have
                                inadvertently collected data from a child, we will delete it promptly.</p>
                            <p>If you are a parent or guardian and believe your child has provided us with personal
                                data, please contact us immediately.</p>
                        </section>

                        {{-- 17 --}}
                        <section id="marketing" class="legal-section-block">
                            <div class="legal-section-number">17</div>
                            <h2>Marketing Communications</h2>
                            <p>We may send you marketing emails (product updates, newsletters) only if you have opted
                                in. You can unsubscribe at any time by:</p>
                            <ul>
                                <li>Clicking the "Unsubscribe" link in any marketing email;</li>
                                <li>Updating your notification preferences in your Account settings;</li>
                                <li>Emailing us at
                                    <a href="mailto:contact@polyspheretech.com">contact@polyspheretech.com</a>.
                                </li>
                            </ul>
                            <p>You will still receive transactional emails (security alerts, password resets, receipts,
                                application updates) as these are essential to the Service.</p>
                        </section>

                        {{-- 18 --}}
                        <section id="automated" class="legal-section-block">
                            <div class="legal-section-number">18</div>
                            <h2>Automated Processing & AI</h2>
                            <p>We use automated systems — including an AI chat assistant and internal lead scoring —
                                to help us respond to enquiries and prioritise work. Here is how we handle them:</p>
                            <ul>
                                <li><strong>No legal-effect decisions:</strong> No automated system makes decisions
                                    that produce legal effects on you or significantly affect you.</li>
                                <li><strong>Human in the loop:</strong> All commercial, contractual, employment, and
                                    candidate decisions are reviewed by a human team member.</li>
                                <li><strong>Right to human review:</strong> You can request human review of any
                                    automated interaction by emailing us at
                                    <a href="mailto:contact@polyspheretech.com">contact@polyspheretech.com</a>.
                                </li>
                                <li><strong>Right to object:</strong> You may object to automated processing by
                                    contacting us. We will respond within 30 days.</li>
                            </ul>
                        </section>

                        {{-- 19 --}}
                        <section id="breach" class="legal-section-block">
                            <div class="legal-section-number">19</div>
                            <h2>Data Breach Notification</h2>
                            <p>In the unfortunate event of a data breach that poses a risk to your rights, we will:</p>
                            <ul>
                                <li>Notify the <strong>Data Protection Commission of Ghana</strong> within <strong>72
                                        hours</strong>;</li>
                                <li>Notify affected users without undue delay;</li>
                                <li>Provide clear information about the breach and steps you can take;</li>
                                <li>Take immediate action to contain and remedy the breach.</li>
                            </ul>
                        </section>

                        {{-- 20 --}}
                        <section id="changes" class="legal-section-block">
                            <div class="legal-section-number">20</div>
                            <h2>Changes to This Policy</h2>
                            <p>We may update this Privacy Policy from time to time. Material changes will be announced
                                via email or a prominent notice on the site at least <strong>14 days</strong> before
                                taking effect.</p>
                            <p>The "Effective Date" at the top indicates when the latest version took effect.</p>
                        </section>

                        {{-- 21 --}}
                        <section id="contact" class="legal-section-block">
                            <div class="legal-section-number">21</div>
                            <h2>Contact & Data Protection Officer</h2>
                            <div class="legal-info-card">
                                <h6>Data Protection Officer (DPO)</h6>
                                <ul>
                                    <li><i class="fal fa-map-marker-alt"></i> Accra, Ghana</li>
                                    <li><i class="fal fa-envelope"></i>
                                        <a href="mailto:contact@polyspheretech.com">contact@polyspheretech.com</a>
                                    </li>
                                    <li><i class="fal fa-phone"></i> <a href="tel:+233597563427">+233 (59) 756-3427</a>
                                    </li>
                                </ul>
                            </div>
                            <p>For data protection complaints, you may also contact the <strong>Data Protection
                                    Commission of Ghana</strong>:</p>
                            <p><i class="fal fa-globe me-2"></i> <a href="https://www.dataprotection.org.gh"
                                    target="_blank" rel="noopener">www.dataprotection.org.gh</a></p>
                        </section>

                        {{-- Footer Note --}}
                        <div class="legal-footer-note">
                            <div>
                                <strong>Last Updated:</strong> {{ $lastUpdated }} · <strong>Version:</strong>
                                {{ $version }}
                            </div>
                            <div class="mt-2">© {{ date('Y') }} Polysphere Tech. All rights reserved.</div>
                        </div>

                    </article>
                </div>

            </div>
        </div>
    </section>

    {{-- Back to top --}}
    <button type="button" class="legal-back-to-top" x-show="showTop" x-transition
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })" style="display:none;">
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
        .legal-section {
            padding: 40px 0 60px;
        }

        .legal-meta-inner {
            gap: 14px;
            padding: 12px 0;
        }

        .legal-meta-item {
            font-size: 13px;
        }

        .legal-meta-actions {
            width: 100%;
            margin-left: 0;
            justify-content: space-between;
        }
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

    .legal-toc-link:hover {
        color: #1d4ed8;
    }

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

    .legal-toc-mobile-header button:hover {
        background: #f1f5f9;
    }

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
        .legal-article {
            padding: 32px 24px;
            border-radius: 14px;
        }
    }

    @media (max-width: 576px) {
        .legal-article {
            padding: 24px 18px;
            font-size: 15px;
            border-radius: 12px;
        }
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
        .legal-notice {
            padding: 16px;
            gap: 12px;
        }

        .legal-notice-icon {
            width: 34px;
            height: 34px;
            font-size: 14px;
        }
    }

    /* ─── Section Blocks ─── */
    .legal-section-block {
        position: relative;
        padding: 40px 0;
        border-top: 1px solid #f1f5f9;
        scroll-margin-top: 100px;
    }

    .legal-section-block:first-of-type {
        border-top: 0;
        padding-top: 0;
    }

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
        .legal-section-block {
            padding: 30px 0;
        }

        .legal-section-block h2 {
            font-size: 20px;
        }
    }

    .legal-subhead {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        margin-top: 22px;
        margin-bottom: 8px;
    }

    .legal-article p {
        margin-bottom: 14px;
    }

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

    .legal-info-card ul li:last-child {
        margin-bottom: 0;
    }

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

    .legal-highlight i {
        color: #f59e0b;
        font-size: 16px;
        margin-top: 2px;
        flex-shrink: 0;
    }

    .legal-highlight-success {
        background: #ecfdf5;
        border-left-color: #10b981;
        color: #065f46;
    }

    .legal-highlight-success i {
        color: #10b981;
    }

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

    .legal-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .legal-table tbody tr:hover {
        background: #f8fafc;
    }

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

        .legal-table thead {
            display: none;
        }

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

        .legal-section {
            padding: 0;
            background: #fff;
        }

        .legal-article {
            border: 0;
            box-shadow: none;
            padding: 0;
            font-size: 12pt;
        }

        .legal-section-block {
            page-break-inside: avoid;
        }

        .legal-section-block h2 {
            page-break-after: avoid;
        }
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
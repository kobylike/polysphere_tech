<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    /**
     * Optional source folder (relative to the project root) where you can
     * drop images before running the seeder. If a matching file is found
     * it will be copied into storage/app/public and attached to the
     * service, exactly like ServiceFormComponent::save() does on upload.
     *
     * Naming convention expected inside that folder:
     *   {slug}-featured.jpg   -> featured_image
     *   {slug}-1.jpg          -> additional_images[0]
     *   {slug}-2.jpg          -> additional_images[1]
     *
     * If no matching file exists, that image is simply skipped (null).
     */
    protected string $sourcePath = 'database/seeders/images/services';

    public function run(): void
    {
        $services = [
            [
                'name' => 'Custom Software Development',
                'icon' => 'fa-solid fa-code',
                'description' => 'We design and develop custom software solutions built around the unique needs of your business. From internal business management systems and enterprise applications to specialized platforms, our solutions are designed to improve efficiency, automate processes, and solve complex business challenges. We use modern technologies and scalable architectures to create secure, reliable, and maintainable software that grows with your organization.',
            ],
            [
                'name' => 'SaaS Development',
                'icon' => 'fa-solid fa-cloud-arrow-up',
                'description' => 'Turn your software idea into a scalable Software-as-a-Service (SaaS) platform. PolySphere Tech develops modern SaaS applications with powerful features, intuitive user experiences, secure authentication, subscription management, payment integration, and cloud-ready infrastructure. Whether you are launching a new SaaS product or transforming an existing solution, we help build platforms designed for long-term growth.',
            ],
            [
                'name' => 'Web Application Development',
                'icon' => 'fa-solid fa-globe',
                'description' => 'We build modern, responsive, and high-performance web applications that help businesses operate more efficiently and deliver better digital experiences. From customer portals and business dashboards to complex platforms and web-based management systems, our applications are developed with scalability, security, usability, and performance in mind.',
            ],
            [
                'name' => 'Mobile App Development',
                'icon' => 'fa-solid fa-mobile-screen',
                'description' => 'Bring your products and services closer to your customers with modern mobile applications. We develop intuitive and reliable mobile experiences designed around your business goals and users. Whether you need a customer-facing application, business management app, marketplace, or mobile extension of an existing platform, we create solutions that are fast, scalable, and easy to use.',
            ],
            [
                'name' => 'AI & Business Automation',
                'icon' => 'fa-solid fa-robot',
                'description' => 'Harness the power of artificial intelligence to automate repetitive tasks, improve decision-making, and increase business productivity. We develop AI-powered solutions and intelligent automation systems that can streamline workflows, process information, enhance customer experiences, and reduce operational inefficiencies. Our goal is to integrate practical AI into your business where it delivers measurable value.',
            ],
            [
                'name' => 'Cloud Solutions',
                'icon' => 'fa-solid fa-cloud',
                'description' => 'Build a stronger technology foundation with scalable and reliable cloud solutions. We help businesses move applications and workloads to modern cloud environments while improving scalability, availability, performance, and operational efficiency. From cloud architecture and deployment to infrastructure optimization, we develop solutions that support businesses as they grow.',
            ],
            [
                'name' => 'Cybersecurity Solutions',
                'icon' => 'fa-solid fa-shield-halved',
                'description' => 'Protect your digital infrastructure, applications, systems, and business information with security-focused technology solutions. We help businesses identify security weaknesses, strengthen application security, implement better access controls, and adopt modern cybersecurity practices. Our approach focuses on reducing risks and building security into technology from the beginning rather than treating it as an afterthought.',
            ],
            [
                'name' => 'Digital Transformation',
                'icon' => 'fa-solid fa-arrows-rotate',
                'description' => 'Modernize your business with technology that improves the way you operate, serve customers, and compete. Our digital transformation services help organizations move away from outdated processes and systems through modern software, automation, cloud technologies, data-driven solutions, and digital platforms. We work with businesses to identify opportunities for improvement and create practical technology strategies for long-term growth.',
            ],
            [
                'name' => 'API & System Integration',
                'icon' => 'fa-solid fa-plug',
                'description' => 'Connect your business applications and digital services into a seamless technology ecosystem. We develop and integrate APIs, payment gateways, third-party platforms, communication services, databases, and other business systems. By connecting the tools you already use, we help eliminate manual processes, improve data flow, and create more efficient digital operations.',
            ],
            [
                'name' => 'IT Consulting',
                'icon' => 'fa-solid fa-lightbulb',
                'description' => 'Make better technology decisions with strategic IT consulting from PolySphere Tech. We help businesses evaluate their technology needs, plan software projects, choose suitable technologies, improve existing systems, and develop practical digital strategies. Whether you are starting a new project, scaling an existing platform, or planning your digital transformation, our expertise helps you move forward with confidence.',
            ],
        ];

        foreach ($services as $index => $item) {
            $slug = Str::slug($item['name']);

            // updateOrCreate keeps this seeder safe to re-run without duplicating rows.
            $service = Service::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'        => $item['name'],
                    'description' => $item['description'],
                    'icon'        => $item['icon'],
                    'status'      => 'active',
                    'order'       => $index + 1,
                ]
            );

            // Featured image (single path, same field ServiceFormComponent writes to).
            $featuredPath = $this->attachImage($slug, 'featured', 'services/featured');
            if ($featuredPath) {
                $service->featured_image = $featuredPath;
            }

            // Up to 2 additional images (array, same as ServiceFormComponent).
            $additional = [];
            foreach (['1', '2'] as $suffix) {
                $path = $this->attachImage($slug, $suffix, 'services/additional');
                if ($path) {
                    $additional[] = $path;
                }
            }
            if (!empty($additional)) {
                $service->additional_images = $additional;
            }

            $service->save();
        }
    }

    /**
     * Look for {slug}-{suffix}.{ext} inside $this->sourcePath, copy it into
     * storage/app/public/{destinationDir} with a random name, and return the
     * stored relative path (what gets saved on the model) — or null if no
     * matching source file was found.
     */
    protected function attachImage(string $slug, string $suffix, string $destinationDir): ?string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $sourceFile = base_path("{$this->sourcePath}/{$slug}-{$suffix}.{$ext}");

            if (File::exists($sourceFile)) {
                $filename = Str::random(20) . '.' . $ext;
                Storage::disk('public')->put(
                    "{$destinationDir}/{$filename}",
                    File::get($sourceFile)
                );

                return "{$destinationDir}/{$filename}";
            }
        }

        return null;
    }
}

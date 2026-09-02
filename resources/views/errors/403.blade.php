@extends('layouts.users')

@section('title', 'Access Denied - 403')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center" style="min-height: 70vh;">
            <div class="col-xl-6 col-lg-8 col-md-10">
                <div class="card border-0 shadow-lg overflow-hidden">
                    <div class="card-body p-5 text-center position-relative">

                        {{-- Decorative background element --}}
                        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10"
                            style="background: radial-gradient(circle at 30% 50%, rgba(99,102,241,0.08) 0%, transparent 70%); pointer-events: none;">
                        </div>

                        {{-- Status code --}}
                        <div class="display-1 fw-bold text-primary" style="font-size: 8rem; line-height: 1; opacity: 0.15;">
                            403
                        </div>

                        {{-- Icon --}}
                        <div class="mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                style="width: 120px; height: 120px; background: linear-gradient(135deg, rgba(239,68,68,0.12), rgba(239,68,68,0.04)); border: 2px solid rgba(239,68,68,0.2);">
                                <i class="fa-solid fa-lock text-danger" style="font-size: 4rem;"></i>
                            </div>
                        </div>

                        {{-- Heading --}}
                        <h2 class="fw-bold mb-2" style="color: #0f172a;">
                            Access Denied
                        </h2>
                        <p class="text-muted fs-5 mb-4">
                            You do not have permission to view this page.
                            <br>
                            <span class="small">If you believe this is an error, please contact your system
                                administrator.</span>
                        </p>

                        {{-- Action buttons --}}
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <a href="{{ route('dashboard') }}" wire:navigate class="btn btn-primary px-4 py-2 rounded-pill">
                                <i class="fa-regular fa-house me-2"></i> Go to Dashboard
                            </a>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
                                <i class="fa-regular fa-arrow-left me-2"></i> Go Back
                            </a>
                        </div>

                        {{-- Additional info – only shown if the exception has a message --}}
                        @if(isset($exception) && $exception->getMessage())
                            <div class="mt-4 p-3 bg-light rounded-3 text-start small">
                                <i class="fa-regular fa-circle-info text-muted me-2"></i>
                                <span class="text-muted">{{ $exception->getMessage() }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Footer note --}}
                <p class="text-center text-muted small mt-4">
                    <i class="fa-regular fa-shield-halved me-1"></i>
                    You are seeing this page because you tried to access a restricted area.
                </p>
            </div>
        </div>
    </div>

    {{-- Optional custom styles for this error page --}}
    @push('styles')
        <style>
            /* Subtle animation */
            .card {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .card:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08) !important;
            }

            .display-1 {
                font-size: 8rem;
                line-height: 1;
                font-weight: 900;
                color: #6366f1;
                opacity: 0.08;
                position: absolute;
                top: -20px;
                right: 20px;
                user-select: none;
                pointer-events: none;
            }

            .btn-primary {
                background: linear-gradient(135deg, #6366f1, #8b5cf6);
                border: none;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(99, 102, 241, 0.3);
            }

            .btn-outline-secondary {
                border-width: 2px;
                transition: all 0.2s ease;
            }

            .btn-outline-secondary:hover {
                background: #f1f5f9;
                border-color: #94a3b8;
            }

            .btn-outline-secondary:active {
                transform: scale(0.96);
            }

            .rounded-circle {
                transition: transform 0.3s ease;
            }

            .rounded-circle:hover {
                transform: scale(1.04);
            }
        </style>
    @endpush

    {{-- Optional scripts (if needed) – nothing required here --}}
    @push('scripts')
        <script>
            // Small UX touch: if the user clicks the "Go Back" button and there's no history,
            // redirect to dashboard instead.
            document.addEventListener('DOMContentLoaded', function () {
                const backBtn = document.querySelector('a[href="javascript:history.back()"]');
                if (backBtn) {
                    backBtn.addEventListener('click', function (e) {
                        if (window.history.length <= 1) {
                            e.preventDefault();
                            window.location.href = '{{ route('dashboard') }}';
                        }
                    });
                }
            });
        </script>
    @endpush
@endsection
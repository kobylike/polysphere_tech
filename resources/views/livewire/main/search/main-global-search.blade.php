<div x-data="searchApp()" x-init="init()">
    {{-- Page Banner --}}
    <section class="page-title-area"
        style="background-image: url('{{ asset('assets/main/imgs/bg/page-title-bg.png') }}');">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="page-title-content text-center">
                        <h1 class="page-title">Search Results</h1>
                        <div class="breadcrumb">
                            <nav aria-label="Breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('index') }}"
                                            wire:navigate.hover>Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Search</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Search Section --}}
    <section class="blog-area pt-120 pb-90">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12">

                    {{-- Search Bar --}}
                    <div class="card border-0 shadow-lg mb-5">
                        <div class="card-body p-4">
                            <div class="search-wrapper">
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 py-3"
                                        placeholder="Search services, projects, blog posts, team members..."
                                        wire:model.live.debounce.300ms="query" x-ref="searchInput">
                                    <button class="btn btn-primary px-4" wire:click="performSearch">
                                        <i class="fas fa-arrow-right"></i>
                                    </button>
                                </div>

                                {{-- Category Filters --}}
                                <div class="d-flex flex-wrap gap-2 mt-3 justify-content-center">
                                    @foreach($categories as $cat)
                                        <button
                                            class="btn btn-sm rounded-pill px-3 {{ $category === $cat ? 'btn-primary' : 'btn-outline-secondary' }}"
                                            wire:click="$set('category', '{{ $cat }}')">
                                            {{ ucfirst($cat) }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Results --}}
                    <div class="card border-0 shadow-lg">
                        <div class="card-header bg-white border-0 py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    @if(!empty($query))
                                        Results for "<span class="text-primary">{{ $query }}</span>"
                                    @else
                                        Search
                                    @endif
                                </h5>
                                @if(!empty($results) && count($results) > 0)
                                    <span class="badge bg-secondary">{{ count($results) }} results</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body p-0">
                            {{-- Loading --}}
                            <div wire:loading wire:target="query, category" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2">Searching...</p>
                            </div>

                            {{-- Results --}}
                            <div wire:loading.remove wire:target="query, category">
                                @if(empty($results) && strlen($query) >= 2)
                                    <div class="text-center py-5">
                                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                        <h5>No results found</h5>
                                        <p class="text-muted">Try adjusting your search terms or filters.</p>
                                    </div>
                                @elseif(empty($results))
                                    <div class="text-center py-5">
                                        <i class="fas fa-search fa-4x text-muted mb-3"></i>
                                        <h5>Start searching</h5>
                                        <p class="text-muted">Type at least 2 characters to see results.</p>
                                    </div>
                                @else
                                    <div class="list-group list-group-flush">
                                        @foreach($results as $result)
                                            <a href="{{ $result['url'] }}" wire:navigate.hover
                                                class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                                <div class="icon-box me-3 bg-{{ $result['color'] }}-light rounded-circle"
                                                    style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas {{ $result['icon'] }} text-{{ $result['color'] }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold">{{ $result['title'] }}</div>
                                                    <small class="text-muted">{{ $result['subtitle'] }}</small>
                                                    <span
                                                        class="badge bg-{{ $result['color'] }} ms-2">{{ ucfirst($result['category']) }}</span>
                                                </div>
                                                <i class="fas fa-chevron-right ms-3 text-muted"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

{{-- Alpine integration for live updates --}}
@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('searchApp', () => ({
                query: @entangle('query').live,
                category: @entangle('category').live,

                init() {
                    this.$nextTick(() => {
                        this.$refs.searchInput?.focus();
                    });
                }
            }));
        });
    </script>
@endpush

{{-- Custom styles --}}
<style>
    .list-group-item-action {
        transition: all 0.2s ease;
    }

    .list-group-item-action:hover {
        background-color: var(--bs-light);
        transform: translateX(4px);
    }

    .icon-box {
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .list-group-item-action:hover .icon-box {
        transform: scale(1.05);
    }

    .input-group .form-control:focus {
        box-shadow: none;
    }

    .shadow-lg {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .08) !important;
    }
</style>
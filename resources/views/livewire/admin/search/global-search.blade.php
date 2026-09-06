<div>
    {{-- Breadcrumb --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Search Results</h5>
            </li>
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" wire:navigate.hover>Home</a></li>
            <li class="breadcrumb-item active">Search</li>
        </ol>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                {{-- If user has no searchable categories --}}
                @if(count($categories) === 1 && $categories[0] === 'all' && !Auth::user()->canAny(['View Users', 'View Projects', 'View Services', 'View Posts']))
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                            <h5>No search permissions</h5>
                            <p class="text-muted">You don't have permission to search any content.</p>
                        </div>
                    </div>
                @else
                    {{-- Search Bar Card --}}
                    <div class="card mb-4 shadow-sm">
                        <div class="card-body py-4">
                            <div class="row align-items-center">
                                <div class="col-lg-8 col-md-10 mx-auto">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0"
                                            placeholder="Search users, projects, services, posts..."
                                            wire:model.live.debounce.300ms="query">
                                        <button class="btn btn-primary" wire:click="performSearch">
                                            <i class="fas fa-arrow-right"></i>
                                        </button>
                                    </div>

                                    {{-- Category filters (only show categories the user has permission to see) --}}
                                    @if(count($categories) > 1)
                                        <div class="d-flex flex-wrap gap-2 mt-3 justify-content-center">
                                            @foreach($categories as $cat)
                                                <button
                                                    class="btn btn-sm rounded-pill px-3 {{ $category === $cat ? 'btn-primary' : 'btn-outline-secondary' }}"
                                                    wire:click="$set('category', '{{ $cat }}')">
                                                    {{ ucfirst($cat) }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Results Card --}}
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4 class="heading mb-0">
                                @if(!empty($query))
                                    Results for "<span class="text-primary">{{ $query }}</span>"
                                @else
                                    Search
                                @endif
                            </h4>
                            @if(!empty($results) && count($results) > 0)
                                <span class="badge bg-secondary">{{ count($results) }} results</span>
                            @endif
                        </div>
                        <div class="card-body p-0">
                            {{-- Loading indicator --}}
                            <div wire:loading wire:target="query, category" class="text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="text-muted mt-2">Searching...</p>
                            </div>

                            {{-- Results list --}}
                            <div wire:loading.remove wire:target="query, category">
                                @if(empty($results) && strlen($query) >= 2)
                                    <div class="text-center py-5">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5>No results found</h5>
                                        <p class="text-muted">Try adjusting your search terms or filters.</p>
                                    </div>
                                @elseif(empty($results))
                                    <div class="text-center py-5">
                                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                        <h5>Start searching</h5>
                                        <p class="text-muted">Type at least 2 characters to see results.</p>
                                    </div>
                                @else
                                    <div class="list-group list-group-flush">
                                        @foreach($results as $result)
                                            <a href="{{ $result['url'] }}" wire:navigate.hover
                                                class="list-group-item list-group-item-action d-flex align-items-center py-3">
                                                <div class="icon-box me-3 bg-{{ $result['color'] }}-light rounded-circle"
                                                    style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
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
                @endif
            </div>
        </div>
    </div>
</div>

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

    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075) !important;
    }
</style>
<div x-data="mainSearch()" x-init="init()" x-show="isOpen" x-cloak class="df-search-area">
    <div class="container">
        <div class="row">
            <div class="col-xl-12">
                <div class="df-search-form">
                    <div class="df-search-close text-center mb-20">
                        <button class="df-search-close-btn" @click="closeSearch()" aria-label="Close search"></button>
                    </div>

                    <form @submit.prevent="performSearch()" wire:keydown.enter.prevent="performSearch">
                        <div class="df-search-input mb-10">
                            <input type="text" placeholder="Search services, case studies, blog posts..."
                                wire:model.live.debounce.300ms="searchQuery" x-ref="searchInput"
                                @keydown.escape="closeSearch()">
                            <button type="submit" wire:click="performSearch">
                                <i class="icon-search"></i>
                            </button>
                        </div>
                        <div class="df-search-category">
                            <span>Popular searches : </span>
                            <a wire:navigate.hover href="{{ route('main.search', ['q' => 'Custom Software']) }}">Custom
                                Software, </a>
                            <a wire:navigate.hover href="{{ route('main.search', ['q' => 'SaaS Platform']) }}">SaaS
                                Platform, </a>
                            <a wire:navigate.hover
                                href="{{ route('main.search', ['q' => 'Digital Transformation']) }}">Digital
                                Transformation, </a>
                            <a wire:navigate.hover href="{{ route('main.search', ['q' => 'IT Consulting']) }}">IT
                                Consulting</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('mainSearch', () => ({
                isOpen: false,

                init() {
                    window.addEventListener('open-main-search', () => {
                        this.isOpen = true;
                        this.$nextTick(() => {
                            this.$refs.searchInput?.focus();
                        });
                    });

                    window.addEventListener('close-main-search', () => {
                        this.isOpen = false;
                    });
                },

                closeSearch() {
                    this.isOpen = false;
                },

                performSearch() {
                    if (this.$refs.searchInput?.value?.trim().length >= 2) {
                        @this.performSearch();
                        this.isOpen = false;
                    }
                }
            }));
        });
    </script>
@endpush

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
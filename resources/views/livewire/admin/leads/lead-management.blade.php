<div class="position-relative" x-data x-on:open-mailto.window="window.location.href = $event.detail.url"
    wire:poll.15s="pollTick">

    {{-- PAGE TITLES --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Chat Leads</h5>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('dashboard') }}">
                    <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M2.125 6.375L8.5 1.41667L14.875 6.375V14.1667C14.875 14.5424 14.7257 14.9027 14.4601 15.1684C14.1944 15.4341 13.8341 15.5833 13.4583 15.5833H3.54167C3.16594 15.5833 2.80561 15.4341 2.53993 15.1684C2.27426 14.9027 2.125 14.5424 2.125 14.1667V6.375Z"
                            stroke="#2C2C2C" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M6.375 15.5833V8.5H10.625V15.5833" stroke="#2C2C2C" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    Home
                </a>
            </li>
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Chat Leads</a></li>
        </ol>
        <div class="d-flex gap-2 align-items-center">
            {{-- Live indicator + pause toggle --}}
            <span
                class="badge border-0 d-flex align-items-center gap-1 px-2 py-1 {{ $this->shouldPoll ? 'text-success' : 'text-secondary' }}"
                style="font-size:11px; background: {{ $this->shouldPoll ? '#d1fae5' : '#f1f5f9' }};"
                title="{{ $this->shouldPoll ? 'Auto-refreshing every 15s' : ($pollingPaused ? 'Paused by you' : 'Paused — modal open') }}">
                <span class="live-dot {{ $this->shouldPoll ? 'pulsing' : '' }}"
                    style="background: {{ $this->shouldPoll ? '#10b981' : '#94a3b8' }};"></span>
                {{ $this->shouldPoll ? 'Live' : 'Paused' }}
            </span>
            <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="togglePolling"
                title="{{ $pollingPaused ? 'Resume live updates' : 'Pause live updates' }}">
                <i class="fa-solid {{ $pollingPaused ? 'fa-play' : 'fa-pause' }}"></i>
            </button>

            <button class="btn btn-primary btn-sm" wire:click="openExportModal">
                <i class="fa-solid fa-file-export me-1"></i> Export
            </button>
            <button class="btn btn-outline-secondary btn-sm" wire:click="resetFilters">
                <i class="fa-solid fa-undo me-1"></i> Reset
            </button>
        </div>
    </div>

    <div class="container-fluid">

        {{-- STATS ROW --}}
        <div class="row g-3 mb-3">
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-primary text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Total Leads</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['total']) }}</h3>
                        </div>
                        <i class="fa-solid fa-inbox fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">🔥 Hot Leads</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['hot']) }}</h3>
                        </div>
                        <i class="fa-solid fa-fire fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-info text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Contacted</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['contacted']) }}</h3>
                        </div>
                        <i class="fa-solid fa-paper-plane fs-24"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card bg-success text-white">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <span class="fs-14">Converted</span>
                            <h3 class="text-white mb-0">{{ number_format($stats['converted']) }}</h3>
                        </div>
                        <i class="fa-solid fa-trophy fs-24"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUB-STATS STRIP --}}
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card border-0"
                    style="background: linear-gradient(135deg, #eef2ff, #e0e7ff); border: 1px solid #c7d2fe !important;">
                    <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3 py-3">
                        <div class="d-flex flex-wrap gap-4 align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-white text-primary border-0 px-3 py-2">
                                    <i class="fa-solid fa-sparkles text-primary me-1"></i>
                                    <strong>{{ $stats['new'] }}</strong> new
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-white text-dark border-0 px-3 py-2">
                                    <i class="fa-solid fa-calendar-day text-primary me-1"></i>
                                    <strong>{{ $stats['today'] }}</strong> today
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-white text-dark border-0 px-3 py-2">
                                    <i class="fa-solid fa-calendar-week text-primary me-1"></i>
                                    <strong>{{ $stats['week'] }}</strong> this week
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-white text-warning border-0 px-3 py-2">
                                    <i class="fa-solid fa-star me-1"></i>
                                    <strong>{{ $stats['starred'] }}</strong> starred
                                </span>
                            </div>
                            @if($stats['spam'] > 0)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-white text-danger border-0 px-3 py-2">
                                        <i class="fa-solid fa-ban me-1"></i>
                                        <strong>{{ $stats['spam'] }}</strong> spam
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTERS & BULK BAR --}}
        <div class="row align-items-center mb-3 g-2">
            <div class="col-xl-9 col-lg-8">
                <div class="d-flex flex-wrap gap-2">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-sm"
                            placeholder="Search email, name, phone, message, IP…"
                            wire:model.live.debounce.300ms="search">
                        <i class="fa-solid fa-search"></i>
                    </div>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="statusFilter">
                        <option value="">All Status</option>
                        @foreach($statuses as $key => $meta)
                            <option value="{{ $key }}">{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="sourceFilter">
                        <option value="">All Sources</option>
                        @foreach($sources as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="scoreFilter">
                        <option value="">All Scores</option>
                        <option value="hot">🔥 Hot (70+)</option>
                        <option value="warm">🌤 Warm (40–69)</option>
                        <option value="cold">❄ Cold (&lt;40)</option>
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="dateRange">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="week">Last 7 days</option>
                        <option value="month">Last 30 days</option>
                        <option value="custom">Custom…</option>
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="hasFilter">
                        <option value="">Any contact info</option>
                        <option value="has-name">Has name</option>
                        <option value="has-phone">Has phone</option>
                        <option value="has-company">Has company</option>
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="spamFilter">
                        <option value="exclude">Exclude spam</option>
                        <option value="only">Spam only</option>
                        <option value="all">Include spam</option>
                    </select>
                    <select class="default-select style-1 form-control form-control-sm w-auto"
                        wire:model.live="perPage">
                        <option value="10">10 / page</option>
                        <option value="20">20 / page</option>
                        <option value="50">50 / page</option>
                        <option value="100">100 / page</option>
                    </select>
                </div>

                @if($dateRange === 'custom')
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        <div>
                            <label class="form-label small mb-1 fw-bold">From</label>
                            <input type="date" class="form-control form-control-sm" wire:model.live="dateFrom">
                        </div>
                        <div>
                            <label class="form-label small mb-1 fw-bold">To</label>
                            <input type="date" class="form-control form-control-sm" wire:model.live="dateTo">
                        </div>
                    </div>
                @endif
            </div>
            @if(count($selectedLeads) > 0)
                <div class="col-xl-3 col-lg-4 text-end">
                    <div class="d-flex flex-wrap gap-1 justify-content-end">
                        <span class="badge bg-dark text-white p-2">{{ count($selectedLeads) }} selected</span>
                        <button class="btn btn-success btn-sm" wire:click="bulkMarkContacted" title="Mark contacted">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                        <button class="btn btn-warning btn-sm" wire:click="bulkStar" title="Star">
                            <i class="fa-solid fa-star"></i>
                        </button>
                        <button class="btn btn-info btn-sm text-white" wire:click="bulkResendNotification"
                            title="Resend notification">
                            <i class="fa-solid fa-envelope"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" wire:click="bulkMarkSpam" title="Mark spam">
                            <i class="fa-solid fa-ban"></i>
                        </button>
                        <button class="btn btn-danger btn-sm" wire:click="confirmBulkDelete" title="Delete">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            @endif
        </div>

        {{-- TABLE --}}
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body p-0">
                        <div class="table-responsive active-projects style-1">
                            <div class="tbl-caption">
                                <h4 class="heading mb-0">Leads</h4>
                                <div>
                                    <span class="text-muted small">
                                        {{ $leads->total() }} total record(s)
                                        @if($lastPolledAt)
                                            · <span class="text-muted" style="font-size:11px;">updated
                                                {{ \Illuminate\Support\Carbon::parse($lastPolledAt)->diffForHumans() }}</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <table class="table leads-table">
                                <thead>
                                    <tr>
                                        <th style="width:40px">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll"
                                                    wire:model.live="selectAll">
                                                <label class="form-check-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                        <th wire:click="sort('email')" style="cursor:pointer;">
                                            Lead
                                            @if($sortBy === 'email')
                                                <i
                                                    class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </th>
                                        <th>Contact</th>
                                        <th class="text-center" wire:click="sort('score')"
                                            style="cursor:pointer; width:100px;">
                                            Score
                                            @if($sortBy === 'score')
                                                <i
                                                    class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </th>
                                        <th wire:click="sort('status')" style="cursor:pointer;">Status</th>
                                        <th wire:click="sort('source')" style="cursor:pointer;">Source</th>
                                        <th>Tags</th>
                                        <th wire:click="sort('created_at')" style="cursor:pointer;">
                                            Captured
                                            @if($sortBy === 'created_at')
                                                <i
                                                    class="fa-solid fa-sort-{{ $sortDir === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                            @endif
                                        </th>
                                        <th class="text-center" style="min-width:220px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($leads as $lead)
                                        @php
                                            $statusMeta = $lead->status_meta;
                                            $tier = $lead->score_tier;
                                            $tierColor = match ($tier) {
                                                'hot' => ['#f59e0b', '#d97706'],
                                                'warm' => ['#0ea5e9', '#0284c7'],
                                                default => ['#94a3b8', '#64748b'],
                                            };
                                        @endphp
                                        <tr
                                            class="{{ in_array((string) $lead->id, $selectedLeads) ? 'table-active' : '' }}">
                                            <td>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model.live="selectedLeads" value="{{ $lead->id }}">
                                                    <label class="form-check-label"></label>
                                                </div>
                                            </td>

                                            {{-- LEAD --}}
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar avatar-md" style="flex-shrink:0;">
                                                        <span class="avatar-text"
                                                            style="background: linear-gradient(135deg, {{ $tierColor[0] }}, {{ $tierColor[1] }});">
                                                            {{ $lead->initials }}
                                                        </span>
                                                    </div>
                                                    <div class="min-w-0">
                                                        <div class="d-flex align-items-center gap-1">
                                                            <h6 class="mb-0 text-truncate" style="max-width:200px;">
                                                                {{ $lead->name ?: $lead->email }}
                                                            </h6>
                                                            @if($lead->is_starred)
                                                                <i class="fa-solid fa-star text-warning"
                                                                    style="font-size:11px;"></i>
                                                            @endif
                                                            @if($lead->is_spam)
                                                                <i class="fa-solid fa-ban text-danger" style="font-size:11px;"
                                                                    title="Spam"></i>
                                                            @endif
                                                        </div>
                                                        @if($lead->name)
                                                            <a href="mailto:{{ $lead->email }}"
                                                                class="text-muted small text-decoration-none d-block text-truncate"
                                                                style="max-width:200px;">
                                                                {{ $lead->email }}
                                                            </a>
                                                        @endif
                                                        <div class="text-muted small" style="font-size:11px;">
                                                            <i class="fa-solid fa-comment" style="opacity:.6;"></i>
                                                            {{ $lead->message_count }} msg
                                                            @if($lead->short_page)
                                                                · <i class="fa-solid fa-link" style="opacity:.6;"></i>
                                                                {{ $lead->short_page }}
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- CONTACT --}}
                                            <td>
                                                <div class="d-flex flex-column gap-1 small">
                                                    @if($lead->phone)
                                                        <div><i class="fa-solid fa-phone text-muted me-1"
                                                                style="font-size:10px;"></i>{{ $lead->phone }}</div>
                                                    @endif
                                                    @if($lead->company)
                                                        <div><i class="fa-solid fa-building text-muted me-1"
                                                                style="font-size:10px;"></i>{{ $lead->company }}</div>
                                                    @endif
                                                    @if(!$lead->phone && !$lead->company)
                                                        <span class="text-muted small">—</span>
                                                    @endif
                                                </div>
                                            </td>

                                            {{-- SCORE --}}
                                            <td class="text-center">
                                                <div class="d-inline-flex align-items-center justify-content-center"
                                                    style="width:44px; height:44px; border-radius:50%; background: linear-gradient(135deg, {{ $tierColor[0] }}22, {{ $tierColor[1] }}22); border: 2px solid {{ $tierColor[0] }};">
                                                    <strong
                                                        style="font-size:13px; color:{{ $tierColor[1] }};">{{ $lead->score }}</strong>
                                                </div>
                                                <div class="small text-muted mt-1"
                                                    style="font-size:10px; text-transform:uppercase; letter-spacing:.05em;">
                                                    {{ $tier }}
                                                </div>
                                            </td>

                                            {{-- STATUS --}}
                                            <td>
                                                <button type="button" class="badge border-0 px-2 py-1"
                                                    style="background: {{ $statusMeta['color'] }}22; color: {{ $statusMeta['color'] }}; cursor: pointer;"
                                                    wire:click="openStatusModal({{ $lead->id }})"
                                                    title="Click to change status">
                                                    <i class="fa-solid {{ $statusMeta['icon'] }} me-1"></i>
                                                    {{ $statusMeta['label'] }}
                                                </button>
                                                @if($lead->contacted_at)
                                                    <div class="small text-muted mt-1" style="font-size:10px;">
                                                        by {{ $lead->contactedBy?->name ?? 'Admin' }}
                                                    </div>
                                                @endif
                                            </td>

                                            {{-- SOURCE --}}
                                            <td>
                                                <span class="badge bg-light text-dark border-0">
                                                    {{ $lead->source }}
                                                </span>
                                            </td>

                                            {{-- TAGS --}}
                                            <td>
                                                @if($lead->tags && count($lead->tags) > 0)
                                                    <div class="d-flex flex-wrap gap-1" style="max-width:140px;">
                                                        @foreach(array_slice($lead->tags, 0, 3) as $tag)
                                                            <span class="badge bg-info text-white border-0"
                                                                style="font-size:10px;">{{ $tag }}</span>
                                                        @endforeach
                                                        @if(count($lead->tags) > 3)
                                                            <span class="badge bg-secondary border-0"
                                                                style="font-size:10px;">+{{ count($lead->tags) - 3 }}</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <button class="btn btn-sm btn-outline-secondary"
                                                        style="padding:2px 8px; font-size:11px;"
                                                        wire:click="openTagModal({{ $lead->id }})">
                                                        <i class="fa-solid fa-plus"></i> tag
                                                    </button>
                                                @endif
                                            </td>

                                            {{-- CAPTURED --}}
                                            <td>
                                                <div class="d-flex flex-column small">
                                                    <span>{{ $lead->created_at->format('M d, Y') }}</span>
                                                    <span class="text-muted"
                                                        style="font-size:11px;">{{ $lead->created_at->diffForHumans() }}</span>
                                                </div>
                                            </td>

                                            {{-- ACTIONS --}}
                                            <td>
                                                <div class="lead-actions">
                                                    <button
                                                        class="btn btn-sm {{ $lead->is_starred ? 'btn-warning' : 'btn-outline-warning' }} action-btn"
                                                        wire:click="toggleStar({{ $lead->id }})"
                                                        title="{{ $lead->is_starred ? 'Unstar' : 'Star this lead' }}">
                                                        <i
                                                            class="fa-{{ $lead->is_starred ? 'solid' : 'regular' }} fa-star"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-primary action-btn"
                                                        wire:click="viewLead({{ $lead->id }})"
                                                        title="View full conversation">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-success action-btn"
                                                        wire:click="openReply({{ $lead->id }})"
                                                        title="Reply to {{ $lead->email }}">
                                                        <i class="fa-solid fa-reply"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-info action-btn"
                                                        wire:click="openNoteModal({{ $lead->id }})" title="Add / edit note">
                                                        <i class="fa-solid fa-note-sticky"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-secondary action-btn"
                                                        wire:click="openTagModal({{ $lead->id }})" title="Manage tags">
                                                        <i class="fa-solid fa-tags"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-outline-danger action-btn"
                                                        wire:click="confirmDelete({{ $lead->id }})" title="Delete">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center py-5">
                                                <i class="fa-solid fa-inbox fs-2 d-block mb-2 text-muted"></i>
                                                <h5>No leads found</h5>
                                                <p class="text-muted">Leads will appear here as visitors share their details
                                                    in the chat widget.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="row align-items-center p-3">
                            <div class="col-md-6">
                                <span class="small text-muted">
                                    Showing {{ $leads->firstItem() ?? 0 }}–{{ $leads->lastItem() ?? 0 }} of
                                    {{ $leads->total() }} leads
                                </span>
                            </div>
                            <div class="col-md-6 text-end">
                                {{ $leads->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- VIEW LEAD MODAL --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($showViewModal && $viewingLead)
        @php
            $statusMeta = $viewingLead->status_meta;
            $tier = $viewingLead->score_tier;
        @endphp
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-sm-down">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header" style="background: linear-gradient(120deg, #0F172A, #312E81);">
                        <h5 class="modal-title text-white">
                            <i class="fa-solid fa-user-circle me-2"></i>Lead Details
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                            wire:click="$set('showViewModal', false)"></button>
                    </div>

                    <div class="modal-body p-0">
                        <div class="row g-0">

                            {{-- LEFT: Contact info --}}
                            <div class="col-lg-5 p-4" style="background: #f8fafc; border-right: 1px solid #e2e8f0;">
                                <div class="d-flex align-items-center gap-3 mb-4">
                                    <div class="avatar avatar-lg" style="width:64px; height:64px;">
                                        <span class="avatar-text"
                                            style="background: linear-gradient(135deg, #6366f1, #8b5cf6); font-size:22px;">
                                            {{ $viewingLead->initials }}
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-grow-1">
                                        <h5 class="mb-0 text-truncate">{{ $viewingLead->name ?: 'Unknown visitor' }}</h5>
                                        <a href="mailto:{{ $viewingLead->email }}"
                                            class="text-primary small text-decoration-none d-block text-truncate">
                                            {{ $viewingLead->email }}
                                        </a>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge border-0 px-2"
                                                style="background: {{ $statusMeta['color'] }}22; color: {{ $statusMeta['color'] }};">
                                                <i class="fa-solid {{ $statusMeta['icon'] }} me-1"></i>
                                                {{ $statusMeta['label'] }}
                                            </span>
                                            @if($viewingLead->is_starred)
                                                <i class="fa-solid fa-star text-warning"></i>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Score --}}
                                <div class="mb-4 p-3 rounded-3" style="background:#fff; border:1px solid #e2e8f0;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="small text-uppercase fw-bold text-muted"
                                            style="letter-spacing:.05em; font-size:11px;">Lead Score</span>
                                        <span
                                            class="badge bg-{{ $tier === 'hot' ? 'warning' : ($tier === 'warm' ? 'info' : 'secondary') }} text-white border-0">
                                            {{ strtoupper($tier) }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div style="font-size:32px; font-weight:800; line-height:1;">
                                            {{ $viewingLead->score }}
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="progress" style="height:8px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $viewingLead->score }}%; background: linear-gradient(90deg, #6366f1, #f59e0b);">
                                                </div>
                                            </div>
                                            <div class="small text-muted mt-1">out of 100</div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Contact details --}}
                                <div class="mb-3">
                                    <div class="small text-uppercase fw-bold text-muted mb-2"
                                        style="letter-spacing:.05em; font-size:11px;">Contact Details</div>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted small" style="width:120px;"><i
                                                        class="fa-solid fa-envelope me-1"></i> Email</td>
                                                <td class="small fw-semibold"><a
                                                        href="mailto:{{ $viewingLead->email }}">{{ $viewingLead->email }}</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small"><i class="fa-solid fa-user me-1"></i> Name</td>
                                                <td class="small fw-semibold">{{ $viewingLead->name ?: '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small"><i class="fa-solid fa-phone me-1"></i> Phone
                                                </td>
                                                <td class="small fw-semibold">{{ $viewingLead->phone ?: '—' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small"><i class="fa-solid fa-building me-1"></i>
                                                    Company</td>
                                                <td class="small fw-semibold">{{ $viewingLead->company ?: '—' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Metadata --}}
                                <div class="mb-3">
                                    <div class="small text-uppercase fw-bold text-muted mb-2"
                                        style="letter-spacing:.05em; font-size:11px;">Metadata</div>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tbody>
                                            <tr>
                                                <td class="text-muted small" style="width:120px;"><i
                                                        class="fa-solid fa-clock me-1"></i> Captured</td>
                                                <td class="small">{{ $viewingLead->created_at->format('M d, Y g:i A') }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="text-muted small"><i class="fa-solid fa-globe me-1"></i> Source
                                                </td>
                                                <td class="small">{{ $viewingLead->source }}</td>
                                            </tr>
                                            @if($viewingLead->page_url)
                                                <tr>
                                                    <td class="text-muted small"><i class="fa-solid fa-link me-1"></i> Page</td>
                                                    <td class="small text-break">
                                                        <a href="{{ $viewingLead->page_url }}" target="_blank" rel="noopener"
                                                            class="text-decoration-none">
                                                            {{ $viewingLead->short_page }}
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                            @if($viewingLead->ip_address)
                                                <tr>
                                                    <td class="text-muted small"><i class="fa-solid fa-network-wired me-1"></i>
                                                        IP</td>
                                                    <td class="small"><code>{{ $viewingLead->ip_address }}</code></td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="text-muted small"><i
                                                        class="fa-solid fa-envelope-circle-check me-1"></i> Notified</td>
                                                <td class="small">
                                                    @if($viewingLead->notified_at)
                                                        <span class="text-success"><i class="fa-solid fa-check"></i>
                                                            {{ $viewingLead->notified_at->diffForHumans() }}</span>
                                                    @else
                                                        <span class="text-muted">Not sent</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            @if($viewingLead->contacted_at)
                                                <tr>
                                                    <td class="text-muted small"><i class="fa-solid fa-paper-plane me-1"></i>
                                                        Contacted</td>
                                                    <td class="small">
                                                        {{ $viewingLead->contacted_at->diffForHumans() }}
                                                        @if($viewingLead->contactedBy)
                                                            <span class="text-muted">by {{ $viewingLead->contactedBy->name }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>

                                {{-- Tags --}}
                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="small text-uppercase fw-bold text-muted"
                                            style="letter-spacing:.05em; font-size:11px;">Tags</div>
                                        <button class="btn btn-sm btn-link text-primary p-0" style="font-size:11px;"
                                            wire:click="openTagModal({{ $viewingLead->id }})">
                                            <i class="fa-solid fa-pen"></i> Manage
                                        </button>
                                    </div>
                                    @if($viewingLead->tags && count($viewingLead->tags) > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($viewingLead->tags as $tag)
                                                <span class="badge bg-info text-white border-0">{{ $tag }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-muted small">No tags yet</div>
                                    @endif
                                </div>
                            </div>

                            {{-- RIGHT: Conversation --}}
                            <div class="col-lg-7 p-4" style="max-height: 75vh; overflow-y: auto;">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <h6 class="mb-0">
                                        <i class="fa-solid fa-comments text-primary me-2"></i>
                                        Conversation
                                        <span
                                            class="badge bg-primary text-white ms-1">{{ $viewingLead->message_count }}</span>
                                    </h6>
                                    <small class="text-muted">Full transcript</small>
                                </div>

                                {{-- Original triggering message --}}
                                <div class="mb-4 p-3 rounded-3"
                                    style="background: linear-gradient(135deg, #fef3c7, #fde68a); border-left: 4px solid #f59e0b;">
                                    <div class="small text-uppercase fw-bold mb-1"
                                        style="color:#92400e; letter-spacing:.05em; font-size:11px;">
                                        <i class="fa-solid fa-star"></i> Triggering message
                                    </div>
                                    <p class="mb-0 small" style="white-space: pre-wrap;">{{ $viewingLead->message }}</p>
                                </div>

                                {{-- Conversation thread --}}
                                @if($viewingLead->conversation && count($viewingLead->conversation) > 0)
                                    <div class="lead-thread">
                                        @foreach($viewingLead->conversation as $turn)
                                            @php $role = $turn['role'] ?? 'assistant'; @endphp
                                            <div class="lead-msg lead-msg-{{ $role }}">
                                                <div
                                                    class="lead-msg-bubble {{ $role === 'user' ? 'lead-msg-bubble-user' : 'lead-msg-bubble-bot' }}">
                                                    <div class="lead-msg-text">{{ $turn['content'] ?? '' }}</div>
                                                    <div class="lead-msg-time">{{ $turn['time'] ?? '' }}</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted text-center py-4">
                                        <i class="fa-solid fa-comment-slash fa-2x mb-2 d-block"></i>
                                        No conversation transcript stored.
                                    </div>
                                @endif

                                {{-- Notes section --}}
                                <div class="mt-4 pt-4" style="border-top: 1px solid #e2e8f0;">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="mb-0">
                                            <i class="fa-solid fa-note-sticky text-info me-2"></i>
                                            Internal Notes
                                        </h6>
                                        <button class="btn btn-sm btn-outline-info"
                                            wire:click="openNoteModal({{ $viewingLead->id }})">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </button>
                                    </div>
                                    @if($viewingLead->notes)
                                        <div class="p-3 rounded-3"
                                            style="background:#f1f5f9; white-space: pre-wrap; font-size:13px;">
                                            {{ $viewingLead->notes }}
                                        </div>
                                    @else
                                        <div class="text-muted small">No notes yet — click Edit to add one.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer justify-content-between">
                        <div class="d-flex gap-2">
                            <button type="button"
                                class="btn btn-{{ $viewingLead->is_starred ? 'warning' : 'outline-warning' }}"
                                wire:click="toggleStar({{ $viewingLead->id }}); viewLead({{ $viewingLead->id }})">
                                <i class="fa-{{ $viewingLead->is_starred ? 'solid' : 'regular' }} fa-star"></i>
                                {{ $viewingLead->is_starred ? 'Starred' : 'Star' }}
                            </button>
                            <button type="button" class="btn btn-outline-danger"
                                wire:click="toggleSpam({{ $viewingLead->id }}); $set('showViewModal', false)">
                                <i class="fa-solid fa-ban"></i>
                                {{ $viewingLead->is_spam ? 'Unmark spam' : 'Mark spam' }}
                            </button>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary"
                                wire:click="$set('showViewModal', false)">Close</button>
                            <button type="button" class="btn btn-primary"
                                wire:click="openStatusModal({{ $viewingLead->id }}); $set('showViewModal', false)">
                                <i class="fa-solid fa-arrow-right-arrow-left"></i> Change Status
                            </button>
                            <button type="button" class="btn btn-success"
                                wire:click="openReply({{ $viewingLead->id }}); $set('showViewModal', false)">
                                <i class="fa-solid fa-reply"></i> Reply
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- STATUS MODAL --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($showStatusModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid fa-arrow-right-arrow-left text-primary me-2"></i>Change
                            Lead Status</h5>
                        <button type="button" class="btn-close" wire:click="$set('showStatusModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">New Status</label>
                            <div class="row g-2">
                                @foreach(\App\Models\ChatLead::STATUSES as $key => $meta)
                                    <div class="col-6">
                                        <label class="d-flex align-items-center gap-2 p-2 rounded-3 w-100"
                                            style="cursor:pointer; border: 2px solid {{ $newStatus === $key ? $meta['color'] : '#e2e8f0' }}; background: {{ $newStatus === $key ? $meta['color'] . '15' : '#fff' }};">
                                            <input type="radio" name="newStatus" value="{{ $key }}" wire:model.live="newStatus"
                                                class="d-none">
                                            <i class="fa-solid {{ $meta['icon'] }}" style="color: {{ $meta['color'] }};"></i>
                                            <span class="fw-semibold small">{{ $meta['label'] }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('newStatus')
                            <div class="text-danger small mt-2">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-bold small">Note <span class="text-muted fw-normal">(optional —
                                    appended to lead notes)</span></label>
                            <textarea class="form-control" rows="3" wire:model="statusNote"
                                placeholder="e.g. Emailed back, waiting on reply…"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showStatusModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="saveStatus" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-solid fa-check"></i> Update Status</span>
                            <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- NOTE MODAL --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($showNoteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid fa-note-sticky text-info me-2"></i>Internal Note</h5>
                        <button type="button" class="btn-close" wire:click="$set('showNoteModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" rows="8" wire:model="noteText"
                            placeholder="Notes are internal only — never shown to visitors."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showNoteModal', false)">Cancel</button>
                        <button class="btn btn-info text-white" wire:click="saveNote">
                            <i class="fa-solid fa-save"></i> Save Note
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- TAG MODAL --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($showTagModal && $selectedLeadId)
        @php $tagLead = \App\Models\ChatLead::find($selectedLeadId); @endphp
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid fa-tags text-info me-2"></i>Manage Tags</h5>
                        <button type="button" class="btn-close" wire:click="$set('showTagModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        @if($tagLead)
                            <div class="mb-3">
                                <label class="form-label fw-bold small">Current Tags</label>
                                @if($tagLead->tags && count($tagLead->tags) > 0)
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($tagLead->tags as $tag)
                                            <span class="badge bg-info text-white border-0 d-flex align-items-center gap-2 px-3 py-2"
                                                style="font-size:13px;">
                                                {{ $tag }}
                                                <button type="button" class="btn-close btn-close-white" style="font-size:9px;"
                                                    wire:click="removeTag('{{ $tag }}')" aria-label="Remove"></button>
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-muted small">No tags yet.</div>
                                @endif
                            </div>
                            <div class="mb-0">
                                <label class="form-label fw-bold small">Add a Tag</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" wire:model="tagInput" wire:keydown.enter="addTag"
                                        placeholder="e.g. high-value, follow-up, enterprise">
                                    <button class="btn btn-primary" wire:click="addTag" type="button">
                                        <i class="fa-solid fa-plus"></i> Add
                                    </button>
                                </div>
                                <small class="text-muted">Lowercase, letters, numbers, hyphens and underscores only.</small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" wire:click="$set('showTagModal', false)">Done</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- DELETE MODALS --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete Lead?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-solid fa-trash-alt fa-3x text-danger mb-3"></i>
                        <p>This permanently removes the lead and its stored conversation.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteLead">
                            <i class="fa-solid fa-trash"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showBulkDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete {{ count($selectedLeads) }} leads?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showBulkDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fa-solid fa-inbox fa-3x text-danger mb-3"></i>
                        <p>You're about to permanently delete <strong>{{ count($selectedLeads) }} lead(s)</strong>. This
                            cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showBulkDeleteModal', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="bulkDelete">
                            <i class="fa-solid fa-trash"></i> Delete All
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- REPLY MODAL --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($showReplyModal && $replyLead)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                <div class="modal-content border-0 shadow-lg">

                    <div class="modal-header" style="background: linear-gradient(120deg, #0F172A, #312E81);">
                        <h5 class="modal-title text-white">
                            <i class="fa-solid fa-reply me-2"></i>Reply to {{ $replyLead->name ?: $replyLead->email }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeReply"></button>
                    </div>

                    <div class="modal-body p-4">

                        {{-- Recipient --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase"
                                style="letter-spacing:.05em; font-size:11px;">To</label>
                            <div class="d-flex align-items-center gap-2" x-data="{ copied: false }">
                                <input type="text" class="form-control form-control-sm bg-light"
                                    value="{{ $replyLead->name ? $replyLead->name . ' <' . $replyLead->email . '>' : $replyLead->email }}"
                                    readonly>
                                <button type="button" class="btn btn-sm btn-outline-secondary flex-shrink-0" x-on:click="
                                                navigator.clipboard.writeText('{{ $replyLead->email }}');
                                                copied = true;
                                                setTimeout(() => copied = false, 1500);
                                            " title="Copy email address">
                                    <i class="fa-solid" :class="copied ? 'fa-check text-success' : 'fa-copy'"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Subject --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase"
                                style="letter-spacing:.05em; font-size:11px;">Subject</label>
                            <input type="text" class="form-control" wire:model="replySubject">
                            @error('replySubject')
                            <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Body --}}
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted text-uppercase"
                                style="letter-spacing:.05em; font-size:11px;">Message</label>
                            <textarea class="form-control" rows="10" wire:model="replyBody"
                                style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size:14px; line-height:1.6;"></textarea>
                            @error('replyBody')
                            <div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>

                        {{-- Quick context from the chat --}}
                        @if($replyLead->message)
                            <div class="p-3 rounded-3 mt-3" style="background:#F8FAFC; border:1px solid #E2E8F0;">
                                <div class="small text-uppercase fw-bold text-muted mb-2"
                                    style="letter-spacing:.05em; font-size:11px;">
                                    <i class="fa-solid fa-quote-left me-1"></i> Original message
                                </div>
                                <div class="small" style="white-space:pre-wrap; color:#334155;">{{ $replyLead->message }}</div>
                            </div>
                        @endif

                    </div>

                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-secondary" wire:click="closeReply"
                            wire:loading.attr="disabled">
                            Cancel
                        </button>
                        <button type="button" class="btn btn-success" wire:click="sendReply" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-solid fa-paper-plane"></i> Open in Mail Client</span>
                            <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Opening…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- EXPORT MODAL --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($showExportModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa-solid fa-file-csv text-primary me-2"></i>Export Leads</h5>
                        <button type="button" class="btn-close" wire:click="$set('showExportModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted mb-3">Choose what to export. The CSV includes contact info, status,
                            score, tags, and the captured message.</p>
                        <div class="d-flex flex-column gap-2">
                            <label class="d-flex align-items-center gap-2 p-3 rounded-3"
                                style="cursor:pointer; border: 2px solid {{ $exportScope === 'filtered' ? '#4338ca' : '#e2e8f0' }}; background: {{ $exportScope === 'filtered' ? '#4338ca12' : '#fff' }};">
                                <input type="radio" name="exportScope" value="filtered" wire:model.live="exportScope"
                                    class="d-none">
                                <i class="fa-solid fa-filter text-primary"></i>
                                <div>
                                    <div class="fw-semibold">Current filters</div>
                                    <small class="text-muted">Export only what's shown with the active filters</small>
                                </div>
                            </label>
                            @if(count($selectedLeads) > 0)
                                <label class="d-flex align-items-center gap-2 p-3 rounded-3"
                                    style="cursor:pointer; border: 2px solid {{ $exportScope === 'selected' ? '#4338ca' : '#e2e8f0' }}; background: {{ $exportScope === 'selected' ? '#4338ca12' : '#fff' }};">
                                    <input type="radio" name="exportScope" value="selected" wire:model.live="exportScope"
                                        class="d-none">
                                    <i class="fa-solid fa-check-double text-success"></i>
                                    <div>
                                        <div class="fw-semibold">Selected only ({{ count($selectedLeads) }})</div>
                                        <small class="text-muted">Export just the checked rows</small>
                                    </div>
                                </label>
                            @endif
                            <label class="d-flex align-items-center gap-2 p-3 rounded-3"
                                style="cursor:pointer; border: 2px solid {{ $exportScope === 'all' ? '#4338ca' : '#e2e8f0' }}; background: {{ $exportScope === 'all' ? '#4338ca12' : '#fff' }};">
                                <input type="radio" name="exportScope" value="all" wire:model.live="exportScope"
                                    class="d-none">
                                <i class="fa-solid fa-database text-secondary"></i>
                                <div>
                                    <div class="fw-semibold">Everything</div>
                                    <small class="text-muted">All leads regardless of filters</small>
                                </div>
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showExportModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="exportCsv" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fa-solid fa-download"></i> Download CSV</span>
                            <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Preparing…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- STYLES --}}
<style>
    .avatar-text {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-weight: 700;
        color: #fff;
    }

    .avatar .avatar-text {
        border-radius: 50%;
    }

    .avatar-md .avatar-text {
        width: 40px;
        height: 40px;
        font-size: 13px;
    }

    .avatar-lg .avatar-text {
        width: 64px;
        height: 64px;
        font-size: 22px;
    }

    .leads-table tbody td {
        vertical-align: middle;
    }

    .leads-table th {
        vertical-align: middle;
        white-space: nowrap;
    }

    .lead-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 3px;
        white-space: nowrap;
        flex-wrap: nowrap;
    }

    .lead-actions .action-btn {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-size: 11.5px;
        flex-shrink: 0;
        line-height: 1;
    }

    /* Conversation thread in modal */
    .lead-thread {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .lead-msg {
        display: flex;
    }

    .lead-msg-user {
        justify-content: flex-end;
    }

    .lead-msg-assistant {
        justify-content: flex-start;
    }

    .lead-msg-bubble {
        max-width: 80%;
        padding: 10px 14px;
        border-radius: 14px;
        font-size: 13px;
        line-height: 1.5;
        word-wrap: break-word;
    }

    .lead-msg-bubble-user {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #fff;
        border-bottom-right-radius: 4px;
    }

    .lead-msg-bubble-bot {
        background: #f1f5f9;
        color: #1e293b;
        border-bottom-left-radius: 4px;
    }

    .lead-msg-text {
        white-space: pre-wrap;
    }

    .lead-msg-time {
        font-size: 10px;
        opacity: .65;
        margin-top: 4px;
    }

    .min-w-0 {
        min-width: 0;
    }

    /* Live polling indicator */
    .live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .live-dot.pulsing {
        animation: live-pulse 1.6s ease-in-out infinite;
    }

    @keyframes live-pulse {

        0%,
        100% {
            opacity: 1;
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4);
        }

        50% {
            opacity: 0.7;
            transform: scale(1.15);
            box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
        }
    }
</style>
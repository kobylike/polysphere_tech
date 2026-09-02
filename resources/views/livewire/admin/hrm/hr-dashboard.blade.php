<div x-data="hrHandler()" @notify.window="showToast($event.detail)" class="position-relative">

    {{-- TOAST --}}
    <div x-show="toastVisible" x-cloak x-transition:enter.duration.300ms.opacity.scale
        x-transition:leave.duration.200ms.opacity.scale class="position-fixed top-0 end-0 p-3"
        style="z-index: 9999; max-width: 420px; width: 100%;">
        <div class="d-flex align-items-center p-3 rounded-4 shadow-lg border-0 text-white gap-3"
            :class="toastType === 'success' ? 'bg-gradient-success' : 'bg-gradient-danger'"
            style="backdrop-filter: blur(8px);">
            <div class="flex-shrink-0">
                <i class="fas fa-2x" :class="toastType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="mb-0 fw-bold" style="color: #ffffff;" x-text="toastTitle"></h6>
                <p class="mb-0 small" style="color: #ffffff; opacity: 0.9;" x-html="toastMessage"></p>
            </div>
            <button @click="dismissToast()" class="btn btn-sm btn-link text-white p-0 opacity-75">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>

    {{-- PAGE TITLES --}}
    <div class="page-titles">
        <ol class="breadcrumb">
            <li>
                <h5 class="bc-title">Core HR</h5>
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
            <li class="breadcrumb-item active"><a href="javascript:void(0)">Core HR</a></li>
        </ol>
    </div>

    <div class="container-fluid">

        {{-- KPI CARDS --}}
        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-6 g-3 mb-2">
            <div class="col">
                <div class="card border-0 shadow-sm kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon" style="background:#eef2ff;">
                            <i class="fas fa-users text-primary"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $stats['total'] }}</h4>
                            <span class="text-muted small">Total Employees</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon" style="background:#ecfdf5;">
                            <i class="fas fa-user-check text-success"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $stats['active'] }}</h4>
                            <span class="text-muted small">Active</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon" style="background:#fef2f2;">
                            <i class="fas fa-user-slash text-danger"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $stats['suspended'] }}</h4>
                            <span class="text-muted small">Suspended</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon" style="background:#fffbeb;">
                            <i class="fas fa-plane-departure text-warning"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $stats['on_leave'] }}</h4>
                            <span class="text-muted small">On Leave Today</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon" style="background:#fef2f2;">
                            <i class="fas fa-calendar-xmark text-danger"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ $stats['absent_today'] }}</h4>
                            <span class="text-muted small">Absent Today</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 shadow-sm kpi-card h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div class="kpi-icon" style="background:#f1f5f9;">
                            <i class="fas fa-clipboard-question text-secondary"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 fw-bold">{{ max($stats['unmarked_today'], 0) }}</h4>
                            <span class="text-muted small">Unmarked Today</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- LEFT COLUMN --}}
            <div class="col-xxl-9 col-xl-8">
                <div class="row">
                    {{-- EMPLOYEE LIST --}}
                    <div class="col-xl-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="table-responsive active-projects style-1">
                                    <div class="tbl-caption flex-wrap gap-2">
                                        <h4 class="heading mb-0">Employees</h4>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button class="btn btn-primary btn-sm" wire:click="openCreate">
                                                <i class="fas fa-plus me-1"></i> Add Employee
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm"
                                                wire:click="openInviteModal">
                                                <i class="fas fa-envelope me-1"></i> Invite
                                            </button>
                                        </div>
                                    </div>
                                    {{-- Filters --}}
                                    <div class="row g-2 p-3">
                                        <div class="col-6 col-md-3">
                                            <input type="text" class="form-control form-control-sm"
                                                placeholder="Search employees..."
                                                wire:model.live.debounce.300ms="search">
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <select class="form-select form-select-sm"
                                                wire:model.live="departmentFilter">
                                                <option value="">All Departments</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept }}">{{ $dept }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <select class="form-select form-select-sm" wire:model.live="employmentType">
                                                <option value="">All Types</option>
                                                <option value="full-time">Full‑Time</option>
                                                <option value="part-time">Part‑Time</option>
                                                <option value="contract">Contract</option>
                                                <option value="intern">Intern</option>
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <select class="form-select form-select-sm" wire:model.live="statusFilter">
                                                <option value="">All Status</option>
                                                <option value="active">Active</option>
                                                <option value="suspended">Suspended</option>
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-2">
                                            <select class="form-select form-select-sm" wire:model.live="perPage">
                                                <option value="10">10 / page</option>
                                                <option value="25">25 / page</option>
                                                <option value="50">50 / page</option>
                                            </select>
                                        </div>
                                        <div class="col-6 col-md-1 text-end">
                                            <button class="btn btn-outline-secondary btn-sm" title="Clear search"
                                                wire:click="$set('search', '')"><i class="fas fa-times"></i></button>
                                        </div>
                                    </div>

                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Employee ID</th>
                                                <th>Employee Name</th>
                                                <th>Email</th>
                                                <th>Contact</th>
                                                <th>Gender</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($employees as $emp)
                                                <tr wire:key="employee-row-{{ $emp->id }}">
                                                    <td><span>{{ $emp->profile->employee_id ?? '—' }}</span></td>
                                                    <td>
                                                        <div class="products">
                                                            <img src="{{ $emp->avatar_url }}" class="avatar avatar-md"
                                                                alt="">
                                                            <div>
                                                                <h6>{{ $emp->name }}</h6>
                                                                <span>{{ $emp->position ?? '—' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="text-primary">{{ $emp->email }}</span></td>
                                                    <td><span>{{ $emp->phone ?? '—' }}</span></td>
                                                    <td>
                                                        @php $gender = $emp->profile->gender ?? null; @endphp
                                                        @if($gender === 'male')
                                                            <span class="badge badge-primary light border-0"><i
                                                                    class="fas fa-mars me-1"></i>Male</span>
                                                        @elseif($gender === 'female')
                                                            <span class="badge badge-danger light border-0"><i
                                                                    class="fas fa-venus me-1"></i>Female</span>
                                                        @elseif($gender === 'other')
                                                            <span class="badge badge-secondary light border-0">Other</span>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                    <td><span>{{ $emp->profile->department ?? '—' }}</span></td>
                                                    <td>
                                                        <span
                                                            class="badge badge-{{ $emp->status === 'active' ? 'success' : 'danger' }} light border-0">
                                                            {{ ucfirst($emp->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex justify-content-center flex-wrap gap-1">
                                                            <button class="btn btn-sm btn-primary"
                                                                wire:click="markAttendance({{ $emp->id }})"
                                                                title="Mark Attendance">
                                                                <i class="fas fa-calendar-check"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-warning"
                                                                wire:click="openEdit({{ $emp->id }})" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-sm btn-danger"
                                                                wire:click="confirmDelete({{ $emp->id }})" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4"><i
                                                            class="fas fa-users-slash fs-2 d-block mb-2 text-muted"></i>
                                                        <p class="text-muted">No employees found.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    <div class="row align-items-center p-3">
                                        <div class="col-md-6">
                                            <span class="text-muted small">Showing
                                                {{ $employees->firstItem() ?? 0 }}–{{ $employees->lastItem() ?? 0 }} of
                                                {{ $employees->total() }} employees</span>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            {{ $employees->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ATTENDANCE TABLE – CLEAN (no debug) --}}
                    <div class="col-xl-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <div class="tbl-caption px-3 pt-3 flex-wrap gap-2">
                                    <h4 class="heading mb-0">Attendance –
                                        {{ Carbon\Carbon::create($year, $month)->format('F Y') }}</h4>
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        {{-- LEGEND --}}
                                        <div class="d-flex align-items-center flex-wrap gap-2 small text-muted me-2">
                                            <span><i class="fas fa-check text-success me-1"></i> Present</span>
                                            <span><i class="fas fa-times text-danger me-1"></i> Absent</span>
                                            <span><i class="fas fa-clock text-warning me-1"></i> Leave</span>
                                            <span><i class="fas fa-sun text-warning me-1"></i> Holiday</span>
                                            <span><i class="fas fa-star text-info me-1"></i> Company Holiday</span>
                                            <span><span class="legend-dot legend-weekend"></span> Weekend</span>
                                            <span><span class="legend-dot legend-today"></span> Today</span>
                                        </div>
                                        <button class="btn btn-primary btn-sm" wire:click="openBulkAttendance"
                                            title="Bulk Mark">
                                            <i class="fas fa-calendar-plus me-1"></i> Bulk Mark
                                        </button>
                                        <button class="btn btn-success btn-sm" wire:click="exportAttendance"
                                            title="Export CSV">
                                            <i class="fas fa-file-export me-1"></i> Export
                                        </button>
                                    </div>
                                </div>
                                <p class="text-muted small px-3 mb-2">
                                    <i class="fas fa-circle-info me-1"></i>
                                    Tip: click any cell in the <strong>Today</strong> column to cycle Present → Absent →
                                    Leave → Cleared.
                                    Click an earlier day to edit or backdate that record.
                                </p>

                                <div class="table-responsive active-projects style-1 attendance-tbl">
                                    <table class="table mb-0">
                                        <thead>
                                            <tr>
                                                <th>Employee</th>
                                                @for($i = 1; $i <= $daysInMonth; $i++)
                                                    @php $d = \Carbon\Carbon::create($year, $month, $i); @endphp
                                                    <th
                                                        class="{{ $d->isWeekend() ? 'weekend-col' : '' }} {{ $d->isToday() ? 'today-col' : '' }}">
                                                        <span>{{ $i }}</span>
                                                        <p>{{ $d->format('D') }}</p>
                                                    </th>
                                                @endfor
                                                <th class="text-center">Rate</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($attendanceData as $row)
                                                <tr wire:key="attendance-row-{{ $row['user']->id }}">
                                                    <td>
                                                        <div class="products">
                                                            <img src="{{ $row['user']->avatar_url }}"
                                                                class="avatar avatar-md" alt="">
                                                            <div>
                                                                <h6>{{ $row['user']->name }}</h6>
                                                                <span>{{ $row['user']->position ?? '—' }}</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    @for($i = 1; $i <= $daysInMonth; $i++)
                                                        @php
                                                            $day = $row['days'][$i];
                                                            $cellDate = \Carbon\Carbon::create($year, $month, $i)->toDateString();
                                                        @endphp
                                                        <td class="att-cell {{ $day['is_weekend'] ? 'weekend-col' : '' }} {{ $day['is_holiday'] ? 'holiday-col' : '' }} {{ $day['is_today'] ? 'today-col' : '' }} {{ !$day['is_future'] ? 'att-cell-clickable' : '' }}"
                                                            @if($day['is_today'])
                                                                wire:click="quickMarkToday({{ $row['user']->id }})"
                                                            title="Click to cycle today's status" @elseif(!$day['is_future'])
                                                                wire:click="editAttendanceCell({{ $row['user']->id }}, '{{ $cellDate }}')"
                                                            title="Click to edit this day" @endif>
                                                            @if($day['display']['icon'])
                                                                <span class="{{ $day['display']['class'] }}">
                                                                    <i class="{{ $day['display']['icon'] }}"></i>
                                                                </span>
                                                            @else
                                                                <span class="text-muted">·</span>
                                                            @endif
                                                        </td>
                                                    @endfor
                                                    <td class="text-center" style="min-width:100px;">
                                                        <div class="d-flex flex-column align-items-center gap-1">
                                                            <span
                                                                class="fw-semibold small">{{ $row['total_present'] }}/{{ $daysInMonth }}</span>
                                                            <div class="progress w-100" style="max-width:70px;height:5px;">
                                                                <div class="progress-bar bg-success" role="progressbar"
                                                                    style="width: {{ $row['attendance_rate'] }}%"></div>
                                                            </div>
                                                            <span class="text-muted"
                                                                style="font-size:10px;">{{ $row['attendance_rate'] }}%</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ $daysInMonth + 2 }}" class="text-center py-4">
                                                        <p class="text-muted mb-0">No employees to display.</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-xxl-3 col-xl-4">
                <div class="row">
                    {{-- Attendance Summary --}}
                    <div class="col-xl-12 col-md-6">
                        <div class="card border-0 shadow-sm h-auto">
                            <div class="card-header pb-0 border-0 flex-wrap gap-2">
                                <h4 class="heading mb-0">Attendance Summary</h4>
                                <select class="default-select status-select normal-select" wire:model.live="month"
                                    wire:change="monthChanged">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="card-body">
                                <div class="position-relative" style="height:210px;">
                                    <canvas id="attendanceChart"></canvas>
                                </div>
                                <div class="text-center mb-2">
                                    <span class="badge badge-light border-0 fs-13">
                                        <i class="fas fa-chart-line me-1 text-primary"></i>
                                        {{ $attendanceStats['rate'] ?? 0 }}% overall present rate
                                    </span>
                                </div>
                                <div class="project-date">
                                    <div class="project-media">
                                        <p class="mb-0">
                                            <svg class="me-2" width="12" height="13" viewBox="0 0 12 13" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect y="0.5" width="12" height="12" rx="3" fill="#10b981" />
                                            </svg>
                                            Present
                                        </p>
                                        <span>{{ $attendanceStats['present'] }}</span>
                                    </div>
                                    <div class="project-media">
                                        <p class="mb-0">
                                            <svg class="me-2" width="12" height="13" viewBox="0 0 12 13" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect y="0.5" width="12" height="12" rx="3" fill="#ef4444" />
                                            </svg>
                                            Absent
                                        </p>
                                        <span>{{ $attendanceStats['absent'] }}</span>
                                    </div>
                                    <div class="project-media">
                                        <p class="mb-0">
                                            <svg class="me-2" width="12" height="13" viewBox="0 0 12 13" fill="none"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <rect y="0.5" width="12" height="12" rx="3" fill="#f59e0b" />
                                            </svg>
                                            Leave
                                        </p>
                                        <span>{{ $attendanceStats['leave'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Upcoming Holidays --}}
                    <div class="col-xl-12 col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                                <h4 class="heading mb-0">Upcoming Holidays</h4>
                                <button class="btn btn-sm btn-primary" wire:click="openHolidayModal()">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </div>
                            <div class="card-body schedules-cal p-2">
                                <div class="events">
                                    <h6>Events</h6>
                                    <div class="dz-scroll event-scroll" style="max-height:250px; overflow-y:auto;">
                                        @forelse($upcomingHolidays as $holiday)
                                            <div class="event-media">
                                                <div class="d-flex align-items-center">
                                                    <div class="event-box">
                                                        <h5 class="mb-0">{{ $holiday->next_date->format('d') }}</h5>
                                                        <span>{{ $holiday->next_date->format('D') }}</span>
                                                    </div>
                                                    <div class="event-data ms-2">
                                                        <h5 class="mb-0">{{ $holiday->name }}</h5>
                                                        <span>{{ $holiday->recurring ? 'Recurring · Company Holiday' : 'Company Holiday' }}</span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span
                                                        class="text-secondary me-2">{{ $holiday->next_date->diffForHumans() }}</span>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        wire:click="deleteHoliday({{ $holiday->id }})" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="text-center py-3 text-muted">No upcoming holidays.</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODALS --}}

    {{-- EMPLOYEE MODAL --}}
    @if($showEmployeeModal)
        <div class="modal fade show d-block" id="employeeModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-xl modal-fullscreen-sm-down">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingEmployeeId ? 'Edit Employee' : 'Add Employee' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showEmployeeModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted small mb-3"><span class="text-danger">*</span> Required field</p>
                        <div class="row g-3">
                            {{-- Employee ID / Gender / Featured Team --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Employee ID</label>
                                <input type="text" class="form-control" wire:model="employee_id" readonly
                                    style="background:#f8fafc; color:#1e293b; cursor:not-allowed;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Gender <span class="text-danger">*</span></label>
                                <select class="form-select @error('gender') is-invalid @enderror" wire:model="gender">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                                @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Featured Team</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="is_featured_team"
                                        wire:model="is_featured_team">
                                    <label class="form-check-label" for="is_featured_team">Display on public team
                                        page</label>
                                </div>
                            </div>

                            {{-- Personal Info --}}
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 fw-bold text-primary"><i class="fas fa-user me-2"></i>
                                    Personal Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">First Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror"
                                    wire:model="first_name">
                                @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror"
                                    wire:model="last_name">
                                @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email">
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6" wire:key="phone-field">
                                <label class="form-label fw-bold small">Phone Number <span
                                        class="text-danger">*</span></label>
                                <div class="phone-wrapper position-relative">
                                    <div class="input-group">
                                        <button type="button" wire:click="toggleCountryDropdown"
                                            class="btn btn-outline-secondary d-flex align-items-center gap-2 phone-country-btn"
                                            style="border-radius: 0.5rem 0 0 0.5rem; border-right: none; background: #f8fafc; padding: 0.45rem 0.65rem; white-space: nowrap;">
                                            <img src="{{ asset('flags/' . $selectedFlag) }}" class="rounded-1"
                                                style="width: 22px; height: 15px; object-fit: cover; flex-shrink: 0;">
                                            <span class="fw-semibold text-dark"
                                                style="font-size: 0.82rem;">{{ $countryCode }}</span>
                                            <i class="fas fa-chevron-down text-muted" style="font-size: 0.6rem;"></i>
                                        </button>
                                        <input type="tel" inputmode="numeric" wire:model.defer="phone"
                                            placeholder="{{ $phoneExample ? 'e.g. ' . $phoneExample : 'Phone number' }}"
                                            maxlength="{{ $countryInfo['maxLength'] ?? 15 }}"
                                            class="form-control phone-number-input @error('phone') is-invalid @enderror"
                                            style="border-radius: 0 0.5rem 0.5rem 0; font-size: 0.9rem; padding: 0.45rem 0.75rem;"
                                            x-data
                                            x-on:input="let v = $el.value.replace(/[^0-9]/g, ''); let max = {{ $countryInfo['maxLength'] ?? 15 }}; if (v.length > max) v = v.substring(0, max); $el.value = v; $wire.setPhone(v);">
                                    </div>
                                    @if($showCountryDropdown)
                                        <div class="dropdown-menu show p-0 mt-1 shadow-lg position-absolute phone-country-dropdown"
                                            x-data x-on:click.away="$wire.closeCountryDropdown()">
                                            <div class="sticky-top bg-white p-2 border-bottom">
                                                <div class="position-relative">
                                                    <i class="fas fa-search position-absolute text-muted"
                                                        style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;"></i>
                                                    <input type="text" wire:model.live.debounce.200ms="countrySearch"
                                                        placeholder="Search country…" class="form-control form-control-sm"
                                                        style="border-radius: 0.4rem; font-size: 0.8rem; padding: 0.3rem 0.5rem 0.3rem 1.8rem;">
                                                </div>
                                            </div>
                                            <div class="p-1">
                                                @forelse($filteredCountries as $country)
                                                    <button type="button"
                                                        wire:click="selectCountry('{{ $country['code'] }}', '{{ $country['flag'] }}')"
                                                        class="dropdown-item d-flex align-items-center gap-2 py-1 px-2 rounded {{ $countryCode === $country['code'] ? 'active' : '' }}"
                                                        style="font-size: 0.8rem;">
                                                        <img src="{{ asset('flags/' . $country['flag']) }}" class="rounded-1"
                                                            style="width: 20px; height: 14px; object-fit: cover; flex-shrink: 0;">
                                                        <span
                                                            class="flex-grow-1 text-truncate text-start">{{ $country['name'] }}</span>
                                                        <span class="text-muted flex-shrink-0"
                                                            style="font-size: 0.7rem;">{{ $country['code'] }}</span>
                                                    </button>
                                                @empty
                                                    <div class="px-2 py-2 text-muted text-center" style="font-size: 0.8rem;">No
                                                        countries found</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
                                    @if($phone)
                                        <div class="form-text text-primary" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                            <i class="fas fa-info-circle me-1"></i> Will be saved as:
                                            <strong>{{ $countryCode }}{{ $phone }}</strong>
                                        </div>
                                    @endif
                                    @error('phone')
                                        <div class="invalid-feedback d-block" style="font-size: 0.75rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- 🔥 NEW: Date of Birth, Country, City --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Date of Birth</label>
                                <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                    wire:model="date_of_birth" max="{{ now()->subYears(18)->format('Y-m-d') }}">
                                @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Country</label>
                                <select class="form-select @error('country_code') is-invalid @enderror"
                                    wire:model="country_code">
                                    <option value="">Select Country</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country['code'] }}">{{ $country['name'] }}</option>
                                    @endforeach
                                </select>
                                @error('country_code')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                    wire:model="city" placeholder="e.g. Accra">
                                @error('city')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Employment Info --}}
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 fw-bold text-primary mt-2"><i
                                        class="fas fa-briefcase me-2"></i> Employment Information</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Department <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <select class="form-select @error('department') is-invalid @enderror"
                                        wire:model="department">
                                        <option value="">Select Department</option>
                                        @foreach($departmentsList as $dept)
                                            <option value="{{ $dept }}">{{ $dept }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary" type="button"
                                        wire:click="$toggle('showNewDepartment')" title="Add new department"><i
                                            class="fas fa-plus"></i></button>
                                </div>
                                @if($showNewDepartment)
                                    <div class="mt-2 d-flex gap-2">
                                        <input type="text"
                                            class="form-control form-control-sm @error('newDepartment') is-invalid @enderror"
                                            wire:model="newDepartment" placeholder="New department name"
                                            @keydown.enter="addDepartment()">
                                        <button class="btn btn-sm btn-success" wire:click="addDepartment"><i
                                                class="fas fa-check"></i></button>
                                        <button class="btn btn-sm btn-secondary"
                                            wire:click="$set('showNewDepartment', false)"><i class="fas fa-times"></i></button>
                                    </div>
                                    @error('newDepartment')
                                    <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                @endif
                                @error('department')
                                <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Position / Job Title <span
                                        class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <select class="form-select @error('position') is-invalid @enderror"
                                        wire:model="position">
                                        <option value="">Select Position</option>
                                        @foreach($positionsList as $pos)
                                            <option value="{{ $pos }}">{{ $pos }}</option>
                                        @endforeach
                                    </select>
                                    <button class="btn btn-sm btn-outline-primary" type="button"
                                        wire:click="$toggle('showNewPosition')" title="Add new position"><i
                                            class="fas fa-plus"></i></button>
                                </div>
                                @if($showNewPosition)
                                    <div class="mt-2 d-flex gap-2">
                                        <input type="text"
                                            class="form-control form-control-sm @error('newPosition') is-invalid @enderror"
                                            wire:model="newPosition" placeholder="New position name"
                                            @keydown.enter="addPosition()">
                                        <button class="btn btn-sm btn-success" wire:click="addPosition"><i
                                                class="fas fa-check"></i></button>
                                        <button class="btn btn-sm btn-secondary" wire:click="$set('showNewPosition', false)"><i
                                                class="fas fa-times"></i></button>
                                    </div>
                                    @error('newPosition')
                                    <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                @endif
                                @error('position')
                                <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Employment Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('employment_type') is-invalid @enderror"
                                    wire:model="employment_type">
                                    <option value="full-time">Full‑Time</option>
                                    <option value="part-time">Part‑Time</option>
                                    <option value="contract">Contract</option>
                                    <option value="intern">Intern</option>
                                </select>
                                @error('employment_type')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small">Hire Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('hire_date') is-invalid @enderror"
                                    wire:model="hire_date" max="{{ now()->format('Y-m-d') }}">
                                @error('hire_date')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            {{-- Emergency Contact --}}
                            <div class="col-12">
                                <h6 class="border-bottom pb-2 fw-bold text-primary mt-2"><i
                                        class="fas fa-phone-alt me-2"></i> Emergency Contact</h6>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Contact Name <span
                                        class="text-danger">*</span></label>
                                <input type="text"
                                    class="form-control @error('emergency_contact_name') is-invalid @enderror"
                                    wire:model="emergency_contact_name">
                                @error('emergency_contact_name')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6" wire:key="emergency-phone-field">
                                <label class="form-label fw-bold small">Contact Phone <span
                                        class="text-danger">*</span></label>
                                <div class="phone-wrapper position-relative">
                                    <div class="input-group">
                                        <button type="button" wire:click="emergency_toggleCountryDropdown"
                                            class="btn btn-outline-secondary d-flex align-items-center gap-2 phone-country-btn"
                                            style="border-radius: 0.5rem 0 0 0.5rem; border-right: none; background: #f8fafc; padding: 0.45rem 0.65rem; white-space: nowrap;">
                                            <img src="{{ asset('flags/' . $emergency_selectedFlag) }}" class="rounded-1"
                                                style="width: 22px; height: 15px; object-fit: cover; flex-shrink: 0;">
                                            <span class="fw-semibold text-dark"
                                                style="font-size: 0.82rem;">{{ $emergency_countryCode }}</span>
                                            <i class="fas fa-chevron-down text-muted" style="font-size: 0.6rem;"></i>
                                        </button>
                                        <input type="tel" inputmode="numeric" wire:model.defer="emergency_contact_phone"
                                            placeholder="{{ $emergency_phoneExample ? 'e.g. ' . $emergency_phoneExample : 'Phone number' }}"
                                            maxlength="{{ $emergency_countryInfo['maxLength'] ?? 15 }}"
                                            class="form-control phone-number-input @error('emergency_contact_phone') is-invalid @enderror"
                                            style="border-radius: 0 0.5rem 0.5rem 0; font-size: 0.9rem; padding: 0.45rem 0.75rem;"
                                            x-data
                                            x-on:input="let v = $el.value.replace(/[^0-9]/g, ''); let max = {{ $emergency_countryInfo['maxLength'] ?? 15 }}; if (v.length > max) v = v.substring(0, max); $el.value = v; $wire.emergency_setPhone(v);">
                                    </div>
                                    @if($emergency_showCountryDropdown)
                                        <div class="dropdown-menu show p-0 mt-1 shadow-lg position-absolute phone-country-dropdown"
                                            x-data x-on:click.away="$wire.emergency_closeCountryDropdown()">
                                            <div class="sticky-top bg-white p-2 border-bottom">
                                                <div class="position-relative">
                                                    <i class="fas fa-search position-absolute text-muted"
                                                        style="left: 10px; top: 50%; transform: translateY(-50%); font-size: 0.7rem;"></i>
                                                    <input type="text" wire:model.live.debounce.200ms="emergency_countrySearch"
                                                        placeholder="Search country…" class="form-control form-control-sm"
                                                        style="border-radius: 0.4rem; font-size: 0.8rem; padding: 0.3rem 0.5rem 0.3rem 1.8rem;">
                                                </div>
                                            </div>
                                            <div class="p-1">
                                                @forelse($emergency_filteredCountries as $country)
                                                    <button type="button"
                                                        wire:click="emergency_selectCountry('{{ $country['code'] }}', '{{ $country['flag'] }}')"
                                                        class="dropdown-item d-flex align-items-center gap-2 py-1 px-2 rounded {{ $emergency_countryCode === $country['code'] ? 'active' : '' }}"
                                                        style="font-size: 0.8rem;">
                                                        <img src="{{ asset('flags/' . $country['flag']) }}" class="rounded-1"
                                                            style="width: 20px; height: 14px; object-fit: cover; flex-shrink: 0;">
                                                        <span
                                                            class="flex-grow-1 text-truncate text-start">{{ $country['name'] }}</span>
                                                        <span class="text-muted flex-shrink-0"
                                                            style="font-size: 0.7rem;">{{ $country['code'] }}</span>
                                                    </button>
                                                @empty
                                                    <div class="px-2 py-2 text-muted text-center" style="font-size: 0.8rem;">No
                                                        countries found</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
                                    @if($emergency_contact_phone)
                                        <div class="form-text text-primary" style="font-size: 0.75rem; margin-top: 0.25rem;">
                                            <i class="fas fa-info-circle me-1"></i> Will be saved as:
                                            <strong>{{ $emergency_countryCode }}{{ $emergency_contact_phone }}</strong>
                                        </div>
                                    @endif
                                    @error('emergency_contact_phone')
                                        <div class="invalid-feedback d-block" style="font-size: 0.75rem;">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showEmployeeModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="saveEmployee" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-save me-1"></i>
                                {{ $editingEmployeeId ? 'Update' : 'Create' }}</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- SINGLE ATTENDANCE MODAL --}}
    @if($showAttendanceModal)
        <div class="modal fade show d-block" id="attendanceModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            Mark Attendance
                            @if($attendanceUserName)
                                <span class="text-muted fw-normal fs-14">— {{ $attendanceUserName }}</span>
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showAttendanceModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Date</label>
                            <input type="date" class="form-control" wire:model="attendanceDate" readonly disabled
                                style="background:#f8fafc; cursor:not-allowed;">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('attendanceStatus') is-invalid @enderror"
                                wire:model="attendanceStatus">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="leave">Leave</option>
                                <option value="holiday">Holiday</option>
                            </select>
                            @error('attendanceStatus')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-bold small">Check-in</label>
                                <input type="time" class="form-control @error('attendanceCheckIn') is-invalid @enderror"
                                    wire:model="attendanceCheckIn">
                                @error('attendanceCheckIn')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold small">Check-out</label>
                                <input type="time" class="form-control @error('attendanceCheckOut') is-invalid @enderror"
                                    wire:model="attendanceCheckOut">
                                @error('attendanceCheckOut')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Notes</label>
                            <textarea class="form-control" wire:model="attendanceNotes" rows="2"
                                placeholder="Optional — reason for absence, leave type, etc."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showAttendanceModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="saveAttendance" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-save me-1"></i> Save</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- BULK ATTENDANCE MODAL --}}
    @if($showBulkAttendanceModal)
        <div class="modal fade show d-block" id="bulkAttendanceModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title">Bulk Mark Attendance</h5>
                        <button type="button" class="btn-close"
                            wire:click="$set('showBulkAttendanceModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('bulkAttendanceDate') is-invalid @enderror"
                                    wire:model="bulkAttendanceDate" max="{{ now()->format('Y-m-d') }}">
                                @error('bulkAttendanceDate')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Status to apply <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('bulkAttendanceStatus') is-invalid @enderror"
                                    wire:model="bulkAttendanceStatus">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="leave">Leave</option>
                                    <option value="holiday">Holiday</option>
                                </select>
                                @error('bulkAttendanceStatus')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <label class="form-label fw-bold small mb-0">Employees <span
                                    class="text-danger">*</span></label>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="bulkSelectAll"
                                    wire:model.live="bulkSelectAll">
                                <label class="form-check-label small" for="bulkSelectAll">Select all active
                                    employees</label>
                            </div>
                        </div>
                        @error('bulkSelectedEmployees')
                        <div class="text-danger small mb-2">{{ $message }}</div> @enderror

                        <div class="border rounded-3 p-2" style="max-height: 260px; overflow-y: auto;">
                            @forelse($allActiveEmployees as $emp)
                                <div class="form-check py-1">
                                    <input type="checkbox" class="form-check-input" id="bulk-emp-{{ $emp->id }}"
                                        value="{{ $emp->id }}" wire:model="bulkSelectedEmployees">
                                    <label class="form-check-label d-flex align-items-center gap-2"
                                        for="bulk-emp-{{ $emp->id }}">
                                        <img src="{{ $emp->avatar_url }}" class="avatar avatar-sm rounded-circle"
                                            style="width:24px;height:24px;object-fit:cover;" alt="">
                                        {{ $emp->name }}
                                        <span class="text-muted small">{{ $emp->profile->department ?? '' }}</span>
                                    </label>
                                </div>
                            @empty
                                <p class="text-muted small mb-0 text-center py-2">No active employees to mark.</p>
                            @endforelse
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-bold small">Notes</label>
                            <textarea class="form-control" wire:model="bulkAttendanceNotes" rows="2"
                                placeholder="Optional — applies to every selected employee"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary"
                            wire:click="$set('showBulkAttendanceModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="saveBulkAttendance" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-save me-1"></i> Save for Selected</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- DELETE MODAL --}}
    @if($showDeleteModal)
        <div class="modal fade show d-block" id="deleteModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Delete Employee?</h5>
                        <button type="button" class="btn-close" wire:click="$set('showDeleteModal', false)"></button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-user-slash text-danger" style="font-size: 3rem;"></i>
                        <p class="mt-3">This action is <strong>permanent</strong> and cannot be undone.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteUser" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-trash me-1"></i> Yes, Delete</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Deleting…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- INVITE MODAL --}}
    @if($showInviteModal)
        <div class="modal fade show d-block" id="inviteModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Invite User</h5>
                        <button type="button" class="btn-close" wire:click="$set('showInviteModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Email Address <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('inviteEmail') is-invalid @enderror"
                                wire:model="inviteEmail" placeholder="e.g. friend@email.com">
                            @error('inviteEmail')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Role</label>
                            <select class="default-select style-1 form-control" wire:model="inviteRoleId">
                                <option value="">— Default (User) —</option>
                                @foreach($assignableRoles as $role)
                                    <option value="{{ \Spatie\Permission\Models\Role::where('name', $role)->first()->id }}">
                                        {{ $role }}</option>
                                @endforeach
                            </select>
                            @error('inviteRoleId')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Job Title</label>
                            <input type="text" class="form-control @error('invitePosition') is-invalid @enderror"
                                wire:model="invitePosition" list="invitePositionList" placeholder="e.g. Lead Developer">
                            <datalist id="invitePositionList">
                                <option value="CEO / Founder">
                                <option value="CTO / Head of Technology">
                                <option value="Lead Developer">
                                <option value="Senior Developer">
                                <option value="Junior Developer">
                                <option value="Product Manager">
                                <option value="Marketing Manager">
                                <option value="Sales Director">
                                <option value="UX/UI Designer">
                                <option value="DevOps Engineer">
                                <option value="Data Scientist">
                                <option value="Project Manager">
                                <option value="Content Creator">
                            </datalist>
                            @error('invitePosition')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Optional — pre-fills their job title once they accept.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Expiry (days) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('inviteExpiryDays') is-invalid @enderror"
                                wire:model="inviteExpiryDays" min="1" max="30">
                            @error('inviteExpiryDays')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <small class="text-muted">Link expires after this many days.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showInviteModal', false)">Cancel</button>
                        <button class="btn btn-success" wire:click="sendInvitation" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-paper-plane me-1"></i> Send Invitation</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Sending…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- HOLIDAY MODAL --}}
    @if($showHolidayModal)
        <div class="modal fade show d-block" id="holidayModal" tabindex="-1" style="background: rgba(0,0,0,0.5);"
            wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $editingHolidayId ? 'Edit Holiday' : 'Add Holiday' }}</h5>
                        <button type="button" class="btn-close" wire:click="$set('showHolidayModal', false)"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Holiday Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('holidayName') is-invalid @enderror"
                                wire:model="holidayName" placeholder="e.g. Independence Day">
                            @error('holidayName')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('holidayDate') is-invalid @enderror"
                                wire:model="holidayDate">
                            @error('holidayDate')
                            <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="holidayRecurring"
                                wire:model="holidayRecurring">
                            <label class="form-check-label fw-bold small" for="holidayRecurring">Recurring yearly</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showHolidayModal', false)">Cancel</button>
                        <button class="btn btn-primary" wire:click="saveHoliday" wire:loading.attr="disabled">
                            <span wire:loading.remove><i class="fas fa-save me-1"></i>
                                {{ $editingHolidayId ? 'Update' : 'Add' }}</span>
                            <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> Saving…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

{{-- STYLES --}}
<style>
    [x-cloak] {
        display: none !important;
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .bg-gradient-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .kpi-card {
        transition: transform .15s ease, box-shadow .15s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .project-date .project-media {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
    }

    .event-media {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .event-box {
        text-align: center;
        background: #f8fafc;
        padding: 6px 12px;
        border-radius: 8px;
        min-width: 50px;
    }

    .event-box h5 {
        margin: 0;
        font-size: 18px;
    }

    .event-box span {
        font-size: 11px;
        color: #94a3b8;
    }

    .event-data h5 {
        font-size: 14px;
        margin: 0;
    }

    .event-data span {
        font-size: 12px;
        color: #94a3b8;
    }

    .avatar {
        object-fit: cover;
    }

    .table th p {
        font-size: 10px;
        margin: 0;
        color: #94a3b8;
        text-transform: uppercase;
    }

    .attendance-tbl {
        max-height: 480px;
        overflow: auto;
    }

    .attendance-tbl .table th,
    .attendance-tbl .table td {
        text-align: center;
        vertical-align: middle;
        white-space: nowrap;
    }

    .attendance-tbl .table thead th {
        position: sticky;
        top: 0;
        background: #fff;
        z-index: 3;
    }

    .attendance-tbl .table th:first-child,
    .attendance-tbl .table td:first-child {
        text-align: left;
        position: sticky;
        left: 0;
        background: #fff;
        z-index: 2;
        min-width: 190px;
    }

    .attendance-tbl .table thead th:first-child {
        z-index: 4;
    }

    .weekend-col {
        background: #f8fafc;
    }

    .holiday-col {
        background: #eef2ff;
    }

    .today-col {
        box-shadow: inset 0 0 0 2px #6366f1;
    }

    .att-cell-clickable {
        cursor: pointer;
        transition: background-color .12s ease;
    }

    .att-cell-clickable:hover {
        background-color: #f1f5f9;
    }

    .legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 3px;
        margin-right: 4px;
        vertical-align: middle;
    }

    .legend-weekend {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
    }

    .legend-today {
        background: #6366f1;
    }

    .phone-country-dropdown {
        border-radius: 0.6rem;
        border: 1px solid #e2e8f0;
        z-index: 1050;
        overflow-y: auto;
    }

    .phone-country-dropdown .dropdown-item:hover {
        background-color: #f1f5f9;
    }

    .phone-country-dropdown .dropdown-item.active {
        background-color: #e0e7ff;
        color: #1e293b;
    }

    @media (max-width: 575.98px) {
        .phone-country-btn span {
            font-size: 0.75rem;
        }

        .phone-country-btn img {
            width: 18px;
            height: 12px;
        }

        .phone-number-input {
            font-size: 0.85rem;
            padding: 0.4rem 0.5rem;
        }

        .phone-country-dropdown {
            width: calc(100vw - 2.5rem) !important;
            left: 0 !important;
            right: auto !important;
            max-height: 200px;
        }

        .kpi-card h4 {
            font-size: 1.1rem;
        }

        .kpi-icon {
            width: 38px;
            height: 38px;
            font-size: 15px;
        }
    }

    @media (min-width: 576px) {
        .phone-country-dropdown {
            width: 280px;
            max-width: 280px;
            max-height: 240px;
        }
    }
</style>

{{-- ALPINE & CHART --}}
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('hrHandler', () => ({
            toastVisible: false,
            toastType: 'success',
            toastTitle: '',
            toastMessage: '',
            toastTimeout: null,
            init() {
                window.addEventListener('livewire:navigate', () => {
                    this.toastVisible = false;
                    clearTimeout(this.toastTimeout);
                });
            },
            showToast(detail) {
                this.toastType = detail.type || 'success';
                this.toastTitle = detail.title || (this.toastType === 'success' ? 'Success' : 'Error');
                this.toastMessage = detail.message || '';
                this.toastVisible = true;
                clearTimeout(this.toastTimeout);
                this.toastTimeout = setTimeout(() => { this.dismissToast(); }, 4000);
            },
            dismissToast() {
                this.toastVisible = false;
                clearTimeout(this.toastTimeout);
            }
        }));
    });

    (function () {
        let chartInstance = null;
        const centerTextPlugin = {
            id: 'centerText',
            beforeDraw(chart) {
                const { ctx, chartArea } = chart;
                if (!chartArea) return;
                const { width, height, top, left } = chartArea;
                const total = chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = '700 26px sans-serif';
                ctx.fillStyle = '#1e293b';
                ctx.fillText(total, left + width / 2, top + height / 2 - 8);
                ctx.font = '11px sans-serif';
                ctx.fillStyle = '#94a3b8';
                ctx.fillText('Marked Days', left + width / 2, top + height / 2 + 12);
                ctx.restore();
            }
        };

        function renderChart(present, absent, leave) {
            const canvas = document.getElementById('attendanceChart');
            if (!canvas || typeof Chart === 'undefined') return;
            present = Number(present) || 0;
            absent = Number(absent) || 0;
            leave = Number(leave) || 0;
            const wrapper = canvas.closest('.position-relative');
            if (present === 0 && absent === 0 && leave === 0) {
                if (chartInstance) { chartInstance.destroy(); chartInstance = null; }
                if (wrapper) {
                    wrapper.querySelectorAll('.no-data-msg').forEach(el => el.remove());
                    const msg = document.createElement('div');
                    msg.className = 'no-data-msg d-flex align-items-center justify-content-center h-100 text-muted small';
                    msg.textContent = 'No attendance data for this month';
                    wrapper.appendChild(msg);
                }
                return;
            }
            wrapper?.querySelectorAll('.no-data-msg').forEach(el => el.remove());
            if (chartInstance) {
                chartInstance.data.datasets[0].data = [present, absent, leave];
                chartInstance.update();
                return;
            }
            chartInstance = new Chart(canvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent', 'Leave'],
                    datasets: [{
                        data: [present, absent, leave],
                        backgroundColor: ['#10b981', '#ef4444', '#f59e0b'],
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                    return `${context.label}: ${context.parsed} (${pct}%)`;
                                }
                            }
                        }
                    }
                },
                plugins: [centerTextPlugin]
            });
        }

        function initialRender() {
            chartInstance = null;
            renderChart(
                {{ (int) ($attendanceStats['present'] ?? 0) }},
                {{ (int) ($attendanceStats['absent'] ?? 0) }},
                {{ (int) ($attendanceStats['leave'] ?? 0) }}
            );
        }
        document.addEventListener('DOMContentLoaded', initialRender);
        document.addEventListener('livewire:navigated', initialRender);
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('chart-data-updated', (data) => {
                renderChart(data.present, data.absent, data.leave);
            });
        });
    })();
</script>
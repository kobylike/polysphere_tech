<?php

namespace App\Livewire\Admin\Departments;

use App\Helpers\ActivityLogger;
use App\Models\Department;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

#[Layout('layouts.users')]
class DepartmentComponent extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ─── Filters ─────────────────────────────────────────────
    public string $search = '';
    public string $statusFilter = '';
    public string $parentFilter = '';
    public string $specialFilter = '';
    public string $sortBy = 'display_order';
    public string $sortDir = 'asc';
    public int $perPage = 15;

    // ─── Modals ──────────────────────────────────────────────
    public bool $showFormModal = false;
    public bool $showViewModal = false;
    public bool $showDeleteModal = false;
    public bool $showBulkDeleteModal = false;

    public ?Department $viewingDepartment = null;
    public ?int $selectedDepartmentId = null;
    public ?int $deleteDepartmentId = null;

    // ─── Form fields ─────────────────────────────────────────
    public string $name = '';
    public string $code = '';
    public string $description = '';
    public string $color = '#2f6fed';
    public string $icon = 'fa-solid fa-building';
    public ?int $parent_id = null;
    public ?int $head_id = null;
    public int $display_order = 0;
    public string $email = '';
    public string $phone = '';
    public string $location = '';
    public ?string $budget = null;
    public ?int $headcount_target = null;
    public string $founded_at = '';
    public bool $is_customer_facing = false;
    public bool $is_active = true;
    public string $notes = '';

    // ─── Phone country picker ────────────────────────────────
    public string $phone_code = '+233';
    public string $phone_flag = 'gh.png';
    public array $phone_countries = [];
    public array $phone_filteredCountries = [];
    public array $phone_countryInfo = [];
    public string $phone_example = '';
    public string $phone_search = '';
    public bool $phone_showDropdown = false;

    // ─── Bulk selection ──────────────────────────────────────
    public array $selectedDepartments = [];
    public bool $selectAll = false;

    // ─── Presets ─────────────────────────────────────────────
    public array $colorPresets = [
        '#2f6fed',
        '#6366f1',
        '#8b5cf6',
        '#a855f7',
        '#ec4899',
        '#ef4444',
        '#f97316',
        '#f59e0b',
        '#eab308',
        '#84cc16',
        '#10b981',
        '#14b8a6',
        '#06b6d4',
        '#0891b2',
        '#64748b',
    ];

    public array $iconPresets = [
        'fa-solid fa-building',
        'fa-solid fa-code',
        'fa-solid fa-palette',
        'fa-solid fa-chart-line',
        'fa-solid fa-bullhorn',
        'fa-solid fa-handshake',
        'fa-solid fa-headset',
        'fa-solid fa-shield-halved',
        'fa-solid fa-database',
        'fa-solid fa-users',
        'fa-solid fa-lightbulb',
        'fa-solid fa-cog',
        'fa-solid fa-flask',
        'fa-solid fa-rocket',
        'fa-solid fa-truck-fast',
        'fa-solid fa-coins',
        'fa-solid fa-scale-balanced',
        'fa-solid fa-compass',
    ];

    // ─── Mount ───────────────────────────────────────────────
    public function mount(): void
    {
        $this->authorize('viewAny', Department::class);
        $this->loadPhoneCountries();
        $this->updatePhoneCountryInfo();
    }

    // ─── Filters / sorting ───────────────────────────────────
    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }
    public function updatingParentFilter(): void
    {
        $this->resetPage();
    }
    public function updatingSpecialFilter(): void
    {
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'asc';
        }
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'statusFilter', 'parentFilter', 'specialFilter', 'selectedDepartments', 'selectAll']);
        $this->sortBy = 'display_order';
        $this->sortDir = 'asc';
        $this->resetPage();
    }

    // ─── Bulk selection ──────────────────────────────────────
    public function updatedSelectAll(bool $value): void
    {
        $this->selectedDepartments = $value
            ? $this->getQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function updatedSelectedDepartments(): void
    {
        $total = $this->getQuery()->count();
        $this->selectAll = $total > 0 && count($this->selectedDepartments) === $total;
    }

    // ─── View ────────────────────────────────────────────────
    public function viewDepartment(int $id): void
    {
        $dept = Department::with(['parent', 'head', 'children'])
            ->withCount(['employees', 'vacancies'])
            ->findOrFail($id);
        $this->authorize('view', $dept);

        $this->viewingDepartment = $dept;
        $this->showViewModal = true;
    }

    // ─── Create / Edit ───────────────────────────────────────
    public function openCreate(): void
    {
        $this->authorize('create', Department::class);

        $this->resetFormFields();
        $this->selectedDepartmentId = null;
        $this->showFormModal = true;
    }

    public function openEdit(int $id): void
    {
        $dept = Department::findOrFail($id);
        $this->authorize('update', $dept);

        $this->selectedDepartmentId = $dept->id;
        $this->name                 = $dept->name;
        $this->code                 = (string) $dept->code;
        $this->description          = (string) $dept->description;
        $this->color                = $dept->color ?: '#2f6fed';
        $this->icon                 = $dept->icon ?: 'fa-solid fa-building';
        $this->parent_id            = $dept->parent_id;
        $this->head_id              = $dept->head_id;
        $this->display_order        = $dept->display_order;
        $this->email                = (string) $dept->email;
        $this->parsePhoneNumber($dept->phone);
        $this->location             = (string) $dept->location;
        $this->budget               = $dept->budget !== null ? (string) $dept->budget : null;
        $this->headcount_target     = $dept->headcount_target;
        $this->founded_at           = $dept->founded_at?->format('Y-m-d') ?? '';
        $this->is_customer_facing   = $dept->is_customer_facing;
        $this->is_active            = (bool) $dept->is_active;
        $this->notes                = (string) $dept->notes;

        $this->showFormModal = true;
    }

    private function resetFormFields(): void
    {
        $this->reset([
            'name',
            'code',
            'description',
            'color',
            'icon',
            'parent_id',
            'head_id',
            'display_order',
            'email',
            'phone',
            'location',
            'budget',
            'headcount_target',
            'founded_at',
            'is_customer_facing',
            'notes',
            'selectedDepartmentId',
            'phone_search',
            'phone_showDropdown',
        ]);
        $this->color        = '#2f6fed';
        $this->icon         = 'fa-solid fa-building';
        $this->phone_code   = '+233';
        $this->phone_flag   = 'gh.png';
        $this->is_active    = true;
        $this->display_order = 0;
        $this->updatePhoneCountryInfo();
    }

    protected function rules(): array
    {
        return [
            'name'               => 'required|string|min:2|max:120',
            'code'               => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('departments', 'code')
                    ->ignore($this->selectedDepartmentId),
            ],
            'description'        => 'nullable|string|max:2000',
            'color'              => 'nullable|string|max:20',
            'icon'               => 'nullable|string|max:60',
            'parent_id'          => 'nullable|exists:departments,id',
            'head_id'            => 'nullable|exists:users,id',
            'display_order'      => 'required|integer|min:0|max:9999',
            'email'              => 'nullable|email:rfc|max:120',
            'phone'              => [
                'nullable',
                'string',
                'regex:/' . ($this->phone_countryInfo['pattern'] ?? '^[0-9]{9}$') . '/',
            ],
            'location'           => 'nullable|string|max:120',
            'budget'             => 'nullable|numeric|min:0',
            'headcount_target'   => 'nullable|integer|min:0|max:10000',
            'founded_at'         => 'nullable|date|before_or_equal:today',
            'is_customer_facing' => 'boolean',
            'is_active'          => 'boolean',
            'notes'              => 'nullable|string|max:5000',
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Give the department a name.',
            'name.min'      => 'Name should be at least 2 characters.',
            'code.unique'   => 'That code is already used by another department.',
            'email.email'   => 'Please enter a valid email address.',
            'phone.regex'   => 'That doesn\'t look like a valid number for the selected country.',
            'founded_at.before_or_equal' => 'Founding date can\'t be in the future.',
        ];
    }

    public function saveDepartment(): void
    {
        if ($this->selectedDepartmentId) {
            $dept = Department::findOrFail($this->selectedDepartmentId);
            $this->authorize('update', $dept);
        } else {
            $this->authorize('create', Department::class);
        }

        $this->validate();

        // ── Circular-reference guard ───────────────────────────
        if ($this->parent_id && $this->selectedDepartmentId) {
            if ($this->parent_id === $this->selectedDepartmentId) {
                $this->addError('parent_id', 'A department cannot be its own parent.');
                return;
            }

            $cursor = Department::find($this->parent_id);
            $depth = 0;
            while ($cursor && $depth < 20) {
                if ($cursor->id === $this->selectedDepartmentId) {
                    $this->addError('parent_id', 'That would create a circular hierarchy.');
                    return;
                }
                $cursor = $cursor->parent;
                $depth++;
            }
        }

        // ── Auto-generate code if blank ────────────────────────
        $code = trim($this->code);
        if ($code === '') {
            $code = $this->generateCode($this->name);
        }

        $data = [
            'name'               => trim($this->name),
            'code'               => strtoupper($code),
            'description'        => $this->description ?: null,
            'color'              => $this->color ?: null,
            'icon'               => $this->icon ?: null,
            'parent_id'          => $this->parent_id ?: null,
            'head_id'            => $this->head_id ?: null,
            'display_order'      => $this->display_order,
            'email'              => $this->email ?: null,
            'phone'              => $this->fullPhone() ?: null,
            'location'           => $this->location ?: null,
            'budget'             => $this->budget !== null && $this->budget !== '' ? $this->budget : null,
            'headcount_target'   => $this->headcount_target ?: null,
            'founded_at'         => $this->founded_at ?: null,
            'is_customer_facing' => $this->is_customer_facing,
            'is_active'          => $this->is_active,
            'notes'              => $this->notes ?: null,
        ];

        try {
            if ($this->selectedDepartmentId) {
                $dept = Department::findOrFail($this->selectedDepartmentId);
                $dept->update($data);

                ActivityLogger::log('Department updated', [
                    'department_id' => $dept->id,
                    'name'          => $dept->name,
                    'updated_by'    => Auth::id(),
                ], 'department');

                $this->dispatch(
                    'notify',
                    type: 'success',
                    title: 'Updated',
                    message: "\"{$dept->name}\" has been updated.",
                );
            } else {
                $dept = Department::create($data);

                ActivityLogger::log('Department created', [
                    'department_id' => $dept->id,
                    'name'          => $dept->name,
                    'created_by'    => Auth::id(),
                ], 'department');

                $this->dispatch(
                    'notify',
                    type: 'success',
                    title: 'Created',
                    message: "\"{$dept->name}\" has been added.",
                );
            }

            $this->showFormModal = false;
            $this->resetFormFields();
        } catch (\Throwable $e) {
            report($e);
            $this->dispatch(
                'notify',
                type: 'error',
                title: 'Save failed',
                message: $e->getMessage(),
            );
        }
    }

    private function generateCode(string $name): string
    {
        $clean = strtoupper(preg_replace('/[^A-Za-z]/', '', $name));
        $code  = substr($clean, 0, 4) ?: 'DEPT';
        $base  = $code;
        $i     = 1;

        while (Department::where('code', $code)
            ->when($this->selectedDepartmentId, fn($q) => $q->where('id', '!=', $this->selectedDepartmentId))
            ->exists()
        ) {
            $code = $base . $i;
            $i++;
            if ($i > 99) {
                $code = 'DEPT' . random_int(100, 999);
                break;
            }
        }

        return $code;
    }

    // ─── Toggle active (inline) ──────────────────────────────
    public function toggleActive(int $id): void
    {
        $dept = Department::findOrFail($id);
        $this->authorize('update', $dept);

        $dept->is_active = ! $dept->is_active;
        $dept->save();

        ActivityLogger::log('Department status toggled', [
            'department_id' => $dept->id,
            'name'          => $dept->name,
            'is_active'     => $dept->is_active,
            'toggled_by'    => Auth::id(),
        ], 'department');

        $this->dispatch(
            'notify',
            type: 'success',
            title: $dept->is_active ? 'Activated' : 'Archived',
            message: "\"{$dept->name}\" is now " . ($dept->is_active ? 'active.' : 'archived.'),
        );
    }

    // ─── Single delete ───────────────────────────────────────
    public function confirmDelete(int $id): void
    {
        $dept = Department::withCount(['employees', 'vacancies'])->findOrFail($id);
        $this->authorize('delete', $dept);

        $this->deleteDepartmentId = $id;

        $inUse = ($dept->employees_count + $dept->vacancies_count) > 0;

        if ($inUse) {
            $this->dispatch(
                'notify',
                type: 'warning',
                title: 'Cannot delete',
                message: "\"{$dept->name}\" has {$dept->employees_count} employees and {$dept->vacancies_count} vacancies. Archive it instead.",
            );
            return;
        }

        $this->showDeleteModal = true;
    }

    public function deleteDepartment(): void
    {
        if (! $this->deleteDepartmentId) return;

        $dept = Department::findOrFail($this->deleteDepartmentId);
        $this->authorize('delete', $dept);

        $inUse = $dept->employees()->exists() || $dept->vacancies()->exists();
        if ($inUse) {
            $this->dispatch(
                'notify',
                type: 'error',
                title: 'Cannot delete',
                message: "\"{$dept->name}\" is still in use. Archive it instead.",
            );
            $this->showDeleteModal = false;
            $this->deleteDepartmentId = null;
            return;
        }

        $name = $dept->name;
        $dept->delete();

        ActivityLogger::log('Department deleted', [
            'department_id' => $dept->id,
            'name'          => $name,
            'deleted_by'    => Auth::id(),
        ], 'department');

        $this->showDeleteModal = false;
        $this->deleteDepartmentId = null;

        $this->dispatch(
            'notify',
            type: 'success',
            title: 'Deleted',
            message: "\"{$name}\" has been removed.",
        );
    }

    // ─── Bulk actions ────────────────────────────────────────
    public function bulkActivate(): void
    {
        if (empty($this->selectedDepartments)) return;
        $this->authorize('update', Department::class);

        $ids = $this->selectedDepartments;
        Department::whereIn('id', $ids)->update(['is_active' => true]);

        ActivityLogger::log('Departments bulk activated', [
            'department_ids' => $ids,
            'activated_by'   => Auth::id(),
        ], 'department');

        $this->selectedDepartments = [];
        $this->selectAll = false;

        $this->dispatch(
            'notify',
            type: 'success',
            title: 'Activated',
            message: count($ids) . ' department(s) activated.',
        );
    }

    public function bulkDeactivate(): void
    {
        if (empty($this->selectedDepartments)) return;
        $this->authorize('update', Department::class);

        $ids = $this->selectedDepartments;
        Department::whereIn('id', $ids)->update(['is_active' => false]);

        ActivityLogger::log('Departments bulk archived', [
            'department_ids' => $ids,
            'archived_by'    => Auth::id(),
        ], 'department');

        $this->selectedDepartments = [];
        $this->selectAll = false;

        $this->dispatch(
            'notify',
            type: 'success',
            title: 'Archived',
            message: count($ids) . ' department(s) archived.',
        );
    }

    public function confirmBulkDelete(): void
    {
        if (empty($this->selectedDepartments)) return;
        $this->authorize('delete', Department::class);

        $inUse = Department::whereIn('id', $this->selectedDepartments)
            ->where(function ($q) {
                $q->whereHas('employees')->orWhereHas('vacancies');
            })
            ->count();

        if ($inUse > 0) {
            $this->dispatch(
                'notify',
                type: 'warning',
                title: 'Some are in use',
                message: "{$inUse} of the selected departments have employees or vacancies. Archive those instead — nothing was deleted.",
            );
            return;
        }

        $this->showBulkDeleteModal = true;
    }

    public function bulkDelete(): void
    {
        $this->authorize('delete', Department::class);

        $blocked = Department::whereIn('id', $this->selectedDepartments)
            ->where(function ($q) {
                $q->whereHas('employees')->orWhereHas('vacancies');
            })
            ->count();

        if ($blocked > 0) {
            $this->dispatch(
                'notify',
                type: 'error',
                title: 'Some are in use',
                message: "{$blocked} department(s) couldn't be deleted because they still have employees or vacancies.",
            );
            $this->showBulkDeleteModal = false;
            return;
        }

        $ids = $this->selectedDepartments;
        Department::whereIn('id', $ids)->delete();

        ActivityLogger::log('Departments bulk deleted', [
            'department_ids' => $ids,
            'deleted_by'     => Auth::id(),
        ], 'department');

        $this->selectedDepartments = [];
        $this->selectAll = false;
        $this->showBulkDeleteModal = false;

        $this->dispatch(
            'notify',
            type: 'success',
            title: 'Deleted',
            message: count($ids) . ' department(s) deleted.',
        );
    }

    // ─── Export CSV ──────────────────────────────────────────
    public function export(): StreamedResponse
    {
        $this->authorize('viewAny', Department::class);

        $departments = $this->getQuery()->with(['parent', 'head'])->withCount(['employees', 'vacancies'])->get();
        $filename    = 'departments-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($departments) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Name',
                'Code',
                'Parent',
                'Head',
                'Email',
                'Phone',
                'Location',
                'Employees',
                'Open vacancies',
                'Headcount target',
                'Budget',
                'Customer-facing',
                'Founded',
                'Status',
            ]);

            foreach ($departments as $d) {
                fputcsv($out, [
                    $d->name,
                    $d->code,
                    $d->parent?->name,
                    $d->head?->name,
                    $d->email,
                    $d->phone,
                    $d->location,
                    $d->employees_count ?? 0,
                    $d->vacancies_count ?? 0,
                    $d->headcount_target,
                    $d->budget,
                    $d->is_customer_facing ? 'Yes' : 'No',
                    $d->founded_at?->toDateString(),
                    $d->is_active ? 'Active' : 'Archived',
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    // ─── Phone country logic ────────────────────────────────
    public function loadPhoneCountries(): void
    {
        $path = public_path('countries-full.json');
        if (! file_exists($path)) {
            $path = public_path('countries.json');
        }

        if (file_exists($path)) {
            $json = file_get_contents($path);
            $countries = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($countries)) {
                usort($countries, fn($a, $b) => strcmp($a['name'], $b['name']));
                $this->phone_countries = $countries;
                $this->phone_filteredCountries = $countries;
                return;
            }
        }

        $this->phone_countries = $this->phone_filteredCountries = [
            ['code' => '+233', 'name' => 'Ghana',          'flag' => 'gh.png', 'pattern' => '^[0-9]{9}$',    'minLength' => 9,  'maxLength' => 9,  'example' => '201234567'],
            ['code' => '+1',   'name' => 'United States',  'flag' => 'us.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '2025550123'],
            ['code' => '+44',  'name' => 'United Kingdom', 'flag' => 'gb.png', 'pattern' => '^[0-9]{10,11}$', 'minLength' => 10, 'maxLength' => 11, 'example' => '7912345678'],
            ['code' => '+91',  'name' => 'India',          'flag' => 'in.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '9876543210'],
            ['code' => '+234', 'name' => 'Nigeria',        'flag' => 'ng.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '8012345678'],
        ];
    }

    public function updatePhoneCountryInfo(): void
    {
        $country = collect($this->phone_countries)->firstWhere('code', $this->phone_code);
        if ($country) {
            $this->phone_countryInfo = $country;
            $this->phone_example = $country['example'] ?? '';
        } else {
            $this->phone_countryInfo = ['name' => 'Ghana', 'pattern' => '^[0-9]{9}$', 'minLength' => 9, 'maxLength' => 9, 'example' => '201234567'];
            $this->phone_example = '201234567';
        }
    }

    public function selectPhoneCountry(string $code, string $flag): void
    {
        $this->phone_code = $code;
        $this->phone_flag = $flag;
        $this->updatePhoneCountryInfo();
        $this->phone = '';
        $this->phone_showDropdown = false;
        $this->phone_search = '';
        $this->phone_filteredCountries = $this->phone_countries;
    }

    public function togglePhoneCountryDropdown(): void
    {
        $this->phone_showDropdown = ! $this->phone_showDropdown;
        if ($this->phone_showDropdown) {
            $this->phone_search = '';
            $this->phone_filteredCountries = $this->phone_countries;
        }
    }

    public function closePhoneCountryDropdown(): void
    {
        $this->phone_showDropdown = false;
        $this->phone_search = '';
        $this->phone_filteredCountries = $this->phone_countries;
    }

    public function searchPhoneCountries(string $term): void
    {
        $this->phone_search = $term;
        $this->phone_filteredCountries = collect($this->phone_countries)
            ->filter(
                fn($c) =>
                stripos($c['name'], $term) !== false ||
                    stripos($c['code'], $term) !== false
            )
            ->values()
            ->toArray();
    }

    public function setPhone(string $value): void
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        $max = $this->phone_countryInfo['maxLength'] ?? 15;
        if (strlen($clean) > $max) {
            $clean = substr($clean, 0, $max);
        }
        $this->phone = $clean;
    }

    public function fullPhone(): string
    {
        $clean = ltrim($this->phone, '0');
        return $clean === '' ? '' : $this->phone_code . $clean;
    }

    private function parsePhoneNumber(?string $phone): void
    {
        if (empty($phone)) {
            $this->phone = '';
            $this->phone_code = '+233';
            $this->phone_flag = 'gh.png';
            $this->updatePhoneCountryInfo();
            return;
        }

        $matchedCountry = null;
        $matchedCode = '';
        foreach ($this->phone_countries as $country) {
            $code = $country['code'];
            if (str_starts_with($phone, $code) && strlen($code) > strlen($matchedCode)) {
                $matchedCode = $code;
                $matchedCountry = $country;
            }
        }

        if ($matchedCountry) {
            $this->phone_code = $matchedCode;
            $this->phone_flag = $matchedCountry['flag'];
            $this->phone = substr($phone, strlen($matchedCode));
        } else {
            $this->phone_code = '+233';
            $this->phone_flag = 'gh.png';
            $this->phone = $phone;
        }
        $this->updatePhoneCountryInfo();
    }

    // ─── Query ───────────────────────────────────────────────
    protected function getQuery()
    {
        return Department::query()
            ->with(['parent', 'head'])
            ->withCount(['employees', 'vacancies'])
            ->when($this->search, function ($q) {
                $term = '%' . $this->search . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('name', 'like', $term)
                        ->orWhere('code', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('location', 'like', $term);
                });
            })
            ->when($this->statusFilter === 'active',   fn($q) => $q->where('is_active', true))
            ->when($this->statusFilter === 'archived', fn($q) => $q->where('is_active', false))
            ->when($this->parentFilter, fn($q) => $q->where('parent_id', $this->parentFilter))
            ->when($this->specialFilter === 'no_head', fn($q) => $q->whereNull('head_id'))
            ->when($this->specialFilter === 'has_head', fn($q) => $q->whereNotNull('head_id'))
            ->when($this->specialFilter === 'customer_facing', fn($q) => $q->where('is_customer_facing', true))
            ->when($this->specialFilter === 'under_staffed', function ($q) {
                $q->whereNotNull('headcount_target')
                    ->whereRaw('(SELECT COUNT(*) FROM user_profiles WHERE user_profiles.department_id = departments.id AND user_profiles.is_employee = 1) < departments.headcount_target');
            })
            ->orderBy($this->sortBy, $this->sortDir);
    }

    // ─── Render ──────────────────────────────────────────────
    public function render()
    {
        $departments = $this->getQuery()->paginate($this->perPage);

        $excludedIds = [];
        if ($this->selectedDepartmentId) {
            $excludedIds[] = $this->selectedDepartmentId;
            $excludedIds = array_merge($excludedIds, Department::find($this->selectedDepartmentId)?->descendantIds() ?? []);
        }

        $parentOptions = Department::orderBy('name')
            ->when(!empty($excludedIds), fn($q) => $q->whereNotIn('id', $excludedIds))
            ->get(['id', 'name']);

        $headOptions = User::orderBy('name')
            ->whereHas('roles', fn($q) => $q->whereIn('name', ['Super Admin', 'Admin', 'Agent', 'User']))
            ->get(['id', 'name', 'email']);

        return view('livewire.admin.departments.department-component', [
            'departments'   => $departments,
            'parentOptions' => $parentOptions,
            'headOptions'   => $headOptions,
            'stats'         => [
                'total'          => Department::count(),
                'active'         => Department::where('is_active', true)->count(),
                'with_head'      => Department::whereNotNull('head_id')->count(),
                'total_staff'    => UserProfile::where('is_employee', true)->count(),
                'open_vacancies' => Vacancy::where('status', 'published')->count(),
            ],
        ]);
    }
}

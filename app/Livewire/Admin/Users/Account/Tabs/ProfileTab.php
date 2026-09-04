<?php

namespace App\Livewire\Admin\Users\Account\Tabs;

use App\Helpers\ActivityLogger; // <-- Added
use App\Helpers\ProfileHelper;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProfileTab extends Component
{
    use WithFileUploads;

    public $user;
    public $name;
    public $email;
    public $phone_local;
    public $countryCode = '+233';
    public $selectedFlag = 'gh.png';
    public $phone_full;

    // Country dropdown (main phone)
    public $countries = [];
    public $filteredCountries = [];
    public $countryInfo = [];
    public $phoneExample = '';
    public $search = '';
    public $showCountryDropdown = false;

    // Employee fields
    public $gender;
    public $date_of_birth;
    public $country_code;
    public $city;
    public $emergency_contact_name;
    public $emergency_contact_phone_local;
    public $emergency_countryCode = '+233';
    public $emergency_selectedFlag = 'gh.png';
    public $emergency_phoneExample = '';
    public $emergency_showCountryDropdown = false;
    public $emergency_search = '';
    public $emergency_filteredCountries = [];
    public $emergency_countryInfo = [];

    // Avatar
    public $avatar;
    public $confirmingAvatarDelete = false;

    // Profile fields
    public $about_me;
    public $skills = [];
    public $education = [];
    public $social_links = [
        'linkedin' => '',
        'github'   => '',
        'twitter'  => '',
        'youtube'  => '',
    ];

    protected function rules()
    {
        $min = $this->countryInfo['minLength'] ?? 5;
        $max = $this->countryInfo['maxLength'] ?? 15;
        $pattern = $this->countryInfo['pattern'] ?? '^[0-9]{' . $min . ',' . $max . '}$';

        $emergencyMin = $this->emergency_countryInfo['minLength'] ?? 5;
        $emergencyMax = $this->emergency_countryInfo['maxLength'] ?? 15;
        $emergencyPattern = $this->emergency_countryInfo['pattern'] ?? '^[0-9]{' . $emergencyMin . ',' . $emergencyMax . '}$';

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user->id)],
            'phone_local' => [
                'nullable',
                'string',
                'regex:/' . $pattern . '/',
                function ($attribute, $value, $fail) {
                    if (empty($value)) return;
                    $full = $this->getFullPhone();
                    if (\App\Models\User::where('phone', $full)->where('id', '!=', $this->user->id)->exists()) {
                        $fail('This phone number is already taken.');
                    }
                },
            ],
            'avatar' => 'nullable|image|max:2048',
            'about_me' => 'nullable|string|max:1000',
            'skills' => 'nullable|array',
            'skills.*.name' => 'required|string|max:100',
            'skills.*.level' => 'required|integer|min:1|max:100',
            'education' => 'nullable|array',
            'education.*.institution' => 'required|string|max:255',
            'education.*.degree' => 'required|string|max:255',
            'education.*.start_year' => 'nullable|string|max:10',
            'education.*.end_year' => 'nullable|string|max:10|after_or_equal:education.*.start_year',
            'education.*.currently_studying' => 'boolean',
            'social_links.*' => 'nullable|url|max:255',
            'gender' => 'nullable|in:male,female,other',
            'date_of_birth' => 'nullable|date|before:today',
            'country_code' => 'nullable|string|max:10',
            'city' => 'nullable|string|max:100',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone_local' => [
                'nullable',
                'string',
                'regex:/' . $emergencyPattern . '/',
            ],
        ];
    }

    public function messages(): array
    {
        $country = $this->countryInfo['name'] ?? 'your country';
        $min = $this->countryInfo['minLength'] ?? 5;
        $max = $this->countryInfo['maxLength'] ?? 15;
        $example = !empty($this->phoneExample) ? " Example: {$this->phoneExample}" : '';

        $emergencyCountry = $this->emergency_countryInfo['name'] ?? 'your country';
        $emergencyMin = $this->emergency_countryInfo['minLength'] ?? 5;
        $emergencyMax = $this->emergency_countryInfo['maxLength'] ?? 15;
        $emergencyExample = !empty($this->emergency_phoneExample) ? " Example: {$this->emergency_phoneExample}" : '';

        return [
            'phone_local.regex' => "Enter a valid {$country} number ({$min}–{$max} digits).{$example}",
            'phone_local.unique' => 'This phone number is already registered.',
            'emergency_contact_phone_local.regex' => "Enter a valid {$emergencyCountry} number ({$emergencyMin}–{$emergencyMax} digits).{$emergencyExample}",
            'education.*.end_year.after_or_equal' => 'End year must be after or equal to start year.',
        ];
    }

    public function mount()
    {
        $this->user = Auth::user();
        $this->name = $this->user->name;
        $this->email = $this->user->email;

        $this->loadCountries();
        $this->parsePhoneNumber($this->user->phone);
        $this->emergency_loadCountries();

        $profile = $this->user->profile;
        if ($profile) {
            $this->about_me = $profile->about_me;
            $this->skills = $profile->skills ?? [];
            $this->education = $profile->education ?? [];
            foreach ($this->education as &$edu) {
                if (!isset($edu['currently_studying'])) {
                    $edu['currently_studying'] = false;
                }
                if ($edu['currently_studying']) {
                    $edu['end_year'] = null;
                }
            }
            if ($profile->social_links) {
                foreach ($profile->social_links as $key => $value) {
                    if (array_key_exists($key, $this->social_links)) {
                        $this->social_links[$key] = $value;
                    }
                }
            }
            $this->gender = $profile->gender;
            $this->date_of_birth = $profile->date_of_birth?->format('Y-m-d');
            $this->country_code = $profile->country_code;
            $this->city = $profile->city;
            $this->emergency_contact_name = $profile->emergency_contact_name;
            $this->emergency_parsePhoneNumber($profile->emergency_contact_phone);
        }
    }

    // ─── Country / Phone logic (main) ────────────────────────────────

    public function loadCountries()
    {
        $path = public_path('countries-full.json');
        if (!file_exists($path)) {
            $path = public_path('countries.json');
        }

        if (file_exists($path)) {
            $json = file_get_contents($path);
            $countries = json_decode($json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($countries)) {
                usort($countries, fn($a, $b) => strcmp($a['name'], $b['name']));
                $this->countries = $countries;
                $this->filteredCountries = $countries;
                return;
            }
        }

        $this->countries = $this->filteredCountries = [
            ['code' => '+233', 'name' => 'Ghana',          'flag' => 'gh.png', 'pattern' => '^[0-9]{9}$',    'minLength' => 9,  'maxLength' => 9,  'example' => '201234567'],
            ['code' => '+1',   'name' => 'United States',  'flag' => 'us.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '2025550123'],
            ['code' => '+44',  'name' => 'United Kingdom', 'flag' => 'gb.png', 'pattern' => '^[0-9]{10,11}$', 'minLength' => 10, 'maxLength' => 11, 'example' => '7912345678'],
            ['code' => '+91',  'name' => 'India',          'flag' => 'in.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '9876543210'],
            ['code' => '+234', 'name' => 'Nigeria',        'flag' => 'ng.png', 'pattern' => '^[0-9]{10}$',   'minLength' => 10, 'maxLength' => 10, 'example' => '8012345678'],
        ];
    }

    public function updateCountryInfo()
    {
        $country = collect($this->countries)->firstWhere('code', $this->countryCode);
        if ($country) {
            $this->countryInfo = $country;
            $this->phoneExample = $country['example'] ?? '';
        } else {
            $this->countryInfo = [
                'name'      => 'Ghana',
                'pattern'   => '^[0-9]{9}$',
                'minLength' => 9,
                'maxLength' => 9,
                'example'   => '201234567',
            ];
            $this->phoneExample = '201234567';
        }
    }

    public function selectCountry($code, $flag)
    {
        $this->countryCode = $code;
        $this->selectedFlag = $flag;
        $this->updateCountryInfo();
        $this->phone_local = '';
        $this->showCountryDropdown = false;
        $this->search = '';
        $this->filteredCountries = $this->countries;
    }

    public function toggleCountryDropdown()
    {
        $this->showCountryDropdown = !$this->showCountryDropdown;
        if ($this->showCountryDropdown) {
            $this->search = '';
            $this->filteredCountries = $this->countries;
        }
    }

    public function closeCountryDropdown()
    {
        $this->showCountryDropdown = false;
        $this->search = '';
        $this->filteredCountries = $this->countries;
    }

    public function searchCountries($searchTerm)
    {
        $this->search = $searchTerm;
        $this->filteredCountries = collect($this->countries)
            ->filter(
                fn($c) =>
                stripos($c['name'], $this->search) !== false ||
                    stripos($c['code'], $this->search) !== false
            )
            ->values()
            ->toArray();
    }

    public function setPhone(string $value): void
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        $max   = $this->countryInfo['maxLength'] ?? 15;

        if (strlen($clean) > $max) {
            $clean = substr($clean, 0, $max);
        }

        $this->phone_local = $clean;
    }

    public function getFullPhone(): string
    {
        $clean = ltrim($this->phone_local, '0');
        return $this->countryCode . $clean;
    }

    private function parsePhoneNumber(?string $phone): void
    {
        if (empty($phone)) {
            $this->phone_local = '';
            $this->countryCode = '+233';
            $this->selectedFlag = 'gh.png';
            $this->updateCountryInfo();
            return;
        }

        $matchedCountry = null;
        $matchedCode = '';
        foreach ($this->countries as $country) {
            $code = $country['code'];
            if (str_starts_with($phone, $code)) {
                if (strlen($code) > strlen($matchedCode)) {
                    $matchedCode = $code;
                    $matchedCountry = $country;
                }
            }
        }

        if ($matchedCountry) {
            $this->countryCode = $matchedCode;
            $this->selectedFlag = $matchedCountry['flag'];
            $this->phone_local = substr($phone, strlen($matchedCode));
        } else {
            $this->countryCode = '+233';
            $this->selectedFlag = 'gh.png';
            $this->phone_local = $phone;
        }
        $this->updateCountryInfo();
    }

    // ─── Emergency Phone logic ──────────────────────────────────────

    public function emergency_loadCountries()
    {
        $this->emergency_filteredCountries = $this->countries;
        $this->emergency_updateCountryInfo();
    }

    public function emergency_updateCountryInfo()
    {
        $country = collect($this->countries)->firstWhere('code', $this->emergency_countryCode);
        if ($country) {
            $this->emergency_countryInfo = $country;
            $this->emergency_phoneExample = $country['example'] ?? '';
        } else {
            $this->emergency_countryInfo = [
                'name'      => 'Ghana',
                'pattern'   => '^[0-9]{9}$',
                'minLength' => 9,
                'maxLength' => 9,
                'example'   => '201234567',
            ];
            $this->emergency_phoneExample = '201234567';
        }
    }

    public function emergency_selectCountry($code, $flag)
    {
        $this->emergency_countryCode = $code;
        $this->emergency_selectedFlag = $flag;
        $this->emergency_updateCountryInfo();
        $this->emergency_contact_phone_local = '';
        $this->emergency_showCountryDropdown = false;
        $this->emergency_search = '';
        $this->emergency_filteredCountries = $this->countries;
    }

    public function emergency_toggleCountryDropdown()
    {
        $this->emergency_showCountryDropdown = !$this->emergency_showCountryDropdown;
        if ($this->emergency_showCountryDropdown) {
            $this->emergency_search = '';
            $this->emergency_filteredCountries = $this->countries;
        }
    }

    public function emergency_closeCountryDropdown()
    {
        $this->emergency_showCountryDropdown = false;
        $this->emergency_search = '';
        $this->emergency_filteredCountries = $this->countries;
    }

    public function emergency_searchCountries($searchTerm)
    {
        $this->emergency_search = $searchTerm;
        $this->emergency_filteredCountries = collect($this->countries)
            ->filter(
                fn($c) =>
                stripos($c['name'], $this->emergency_search) !== false ||
                    stripos($c['code'], $this->emergency_search) !== false
            )
            ->values()
            ->toArray();
    }

    public function emergency_setPhone(string $value): void
    {
        $clean = preg_replace('/[^0-9]/', '', $value);
        $max   = $this->emergency_countryInfo['maxLength'] ?? 15;

        if (strlen($clean) > $max) {
            $clean = substr($clean, 0, $max);
        }

        $this->emergency_contact_phone_local = $clean;
    }

    public function emergency_getFullPhone(): string
    {
        $clean = ltrim($this->emergency_contact_phone_local, '0');
        return $this->emergency_countryCode . $clean;
    }

    private function emergency_parsePhoneNumber(?string $phone): void
    {
        if (empty($phone)) {
            $this->emergency_contact_phone_local = '';
            $this->emergency_countryCode = '+233';
            $this->emergency_selectedFlag = 'gh.png';
            $this->emergency_updateCountryInfo();
            return;
        }

        $matchedCountry = null;
        $matchedCode = '';
        foreach ($this->countries as $country) {
            $code = $country['code'];
            if (str_starts_with($phone, $code)) {
                if (strlen($code) > strlen($matchedCode)) {
                    $matchedCode = $code;
                    $matchedCountry = $country;
                }
            }
        }

        if ($matchedCountry) {
            $this->emergency_countryCode = $matchedCode;
            $this->emergency_selectedFlag = $matchedCountry['flag'];
            $this->emergency_contact_phone_local = substr($phone, strlen($matchedCode));
        } else {
            $this->emergency_countryCode = '+233';
            $this->emergency_selectedFlag = 'gh.png';
            $this->emergency_contact_phone_local = $phone;
        }
        $this->emergency_updateCountryInfo();
    }

    // ─── Avatar ──────────────────────────────────────────────────────────────

    public function updatedAvatar()
    {
        $this->validate(['avatar' => 'nullable|image|max:2048']);
    }

    public function confirmAvatarDelete()
    {
        $this->confirmingAvatarDelete = true;
    }

    public function cancelAvatarDelete()
    {
        $this->confirmingAvatarDelete = false;
    }

    public function deleteAvatar()
    {
        if ($this->user->avatar) {
            $path = storage_path('app/public/' . $this->user->avatar);
            if (file_exists($path)) {
                unlink($path);
            }
            $this->user->avatar = null;
            $this->user->save();

            // 🔥 Log avatar deletion
            ActivityLogger::log('Avatar deleted', [
                'user_id' => $this->user->id,
                'email'   => $this->user->email,
            ], 'profile');
        }
        $this->avatar = null;
        $this->confirmingAvatarDelete = false;

        $this->user = $this->user->fresh();
        ProfileHelper::broadcast($this->user);

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Avatar removed',
            'message' => 'Your profile picture has been deleted.',
        ]);
    }

    // ─── Skills ──────────────────────────────────────────────────────────────

    public function addSkill()
    {
        $this->skills[] = ['name' => '', 'level' => 50];
    }

    public function removeSkill($index)
    {
        unset($this->skills[$index]);
        $this->skills = array_values($this->skills);
    }

    // ─── Education ───────────────────────────────────────────────────────────

    public function addEducation()
    {
        $this->education[] = [
            'institution' => '',
            'degree' => '',
            'start_year' => '',
            'end_year' => '',
            'currently_studying' => false,
        ];
    }

    public function removeEducation($index)
    {
        unset($this->education[$index]);
        $this->education = array_values($this->education);
    }

    public function toggleCurrentlyStudying($index)
    {
        if (isset($this->education[$index])) {
            $this->education[$index]['currently_studying'] = !$this->education[$index]['currently_studying'];
            if ($this->education[$index]['currently_studying']) {
                $this->education[$index]['end_year'] = null;
            }
        }
    }

    // ─── Save ────────────────────────────────────────────────────────────────

    public function save()
    {
        $this->validate();

        // Handle avatar upload
        if ($this->avatar) {
            if ($this->user->avatar) {
                $oldPath = storage_path('app/public/' . $this->user->avatar);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $path = $this->avatar->store('avatars', 'public');
            $this->user->avatar = $path;

            // 🔥 Log avatar upload
            ActivityLogger::log('Avatar uploaded', [
                'user_id' => $this->user->id,
                'email'   => $this->user->email,
                'path'    => $path,
            ], 'profile');
        }

        // Update user
        $this->user->update([
            'name'  => $this->name,
            'email' => $this->email,
            'phone' => $this->getFullPhone(),
        ]);

        // Update or create profile
        $profile = $this->user->profile;
        if (!$profile) {
            $profile = $this->user->profile()->create([]);
        }

        // Clean education data
        foreach ($this->education as &$edu) {
            if ($edu['currently_studying']) {
                $edu['end_year'] = null;
            }
        }

        $oldProfileData = $profile->getAttributes(); // for logging context

        $profile->update([
            'about_me'                => $this->about_me,
            'skills'                  => $this->skills,
            'education'               => $this->education,
            'social_links'            => $this->social_links,
            'gender'                  => $this->gender,
            'date_of_birth'           => $this->date_of_birth ? Carbon::parse($this->date_of_birth) : null,
            'country_code'            => $this->country_code,
            'city'                    => $this->city,
            'emergency_contact_name'  => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone_local ? $this->emergency_getFullPhone() : null,
        ]);

        // 🔥 Log profile update
        ActivityLogger::log('Profile updated', [
            'user_id'      => $this->user->id,
            'email'        => $this->user->email,
            'changed_fields' => array_keys(array_diff_assoc($profile->getAttributes(), $oldProfileData)),
        ], 'profile');

        $this->user = $this->user->fresh();
        ProfileHelper::broadcast($this->user);
        $this->avatar = null;

        $this->dispatch('notify', [
            'type'    => 'success',
            'title'   => 'Profile updated!',
            'message' => 'Your changes have been saved successfully.',
        ]);
    }

    // ─── Render ─────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.admin.users.account.tabs.profile-tab');
    }
}

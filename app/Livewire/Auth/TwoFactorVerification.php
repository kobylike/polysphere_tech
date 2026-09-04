<?php

namespace App\Livewire\Auth;

use App\Helpers\ActivityLogger; // <-- Correct import
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

#[Layout('layouts.auth')]
#[Title('Two-Factor Verification')]
class TwoFactorVerification extends Component
{
    public $mode = 'totp';

    public $code = '';
    public $code1 = '';
    public $code2 = '';
    public $code3 = '';
    public $code4 = '';
    public $code5 = '';
    public $code6 = '';

    public $recoveryCode = '';

    public $loading = false;
    public $showHelp = false;
    public $timeLeft = 30;
    public $timerActive = true;
    public $remainingAttempts = 5;
    public $errorMessage = '';
    public $showTimeWarning = false;
    public $timeSyncStatus = '';
    public $showTimeSyncInfo = false;
    public $debugInfo = '';
    public $isLocalhost = false;

    public $userEmail = '';
    public $userName = '';
    public $deviceName = '';
    public $ipAddress = '';

    protected $loginId;
    protected $rememberMe;
    protected $loginTime;

    public function mount(Request $request)
    {
        $this->isLocalhost = app()->environment('local') ||
            $request->ip() === '127.0.0.1' ||
            $request->ip() === 'localhost';

        $this->loginId = Session::get('login.id');
        $this->rememberMe = Session::get('login.remember', false);
        $this->loginTime = Session::get('login.time', now());

        if (!$this->loginId) {
            Session::flash('error', 'Session expired. Please login again.');
            return redirect()->route('login');
        }

        if ($this->loginTime && now()->diffInMinutes($this->loginTime) > 5) {
            Session::forget(['login.id', 'login.remember', 'login.time']);
            Session::flash('error', 'Session expired. Please login again.');
            return redirect()->route('login');
        }

        $user = \App\Models\User::find($this->loginId);
        if (!$user) {
            Session::forget(['login.id', 'login.remember', 'login.time']);
            Session::flash('error', 'User not found. Please login again.');
            return redirect()->route('login');
        }

        if ($user->isLocked()) {
            Session::forget(['login.id', 'login.remember', 'login.time', '2fa_attempts']);
            Session::flash('error', 'Too many failed attempts. Please try again later.');
            return redirect()->route('login');
        }

        $this->userEmail = $user->email;
        $this->userName = $user->name;
        $this->deviceName = $this->getDeviceName($request);
        $this->ipAddress = $request->ip();

        $this->remainingAttempts = Session::get('2fa_attempts', 5);

        if (!Session::has('login.time')) {
            Session::put('login.time', now());
        }

        $this->updateTimer();
        Session::put('timer_start', time());

        Log::info('2FA Verification mounted', [
            'user_id' => $user->id,
            'localhost' => $this->isLocalhost,
            'server_time' => time(),
            'server_time_formatted' => date('Y-m-d H:i:s'),
            'ip' => $this->ipAddress
        ]);

        if ($this->isLocalhost) {
            $this->debugInfo = "Localhost detected. Using larger verification window.";
            $this->showTimeSyncInfo = true;
        }
    }

    protected function getDeviceName(Request $request)
    {
        $userAgent = $request->userAgent();

        if (strpos($userAgent, 'Windows') !== false) {
            return 'Windows Device';
        } elseif (strpos($userAgent, 'Mac') !== false) {
            return 'Mac Device';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            return 'Linux Device';
        } elseif (strpos($userAgent, 'Android') !== false) {
            return 'Android Device';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            return 'Apple Device';
        }

        return 'Unknown Device';
    }

    public function updated($property, $value)
    {
        if (Str::startsWith($property, 'code')) {
            $this->errorMessage = '';
            $this->debugInfo = '';
        }

        if (Str::startsWith($property, 'code') && strlen($value) === 1) {
            $currentNum = (int) Str::after($property, 'code');
            if ($currentNum < 6) {
                $this->dispatch('focus-next', ['input' => 'code' . ($currentNum + 1)]);
            } else {
                $this->js('setTimeout(() => {$wire.verifyTotp()}, 300)');
            }
        }

        if (Str::startsWith($property, 'code') && strlen($value) === 0) {
            $currentNum = (int) Str::after($property, 'code');
            if ($currentNum > 1) {
                $this->dispatch('focus-previous', ['input' => 'code' . ($currentNum - 1)]);
            }
        }
    }

    public function verifyTotp()
    {
        $this->loading = true;
        $this->errorMessage = '';
        $this->debugInfo = '';

        $fullCode = $this->code1 . $this->code2 . $this->code3 .
            $this->code4 . $this->code5 . $this->code6;

        if (strlen($fullCode) !== 6 || !is_numeric($fullCode)) {
            $this->errorMessage = 'Please enter a valid 6-digit code.';
            $this->loading = false;
            $this->dispatch('shake-inputs');
            return;
        }

        $this->code = $fullCode;

        if (!Session::has('login.id')) {
            $this->errorMessage = 'Session expired. Please login again.';
            $this->loading = false;
            return;
        }

        $userId = Session::get('login.id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            Session::forget(['login.id', 'login.remember', 'login.time']);
            $this->errorMessage = 'User not found. Please login again.';
            $this->loading = false;
            return;
        }

        if ($user->isLocked()) {
            $this->errorMessage = 'Account temporarily locked. Please try again later.';
            $this->loading = false;
            Session::forget(['login.id', 'login.remember', 'login.time', '2fa_attempts']);
            return;
        }

        if ($this->remainingAttempts <= 0) {
            $this->errorMessage = 'Too many failed attempts. Account locked for 15 minutes.';
            $this->loading = false;

            $user->lockAccount();
            $user->logLoginActivity($this->ipAddress, true, false, 'Too many 2FA attempts');

            Session::forget(['login.id', 'login.remember', 'login.time', '2fa_attempts']);
            return;
        }

        Log::info('2FA verification attempt', [
            'user_id' => $user->id,
            'code_entered' => $fullCode,
            'remaining_attempts' => $this->remainingAttempts,
            'server_time' => time(),
            'server_time_formatted' => date('Y-m-d H:i:s'),
            'time_left_on_timer' => $this->timeLeft,
            'localhost' => $this->isLocalhost,
            'timezone' => config('app.timezone'),
            'seconds_into_window' => time() % 30,
            'seconds_remaining' => 30 - (time() % 30)
        ]);

        $serverTimeInfo = $user->getServerTimeInfo();

        if ($this->isLocalhost) {
            $currentServerCode = $user->getCurrentTotpCode();
            $this->debugInfo = sprintf(
                "Server Time: %s | Time Window: %d | Seconds into window: %d | Expected Code (approx): %s",
                $serverTimeInfo['server_time_formatted'],
                $serverTimeInfo['totp_time_window'],
                $serverTimeInfo['seconds_into_window'],
                $currentServerCode
            );

            Log::debug('Localhost 2FA Debug', [
                'expected_code' => $currentServerCode,
                'entered_code' => $fullCode,
                'match' => $currentServerCode === $fullCode,
                'server_time_info' => $serverTimeInfo
            ]);
        }

        $debugMode = $this->isLocalhost;
        if (!$user->verifyTwoFactorCode($this->code, $debugMode)) {
            $this->remainingAttempts--;
            Session::put('2fa_attempts', $this->remainingAttempts);

            if ($this->isLocalhost) {
                $currentServerCode = $user->getCurrentTotpCode();
                $timeInfo = $user->getServerTimeInfo();

                $this->errorMessage = sprintf(
                    "Invalid code. Server expects (approx): %s | You entered: %s | Time: %s | %d attempts remaining.",
                    $currentServerCode,
                    $fullCode,
                    $timeInfo['server_time_formatted'],
                    $this->remainingAttempts
                );

                $this->debugInfo = sprintf(
                    "Server seconds into 30s window: %d | Remaining: %ds | Timezone: %s",
                    $timeInfo['seconds_into_window'],
                    $timeInfo['seconds_remaining'],
                    $timeInfo['timezone']
                );
            } else {
                $this->errorMessage = $this->remainingAttempts > 0
                    ? "Invalid code. {$this->remainingAttempts} attempts remaining."
                    : "Invalid code. No attempts remaining.";
            }

            $this->loading = false;
            $this->dispatch('shake-inputs');
            $this->clearCodeInputs();

            $user->logLoginActivity($this->ipAddress, true, false, 'Invalid 2FA code');

            return;
        }

        $this->completeLogin($user);
    }

    public function verifyRecovery()
    {
        $this->loading = true;
        $this->errorMessage = '';
        $this->debugInfo = '';

        if (empty($this->recoveryCode)) {
            $this->errorMessage = 'Please enter a recovery code.';
            $this->loading = false;
            return;
        }

        if (!Session::has('login.id')) {
            $this->errorMessage = 'Session expired. Please login again.';
            $this->loading = false;
            return;
        }

        $userId = Session::get('login.id');
        $user = \App\Models\User::find($userId);

        if (!$user) {
            Session::forget(['login.id', 'login.remember', 'login.time']);
            $this->errorMessage = 'User not found. Please login again.';
            $this->loading = false;
            return;
        }

        if (!$user->useRecoveryCode($this->recoveryCode)) {
            $this->errorMessage = 'Invalid recovery code.';
            $this->loading = false;

            $user->logLoginActivity($this->ipAddress, true, false, 'Invalid recovery code');

            return;
        }

        $this->completeLogin($user);
    }

    protected function completeLogin($user)
    {
        $rememberMe = Session::get('login.remember', false);

        Auth::login($user, $rememberMe);

        $user->updateLastLogin();
        $user->logLoginActivity($this->ipAddress, true, true);
        $user->resetFailedLoginAttempts();

        // ✅ Log successful 2FA verification
        ActivityLogger::log('Two-factor authentication verified', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'method'  => $this->mode, // 'totp' or 'recovery'
            'ip'      => $this->ipAddress,
        ], 'auth');

        Session::forget(['login.id', 'login.remember', 'login.time', '2fa_attempts', 'timer_start']);

        $intendedUrl = Session::get('url.intended', route('dashboard'));

        $this->loading = false;

        $this->dispatch('verification-success');

        $this->js("
            setTimeout(() => {
                window.location.href = '{$intendedUrl}';
            }, 1000);
        ");
    }

    public function switchMode($newMode)
    {
        $this->mode = $newMode;
        $this->errorMessage = '';
        $this->debugInfo = '';
        $this->showTimeSyncInfo = false;

        if ($newMode === 'totp') {
            $this->clearCodeInputs();
            $this->js('setTimeout(() => { document.getElementById("code-1")?.focus() }, 100)');
        } else {
            $this->recoveryCode = '';
            $this->js('setTimeout(() => { document.getElementById("recovery-input")?.focus() }, 100)');
        }
    }

    public function resendCode()
    {
        Session::put('timer_start', time());
        $this->updateTimer();
        $this->clearCodeInputs();
        $this->errorMessage = '';
        $this->debugInfo = '';
        $this->showTimeSyncInfo = false;
        $this->timerActive = true;

        $userId = Session::get('login.id');
        $user = \App\Models\User::find($userId);

        if ($user) {
            Log::info('2FA code resend requested', [
                'user_id' => $user->id,
                'server_time' => time(),
                'localhost' => $this->isLocalhost
            ]);
        }

        $this->dispatch('show-toast', [
            'type' => 'info',
            'message' => 'Please enter the new code from your authenticator app.',
            'duration' => 3000
        ]);
    }

    public function logout()
    {
        Session::forget(['login.id', 'login.remember', 'login.time', '2fa_attempts', 'timer_start']);
        return redirect()->route('login');
    }

    public function clearCodeInputs()
    {
        $this->code1 = $this->code2 = $this->code3 =
            $this->code4 = $this->code5 = $this->code6 = '';
    }

    public function shakeInputs()
    {
        $this->dispatch('shake-inputs');
    }

    public function toggleHelp()
    {
        $this->showHelp = !$this->showHelp;
    }

    public function updateTimer()
    {
        if (!$this->timerActive) {
            return;
        }

        $currentTime = time();
        $secondsIntoWindow = $currentTime % 30;
        $this->timeLeft = 30 - $secondsIntoWindow;

        if ($this->isLocalhost && $this->timeLeft % 10 == 0) {
            Log::debug('Localhost TOTP Timer State', [
                'user_id' => Session::get('login.id'),
                'time_left' => $this->timeLeft,
                'seconds_into_window' => $secondsIntoWindow,
                'server_time' => $currentTime,
                'server_time_formatted' => date('Y-m-d H:i:s'),
                'timezone' => config('app.timezone')
            ]);
        }

        if ($this->timeLeft <= 0) {
            $this->timeLeft = 0;
            $this->timerActive = false;
            $this->showTimeWarning = false;
            $this->js('setTimeout(() => {$wire.resendCode()}, 2000)');
        } elseif ($this->timeLeft <= 10) {
            $this->showTimeWarning = true;
        } else {
            $this->showTimeWarning = false;
        }
    }

    public function decrementTimer()
    {
        $this->updateTimer();
    }

    public function checkTimeSync()
    {
        $userId = Session::get('login.id');
        $user = \App\Models\User::find($userId);

        if ($user) {
            $timeInfo = $user->getServerTimeInfo();
            $serverCode = $user->getCurrentTotpCode();

            $message = sprintf(
                "Server Time: %s | Timezone: %s | Current Code (approx): %s | Seconds into window: %d",
                $timeInfo['server_time_formatted'],
                $timeInfo['timezone'],
                $serverCode,
                $timeInfo['seconds_into_window']
            );

            $this->debugInfo = $message;
            $this->showTimeSyncInfo = true;

            $this->dispatch('show-toast', [
                'type' => 'info',
                'message' => 'Time sync info displayed below.',
                'duration' => 3000
            ]);

            Log::info('Manual time sync check', [
                'user_id' => $user->id,
                'time_info' => $timeInfo,
                'current_code' => $serverCode
            ]);
        }
    }

    public function resetTimeOffset()
    {
        $userId = Session::get('login.id');
        $user = \App\Models\User::find($userId);

        if ($user) {
            $user->update(['totp_time_offset' => 0]);
            $this->debugInfo = 'Time offset reset to 0. Please try entering your code again.';
            $this->showTimeSyncInfo = true;

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Time offset has been reset.',
                'duration' => 3000
            ]);

            Log::info('Time offset reset', ['user_id' => $user->id]);
        }
    }

    public function getExpectedCode()
    {
        $userId = Session::get('login.id');
        $user = \App\Models\User::find($userId);

        if ($user) {
            $serverCode = $user->getCurrentTotpCode();
            $timeInfo = $user->getServerTimeInfo();

            $this->debugInfo = sprintf(
                "Expected Code (approx): %s | Valid until: %s | Server: %s",
                $serverCode,
                date('H:i:s', time() + $timeInfo['seconds_remaining']),
                $timeInfo['server_time_formatted']
            );

            $this->showTimeSyncInfo = true;

            if ($this->isLocalhost && app()->environment('local')) {
                $this->code1 = substr($serverCode, 0, 1);
                $this->code2 = substr($serverCode, 1, 1);
                $this->code3 = substr($serverCode, 2, 1);
                $this->code4 = substr($serverCode, 3, 1);
                $this->code5 = substr($serverCode, 4, 1);
                $this->code6 = substr($serverCode, 5, 1);
            }
        }
    }

    public function render()
    {
        return view('livewire.auth.two-factor-verification');
    }
}

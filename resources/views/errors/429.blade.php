<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Too Many Requests - 429</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/main/imgs/favicon.svg') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .error-card {
            max-width: 520px;
            width: 100%;
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.06);
            padding: 3rem 2.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .error-card .bg-decoration {
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 240px;
            height: 240px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.05) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .error-card .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(99, 102, 241, 0.02));
            border: 2px solid rgba(99, 102, 241, 0.12);
            margin-bottom: 1.5rem;
        }

        .error-card .icon-wrapper i {
            font-size: 3.5rem;
            color: #6366f1;
        }

        .error-card h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.5rem;
        }

        .error-card p {
            color: #64748b;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .error-card .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: center;
        }

        .error-card .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.6rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .error-card .btn-primary {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.25);
        }

        .error-card .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(99, 102, 241, 0.35);
        }

        .error-card .btn-secondary {
            background: #f1f5f9;
            color: #1e293b;
            border: 1px solid #e2e8f0;
        }

        .error-card .btn-secondary:hover {
            background: #e2e8f0;
        }

        .error-card .status-code {
            position: absolute;
            top: 10px;
            left: 20px;
            font-size: 5rem;
            font-weight: 900;
            color: #6366f1;
            opacity: 0.04;
            line-height: 1;
            user-select: none;
            pointer-events: none;
        }

        @media (max-width: 480px) {
            .error-card {
                padding: 2rem 1.5rem;
            }

            .error-card .icon-wrapper {
                width: 90px;
                height: 90px;
            }

            .error-card .icon-wrapper i {
                font-size: 2.5rem;
            }

            .error-card h2 {
                font-size: 1.4rem;
            }

            .error-card .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="bg-decoration"></div>
        <div class="status-code">429</div>

        <div class="icon-wrapper">
            <i class="fa-regular fa-gauge-high"></i>
        </div>

        <h2>Too Many Requests</h2>
        <p>
            You've made too many requests in a short period.
            <br>Please wait a moment and try again.
        </p>

        <div class="btn-group">
            <a href="javascript:location.reload()" class="btn btn-primary">
                <i class="fa-regular fa-rotate"></i> Retry
            </a>
            <a href="{{ url('/') }}" class="btn btn-secondary">
                <i class="fa-regular fa-house"></i> Go Home
            </a>
        </div>

        @if(isset($exception) && $exception->getMessage() && config('app.debug'))
            <div
                style="margin-top:2rem; padding:1rem; background:#f0f9ff; border-radius:0.75rem; text-align:left; font-size:0.8rem; color:#0369a1; border:1px solid #bae6fd;">
                <strong>Debug:</strong> {{ $exception->getMessage() }}
            </div>
        @endif
    </div>
</body>

</html>
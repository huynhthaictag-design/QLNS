<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HappyTime')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
            background: radial-gradient(1200px 600px at 10% -10%, #d8b4fe 0%, transparent 50%),
                        radial-gradient(900px 500px at 110% 10%, #a78bfa 0%, transparent 45%),
                        #f3eefc;
            color: #2b2440;
        }
        .auth-wrap {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: #fff;
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 20px 50px rgba(90, 60, 140, .12);
        }
        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 1.25rem;
            margin-bottom: 8px;
        }
        .brand .mark {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, #a78bfa, #7c3aed);
            color: #fff; display: flex; align-items: center; justify-content: center;
        }
        .form-control, .form-select {
            border-radius: 12px;
            padding: 10px 14px;
            border-color: #eee7f8;
            background: #fbf9ff;
        }
        .btn-auth {
            width: 100%;
            background: linear-gradient(90deg, #8b5cf6, #7c3aed);
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 12px;
            font-weight: 700;
        }
        .muted { color: #8b8499; }
        a { color: #7c3aed; font-weight: 600; text-decoration: none; }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="auth-card">
        @yield('content')
    </div>
</div>
</body>
</html>

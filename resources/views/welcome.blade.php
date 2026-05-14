<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Профессионалы — Подготовка к чемпионату</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
        }
        h1 {
            font-size: 2rem;
            color: #1a1a2e;
            margin-bottom: 0.5rem;
        }
        p {
            color: #666;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 600;
            margin: 0.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-secondary {
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
        }
        .btn-dashboard {
            background: #10b981;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🏆 Профессионалы</h1>
        <p>Платформа для подготовки к чемпионату «Профессионалы»</p>

        @auth
            <a href="{{ route('profile.edit') }}" class="btn btn-dashboard">Мой профиль</a>
            <a href="{{ route('competences.index') }}" class="btn btn-primary">Компетенции</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-primary">Войти</a>
            <a href="{{ route('register') }}" class="btn btn-secondary">Регистрация</a>
        @endauth
    </div>
</body>
</html>

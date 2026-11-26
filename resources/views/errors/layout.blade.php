<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('code') — @yield('title') | MaintainNow</title>
    <style>
        body {
            margin: 0;
            font-family: 'Geist', Inter, system-ui, -apple-system, sans-serif;
            background: #f2f4f7;
            color: #0a0a0a;
            display: flex;
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border: 1px solid #e3e6ec;
            border-radius: 8px;
            padding: 28px;
        }
        .code {
            font-family: 'Geist Mono', ui-monospace, monospace;
            font-size: 12px;
            color: #5b616e;
            margin-bottom: 8px;
        }
        h1 {
            margin: 0 0 8px;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.01em;
        }
        p {
            margin: 0 0 20px;
            font-size: 14px;
            line-height: 1.5;
            color: #5b616e;
        }
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            height: 36px;
            padding: 0 14px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border: 1px solid #d0d5dd;
            background: #ffffff;
            color: #0a0a0a;
        }
        .btn.primary {
            background: #141414;
            color: #ffffff;
            border-color: #141414;
        }
        .detail {
            margin-bottom: 18px;
            padding: 10px 12px;
            background: #f4f5f7;
            border: 1px solid #e3e6ec;
            border-radius: 6px;
            font-family: ui-monospace, monospace;
            font-size: 12px;
            color: #c4242a;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">MaintainNow · HTTP @yield('code')</div>
        <h1>@yield('heading')</h1>
        <p>@yield('message')</p>
        @yield('extra')
        <div class="actions">
            <a href="{{ route('dashboard') }}" class="btn primary">Kembali ke Ringkasan</a>
            <a href="javascript:history.back()" class="btn">Halaman Sebelumnya</a>
        </div>
    </div>
</body>
</html>

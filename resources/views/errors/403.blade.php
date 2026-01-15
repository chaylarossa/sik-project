<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 | Forbidden</title>
    <style>
        :root { color-scheme: light; }
        body { margin: 0; font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: #f7fafc; color: #1f2937; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 32px; width: min(520px, 92vw); box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08); }
        .title { font-size: 18px; font-weight: 700; margin: 0 0 8px; }
        .muted { color: #6b7280; margin: 0 0 20px; line-height: 1.5; }
        .row { display: flex; gap: 10px; flex-wrap: wrap; }
        button, a.button { appearance: none; border: 1px solid #e5e7eb; background: #f9fafb; color: #111827; padding: 10px 14px; border-radius: 10px; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.15s ease; }
        button:hover, a.button:hover { border-color: #cbd5e1; background: #fff; }
        .primary { background: #4f46e5; border-color: #4338ca; color: #fff; }
        .primary:hover { background: #4338ca; }
    </style>
</head>
<body>
    <div class="card">
        <div class="title">403 · Tidak ada izin</div>
        <p class="muted">Akun ini tidak punya akses ke halaman yang diminta. Silakan keluar lalu masuk sebagai role lain (mis. Administrator) atau buka halaman yang sesuai peran Anda.</p>
        <div class="row">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="primary">Keluar</button>
            </form>
            <a class="button" href="{{ route('login') }}">Kembali ke Login</a>
            <a class="button" href="{{ url('/') }}">Ke Halaman Utama</a>
        </div>
    </div>
</body>
</html>

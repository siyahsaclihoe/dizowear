<!-- 404 Error Page -->
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sayfa Bulunamadı | Dızo Wear</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: #000; 
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .container { padding: 40px; }
        .error-code { font-size: 150px; font-weight: 800; line-height: 1; }
        h1 { font-size: 32px; margin: 20px 0; }
        p { color: #888; margin-bottom: 30px; }
        .btn {
            display: inline-block;
            padding: 15px 40px;
            background: #fff;
            color: #000;
            text-decoration: none;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn:hover { background: #f0f0f0; transform: translateY(-2px); }
        .logo { font-size: 24px; font-weight: 800; margin-bottom: 40px; }
        .logo span { color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">DIZO<span>WEAR</span></div>
        <div class="error-code">404</div>
        <h1>Sayfa Bulunamadı</h1>
        <p>Aradığınız sayfa mevcut değil veya taşınmış olabilir.</p>
        <a href="/dizowear/" class="btn">Ana Sayfaya Dön</a>
    </div>
</body>
</html>

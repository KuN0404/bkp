<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FinReceipt — Sistem Bukti Penerimaan Keuangan</title>
    <meta name="description" content="FinReceipt adalah platform modern untuk membuat dan mengelola surat bukti penerimaan keuangan secara digital, cepat, dan aman.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #111118;
            --bg-card: #16161e;
            --bg-card-hover: #1c1c26;
            --border-color: #2a2a3a;
            --border-glow: #e74c3c33;
            --text-primary: #f0f0f5;
            --text-secondary: #8888a0;
            --text-muted: #55556a;
            --accent: #e74c3c;
            --accent-light: #ff6b5b;
            --accent-dark: #c0392b;
            --accent-glow: rgba(231, 76, 60, 0.15);
            --gradient-start: #e74c3c;
            --gradient-end: #ff8c69;
            --success: #2ecc71;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ===== Background Aurora Effect ===== */
        .aurora {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }
        .aurora::before {
            content: '';
            position: absolute;
            top: -50%; left: -50%;
            width: 200%; height: 200%;
            background: radial-gradient(ellipse at 20% 50%, rgba(231,76,60,0.06) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(52,73,94,0.08) 0%, transparent 50%),
                        radial-gradient(ellipse at 50% 80%, rgba(231,76,60,0.04) 0%, transparent 50%);
            animation: aurora-drift 20s ease-in-out infinite alternate;
        }
        @keyframes aurora-drift {
            0% { transform: translate(0, 0) rotate(0deg); }
            100% { transform: translate(-30px, 20px) rotate(3deg); }
        }

        /* ===== Stars / Particles ===== */
        .stars {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 0;
        }
        .star {
            position: absolute;
            width: 2px; height: 2px;
            background: rgba(255,255,255,0.3);
            border-radius: 50%;
            animation: twinkle 3s ease-in-out infinite alternate;
        }
        @keyframes twinkle {
            0% { opacity: 0.2; transform: scale(1); }
            100% { opacity: 0.8; transform: scale(1.3); }
        }

        /* ===== Navigation ===== */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: rgba(10,10,15,0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border-color);
        }
        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-primary);
        }
        .nav-logo {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--gradient-end));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            color: #fff;
            box-shadow: 0 0 20px var(--accent-glow);
        }
        .nav-title {
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: -0.02em;
        }
        .nav-title span { color: var(--accent); }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .nav-link {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            color: var(--text-secondary);
        }
        .nav-link:hover { color: var(--text-primary); background: rgba(255,255,255,0.05); }
        .nav-link-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff !important;
            border: none;
            box-shadow: 0 4px 15px rgba(231,76,60,0.3);
        }
        .nav-link-primary:hover {
            background: linear-gradient(135deg, var(--accent-light), var(--accent));
            box-shadow: 0 4px 25px rgba(231,76,60,0.5);
            transform: translateY(-1px);
        }

        /* ===== Hero Section ===== */
        .hero {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 4rem;
        }
        .hero-icon {
            width: 72px; height: 72px;
            background: linear-gradient(135deg, var(--accent), var(--gradient-end));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            box-shadow: 0 0 60px rgba(231,76,60,0.25), 0 0 120px rgba(231,76,60,0.1);
            animation: float 6s ease-in-out infinite;
        }
        .hero-icon svg { width: 36px; height: 36px; color: #fff; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 800;
            letter-spacing: -0.03em;
            line-height: 1.1;
            margin-bottom: 1rem;
        }
        .hero h1 .gradient-text {
            background: linear-gradient(135deg, var(--accent), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-tagline {
            font-size: 1rem;
            font-weight: 600;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 0.5rem;
        }
        .hero-desc {
            font-size: 1.125rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.7;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            background: var(--accent-glow);
            border: 1px solid rgba(231,76,60,0.3);
            color: var(--accent-light);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 3rem;
            cursor: default;
            transition: all 0.3s ease;
        }
        .hero-badge:hover { background: rgba(231,76,60,0.2); }
        .hero-badge-dot {
            width: 8px; height: 8px;
            background: var(--success);
            border-radius: 50%;
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }
        .hero-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.85rem 2rem;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            color: #fff;
            box-shadow: 0 4px 20px rgba(231,76,60,0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(231,76,60,0.5);
        }
        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }
        .btn-secondary:hover {
            background: var(--bg-card-hover);
            border-color: var(--accent);
            transform: translateY(-2px);
        }
        .btn svg { width: 18px; height: 18px; }

        /* ===== Section Common ===== */
        .section {
            position: relative;
            z-index: 1;
            max-width: 1200px;
            margin: 0 auto;
            padding: 5rem 2rem;
        }
        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }
        .section-icon {
            color: var(--accent);
            font-size: 1.25rem;
        }
        .section-label {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--accent);
        }
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 1rem;
        }
        .section-desc {
            color: var(--text-secondary);
            max-width: 600px;
            margin-bottom: 3rem;
        }

        /* ===== Feature Cards ===== */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
        }
        .feature-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, var(--accent), transparent);
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .feature-card:hover {
            border-color: rgba(231,76,60,0.3);
            background: var(--bg-card-hover);
            transform: translateY(-4px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .feature-card:hover::before { opacity: 1; }
        .feature-card-icon {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: var(--accent-glow);
            border: 1px solid rgba(231,76,60,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            color: var(--accent);
        }
        .feature-card-icon svg { width: 24px; height: 24px; }
        .feature-card h3 {
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .feature-card p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ===== Steps Section ===== */
        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            counter-reset: step;
        }
        .step {
            position: relative;
            padding: 2rem;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            transition: all 0.3s ease;
            counter-increment: step;
        }
        .step::before {
            content: counter(step, decimal-leading-zero);
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent), var(--gradient-end));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            opacity: 0.3;
            line-height: 1;
            display: block;
            margin-bottom: 1rem;
        }
        .step:hover {
            border-color: rgba(231,76,60,0.3);
            transform: translateY(-4px);
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        .step h3 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .step p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* ===== Testimonials ===== */
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.25rem;
        }
        .testimonial-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.3s ease;
        }
        .testimonial-card:hover {
            border-color: rgba(231,76,60,0.2);
            transform: translateY(-2px);
        }
        .testimonial-quote {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.7;
            margin-bottom: 1.25rem;
            font-style: italic;
        }
        .testimonial-quote::before { content: '"'; color: var(--accent); font-size: 1.5rem; }
        .testimonial-quote::after { content: '"'; color: var(--accent); font-size: 1.5rem; }
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .testimonial-avatar {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--gradient-end));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            font-size: 0.85rem;
        }
        .testimonial-name {
            font-weight: 600;
            font-size: 0.9rem;
        }
        .testimonial-role {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        /* ===== CTA Section ===== */
        .cta-section {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 5rem 2rem;
        }
        .cta-box {
            max-width: 700px;
            margin: 0 auto;
            padding: 4rem 3rem;
            background: linear-gradient(135deg, rgba(231,76,60,0.08), rgba(10,10,15,0.9));
            border: 1px solid rgba(231,76,60,0.2);
            border-radius: 24px;
            box-shadow: 0 0 80px rgba(231,76,60,0.08);
        }
        .cta-box h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .cta-box p {
            color: var(--text-secondary);
            margin-bottom: 2rem;
        }

        /* ===== Footer ===== */
        .footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 2rem;
            border-top: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 0.85rem;
        }
        .footer a { color: var(--accent); text-decoration: none; }
        .footer a:hover { text-decoration: underline; }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .nav { padding: 0.75rem 1rem; }
            .nav-title { font-size: 1rem; }
            .hero { padding: 7rem 1.25rem 3rem; }
            .hero h1 { font-size: 2rem; }
            .hero-desc { font-size: 1rem; }
            .section { padding: 3rem 1.25rem; }
            .section-title { font-size: 1.5rem; }
            .cta-box { padding: 2.5rem 1.5rem; }
            .cta-box h2 { font-size: 1.5rem; }
            .features-grid { grid-template-columns: 1fr; }
            .steps { grid-template-columns: 1fr; }
            .testimonials-grid { grid-template-columns: 1fr; }
        }

        /* ===== Animations ===== */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Aurora Background -->
    <div class="aurora"></div>

    <!-- Stars -->
    <div class="stars" id="stars"></div>

    <!-- Navigation -->
    <nav class="nav" id="main-nav">
        <a href="/" class="nav-brand">
            <div class="nav-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:20px;height:20px;">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <span class="nav-title">Fin<span>Receipt</span></span>
        </a>
        <div class="nav-links">
            <a href="{{ url('/panels') }}" class="nav-link nav-link-primary" id="dashboard-link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width:16px;height:16px;margin-right:4px;">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                </svg>
                Login
            </a>
          </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-badge">
            <span class="hero-badge-dot"></span>
            Platform Keuangan Digital Terpercaya
        </div>

        <div class="hero-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
        </div>

        <h1>
            <span class="gradient-text">FinReceipt</span>
        </h1>
        <p class="hero-tagline">Sistem Bukti Penerimaan Keuangan</p>
        <p class="hero-desc">
            Platform modern untuk membuat, mengelola, dan mencetak surat bukti penerimaan keuangan secara digital. Cepat, aman, dan terpercaya.
        </p>

        <div class="hero-actions">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/admin') }}" class="btn btn-primary" id="hero-dashboard-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                        Buka Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary" id="hero-login-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4m-5-4 5-5-5-5m5 5H3"/></svg>
                        Masuk Sekarang
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-secondary" id="hero-register-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            Daftar Akun
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </section>

    <!-- Features Section -->
    <section class="section fade-in" id="features">
        <div class="section-header">
            <span class="section-icon">❯</span>
            <span class="section-label">Fitur Unggulan</span>
        </div>
        <h2 class="section-title">Semua yang Anda Butuhkan</h2>
        <p class="section-desc">Kelola seluruh proses bukti penerimaan keuangan dalam satu platform yang terintegrasi.</p>

        <div class="features-grid">
            <div class="feature-card" id="feature-create">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <h3>Buat Surat Cepat</h3>
                <p>Buat surat bukti penerimaan keuangan hanya dalam beberapa klik. Template otomatis yang siap digunakan.</p>
            </div>

            <div class="feature-card" id="feature-manage">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
                <h3>Kelola Arsip Digital</h3>
                <p>Semua dokumen tersimpan rapi dan aman secara digital. Cari dan akses dokumen kapan saja, di mana saja.</p>
            </div>

            <div class="feature-card" id="feature-print">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 6 2 18 2 18 9"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                </div>
                <h3>Cetak Profesional</h3>
                <p>Cetak surat dengan format resmi dan profesional. Hasil cetak siap digunakan untuk keperluan administrasi.</p>
            </div>

            <div class="feature-card" id="feature-secure">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h3>Keamanan Terjamin</h3>
                <p>Data keuangan Anda dilindungi dengan sistem keamanan berlapis. Akses hanya untuk pengguna berwenang.</p>
            </div>

            <div class="feature-card" id="feature-report">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"/>
                        <line x1="12" y1="20" x2="12" y2="4"/>
                        <line x1="6" y1="20" x2="6" y2="14"/>
                    </svg>
                </div>
                <h3>Laporan & Rekap</h3>
                <p>Dapatkan rekapitulasi dan laporan keuangan secara otomatis. Pantau penerimaan dengan mudah dan akurat.</p>
            </div>

            <div class="feature-card" id="feature-multi-user">
                <div class="feature-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <h3>Multi Pengguna</h3>
                <p>Dukung banyak pengguna dengan hak akses berbeda. Koordinasi tim keuangan menjadi lebih efisien.</p>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="section fade-in" id="how-it-works">
        <div class="section-header">
            <span class="section-icon">❯</span>
            <span class="section-label">Cara Kerja</span>
        </div>
        <h2 class="section-title">Mudah dan Cepat</h2>
        <p class="section-desc">Hanya tiga langkah sederhana untuk membuat bukti penerimaan keuangan.</p>

        <div class="steps">
            <div class="step" id="step-1">
                <h3>Masuk ke Sistem</h3>
                <p>Login menggunakan akun Anda yang telah terdaftar. Akses dashboard yang intuitif dan mudah digunakan.</p>
            </div>
            <div class="step" id="step-2">
                <h3>Isi Data Penerimaan</h3>
                <p>Masukkan informasi penerimaan keuangan ke dalam formulir yang telah disediakan. Data terisi otomatis untuk field berulang.</p>
            </div>
            <div class="step" id="step-3">
                <h3>Cetak & Simpan</h3>
                <p>Cetak surat bukti penerimaan dalam format profesional atau simpan sebagai arsip digital untuk keperluan di masa depan.</p>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section fade-in" id="testimonials">
        <div class="section-header">
            <span class="section-icon">❯</span>
            <span class="section-label">Testimoni</span>
        </div>
        <h2 class="section-title">Dipercaya Oleh Banyak Pengguna</h2>
        <p class="section-desc">Lihat apa yang dikatakan pengguna tentang FinReceipt.</p>

        <div class="testimonials-grid">
            <div class="testimonial-card" id="testimonial-1">
                <p class="testimonial-quote">Sangat membantu proses administrasi keuangan kami. Pembuatan bukti penerimaan yang dulunya memakan waktu lama, sekarang bisa selesai dalam hitungan menit.</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">AS</div>
                    <div>
                        <div class="testimonial-name">Andi Setiawan</div>
                        <div class="testimonial-role">Bendahara Dinas</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card" id="testimonial-2">
                <p class="testimonial-quote">Sistem yang sangat user-friendly. Tim keuangan kami tidak butuh pelatihan lama untuk menggunakannya. Sangat direkomendasikan!</p>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">RN</div>
                    <div>
                        <div class="testimonial-name">Rina Nurhayati</div>
                        <div class="testimonial-role">Staff Keuangan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section fade-in" id="cta">
        <div class="cta-box">
            <h2>Siap Mengelola Keuangan<br>Lebih Efisien?</h2>
            <p>Mulai gunakan FinReceipt sekarang dan rasakan kemudahan dalam membuat surat bukti penerimaan keuangan.</p>
            <div class="hero-actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/admin') }}" class="btn btn-primary" id="cta-dashboard-btn">
                            Buka Dashboard
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary" id="cta-login-btn">
                            Mulai Sekarang
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14m-7-7 7 7-7 7"/></svg>
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="footer">
        <p>&copy; {{ date('Y') }} <a href="/">FinReceipt</a>. Sistem Bukti Penerimaan Keuangan.</p>
    </footer>

    <!-- Scripts -->
    <script>
        // Generate random stars
        (function() {
            const starsContainer = document.getElementById('stars');
            const count = 60;
            for (let i = 0; i < count; i++) {
                const star = document.createElement('div');
                star.className = 'star';
                star.style.left = Math.random() * 100 + '%';
                star.style.top = Math.random() * 100 + '%';
                star.style.animationDelay = (Math.random() * 5) + 's';
                star.style.animationDuration = (2 + Math.random() * 4) + 's';
                star.style.width = star.style.height = (1 + Math.random() * 2) + 'px';
                starsContainer.appendChild(star);
            }
        })();

        // Scroll-triggered fade in
        (function() {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.fade-in').forEach(function(el) {
                observer.observe(el);
            });
        })();

        // Navbar shadow on scroll
        (function() {
            const nav = document.getElementById('main-nav');
            window.addEventListener('scroll', function() {
                if (window.scrollY > 20) {
                    nav.style.boxShadow = '0 4px 30px rgba(0,0,0,0.3)';
                } else {
                    nav.style.boxShadow = 'none';
                }
            });
        })();
    </script>
</body>
</html>

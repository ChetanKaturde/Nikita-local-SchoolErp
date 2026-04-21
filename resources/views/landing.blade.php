<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduNexus — Modern School Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --navy: #0B1D35;
            --navy-mid: #122647;
            --navy-light: #1a3560;
            --gold: #E8A838;
            --gold-light: #F0C060;
            --cream: #FAF6EE;
            --cream-dark: #F0EAD6;
            --slate: #8A9BB5;
            --white: #ffffff;
            --text-dark: #0B1D35;
            --text-mid: #3D5275;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cream);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        h1, h2, h3, .serif { font-family: 'DM Serif Display', serif; }

        /* ── Noise overlay ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 1000;
            opacity: 0.4;
        }

        /* ── Navbar ── */
        .navbar {
            background: rgba(11, 29, 53, 0.95);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(232, 168, 56, 0.15);
            padding: 1rem 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 900;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-mark {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'DM Serif Display', serif;
            font-size: 18px;
            color: var(--navy);
            font-weight: bold;
        }

        .brand-text {
            font-family: 'DM Serif Display', serif;
            font-size: 1.35rem;
            color: var(--white);
            letter-spacing: -0.02em;
        }

        .brand-text span { color: var(--gold); }

        .nav-link {
            color: rgba(255,255,255,0.7) !important;
            font-size: 0.88rem;
            font-weight: 500;
            letter-spacing: 0.02em;
            padding: 0.5rem 1rem !important;
            transition: color 0.2s;
        }

        .nav-link:hover { color: var(--gold) !important; }

        .btn-nav-primary {
            background: var(--gold);
            color: var(--navy);
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1.4rem;
            border-radius: 8px;
            border: none;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-block;
        }

        .btn-nav-primary:hover {
            background: var(--gold-light);
            color: var(--navy);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(232,168,56,0.4);
        }

        /* ── Hero ── */
        .hero {
            background: var(--navy);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 120px 0 80px;
        }

        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(232,168,56,0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(232,168,56,0.06) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        .hero-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            pointer-events: none;
        }

        .orb-1 {
            width: 600px; height: 600px;
            background: rgba(232,168,56,0.12);
            top: -150px; right: -100px;
        }

        .orb-2 {
            width: 400px; height: 400px;
            background: rgba(26,53,96,0.6);
            bottom: -100px; left: -100px;
        }

        .hero-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(232,168,56,0.1);
            border: 1px solid rgba(232,168,56,0.3);
            color: var(--gold);
            font-size: 0.78rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.4rem 1rem;
            border-radius: 100px;
            margin-bottom: 1.5rem;
            animation: fadeUp 0.6s ease both;
        }

        .hero h1 {
            font-size: clamp(2.8rem, 6vw, 5rem);
            line-height: 1.05;
            color: var(--white);
            letter-spacing: -0.03em;
            margin-bottom: 1.5rem;
            animation: fadeUp 0.6s 0.1s ease both;
        }

        .hero h1 em {
            font-style: italic;
            color: var(--gold);
        }

        .hero-desc {
            font-size: 1.05rem;
            color: var(--slate);
            line-height: 1.7;
            max-width: 520px;
            margin-bottom: 2.5rem;
            animation: fadeUp 0.6s 0.2s ease both;
        }

        .hero-ctas {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            animation: fadeUp 0.6s 0.3s ease both;
        }

        .btn-hero-primary {
            background: var(--gold);
            color: var(--navy);
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.85rem 2rem;
            border-radius: 10px;
            border: none;
            text-decoration: none;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-hero-primary:hover {
            background: var(--gold-light);
            color: var(--navy);
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(232,168,56,0.4);
        }

        .btn-hero-outline {
            background: transparent;
            color: var(--white);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.85rem 2rem;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.2);
            text-decoration: none;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-hero-outline:hover {
            background: rgba(255,255,255,0.08);
            color: var(--white);
            border-color: rgba(255,255,255,0.4);
        }

        /* Hero visual */
        .hero-visual {
            position: relative;
            animation: fadeUp 0.6s 0.4s ease both;
        }

        .dashboard-card {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 24px;
            backdrop-filter: blur(20px);
        }

        .dash-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .dash-avatar {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--gold), #c47a1e);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--navy);
        }

        .dash-title {
            color: var(--white);
            font-size: 0.9rem;
            font-weight: 600;
        }

        .dash-subtitle {
            color: var(--slate);
            font-size: 0.75rem;
        }

        .stat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            margin-bottom: 16px;
        }

        .stat-box {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 14px 12px;
            text-align: center;
        }

        .stat-box .num {
            font-family: 'DM Serif Display', serif;
            font-size: 1.5rem;
            color: var(--white);
            line-height: 1;
        }

        .stat-box .lbl {
            font-size: 0.68rem;
            color: var(--slate);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .stat-box.accent .num { color: var(--gold); }

        .progress-section { margin-bottom: 14px; }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
            font-size: 0.75rem;
            color: var(--slate);
        }

        .progress-bar-bg {
            background: rgba(255,255,255,0.08);
            border-radius: 100px;
            height: 6px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 100px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
            transition: width 1.5s ease;
        }

        .progress-fill.blue { background: linear-gradient(90deg, #4A90D9, #7AB8F5); }
        .progress-fill.green { background: linear-gradient(90deg, #4CAF85, #7DCCA8); }

        .floating-badge {
            position: absolute;
            background: var(--white);
            border-radius: 12px;
            padding: 10px 14px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: float 3s ease-in-out infinite;
        }

        .badge-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem;
        }

        .badge-text-main { font-size: 0.8rem; font-weight: 700; color: var(--navy); line-height: 1; }
        .badge-text-sub { font-size: 0.7rem; color: #888; }

        .badge-1 { top: -20px; right: -30px; animation-delay: 0s; }
        .badge-2 { bottom: 20px; left: -30px; animation-delay: 1.5s; }

        /* ── Stats Band ── */
        .stats-band {
            background: var(--navy);
            border-top: 1px solid rgba(232,168,56,0.15);
            border-bottom: 1px solid rgba(232,168,56,0.15);
            padding: 3rem 0;
        }

        .stat-item { text-align: center; }

        .stat-num {
            font-family: 'DM Serif Display', serif;
            font-size: 2.8rem;
            color: var(--gold);
            line-height: 1;
        }

        .stat-label {
            font-size: 0.82rem;
            color: var(--slate);
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 6px;
        }

        .divider-v {
            width: 1px;
            height: 60px;
            background: rgba(255,255,255,0.1);
            margin: 0 auto;
        }

        /* ── Section shared ── */
        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1rem;
        }

        .section-label::before {
            content: '';
            width: 24px; height: 2px;
            background: var(--gold);
            border-radius: 2px;
        }

        .section-title {
            font-size: clamp(1.9rem, 4vw, 2.8rem);
            color: var(--navy);
            letter-spacing: -0.03em;
            line-height: 1.15;
            margin-bottom: 1rem;
        }

        .section-desc {
            color: var(--text-mid);
            font-size: 1rem;
            line-height: 1.7;
        }

        /* ── Features ── */
        .features-section {
            padding: 100px 0;
            background: var(--cream);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: 3rem;
        }

        @media (max-width: 1199px) { .feature-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 575px) { .feature-grid { grid-template-columns: 1fr; } }

        .feature-card {
            background: var(--white);
            border: 1px solid var(--cream-dark);
            border-radius: 18px;
            padding: 28px 24px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 48px rgba(11,29,53,0.1);
            border-color: var(--gold-light);
        }

        .feature-card:hover::before { transform: scaleX(1); }

        .feature-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            margin-bottom: 18px;
        }

        .feature-card h4 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.05rem;
            color: var(--navy);
            margin-bottom: 10px;
            letter-spacing: -0.02em;
        }

        .feature-card p {
            font-size: 0.85rem;
            color: var(--text-mid);
            line-height: 1.65;
        }

        /* ── Portal Cards ── */
        .portals-section {
            padding: 100px 0;
            background: var(--navy);
            position: relative;
            overflow: hidden;
        }

        .portals-section .section-title { color: var(--white); }
        .portals-section .section-desc { color: var(--slate); }

        .portals-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 3rem;
        }

        @media (max-width: 767px) { .portals-grid { grid-template-columns: 1fr; } }

        .portal-card {
            border-radius: 24px;
            padding: 40px 32px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
        }

        .portal-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 24px;
            background: linear-gradient(180deg, transparent 40%, rgba(0,0,0,0.3));
            pointer-events: none;
        }

        .portal-card:hover {
            transform: translateY(-8px);
            text-decoration: none;
        }

        .portal-card.staff {
            background: linear-gradient(135deg, #1a3560, #0d2444);
            border: 1px solid rgba(232,168,56,0.2);
        }

        .portal-card.student {
            background: linear-gradient(135deg, #1a4a2e, #0d2a1a);
            border: 1px solid rgba(76,175,133,0.2);
        }

        .portal-card.admission {
            background: linear-gradient(135deg, #3a1a5e, #1e0d35);
            border: 1px solid rgba(160,90,220,0.2);
        }

        .portal-badge {
            display: inline-block;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            padding: 0.3rem 0.8rem;
            border-radius: 100px;
            margin-bottom: 1.2rem;
        }

        .portal-card.staff .portal-badge { background: rgba(232,168,56,0.2); color: var(--gold); }
        .portal-card.student .portal-badge { background: rgba(76,175,133,0.2); color: #4CAF85; }
        .portal-card.admission .portal-badge { background: rgba(160,90,220,0.2); color: #B078E0; }

        .portal-icon {
            width: 64px; height: 64px;
            border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
        }

        .portal-card.staff .portal-icon { background: rgba(232,168,56,0.15); }
        .portal-card.student .portal-icon { background: rgba(76,175,133,0.15); }
        .portal-card.admission .portal-icon { background: rgba(160,90,220,0.15); }

        .portal-card h3 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.5rem;
            color: var(--white);
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .portal-card p {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.6);
            line-height: 1.65;
            margin-bottom: 1.8rem;
        }

        .portal-arrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            transition: gap 0.2s;
        }

        .portal-card.staff .portal-arrow { color: var(--gold); }
        .portal-card.student .portal-arrow { color: #4CAF85; }
        .portal-card.admission .portal-arrow { color: #B078E0; }

        .portal-card:hover .portal-arrow { gap: 14px; }

        /* ── About ── */
        .about-section {
            padding: 100px 0;
            background: var(--cream);
        }

        .about-card {
            background: var(--navy);
            border-radius: 24px;
            padding: 48px;
            color: var(--white);
            position: relative;
            overflow: hidden;
        }

        .about-card::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(232,168,56,0.06);
        }

        .check-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }

        .check-item:last-child { border-bottom: none; }

        .check-icon {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(232,168,56,0.15);
            border: 1px solid rgba(232,168,56,0.3);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            color: var(--gold);
            font-size: 0.75rem;
        }

        .check-text { font-size: 0.9rem; color: rgba(255,255,255,0.8); }

        .contact-box {
            background: var(--white);
            border-radius: 24px;
            padding: 40px;
            border: 1px solid var(--cream-dark);
            height: 100%;
        }

        .contact-box h3 {
            font-family: 'DM Serif Display', serif;
            font-size: 1.5rem;
            color: var(--navy);
            margin-bottom: 1.5rem;
        }

        .contact-row {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 0;
            border-bottom: 1px solid var(--cream-dark);
        }

        .contact-row:last-of-type { border-bottom: none; }

        .contact-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: #EFF4FC;
            display: flex; align-items: center; justify-content: center;
            color: var(--navy-light);
            font-size: 0.95rem;
            flex-shrink: 0;
        }

        .contact-detail .label { font-size: 0.72rem; color: var(--slate); text-transform: uppercase; letter-spacing: 0.06em; }
        .contact-detail .value { font-size: 0.9rem; color: var(--navy); font-weight: 500; }

        .btn-full {
            display: block;
            width: 100%;
            background: var(--navy);
            color: var(--white);
            text-align: center;
            font-weight: 700;
            font-size: 0.9rem;
            padding: 1rem;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.25s;
            margin-top: 1.8rem;
            border: none;
        }

        .btn-full:hover {
            background: var(--navy-light);
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(11,29,53,0.25);
        }

        .btn-full.gold {
            background: var(--gold);
            color: var(--navy);
        }

        .btn-full.gold:hover {
            background: var(--gold-light);
            box-shadow: 0 10px 30px rgba(232,168,56,0.35);
        }

        /* ── Footer ── */
        footer {
            background: #070F1E;
            padding: 60px 0 30px;
            border-top: 1px solid rgba(232,168,56,0.1);
        }

        .footer-brand { margin-bottom: 1rem; }

        .footer-tagline {
            font-size: 0.85rem;
            color: var(--slate);
            line-height: 1.6;
            max-width: 260px;
        }

        .footer-heading {
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 1.2rem;
        }

        .footer-link {
            display: block;
            font-size: 0.88rem;
            color: var(--slate);
            text-decoration: none;
            padding: 5px 0;
            transition: color 0.2s;
        }

        .footer-link:hover { color: var(--white); }

        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.07);
            margin-top: 48px;
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
        }

        .footer-copy { font-size: 0.8rem; color: var(--slate); }

        /* ── Animations ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-10px); }
        }

        /* ── Scroll reveal ── */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* ── Utility ── */
        .bg-icon-1 { background: rgba(232,168,56,0.1); color: var(--gold); }
        .bg-icon-2 { background: rgba(76,175,133,0.1); color: #4CAF85; }
        .bg-icon-3 { background: rgba(74,144,217,0.1); color: #4A90D9; }
        .bg-icon-4 { background: rgba(255,100,100,0.1); color: #FF6464; }
        .bg-icon-5 { background: rgba(160,90,220,0.1); color: #A05ADC; }
        .bg-icon-6 { background: rgba(255,165,0,0.1); color: orange; }
        .bg-icon-7 { background: rgba(0,188,188,0.1); color: #00BCBC; }
        .bg-icon-8 { background: rgba(240,80,140,0.1); color: #F0508C; }

        @media (max-width: 991px) {
            .hero-visual { margin-top: 3rem; }
        }
    </style>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="#">
            <div class="brand-mark">E</div>
            <span class="brand-text">Edu<span>Nexus</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <i class="bi bi-list text-white fs-5"></i>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-center gap-1">
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="#portals">Portals</a></li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item ms-2"><a class="nav-link" href="{{ route('login') }}">Staff Login</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('student.login') }}">Student Login</a></li>
                <li class="nav-item ms-2">
                    <a class="btn-nav-primary" href="{{ route('admissions.apply.form') }}">Apply Now</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- ── HERO ── -->
<section class="hero">
    <div class="hero-grid"></div>
    <div class="hero-orb orb-1"></div>
    <div class="hero-orb orb-2"></div>
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="hero-label">
                    <i class="bi bi-patch-check-fill"></i>
                    Trusted School Management Platform
                </div>
                <h1>The <em>Smart</em> Way to Run Your School</h1>
                <p class="hero-desc">EduNexus brings attendance, timetables, fees, exams, and communications into one seamless, beautifully designed platform — so educators can focus on what truly matters.</p>
                <div class="hero-ctas">
                    <a href="{{ route('login') }}" class="btn-hero-primary">
                        <i class="bi bi-arrow-right-circle-fill"></i> Staff Login
                    </a>
                    <a href="{{ route('admissions.apply.form') }}" class="btn-hero-outline">
                        <i class="bi bi-file-earmark-text"></i> Apply for Admission
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-visual">
                    <div class="dashboard-card">
                        <div class="dash-header">
                            <div>
                                <div class="dash-title">Principal Dashboard</div>
                                <div class="dash-subtitle">Academic Year 2024–25</div>
                            </div>
                            <div class="dash-avatar">AK</div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-box accent">
                                <div class="num">1,248</div>
                                <div class="lbl">Students</div>
                            </div>
                            <div class="stat-box">
                                <div class="num">86</div>
                                <div class="lbl">Staff</div>
                            </div>
                            <div class="stat-box">
                                <div class="num">94%</div>
                                <div class="lbl">Attendance</div>
                            </div>
                        </div>
                        <div class="progress-section">
                            <div class="progress-label">
                                <span>Fee Collection</span><span style="color:var(--gold)">78%</span>
                            </div>
                            <div class="progress-bar-bg"><div class="progress-fill" style="width:78%"></div></div>
                        </div>
                        <div class="progress-section">
                            <div class="progress-label">
                                <span>Exam Completion</span><span style="color:#4A90D9">62%</span>
                            </div>
                            <div class="progress-bar-bg"><div class="progress-fill blue" style="width:62%"></div></div>
                        </div>
                        <div class="progress-section">
                            <div class="progress-label">
                                <span>Library Books Returned</span><span style="color:#4CAF85">91%</span>
                            </div>
                            <div class="progress-bar-bg"><div class="progress-fill green" style="width:91%"></div></div>
                        </div>
                    </div>
                    <!-- Floating badges -->
                    <div class="floating-badge badge-1">
                        <div class="badge-icon" style="background:#FFF3CD;">🎓</div>
                        <div>
                            <div class="badge-text-main">Exam Results Published</div>
                            <div class="badge-text-sub">Class X — Just now</div>
                        </div>
                    </div>
                    <div class="floating-badge badge-2">
                        <div class="badge-icon" style="background:#D4EDDA;">✅</div>
                        <div>
                            <div class="badge-text-main">Attendance Marked</div>
                            <div class="badge-text-sub">All sections complete</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── STATS BAND ── -->
<div class="stats-band">
    <div class="container">
        <div class="row text-center">
            <div class="col-6 col-md-3 stat-item reveal">
                <div class="stat-num">500+</div>
                <div class="stat-label">Schools Onboarded</div>
            </div>
            <div class="col-6 col-md-3 stat-item reveal reveal-delay-1">
                <div class="stat-num">2M+</div>
                <div class="stat-label">Students Managed</div>
            </div>
            <div class="col-6 col-md-3 stat-item reveal reveal-delay-2">
                <div class="stat-num">99.9%</div>
                <div class="stat-label">Platform Uptime</div>
            </div>
            <div class="col-6 col-md-3 stat-item reveal reveal-delay-3">
                <div class="stat-num">4.9★</div>
                <div class="stat-label">Average Rating</div>
            </div>
        </div>
    </div>
</div>

<!-- ── FEATURES ── -->
<section class="features-section" id="features">
    <div class="container">
        <div class="text-center mb-2 reveal">
            <div class="section-label">Platform Capabilities</div>
            <h2 class="section-title">Every Tool Your School Needs</h2>
            <p class="section-desc" style="max-width:500px;margin:0 auto;">A unified system handling every administrative and academic workflow under one roof.</p>
        </div>
        <div class="feature-grid">
            <div class="feature-card reveal">
                <div class="feature-icon bg-icon-1"><i class="bi bi-calendar2-check"></i></div>
                <h4>Attendance Management</h4>
                <p>Daily attendance tracking for students and staff with one-click marking, real-time reports, and SMS/email alerts to parents.</p>
            </div>
            <div class="feature-card reveal reveal-delay-1">
                <div class="feature-icon bg-icon-2"><i class="bi bi-grid-3x3-gap"></i></div>
                <h4>Timetable Management</h4>
                <p>Intelligent class scheduling with automatic conflict detection, teacher allocation, and instant publishing to all portals.</p>
            </div>
            <div class="feature-card reveal reveal-delay-2">
                <div class="feature-icon bg-icon-3"><i class="bi bi-wallet2"></i></div>
                <h4>Fee Management</h4>
                <p>Flexible fee structures, installment plans, online payment gateway, automated receipts, and defaulter notifications.</p>
            </div>
            <div class="feature-card reveal reveal-delay-3">
                <div class="feature-icon bg-icon-4"><i class="bi bi-bar-chart-line"></i></div>
                <h4>Exam & Results</h4>
                <p>Configure examinations, enter marks, auto-calculate grades, generate report cards, and share results securely online.</p>
            </div>
            <div class="feature-card reveal">
                <div class="feature-icon bg-icon-5"><i class="bi bi-people"></i></div>
                <h4>Student Management</h4>
                <p>Comprehensive student profiles with academic history, documents, parent contacts, and progression tracking.</p>
            </div>
            <div class="feature-card reveal reveal-delay-1">
                <div class="feature-icon bg-icon-6"><i class="bi bi-person-badge"></i></div>
                <h4>Staff Management</h4>
                <p>Staff profiles, leave management, payroll integration, and role-based access for teachers, office staff, and principals.</p>
            </div>
            <div class="feature-card reveal reveal-delay-2">
                <div class="feature-icon bg-icon-7"><i class="bi bi-book-half"></i></div>
                <h4>Library Management</h4>
                <p>Book catalogue, issue and return tracking, fine calculation, and automated overdue reminders for students.</p>
            </div>
            <div class="feature-card reveal reveal-delay-3">
                <div class="feature-icon bg-icon-8"><i class="bi bi-chat-dots"></i></div>
                <h4>Communication Hub</h4>
                <p>Circular announcements, push notifications, parent-teacher messaging, and event broadcasts all from one place.</p>
            </div>
        </div>
    </div>
</section>

<!-- ── PORTALS ── -->
<section class="portals-section" id="portals">
    <div class="container">
        <div class="text-center mb-2 reveal">
            <div class="section-label">Access Portals</div>
            <h2 class="section-title">Your Role, Your Portal</h2>
            <p class="section-desc" style="max-width:480px;margin:0 auto;">Tailored dashboards for every stakeholder — fast, focused, and frictionless.</p>
        </div>
        <div class="portals-grid">
            <a href="{{ route('login') }}" class="portal-card staff reveal">
                <div class="portal-badge">Staff & Admin</div>
                <div class="portal-icon"><i class="bi bi-person-workspace" style="color:var(--gold)"></i></div>
                <h3>Staff Portal</h3>
                <p>For principals, teachers, office staff, and administrators. Full access to school operations, academic management, and reporting.</p>
                <div class="portal-arrow">Access Portal <i class="bi bi-arrow-right"></i></div>
            </a>
            <a href="{{ route('student.login') }}" class="portal-card student reveal reveal-delay-1">
                <div class="portal-badge">Students</div>
                <div class="portal-icon"><i class="bi bi-mortarboard" style="color:#4CAF85"></i></div>
                <h3>Student Portal</h3>
                <p>Students can check attendance, view timetables, download results, track fee dues, and access the digital library catalogue.</p>
                <div class="portal-arrow">Access Portal <i class="bi bi-arrow-right"></i></div>
            </a>
            <a href="{{ route('admissions.apply.form') }}" class="portal-card admission reveal reveal-delay-2">
                <div class="portal-badge">New Students</div>
                <div class="portal-icon"><i class="bi bi-file-earmark-person" style="color:#B078E0"></i></div>
                <h3>Admissions</h3>
                <p>Apply online for the upcoming academic year. Submit documents, track application status, and receive admission decisions digitally.</p>
                <div class="portal-arrow">Apply Now <i class="bi bi-arrow-right"></i></div>
            </a>
        </div>
    </div>
</section>

<!-- ── ABOUT + CONTACT ── -->
<section class="about-section" id="about">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-7 reveal">
                <div class="about-card h-100">
                    <div class="section-label">About Our School</div>
                    <h2 style="font-family:'DM Serif Display',serif;font-size:2rem;color:var(--white);letter-spacing:-0.03em;margin-bottom:1rem;">Education Reimagined with Modern Technology</h2>
                    <p style="font-size:0.93rem;color:var(--slate);line-height:1.75;margin-bottom:2rem;">We believe every administrator's time is better spent on students than on paperwork. EduNexus handles the operational complexity so that educators can dedicate themselves fully to nurturing the next generation.</p>
                    <div class="check-item">
                        <div class="check-icon"><i class="bi bi-check"></i></div>
                        <span class="check-text">Efficient attendance tracking with parent instant alerts</span>
                    </div>
                    <div class="check-item">
                        <div class="check-icon"><i class="bi bi-check"></i></div>
                        <span class="check-text">Secure online fee payments with automated reconciliation</span>
                    </div>
                    <div class="check-item">
                        <div class="check-icon"><i class="bi bi-check"></i></div>
                        <span class="check-text">Real-time academic updates accessible from any device</span>
                    </div>
                    <div class="check-item">
                        <div class="check-icon"><i class="bi bi-check"></i></div>
                        <span class="check-text">Seamless parent-teacher communication and engagement</span>
                    </div>
                    <div class="check-item">
                        <div class="check-icon"><i class="bi bi-check"></i></div>
                        <span class="check-text">Role-based access controls for complete data security</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 reveal reveal-delay-2">
                <div class="contact-box h-100">
                    <h3>Get in Touch</h3>
                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div class="contact-detail">
                            <div class="label">Address</div>
                            <div class="value">123 School Road, Education City</div>
                        </div>
                    </div>
                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div class="contact-detail">
                            <div class="label">Email</div>
                            <div class="value">info@schoolerp.com</div>
                        </div>
                    </div>
                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div class="contact-detail">
                            <div class="label">Phone</div>
                            <div class="value">+91 1234567890</div>
                        </div>
                    </div>
                    <div class="contact-row">
                        <div class="contact-icon"><i class="bi bi-clock-fill"></i></div>
                        <div class="contact-detail">
                            <div class="label">Office Hours</div>
                            <div class="value">Mon – Sat, 8:00 AM – 4:00 PM</div>
                        </div>
                    </div>
                    <a href="{{ route('admissions.apply.form') }}" class="btn-full gold">
                        <i class="bi bi-pencil-square me-2"></i>Apply for Admission
                    </a>
                    <a href="{{ route('login') }}" class="btn-full mt-2" style="background:var(--navy-light)">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Staff Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── FOOTER ── -->
<footer>
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="footer-brand d-flex align-items-center gap-2">
                    <div class="brand-mark">E</div>
                    <span class="brand-text">Edu<span>Nexus</span></span>
                </div>
                <p class="footer-tagline">Empowering schools with modern technology — from attendance to admissions, all in one place.</p>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="footer-heading">Platform</div>
                <a href="#features" class="footer-link">Features</a>
                <a href="#portals" class="footer-link">Portals</a>
                <a href="#about" class="footer-link">About</a>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="footer-heading">Quick Access</div>
                <a href="{{ route('login') }}" class="footer-link">Staff Login</a>
                <a href="{{ route('student.login') }}" class="footer-link">Student Portal</a>
                <a href="{{ route('admissions.apply.form') }}" class="footer-link">Admissions</a>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="footer-heading">Contact</div>
                <p class="footer-tagline" style="max-width:100%">
                    123 School Road, Education City<br>
                    info@schoolerp.com · +91 1234567890
                </p>
            </div>
        </div>
        <div class="footer-bottom">
            <span class="footer-copy">&copy; 2025 EduNexus. All rights reserved.</span>
            <span class="footer-copy">Designed and Developed By Lemmecode</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Scroll reveal
    const reveals = document.querySelectorAll('.reveal');
    const io = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('visible'); io.unobserve(e.target); }
        });
    }, { threshold: 0.12 });
    reveals.forEach(el => io.observe(el));

    // Navbar scroll effect
    window.addEventListener('scroll', () => {
        const nav = document.querySelector('.navbar');
        nav.style.boxShadow = window.scrollY > 20 ? '0 4px 30px rgba(0,0,0,0.4)' : 'none';
    });

    // Animate progress bars on scroll
    const progressFills = document.querySelectorAll('.progress-fill');
    const progressIO = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                const target = e.target.style.width;
                e.target.style.width = '0';
                setTimeout(() => e.target.style.width = target, 100);
            }
        });
    }, { threshold: 0.5 });
    progressFills.forEach(el => progressIO.observe(el));
</script>
</body>
</html>
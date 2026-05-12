<?php
require_once "include/db_config.php";
$sql = "SELECT * FROM system_config WHERE id =1";
$system_conf = mysqli_query($GLOBALS["___mysqli_ston"],$sql);
$row = mysqli_fetch_array($system_conf);
$title_bar = $row["title_bar"];
$icon_bar = $row["icon_bar"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="assets/img/system_data/favicon.ico">
  <title><?php echo $title_bar; ?> - Registrasi Kartu</title>
  
  <!-- Google Fonts - Unique Typography -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
  <link href="assets/css/Material_icon.css" rel="stylesheet">
  
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --primary: #00d4ff;
      --secondary: #7c3aed;
      --accent: #ec4899;
      --success: #22c55e;
      --warning: #f59e0b;
      --danger: #ef4444;
      --dark: #0f172a;
      --gray: #64748b;
      --light: #f8fafc;
      --white: #ffffff;
    }

    #rfid-reg-app {
      font-family: 'Poppins', sans-serif;
      background: #0f172a;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      overflow: hidden;
    }

    /* Animated Gradient Mesh Background */
    #rfid-reg-app::before {
      content: '';
      position: absolute;
      width: 150%;
      height: 150%;
      background: 
        radial-gradient(circle at 20% 30%, rgba(0, 212, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(124, 58, 237, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 50% 50%, rgba(236, 72, 153, 0.1) 0%, transparent 50%);
      animation: meshMove 20s ease-in-out infinite;
      pointer-events: none;
    }

    @keyframes meshMove {
      0%, 100% { transform: translate(0, 0) rotate(0deg); }
      33% { transform: translate(-50px, 50px) rotate(5deg); }
      66% { transform: translate(50px, -50px) rotate(-5deg); }
    }

    /* Floating Particles */
    .particle {
      position: absolute;
      width: 4px;
      height: 4px;
      background: var(--primary);
      border-radius: 50%;
      opacity: 0.3;
      animation: float 15s infinite ease-in-out;
    }

    .particle:nth-child(1) { top: 10%; left: 20%; animation-delay: 0s; }
    .particle:nth-child(2) { top: 60%; left: 80%; animation-delay: 2s; }
    .particle:nth-child(3) { top: 30%; left: 50%; animation-delay: 4s; }
    .particle:nth-child(4) { top: 80%; left: 30%; animation-delay: 6s; }
    .particle:nth-child(5) { top: 40%; left: 70%; animation-delay: 8s; }

    @keyframes float {
      0%, 100% { transform: translateY(0) translateX(0); opacity: 0.3; }
      25% { transform: translateY(-80px) translateX(40px); opacity: 0.6; }
      50% { transform: translateY(-160px) translateX(-40px); opacity: 0.3; }
      75% { transform: translateY(-80px) translateX(40px); opacity: 0.6; }
    }

    .rfid-container {
      width: 100%;
      max-width: 550px;
      position: relative;
      z-index: 10;
    }

    /* Glassmorphism Card with 3D Effect */
    .rfid-card {
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(20px) saturate(180%);
      -webkit-backdrop-filter: blur(20px) saturate(180%);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 32px;
      box-shadow: 
        0 8px 32px 0 rgba(0, 0, 0, 0.37),
        inset 0 1px 0 0 rgba(255, 255, 255, 0.1);
      overflow: hidden;
      animation: cardAppear 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
      position: relative;
    }

    @keyframes cardAppear {
      from {
        opacity: 0;
        transform: translateY(50px) rotateX(10deg);
      }
      to {
        opacity: 1;
        transform: translateY(0) rotateX(0);
      }
    }

    /* Glowing Border Animation */
    .rfid-card::before {
      content: '';
      position: absolute;
      top: -2px;
      left: -2px;
      right: -2px;
      bottom: -2px;
      background: linear-gradient(45deg, var(--primary), var(--secondary), var(--accent), var(--primary));
      border-radius: 32px;
      opacity: 0;
      z-index: -1;
      background-size: 400% 400%;
      animation: gradientShift 8s ease infinite;
      transition: opacity 0.3s;
    }

    .rfid-card:hover::before {
      opacity: 0.3;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .rfid-header {
      background: linear-gradient(135deg, rgba(0, 212, 255, 0.2) 0%, rgba(124, 58, 237, 0.2) 100%);
      backdrop-filter: blur(10px);
      padding: 40px 35px;
      position: relative;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .rfid-header-glow {
      position: absolute;
      top: -50%;
      left: 50%;
      transform: translateX(-50%);
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(0, 212, 255, 0.4), transparent);
      filter: blur(60px);
      animation: glowPulse 4s ease-in-out infinite;
    }

    @keyframes glowPulse {
      0%, 100% { opacity: 0.5; transform: translateX(-50%) scale(1); }
      50% { opacity: 0.8; transform: translateX(-50%) scale(1.2); }
    }

    .rfid-header h1 {
      color: var(--white);
      font-size: 32px;
      font-weight: 800;
      margin: 0 0 8px 0;
      position: relative;
      z-index: 1;
      background: linear-gradient(135deg, var(--primary), var(--white), var(--accent));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      letter-spacing: -0.5px;
    }

    .rfid-header p {
      color: rgba(255, 255, 255, 0.7);
      font-size: 15px;
      margin: 0;
      font-weight: 400;
      position: relative;
      z-index: 1;
    }

    .rfid-body {
      padding: 45px 35px;
      position: relative;
    }

    /* NFC Scanning - Premium Animation */
    .rfid-nfc-wrapper {
      text-align: center;
      padding: 30px 0;
    }

    .rfid-nfc-icon-container {
      width: 140px;
      height: 140px;
      margin: 0 auto 35px;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* 3D Rotating Ring */
    .rfid-nfc-ring {
      position: absolute;
      width: 100%;
      height: 100%;
      border: 3px solid transparent;
      border-top-color: var(--primary);
      border-right-color: var(--primary);
      border-radius: 50%;
      animation: rotate3D 3s linear infinite;
    }

    @keyframes rotate3D {
      0% { transform: rotateY(0deg) rotateX(20deg); }
      100% { transform: rotateY(360deg) rotateX(20deg); }
    }

    .rfid-nfc-ring:nth-child(2) {
      border-top-color: var(--accent);
      border-right-color: var(--accent);
      animation-delay: 1s;
      animation-duration: 2s;
    }

    /* Pulse Waves */
    .rfid-nfc-pulse {
      position: absolute;
      width: 60px;
      height: 60px;
      border: 3px solid var(--primary);
      border-radius: 50%;
      animation: pulseWave 2.5s ease-out infinite;
    }

    .rfid-nfc-pulse:nth-child(3) { animation-delay: 0.5s; }
    .rfid-nfc-pulse:nth-child(4) { animation-delay: 1s; }
    .rfid-nfc-pulse:nth-child(5) { animation-delay: 1.5s; }

    @keyframes pulseWave {
      0% {
        width: 60px;
        height: 60px;
        opacity: 1;
      }
      100% {
        width: 160px;
        height: 160px;
        opacity: 0;
      }
    }

    .rfid-nfc-icon {
      width: 70px;
      height: 70px;
      position: relative;
      z-index: 5;
      filter: drop-shadow(0 0 20px rgba(0, 212, 255, 0.5));
    }

    .rfid-nfc-icon svg {
      width: 100%;
      height: 100%;
      fill: var(--primary);
    }

    .rfid-scan-title {
      font-size: 28px;
      font-weight: 700;
      color: var(--white);
      margin-bottom: 12px;
      letter-spacing: -0.5px;
    }

    .rfid-scan-desc {
      color: rgba(255, 255, 255, 0.6);
      font-size: 15px;
      margin-bottom: 25px;
      line-height: 1.6;
    }

    /* Modern Buttons */
    .rfid-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      padding: 14px 28px;
      border: none;
      border-radius: 14px;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      font-family: 'Poppins', sans-serif;
      text-decoration: none;
      position: relative;
      overflow: hidden;
    }

    .rfid-btn::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: width 0.5s, height 0.5s;
    }

    .rfid-btn:active::after {
      width: 300px;
      height: 300px;
    }

    .rfid-btn-primary {
      background: linear-gradient(135deg, var(--primary), #0ea5e9);
      color: var(--dark);
      box-shadow: 0 4px 20px rgba(0, 212, 255, 0.4);
      font-weight: 700;
    }

    .rfid-btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(0, 212, 255, 0.6);
    }

    .rfid-btn-secondary {
      background: rgba(255, 255, 255, 0.05);
      color: var(--white);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .rfid-btn-secondary:hover {
      background: rgba(255, 255, 255, 0.1);
      border-color: rgba(255, 255, 255, 0.2);
    }

    .rfid-btn-success {
      background: linear-gradient(135deg, var(--success), #16a34a);
      color: var(--white);
      box-shadow: 0 4px 20px rgba(34, 197, 94, 0.4);
      font-weight: 700;
    }

    .rfid-btn-success:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(34, 197, 94, 0.6);
    }

    .rfid-btn-glass {
      background: rgba(0, 212, 255, 0.1);
      color: var(--primary);
      border: 1px solid rgba(0, 212, 255, 0.3);
      backdrop-filter: blur(10px);
    }

    .rfid-btn-glass:hover {
      background: rgba(0, 212, 255, 0.2);
      border-color: rgba(0, 212, 255, 0.5);
    }

    .rfid-btn-link {
      background: transparent;
      color: var(--primary);
      padding: 12px 20px;
    }

    .rfid-btn-link:hover {
      background: rgba(0, 212, 255, 0.1);
    }

    .rfid-btn-full {
      width: 100%;
    }

    /* UID Badge - Futuristic */
    .rfid-uid-badge {
      display: inline-block;
      background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(124, 58, 237, 0.2));
      border: 1px solid rgba(0, 212, 255, 0.3);
      color: var(--primary);
      padding: 10px 20px;
      border-radius: 12px;
      font-family: 'JetBrains Mono', monospace;
      font-size: 15px;
      font-weight: 700;
      letter-spacing: 2px;
      box-shadow: 0 0 20px rgba(0, 212, 255, 0.3);
      position: relative;
      overflow: hidden;
    }

    .rfid-uid-badge::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
      animation: shimmer 3s infinite;
    }

    @keyframes shimmer {
      to { left: 100%; }
    }

    .rfid-uid-old {
      background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2));
      border-color: rgba(239, 68, 68, 0.4);
      color: var(--danger);
      box-shadow: 0 0 20px rgba(239, 68, 68, 0.3);
    }

    .rfid-uid-new {
      background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(22, 163, 74, 0.2));
      border-color: rgba(34, 197, 94, 0.4);
      color: var(--success);
      box-shadow: 0 0 20px rgba(34, 197, 94, 0.3);
    }

    /* Choice Grid - 3D Cards */
    .rfid-choice-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 25px;
    }

    .rfid-choice-card {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 20px;
      padding: 35px 25px;
      cursor: pointer;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .rfid-choice-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: linear-gradient(135deg, rgba(0, 212, 255, 0.1), rgba(236, 72, 153, 0.1));
      opacity: 0;
      transition: opacity 0.4s;
    }

    .rfid-choice-card:hover {
      border-color: rgba(0, 212, 255, 0.5);
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 12px 40px rgba(0, 212, 255, 0.3);
    }

    .rfid-choice-card:hover::before {
      opacity: 1;
    }

    .rfid-choice-icon {
      width: 60px;
      height: 60px;
      margin: 0 auto 16px;
      background: linear-gradient(135deg, var(--primary), var(--accent));
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--white);
      font-size: 28px;
      box-shadow: 0 8px 20px rgba(0, 212, 255, 0.4);
      transition: transform 0.4s;
      position: relative;
      z-index: 1;
    }

    .rfid-choice-card:hover .rfid-choice-icon {
      transform: rotateY(360deg);
    }

    .rfid-choice-card h3 {
      font-size: 17px;
      font-weight: 600;
      color: var(--white);
      margin: 0;
      position: relative;
      z-index: 1;
    }

    /* Info Box - Glass Panel */
    .rfid-info-panel {
      background: rgba(255, 255, 255, 0.03);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 20px;
      padding: 25px;
      margin-bottom: 25px;
    }

    .rfid-info-panel.success {
      background: rgba(34, 197, 94, 0.05);
      border-color: rgba(34, 197, 94, 0.2);
    }

    .rfid-info-panel h3 {
      font-size: 17px;
      font-weight: 700;
      color: var(--white);
      margin: 0 0 18px 0;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .rfid-info-panel table {
      width: 100%;
      border-collapse: collapse;
    }

    .rfid-info-panel table td {
      padding: 10px 0;
      font-size: 14px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .rfid-info-panel table tr:last-child td {
      border-bottom: none;
    }

    .rfid-info-panel table td:first-child {
      color: rgba(255, 255, 255, 0.5);
      font-weight: 500;
      width: 40%;
    }

    .rfid-info-panel table td:last-child {
      color: var(--white);
      font-weight: 600;
    }

    /* Form Controls - Modern */
    .rfid-form-group {
      margin-bottom: 22px;
    }

    .rfid-form-label {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: var(--white);
      margin-bottom: 10px;
    }

    .rfid-form-input {
      width: 100%;
      padding: 15px 18px;
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 14px;
      font-size: 15px;
      font-family: 'Poppins', sans-serif;
      color: var(--white);
      transition: all 0.3s;
    }

    .rfid-form-input:focus {
      outline: none;
      border-color: var(--primary);
      background: rgba(255, 255, 255, 0.08);
      box-shadow: 0 0 0 4px rgba(0, 212, 255, 0.1);
    }

    .rfid-form-input::placeholder {
      color: rgba(255, 255, 255, 0.3);
    }

    /* Radio Buttons - Modern Toggle */
    .rfid-radio-group {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-bottom: 28px;
    }

    .rfid-radio-wrapper {
      position: relative;
    }

    .rfid-radio-wrapper input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    }

    .rfid-radio-wrapper label {
      display: inline-block;
      padding: 12px 28px;
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 12px;
      cursor: pointer;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.5);
      transition: all 0.3s;
    }

    .rfid-radio-wrapper input:checked + label {
      background: linear-gradient(135deg, var(--primary), #0ea5e9);
      border-color: var(--primary);
      color: var(--dark);
      box-shadow: 0 4px 15px rgba(0, 212, 255, 0.4);
    }

    /* Search Results - Sleek List */
    .rfid-search-container {
      max-height: 340px;
      overflow-y: auto;
      margin-bottom: 22px;
      border-radius: 16px;
      background: rgba(255, 255, 255, 0.02);
      border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .rfid-search-container::-webkit-scrollbar {
      width: 6px;
    }

    .rfid-search-container::-webkit-scrollbar-track {
      background: rgba(255, 255, 255, 0.02);
      border-radius: 10px;
    }

    .rfid-search-container::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 10px;
    }

    .rfid-search-item {
      padding: 18px 22px;
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
      cursor: pointer;
      transition: all 0.2s;
    }

    .rfid-search-item:last-child {
      border-bottom: none;
    }

    .rfid-search-item:hover {
      background: rgba(0, 212, 255, 0.08);
      padding-left: 28px;
    }

    .rfid-search-item h4 {
      font-size: 16px;
      font-weight: 600;
      color: var(--white);
      margin: 0 0 8px 0;
    }

    .rfid-search-item p {
      font-size: 13px;
      color: rgba(255, 255, 255, 0.5);
      margin: 0 0 6px 0;
    }

    .rfid-search-item .rfid-uid-info {
      font-size: 12px;
      font-family: 'JetBrains Mono', monospace;
      font-weight: 600;
      margin-top: 8px;
    }

    .rfid-search-item .rfid-uid-info.has-card {
      color: var(--danger);
    }

    .rfid-search-item .rfid-uid-info.no-card {
      color: rgba(255, 255, 255, 0.3);
    }

    /* Alert - Glass Style */
    .rfid-alert {
      padding: 18px 22px;
      border-radius: 14px;
      margin-bottom: 22px;
      display: flex;
      align-items: center;
      gap: 14px;
      font-size: 14px;
      backdrop-filter: blur(10px);
    }

    .rfid-alert-info {
      background: rgba(0, 212, 255, 0.1);
      color: var(--primary);
      border: 1px solid rgba(0, 212, 255, 0.3);
    }

    /* Footer - Minimalist */
    .rfid-footer {
      padding: 22px 35px;
      background: rgba(255, 255, 255, 0.02);
      text-align: center;
      border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .rfid-footer a {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
      font-size: 14px;
      transition: all 0.3s;
    }

    .rfid-footer a:hover {
      gap: 12px;
      color: var(--white);
    }

    /* Utilities */
    .rfid-text-center { text-align: center; }
    .rfid-mb-25 { margin-bottom: 25px; }
    .rfid-mb-15 { margin-bottom: 15px; }
    .rfid-mb-12 { margin-bottom: 12px; }

    .rfid-title-lg {
      font-size: 26px;
      font-weight: 700;
      color: var(--white);
      letter-spacing: -0.5px;
    }

    /* Fade Animation */
    .rfid-fade-in {
      animation: fadeSlide 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes fadeSlide {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Responsive */
    @media (max-width: 576px) {
      .rfid-header {
        padding: 30px 25px;
      }

      .rfid-body {
        padding: 35px 25px;
      }

      .rfid-choice-grid {
        grid-template-columns: 1fr;
      }

      .rfid-btn {
        padding: 12px 22px;
        font-size: 14px;
      }

      .rfid-header h1 {
        font-size: 26px;
      }
    }
  </style>
</head>

<body id="rfid-reg-app">
  <!-- Floating Particles -->
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>
  <div class="particle"></div>

  <div class="rfid-container">
    <div class="rfid-card">
      <div class="rfid-header">
        <div class="rfid-header-glow"></div>
        <h1>🎴 Registrasi Kartu RFID</h1>
        <p>Tempelkan kartu pada perangkat reader</p>
      </div>

      <div class="rfid-body">
        
        <!-- STEP 1: SCANNING -->
        <div id="step-scan">
          <div class="rfid-nfc-wrapper">
            <div class="rfid-nfc-icon-container">
              <div class="rfid-nfc-ring"></div>
              <div class="rfid-nfc-ring"></div>
              <div class="rfid-nfc-pulse"></div>
              <div class="rfid-nfc-pulse"></div>
              <div class="rfid-nfc-pulse"></div>
              <div class="rfid-nfc-icon">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M20 2H4c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 18H4V4h16v16zM18 6h-5c-1.1 0-2 .9-2 2v2.28c-.6.35-1 .98-1 1.72 0 1.1.9 2 2 2s2-.9 2-2c0-.74-.4-1.37-1-1.72V8h3v8H8V8h2V6H6v12h12V6z"/>
                </svg>
              </div>
            </div>
            <h2 class="rfid-scan-title">Menunggu Kartu...</h2>
            <p class="rfid-scan-desc">Silahkan tap kartu RFID pada reader untuk memulai</p>
            <button class="rfid-btn rfid-btn-glass" onclick="refreshScan()">
              <span class="material-icons"></span>
              Refresh Scan
            </button>
          </div>
        </div>

        <!-- STEP 2: CARD DETECTED -->
        <div id="step-card-detected" style="display:none;" class="rfid-fade-in">
          <div class="rfid-text-center rfid-mb-25">
            <h2 class="rfid-title-lg rfid-mb-15">
              <span class="material-icons" style="color: var(--success); font-size: 32px; vertical-align: middle;"></span>
              Kartu Terdeteksi!
            </h2>
            <span class="rfid-uid-badge" id="display-uid">UID: -</span>
          </div>
          
          <!-- Existing Card Data -->
          <div id="existing-card-data" style="display:none;">
            <div class="rfid-info-panel success">
              <h3>
                <span class="material-icons" style="color: var(--success);">badge</span>
                Kartu Sudah Terdaftar
              </h3>
              <table>
                <tr>
                  <td>Nama</td>
                  <td id="card-nama">-</td>
                </tr>
                <tr>
                  <td>Nomor Induk</td>
                  <td id="card-nomor">-</td>
                </tr>
                <tr>
                  <td>Role</td>
                  <td id="card-role">-</td>
                </tr>
                <tr>
                  <td>UID Kartu</td>
                  <td><span class="rfid-uid-badge" id="card-uid">-</span></td>
                </tr>
              </table>
            </div>
          </div>

          <!-- New Card Options -->
          <div id="new-card-options" style="display:none;">
            <div class="rfid-alert rfid-alert-info">
              <span class="material-icons"></span>
              <span>Kartu belum terdaftar di sistem</span>
            </div>
            <div class="rfid-choice-grid">
              <div class="rfid-choice-card" onclick="goToUpdate()">
                <div class="rfid-choice-icon">
                  <span class="material-icons"></span>
                </div>
                <h3>Update Kartu</h3>
              </div>
              <div class="rfid-choice-card" onclick="goToNew()">
                <div class="rfid-choice-icon">
                  <span class="material-icons"></span>
                </div>
                <h3>Tambah Baru</h3>
              </div>
            </div>
          </div>

          <button class="rfid-btn rfid-btn-link rfid-btn-full" onclick="resetFlow()">
            <span class="material-icons"></span>
            Scan Kartu Lain
          </button>
        </div>

        <!-- STEP 3: UPDATE CARD -->
        <div id="step-update" style="display:none;" class="rfid-fade-in">
          <h2 class="rfid-title-lg rfid-text-center rfid-mb-25">
            Cari Data untuk Update
          </h2>
          
          <div class="rfid-radio-group">
            <div class="rfid-radio-wrapper">
              <input type="radio" name="searchType" id="searchSiswa" value="siswa" checked onchange="clearSearch()">
              <label for="searchSiswa">👨‍🎓 Siswa</label>
            </div>
            <div class="rfid-radio-wrapper">
              <input type="radio" name="searchType" id="searchGuru" value="guru" onchange="clearSearch()">
              <label for="searchGuru">👨‍🏫 Guru</label>
            </div>
          </div>

          <div class="rfid-form-group">
            <input type="text" class="rfid-form-input" id="searchInput" placeholder="🔍 Ketik nama minimal 3 huruf...">
          </div>
          
          <div id="searchResults" class="rfid-search-container">
            <!-- Results go here -->
          </div>

          <button class="rfid-btn rfid-btn-secondary rfid-btn-full" onclick="backToCardDetected()">
            <span class="material-icons"></span>
            Kembali
          </button>
        </div>

        <!-- STEP 4: CONFIRM UPDATE -->
        <div id="step-confirm-update" style="display:none;" class="rfid-fade-in">
          <h2 class="rfid-title-lg rfid-text-center rfid-mb-25">
            Konfirmasi Update Kartu
          </h2>
          
          <div class="rfid-info-panel">
            <h3>
              <span class="material-icons"></span>
              Data Siswa/Guru
            </h3>
            <table>
              <tr>
                <td>Nama</td>
                <td id="confirm-nama">-</td>
              </tr>
              <tr>
                <td>Nomor Induk</td>
                <td id="confirm-nomor">-</td>
              </tr>
              <tr>
                <td>Role</td>
                <td id="confirm-role">-</td>
              </tr>
            </table>
          </div>

          <div class="rfid-info-panel">
            <h3>
              <span class="material-icons"></span>
              Perubahan Kartu
            </h3>
            <table>
              <tr>
                <td>UID Lama</td>
                <td><span class="rfid-uid-badge rfid-uid-old" id="confirm-old-uid">-</span></td>
              </tr>
              <tr>
                <td>UID Baru</td>
                <td><span class="rfid-uid-badge rfid-uid-new" id="confirm-new-uid">-</span></td>
              </tr>
            </table>
          </div>

          <input type="hidden" id="update-id">
          <input type="hidden" id="update-type">

          <button class="rfid-btn rfid-btn-success rfid-btn-full rfid-mb-12" onclick="confirmUpdate()">
            <span class="material-icons"></span>
            Update Kartu
          </button>
          <button class="rfid-btn rfid-btn-secondary rfid-btn-full" onclick="backToUpdate()">
            <span class="material-icons"></span>
            Kembali
          </button>
        </div>

        <!-- STEP 5: NEW REGISTRATION -->
        <div id="step-new" style="display:none;" class="rfid-fade-in">
          <h2 class="rfid-title-lg rfid-text-center rfid-mb-25">
            Registrasi Baru
          </h2>
          
          <div class="rfid-alert rfid-alert-info">
            <span class="material-icons">credit_card</span>
            <span>UID Kartu: <strong id="new-uid-display">-</strong></span>
          </div>

          <form id="form-register">
            <input type="hidden" id="reg_uid" name="uid">
            
            <div class="rfid-form-group">
              <label class="rfid-form-label">Nama Lengkap</label>
              <input type="text" class="rfid-form-input" id="reg_nama" name="nama" placeholder="Masukkan nama lengkap" required>
            </div>
            
            <div class="rfid-form-group">
              <label class="rfid-form-label">Nomor Induk (NIS/NIP)</label>
              <input type="text" class="rfid-form-input" id="reg_nomor" name="nomor" placeholder="Masukkan nomor induk" required>
            </div>

            <div class="rfid-form-group">
              <label class="rfid-form-label">Role</label>
              <select class="rfid-form-input" id="reg_role" name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="Siswa">👨‍🎓 Siswa</option>
                <option value="Guru">👨‍🏫 Guru</option>
              </select>
            </div>

            <button type="submit" class="rfid-btn rfid-btn-primary rfid-btn-full rfid-mb-12">
              <span class="material-icons">save</span>
              Simpan Data
            </button>
            <button type="button" class="rfid-btn rfid-btn-secondary rfid-btn-full" onclick="backToCardDetected()">
              <span class="material-icons"></span>
              Kembali
            </button>
          </form>
        </div>

      </div>
      
      <!-- Footer -->
      <div class="rfid-footer">
        <a href="https://absensi.mialikhlaspondokgede.sch.id/login.php">
          <span class="material-icons"></span>
          Kembali ke Login
        </a>
      </div>
    </div>
  </div>

  <script src="assets/js/jquery-3.6.3.min.js"></script>

  <script>
    let currentUID = "";
    let pollingInterval;
    let lastCheckedUID = "";

    $(document).ready(function() {
        startPolling();

        $('#searchInput').on('keyup', function() {
            let query = $(this).val();
            let type = $('input[name="searchType"]:checked').val();
            
            if(query.length >= 3) {
                $.ajax({
                    url: 'public_search.php',
                    method: 'POST',
                    data: { query: query, type: type },
                    success: function(data) {
                        $('#searchResults').html(data);
                    }
                });
            } else {
                $('#searchResults').html('<div style="text-align: center; padding: 30px; color: rgba(255,255,255,0.4);">Ketik minimal 3 huruf untuk mencari...</div>');
            }
        });

        $('#form-register').on('submit', function(e) {
            e.preventDefault();
            
            if(!$('#reg_nama').val() || !$('#reg_nomor').val() || !$('#reg_role').val()) {
                alert("Mohon lengkapi semua field!");
                return;
            }

            $.ajax({
                url: 'public_register_card.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if(response.status === 'success'){
                        alert('✅ ' + response.message);
                        resetFlow();
                    } else {
                        alert('❌ ' + response.message);
                    }
                },
                error: function() {
                    alert("❌ Terjadi kesalahan sistem.");
                }
            });
        });
    });

    function startPolling() {
        pollingInterval = setInterval(function() {
            if ($('#step-scan').is(':visible')) {
                $.ajax({
                    url: 'pages/dashboard/control/check_latest_card.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success' && response.uid) {
                            if (lastCheckedUID !== response.uid) {
                                lastCheckedUID = response.uid;
                                currentUID = response.uid;
                                handleNewCard(currentUID);
                            }
                        }
                    }
                });
            }
        }, 1000);
    }

    function handleNewCard(uid) {
        $.ajax({
            url: 'public_check_card.php',
            method: 'POST',
            data: { uid: uid },
            dataType: 'json',
            success: function(response) {
                $('#display-uid').text(uid);
                $('#reg_uid').val(uid);
                $('#new-uid-display').text(uid);
                
                if(response.status === 'registered') {
                    $('#card-nama').text(response.data.nama);
                    $('#card-nomor').text(response.data.nomor);
                    $('#card-role').text(response.data.role);
                    $('#card-uid').text(response.data.uid);
                    
                    $('#existing-card-data').show();
                    $('#new-card-options').hide();
                } else {
                    $('#existing-card-data').hide();
                    $('#new-card-options').show();
                }
                
                $('#step-scan').hide();
                $('#step-card-detected').fadeIn().addClass('rfid-fade-in');
            },
            error: function() {
                alert("❌ Gagal memeriksa status kartu");
            }
        });
    }

    function resetFlow() {
        currentUID = "";
        lastCheckedUID = "";
        $('#step-card-detected').hide();
        $('#step-update').hide();
        $('#step-confirm-update').hide();
        $('#step-new').hide();
        $('#step-scan').fadeIn().addClass('rfid-fade-in');
        $('#searchResults').html('');
        $('#searchInput').val('');
        $('#form-register')[0].reset();
    }

    function refreshScan() {
        lastCheckedUID = "";
        currentUID = "";
        alert("🔄 Scan direset! Silahkan tap kartu untuk scan ulang.");
    }

    function goToUpdate() {
        $('#step-card-detected').hide();
        $('#step-update').fadeIn().addClass('rfid-fade-in');
    }

    function goToNew() {
        $('#step-card-detected').hide();
        $('#step-new').fadeIn().addClass('rfid-fade-in');
    }

    function backToCardDetected() {
        $('#step-update').hide();
        $('#step-new').hide();
        $('#step-confirm-update').hide();
        $('#step-card-detected').fadeIn().addClass('rfid-fade-in');
    }

    function backToUpdate() {
        $('#step-confirm-update').hide();
        $('#step-update').fadeIn().addClass('rfid-fade-in');
    }

    function clearSearch() {
        $('#searchResults').html('');
        $('#searchInput').val('');
    }
    
    function selectUser(id, type, name, nomor, role, oldUID) {
        $('#confirm-nama').text(name);
        $('#confirm-nomor').text(nomor);
        $('#confirm-role').text(role);
        $('#confirm-old-uid').text(oldUID ? oldUID : 'Belum ada kartu');
        $('#confirm-new-uid').text(currentUID);
        $('#update-id').val(id);
        $('#update-type').val(type);
        
        $('#step-update').hide();
        $('#step-confirm-update').fadeIn().addClass('rfid-fade-in');
    }

    function confirmUpdate() {
        let id = $('#update-id').val();
        let type = $('#update-type').val();
        
        $.ajax({
            url: 'public_update_card.php',
            method: 'POST',
            data: { 
                id: id, 
                type: type, 
                uid: currentUID 
            },
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    alert('✅ ' + response.message);
                    resetFlow();
                } else {
                    alert('❌ ' + response.message);
                }
            },
            error: function() {
                alert("❌ Terjadi kesalahan sistem.");
            }
        });
    }
  </script>
</body>
</html>
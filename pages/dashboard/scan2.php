<?php
// pages/dashboard/scan2.php
date_default_timezone_set('Asia/Jakarta');

require_once "../../include/db_config.php";
if (!isset($conn) && isset($link)) {
    $conn = $link;
}
require_once "../../include/helpers.php";
include "control/confignusers_data.php";

// CATATAN: Query statistik awal ($d_hadir, $d_izin, dll) dihapus 
// karena data langsung di-load secara real-time via AJAX (get_stats.php).
// Ini mencegah query ganda dan memperingan beban database.
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Presensi — Scan Kartu</title>

<link href="../../assets/css/Roboto.css" rel="stylesheet">
<link href="../../assets/css/nucleo-icons.css" rel="stylesheet">
<link href="../../assets/css/nucleo-svg.css" rel="stylesheet">
<link href="../../assets/css/Material_icon.css" rel="stylesheet">
<link id="pagestyle" href="../../assets/css/material-dashboard-pro.min.css?v=3.0.6" rel="stylesheet" />
<link href="../../assets/css/animate.min.css" rel="stylesheet" />

<style>
/* ═══════════════════════════════════════════════════════════ */
/* MODERN ANIMATED GRADIENT BACKGROUND */
/* ═══════════════════════════════════════════════════════════ */
.bg-modern {
    background: linear-gradient(-45deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
    background-size: 400% 400%;
    animation: gradient 20s ease infinite;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
}

@keyframes gradient {
    0% { background-position: 0% 50%; }
    25% { background-position: 50% 50%; }
    50% { background-position: 100% 50%; }
    75% { background-position: 50% 100%; }
    100% { background-position: 0% 50%; }
}

/* FLOATING PARTICLES EFFECT */
.bg-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%);
    animation: floatParticles 30s ease-in-out infinite;
}

@keyframes floatParticles {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

/* ═══════════════════════════════════════════════════════════ */
/* BASE STYLES */
/* ═══════════════════════════════════════════════════════════ */
:root {
  --glass-bg: rgba(255, 255, 255, 0.12);
  --glass-border: rgba(255, 255, 255, 0.25);
  --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
  --text-primary: rgba(255, 255, 255, 0.95);
  --text-secondary: rgba(255, 255, 255, 0.7);
  --text-muted: rgba(255, 255, 255, 0.5);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
  margin: 0;
  padding: 16px;
  font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  overflow-x: hidden;
  color: var(--text-primary);
  line-height: 1.6;
}

.app-shell { 
    max-width: 1400px; 
    margin: auto; 
    position: relative; 
    z-index: 1; 
}

/* ═══════════════════════════════════════════════════════════ */
/* HEADER SECTION */
/* ═══════════════════════════════════════════════════════════ */
.app-header { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-bottom: 24px;
    padding: 16px 20px;
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
}

.logo {
  width: 60px;
  height: 60px;
  border-radius: 18px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.1));
  backdrop-filter: blur(15px);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.3);
  border: 2px solid rgba(255, 255, 255, 0.25);
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.logo:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
}

.logo img {
    width: 45px;
    height: 45px;
    object-fit: contain;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

.header-title {
    flex: 1;
    margin-left: 20px;
}

.header-title h3 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    color: white;
    text-shadow: 0 3px 6px rgba(0,0,0,0.3);
    letter-spacing: -0.5px;
}

.header-subtitle {
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 400;
    margin-top: 2px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.header-time {
    text-align: right;
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.15);
}

.time-display {
    font-size: 32px;
    font-weight: 700;
    color: white;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
    line-height: 1;
    font-variant-numeric: tabular-nums;
}

.date-display {
    color: var(--text-secondary);
    font-size: 13px;
    font-weight: 500;
    margin-top: 4px;
}

/* ═══════════════════════════════════════════════════════════ */
/* HEADER LOGOUT CARD - ELEGANT DESIGN */
/* ═══════════════════════════════════════════════════════════ */
.header-logout-card {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border-radius: 16px;
    border: 1px solid var(--glass-border);
    padding: 4px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.header-logout-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
    border-color: rgba(255, 255, 255, 0.35);
}

.btn-back-login {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 18px;
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.15) 100%);
    border: 2px solid rgba(239, 68, 68, 0.35);
    border-radius: 12px;
    color: white;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.btn-back-login::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    transition: left 0.5s ease;
}

.btn-back-login:hover::before {
    left: 100%;
}

.btn-back-login:hover {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.3) 0%, rgba(220, 38, 38, 0.25) 100%);
    border-color: rgba(239, 68, 68, 0.55);
    transform: scale(1.02);
}

.btn-back-login:active {
    transform: scale(0.98);
}

.btn-back-login .material-icons {
    font-size: 28px;
    color: #fca5a5;
    text-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
    transition: all 0.3s ease;
}

.btn-back-login:hover .material-icons {
    color: #fecaca;
    transform: rotate(-15deg);
}

.logout-text {
    display: flex;
    flex-direction: column;
    gap: 0px;
    line-height: 1.2;
}

.logout-label {
    font-size: 10px;
    color: var(--text-muted);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.logout-title {
    font-size: 16px;
    color: white;
    font-weight: 700;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    letter-spacing: 0.3px;
}

@media(max-width: 1000px) {
    .header-logout-card {
        padding: 3px;
    }
    .btn-back-login {
        padding: 10px 14px;
        gap: 8px;
    }
    .logout-text {
        display: none;
    }
    .btn-back-login .material-icons {
        font-size: 24px;
    }
}

@media(max-width: 768px) {
    .header-logout-card {
        padding: 2px;
    }
    .btn-back-login {
        padding: 8px 10px;
    }
    .btn-back-login .material-icons {
        font-size: 22px;
    }
}

/* ═══════════════════════════════════════════════════════════ */
/* GLASS PANEL - ENHANCED */
/* ═══════════════════════════════════════════════════════════ */
.glass-panel {
    background: var(--glass-bg);
    box-shadow: var(--glass-shadow);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
    border: 1px solid var(--glass-border);
    padding: 24px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.glass-panel::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
}

.glass-panel:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.45);
    border-color: rgba(255, 255, 255, 0.35);
}

.panel-header {
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}

.panel-title {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    gap: 10px;
}

.panel-subtitle {
    color: var(--text-muted);
    font-size: 13px;
    font-weight: 400;
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ═══════════════════════════════════════════════════════════ */
/* MAIN LAYOUT GRID */
/* ═══════════════════════════════════════════════════════════ */
.main-row {
  display: grid;
  grid-template-columns: 420px 1fr;
  gap: 24px;
  align-items: start;
}

@media(max-width: 1200px) {
  .main-row { 
    grid-template-columns: 380px 1fr;
    gap: 20px;
  }
}

@media(max-width: 1000px) {
  .main-row { 
    grid-template-columns: 1fr;
  }
  .app-header {
    flex-direction: column;
    gap: 16px;
    text-align: center;
  }
  .header-title {
    margin-left: 0;
  }
}

/* ═══════════════════════════════════════════════════════════ */
/* SCAN AREA - PREMIUM DESIGN */
/* ═══════════════════════════════════════════════════════════ */
.scan-area {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 380px;
  position: relative;
  padding: 20px;
}

.scan-image-wrapper {
    position: relative;
    margin-bottom: 20px;
}

.scan-image-wrapper::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    animation: scanPulse 3s ease-in-out infinite;
}

@keyframes scanPulse {
    0%, 100% { 
        transform: translate(-50%, -50%) scale(1);
        opacity: 0.5;
    }
    50% { 
        transform: translate(-50%, -50%) scale(1.1);
        opacity: 0.8;
    }
}

#scan_circle {
    width: 260px;
    max-width: 100%;
    border-radius: 24px;
    box-shadow: 
        0 15px 50px rgba(0,0,0,0.4),
        inset 0 -2px 10px rgba(0,0,0,0.2),
        inset 0 2px 10px rgba(255,255,255,0.1);
    border: 4px solid rgba(255,255,255,0.3);
    transition: all 0.4s ease;
    position: relative;
    z-index: 1;
}

#scan_circle:hover {
    transform: scale(1.02);
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
    border-color: rgba(255,255,255,0.5);
}

#scan_status {
    font-size: 18px;
    font-weight: 600;
    margin-top: 16px;
    color: white;
    text-shadow: 0 2px 8px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

#scan_status::before {
    content: '●';
    color: #4ade80;
    animation: blink 2s ease-in-out infinite;
}

@keyframes blink {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.3; }
}

#scan_element_container {
    width: 100%;
    margin-top: 20px;
    text-align: center;
}

/* ═══════════════════════════════════════════════════════════ */
/* LAST TAP SECTION */
/* ═══════════════════════════════════════════════════════════ */
.last-tap-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.last-tap-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: white;
    display: flex;
    align-items: center;
    gap: 8px;
}

#last_updated {
    font-size: 11px;
    color: var(--text-muted);
    background: rgba(255, 255, 255, 0.1);
    padding: 4px 10px;
    border-radius: 8px;
    font-weight: 500;
}

#last_scan_container {
    max-height: 420px;
    overflow-y: auto;
    padding-right: 6px;
}

#last_scan_container::-webkit-scrollbar {
    width: 6px;
}

#last_scan_container::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

#last_scan_container::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    transition: background 0.3s;
}

#last_scan_container::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
}

.last-item {
  display: flex;
  gap: 14px;
  align-items: center;
  background: rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 14px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  margin-bottom: 12px;
  transition: all 0.3s ease;
}

.last-item:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateX(4px);
    border-color: rgba(255, 255, 255, 0.25);
}

.avatar {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  background: linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1));
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
  color: white;
  font-weight: 700;
  text-shadow: 0 2px 4px rgba(0,0,0,0.2);
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.last-meta {
    flex: 1;
}

.last-meta .name { 
    font-weight: 700;
    margin-bottom: 4px;
    font-size: 16px;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.last-meta .info { 
    font-size: 12px;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 6px;
}

.last-time { 
    margin-left: auto;
    text-align: right;
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
}

/* ═══════════════════════════════════════════════════════════ */
/* STATS GRID - ENHANCED */
/* ═══════════════════════════════════════════════════════════ */
.stats-section-header {
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    border-radius: 16px;
    border: 1px solid var(--glass-border);
}

.stats-section-title {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
    color: white;
    text-shadow: 0 2px 6px rgba(0,0,0,0.3);
    display: flex;
    align-items: center;
    gap: 10px;
}

.stats-auto-update {
    font-size: 11px;
    color: var(--text-muted);
    background: rgba(255, 255, 255, 0.1);
    padding: 6px 12px;
    border-radius: 20px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
}

.stats-auto-update::before {
    content: '●';
    color: #4ade80;
    animation: blink 2s ease-in-out infinite;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 16px;
}

@media(max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 12px;
    }
}

/* ═══════════════════════════════════════════════════════════ */
/* STAT COMPACT CARD - PREMIUM */
/* ═══════════════════════════════════════════════════════════ */
.stat-compact {
    background: var(--glass-bg);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    border: 1px solid var(--glass-border);
    padding: 18px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 130px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.stat-compact::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, 
        transparent,
        rgba(255,255,255,0.4),
        transparent
    );
    transform: translateX(-100%);
    transition: transform 0.6s ease;
}

.stat-compact:hover::before {
    transform: translateX(100%);
}

.stat-compact:hover {
    transform: translateY(-5px) scale(1.02);
    background: rgba(255, 255, 255, 0.18);
    box-shadow: 0 15px 45px rgba(31, 38, 135, 0.5);
    border-color: rgba(255, 255, 255, 0.35);
}

.stat-compact:active {
    transform: translateY(-3px) scale(1);
}

.class-name {
    font-size: 17px;
    font-weight: 800;
    margin-bottom: 10px;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    letter-spacing: 0.5px;
}

.hadir-count {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 4px;
    display: flex;
    align-items: baseline;
    justify-content: center;
    gap: 4px;
}

.stat-hadir {
    color: #4ade80;
    text-shadow: 0 2px 8px rgba(74, 222, 128, 0.5);
}

.hadir-count span:last-child {
    font-size: 15px;
    color: white;
    opacity: 0.6;
    font-weight: 600;
}

.total-count {
    font-size: 11px;
    color: var(--text-muted);
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 600;
}

.mini-badges {
    display: flex;
    gap: 6px;
    justify-content: center;
    flex-wrap: wrap;
}

.mini-badge {
    font-size: 10px;
    padding: 3px 8px;
    border-radius: 10px;
    color: white;
    font-weight: 700;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: transform 0.2s ease;
}

.mini-badge:hover {
    transform: scale(1.1);
}

.bg-izin { 
    background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
}

.bg-sakit { 
    background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
}

.bg-pulang { 
    background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
}

/* ═══════════════════════════════════════════════════════════ */
/* MODAL - MODERN DESIGN */
/* ═══════════════════════════════════════════════════════════ */
#detailModal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.85);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(8px);
}

.modal-content-wrapper {
    width: 90%;
    max-width: 550px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    background: var(--glass-bg);
    backdrop-filter: blur(25px);
    border-radius: 24px;
    border: 1px solid var(--glass-border);
    box-shadow: 0 25px 80px rgba(0, 0, 0, 0.6);
    overflow: hidden;
}

.modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: rgba(255, 255, 255, 0.05);
}

#modalTitle {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: white;
    text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.modal-close-btn {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.modal-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: rotate(90deg);
}

#modalContent {
    padding: 20px 24px;
    overflow-y: auto;
    flex: 1;
}

#modalContent::-webkit-scrollbar {
    width: 8px;
}

#modalContent::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
}

#modalContent::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
}

/* ═══════════════════════════════════════════════════════════ */
/* UTILITY CLASSES */
/* ═══════════════════════════════════════════════════════════ */
.text-muted {
    color: var(--text-muted) !important;
}

.text-danger {
    color: #f87171 !important;
}

/* Material Icons Adjustment */
.material-icons {
    font-size: inherit;
    vertical-align: middle;
}

/* Loading State */
.loading-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity:1; }
    50% { opacity: 0.5; }
}

/* Smooth Transitions */
* {
    transition: background-color 0.3s ease, border-color 0.3s ease;
}
</style>
</head>

<body>
<div class="bg-modern"></div>

<div class="app-shell animate__animated animate__fadeIn">

  <!-- ═══════════════════════════════════════════════════════════ -->
  <!-- HEADER -->
  <!-- ═══════════════════════════════════════════════════════════ -->
  <div class="app-header">
    <div style="display:flex;gap:16px;align-items:center;">
      <div class="logo">
        <img src="../../<?php echo isset($icon_dashboard) ? $icon_dashboard : 'assets/img/logo_sekolah.png'; ?>" alt="Logo" onerror="this.src='../../assets/img/logo_sekolah.png'">
      </div>
      <div class="header-title">
         <h3>Presensi Sekolah</h3>
        <div class="header-subtitle">
          <span class="material-icons" style="font-size:14px;">credit_card</span>
          Scan kartu & pantau kehadiran real-time
        </div>
      </div>
    </div>
    <div style="display:flex;gap:12px;align-items:center;">
      <div class="header-time">
          <div class="time-display"><?php echo date("H:i");?></div>
          <div class="date-display"><?php echo date("d M Y");?></div>
      </div>
      <div class="header-logout-card">
        <a href="https://absensi.mialikhlaspondokgede.sch.id/login.php" class="btn-back-login">
          <span class="material-icons">logout</span>
          <div class="logout-text">
            <div class="logout-label">Kembali ke</div>
            <div class="logout-title">Login</div>
          </div>
        </a>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════════════ -->
  <!-- MAIN CONTENT -->
  <!-- ═══════════════════════════════════════════════════════════ -->
  <div class="main-row">

    <!-- ─────────────────────────────────────────────────────── -->
    <!-- LEFT COLUMN: SCAN + LAST TAP -->
    <!-- ─────────────────────────────────────────────────────── -->
    <div style="display: flex; flex-direction: column; gap: 20px;">
        
        <!-- SCAN KARTU PANEL -->
        <div class="glass-panel animate__animated animate__zoomIn">
          <div class="panel-header" style="text-align: center; border-bottom: none; padding-bottom: 0;">
              <h4 class="panel-title" style="justify-content: center;">
                <span class="material-icons">contactless</span>
                Scan Kartu
              </h4>
              <div class="panel-subtitle" style="justify-content: center;">
                <span class="material-icons" style="font-size:12px;">info</span>
                Tempelkan kartu pada reader
              </div>
          </div>

          <div class="scan-area">
            <div class="scan-image-wrapper">
                <img id="scan_circle" 
                     src="../../assets/img/scan2.gif" 
                     alt="Scan Animation"
                     loading="lazy">
            </div>
            
            <div id="scan_status">
              Menunggu scan...
            </div>

            <div id="scan_element_container"></div>
          </div>
        </div>

        <!-- TERAKHIR TAP PANEL -->
        <div class="glass-panel animate__animated animate__fadeInUp" style="flex: 1;">
          <div class="last-tap-header">
              <h5 class="last-tap-title">
                <span class="material-icons" style="font-size:20px;">history</span>
                Terakhir Tap
              </h5>
              <div id="last_updated">--:--</div>
          </div>

          <div id="last_scan_container">
            <div class="text-muted" style="text-align:center; padding: 20px;">
              <span class="material-icons" style="font-size:32px; opacity:0.3;">hourglass_empty</span>
              <div style="margin-top:8px;">Memuat data...</div>
            </div>
          </div>
        </div>
    </div>

    <!-- ─────────────────────────────────────────────────────── -->
    <!-- RIGHT COLUMN: COMPACT STATS GRID -->
    <!-- ─────────────────────────────────────────────────────── -->
    <div>
        <div class="stats-section-header">
            <h4 class="stats-section-title">
              <span class="material-icons">dashboard</span>
              Kehadiran Per Kelas
            </h4>
            <div class="stats-auto-update">
              Auto-update
            </div>
        </div>
        
        <div id="stats_container" class="stats-grid">
            <!-- Loading State -->
            <div class="stat-compact loading-pulse">
                <div class="class-name">Loading...</div>
                <div class="hadir-count">
                  <span class="stat-hadir">--</span> / <span style="font-size:14px; color:white; opacity:0.6;">--</span>
                </div>
                <div class="total-count">Memuat data</div>
            </div>
        </div>
    </div>

  </div>

</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- CLASS DETAIL MODAL -->
<!-- ═══════════════════════════════════════════════════════════ -->
<div id="detailModal">
    <div class="modal-content-wrapper animate__animated animate__zoomIn">
        <div class="modal-header">
            <h5 id="modalTitle">Detail Kelas</h5>
            <button class="modal-close-btn" onclick="closeModal()" aria-label="Close">
              <span class="material-icons" style="font-size:20px;">close</span>
            </button>
        </div>
        <div id="modalContent">
            <div class="text-muted" style="text-align:center; padding: 30px;">
              <span class="material-icons" style="font-size:48px; opacity:0.3;">cloud_download</span>
              <div style="margin-top:12px;">Loading...</div>
            </div>
        </div>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════ -->
<!-- SCRIPTS -->
<!-- ═══════════════════════════════════════════════════════════ -->
<script src="../../assets/js/jquery-3.5.1.js"></script>
<script>
$(function(){
  console.log('🚀 Presensi System Initialized');
  
  let clearTimer;
  let scanActive = false;

  // ═══════════════════════════════════════════════════════════
  // SCAN POLLING - Enhanced
  // ═══════════════════════════════════════════════════════════
  function pollScan() {
      $.get('view/scan_element.php', function(data) {
          if (data.trim().length > 0 && !scanActive) {
              scanActive = true;
              
              // Display scan result
              $("#scan_element_container").html(data);
              
              // Visual feedback
              $("#scan_status").fadeOut(200);
              $("#scan_circle").css({
                'border-color': 'rgba(74, 222, 128, 0.6)',
                'box-shadow': '0 20px 60px rgba(74, 222, 128, 0.3)'
              }).fadeOut(300);
              
              // Reset timer
              if(clearTimer) clearTimeout(clearTimer);
              
              // Auto-clear after 15 seconds
              clearTimer = setTimeout(function() {
                  $("#scan_element_container").fadeOut(300, function() {
                      $(this).empty();
                      $("#scan_status").fadeIn(300);
                      $("#scan_circle").css({
                        'border-color': 'rgba(255,255,255,0.3)',
                        'box-shadow': '0 15px 50px rgba(0,0,0,0.4)'
                      }).fadeIn(300);
                      scanActive = false;
                  });
              }, 15000);
          }
      }).fail(function() {
          console.error('❌ Scan polling failed');
      }).always(function() {
          setTimeout(pollScan, 1000);
      });
  }
  
  // Start scan polling
  pollScan();

  // ═══════════════════════════════════════════════════════════
  // LAST TAP POLLING
  // ═══════════════════════════════════════════════════════════
  function updateLastTap() {
    $("#last_scan_container").load('view/last_scan.php', function(response, status) {
      if (status === "success") {
        $("#last_updated").text(new Date().toLocaleTimeString('id-ID', {
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit'
        }));
      } else {
        console.error('❌ Last tap update failed');
      }
    });
  }
  
  // Initial load
  updateLastTap();
  
  // Polling every 1.5 seconds
  setInterval(updateLastTap, 1500);

  // ═══════════════════════════════════════════════════════════
  // STATISTICS POLLING - Enhanced with Animation
  // ═══════════════════════════════════════════════════════════
  var previousStats = {};
  var firstLoad = true;

  function updateStats() {
      $.getJSON('view/get_stats.php', function(data) {
          if(data) {
              // Clear loading state on first load
              if (firstLoad) {
                  $('#stats_container').empty();
                  firstLoad = false;
              }

              // Update student classes
              if(data.siswa && data.siswa.length > 0) {
                  $.each(data.siswa, function(index, item) {
                      updateStatCard(item.kelas, item, false);
                  });
              }

              // Update teacher stats
              if(data.guru) {
                  updateStatCard('GURU', data.guru, true);
              }
          }
      }).fail(function() {
          console.error('❌ Stats update failed');
      });
  }

  function updateStatCard(id, data, isGuru) {
      var safeId = id.replace(/\s+/g, '-');
      var cardId = 'stat-card-' + safeId;
      var label = isGuru ? 'GURU' : id;
      var clickHandler = isGuru ? 
          "showClassDetails('GURU', 'guru')" : 
          "showClassDetails('" + id.replace(/'/g, "\\'") + "', 'siswa')";
      
      // Check if card exists
      if ($('#' + cardId).length === 0) {
          // Create new card
          var borderStyle = isGuru ? 'border: 2px solid #ab47bc; background: rgba(171, 71, 188, 0.08);' : '';
          var html = `
            <div id="${cardId}" 
                 onclick="${clickHandler}" 
                 class="stat-compact animate__animated animate__fadeInUp" 
                 style="${borderStyle}">
                <div class="class-name">
                  ${isGuru ? '<span class="material-icons" style="font-size:16px; vertical-align:middle;">school</span> ' : ''}
                  ${label}
                </div>
                <div class="hadir-count">
                    <span class="stat-hadir">${data.hadir}</span> / 
                    <span style="font-size:14px; color:white; opacity:0.6;">${data.total}</span>
                </div>
                <div class="total-count">Hadir</div>
                
                <div class="mini-badges">
                    <span class="mini-badge bg-izin" title="Izin">
                      <span class="material-icons" style="font-size:9px;">info</span>
                      <span class="stat-izin">${data.izin}</span>
                    </span>
                    <span class="mini-badge bg-sakit" title="Sakit">
                      <span class="material-icons" style="font-size:9px;">sick</span>
                      <span class="stat-sakit">${data.sakit}</span>
                    </span>
                    <span class="mini-badge bg-pulang" title="Pulang">
                      <span class="material-icons" style="font-size:9px;">home</span>
                      <span class="stat-pulang">${data.pulang}</span>
                    </span>
                </div>
            </div>
          `;
          
          // Insert position: Teachers always last
          if (!isGuru && $('#stat-card-GURU').length > 0) {
              $(html).insertBefore('#stat-card-GURU');
          } else {
              $('#stats_container').append(html);
          }
          
          // Store initial state
          previousStats[id] = JSON.stringify(data);
          
      } else {
          // Update existing card
          var currentDataStr = JSON.stringify(data);
          
          if (previousStats[id] !== currentDataStr) {
              var card = $('#' + cardId);
              
              // Update values with animation
              card.find('.stat-hadir').fadeOut(200, function() {
                $(this).text(data.hadir).fadeIn(200);
              });
              card.find('.stat-izin').text(data.izin);
              card.find('.stat-sakit').text(data.sakit);
              card.find('.stat-pulang').text(data.pulang);
              
              // Pulse animation
              card.removeClass('animate__pulse');
              void card[0].offsetWidth; // Trigger reflow
              card.addClass('animate__pulse');
              
              // Update stored state
              previousStats[id] = currentDataStr;
          }
      }
  }
  
  // Initial load
  updateStats();
  
  // Polling every 3 seconds
  setInterval(updateStats, 3000);

  // ═══════════════════════════════════════════════════════════
  // REAL-TIME CLOCK UPDATE
  // ═══════════════════════════════════════════════════════════
  function updateClock() {
    var now = new Date();
    var hours = String(now.getHours()).padStart(2, '0');
    var minutes = String(now.getMinutes()).padStart(2, '0');
    $('.time-display').text(hours + ':' + minutes);
  }
  
  setInterval(updateClock, 1000);
  
  console.log('✅ All systems operational');
});

// ═══════════════════════════════════════════════════════════
// MODAL FUNCTIONS - Global Scope
// ═══════════════════════════════════════════════════════════
function showClassDetails(kelas, type) {
    $('#detailModal').css('display', 'flex').hide().fadeIn(300);
    
    var titleText = type === 'guru' ? 
        '<span class="material-icons" style="font-size:20px; vertical-align:middle;">school</span> Detail Guru' : 
        '<span class="material-icons" style="font-size:20px; vertical-align:middle;">groups</span> Detail Kelas ' + kelas;
    
    $('#modalTitle').html(titleText);
    $('#modalContent').html(`
      <div class="text-muted" style="text-align:center; padding: 40px;">
        <div class="loading-pulse">
          <span class="material-icons" style="font-size:56px; opacity:0.4;">cloud_download</span>
          <div style="margin-top:16px; font-size:14px;">Memuat data...</div>
        </div>
      </div>
    `);
    
    $.get('view/get_class_details.php', { 
      kelas: kelas, 
      type: type 
    }, function(data) {
        $('#modalContent').hide().html(data).fadeIn(300);
    }).fail(function() {
        $('#modalContent').html(`
          <div class="text-danger" style="text-align:center; padding: 40px;">
            <span class="material-icons" style="font-size:56px; opacity:0.5;">error_outline</span>
            <div style="margin-top:16px; font-weight:600;">Gagal memuat data</div>
            <div style="margin-top:8px; font-size:13px; opacity:0.7;">Silakan coba lagi</div>
          </div>
        `);
    });
    
    // Prevent event bubbling
    return false;
}

function closeModal() {
    $('#detailModal').fadeOut(300);
}

// Close modal on background click
$(document).on('click', '#detailModal', function(e) {
    if (e.target.id === 'detailModal') {
        closeModal();
    }
});

// Close modal on ESC key
$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && $('#detailModal').is(':visible')) {
        closeModal();
    }
});
</script>
</body>
</html>
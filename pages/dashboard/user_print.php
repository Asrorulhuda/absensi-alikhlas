<?php
date_default_timezone_set('Asia/Jakarta');
session_start();

// Ensure user has Admin access
if (!isset($_SESSION['akses']) || $_SESSION['akses'] != 'Admin') {
    header('location:../../index');
    exit();
}

require_once "../../include/db_config.php";
require_once "../../include/helpers.php";
sync_user_accounts();

// Fetch system settings for branding
$sql_conf = "SELECT * FROM system_config WHERE id = 1";
$conf_res = mysqli_query($GLOBALS["___mysqli_ston"], $sql_conf);
$conf_row = mysqli_fetch_array($conf_res);
$company_name = $conf_row["company"] ?? "MI Al-Ikhlas";
$logo_print = $conf_row["print_logo"] ?? "assets/img/asr_edu.png";

// Determine active filter
$role_filter = "";
$active_role = "all";

if (isset($_GET['role']) && in_array($_GET['role'], ['Admin', 'Guru', 'User'])) {
    $active_role = $_GET['role'];
    $role_filter = " WHERE u.level_akses = '" . mysqli_real_escape_string($GLOBALS["___mysqli_ston"], $active_role) . "'";
} elseif (isset($_GET['id'])) {
    $id = intval(base64_decode($_GET['id']));
    $role_filter = " WHERE u.id = $id";
    $active_role = "single";
}

// Fetch users with linked Siswa & Guru details, including RFID card UIDs
$sql_users = "SELECT u.*, 
                     s.s_uid, s.s_nis, s.s_kelas, s.s_nama,
                     g.g_uid, g.g_nip, g.g_jabatan, g.g_nama
              FROM users u
              LEFT JOIN data_siswa s ON u.id_siswa = s.s_id
              LEFT JOIN data_guru g ON u.id_guru = g.g_id
              $role_filter
              ORDER BY u.level_akses ASC, u.name ASC";

$q_users = mysqli_query($GLOBALS["___mysqli_ston"], $sql_users);
$users_list = [];
if ($q_users) {
    while ($row = mysqli_fetch_assoc($q_users)) {
        $users_list[] = $row;
    }
}

// Build login URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$login_url = $protocol . $_SERVER['HTTP_HOST'] . str_replace('pages/dashboard/user_print.php', 'login.php', $_SERVER['SCRIPT_NAME']);
$login_url_encoded = urlencode($login_url);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="icon" type="image/png" href="../../<?php echo htmlspecialchars($conf_row['icon_bar'] ?? 'favicon.ico'); ?>">
  <title>Cetak Akun Login — <?php echo htmlspecialchars($company_name); ?></title>
  
  <!-- Fonts & Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Fira+Code:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
  
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
            mono: ['Fira Code', 'monospace']
          }
        }
      }
    }
  </script>
  
  <style>
    body {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
        background-color: #f8fafc;
        color: #1e293b;
    }
    
    /* Print Specific Styles */
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .print-page {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
            border: none !important;
            background: transparent !important;
        }
        .page-break-avoid {
            page-break-inside: avoid;
            break-inside: avoid;
        }
        
        /* Layout media overrides */
        .print-grid-premium {
            display: grid !important;
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 10px !important;
        }
        .print-grid-mini {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 6px !important;
        }
        .print-table {
            display: table !important;
            width: 100% !important;
        }
        
        .card-border-dashed {
            background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='12' ry='12' stroke='%2394a3b8' stroke-width='1' stroke-dasharray='4%2c 4' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e") !important;
            border: none !important;
        }
    }
    
    .card-border-dashed {
        background-image: url("data:image/svg+xml,%3csvg width='100%25' height='100%25' xmlns='http://www.w3.org/2000/svg'%3e%3crect width='100%25' height='100%25' fill='none' rx='16' ry='16' stroke='%23cbd5e1' stroke-width='1.5' stroke-dasharray='6%2c 6' stroke-dashoffset='0' stroke-linecap='square'/%3e%3c/svg%3e");
    }
  </style>
</head>
<body class="min-h-screen antialiased bg-slate-50">

  <!-- TOP HEADER / CONTROLS (Screen Only) -->
  <header class="no-print w-full bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex flex-col md:flex-row items-center justify-between gap-4">
      
      <!-- App title / Info -->
      <div class="flex items-center gap-3">
        <a href="user" class="p-2 hover:bg-slate-100 rounded-xl transition-all flex items-center justify-center text-slate-500 hover:text-slate-700">
          <span class="material-icons-round">arrow_back</span>
        </a>
        <div>
          <h1 class="text-lg font-extrabold text-slate-800 leading-tight">Cetak Akun Login</h1>
          <p class="text-xs text-slate-400 font-medium">Cetak data login (Name, UID, Username, Password) langsung ke kertas A4</p>
        </div>
      </div>

      <!-- Action & View Toggles -->
      <div class="flex flex-wrap items-center gap-3">
        
        <!-- Role Filters -->
        <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
          <a href="?role=all" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $active_role == 'all' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'; ?>">Semua</a>
          <a href="?role=Admin" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $active_role == 'Admin' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'; ?>">Admin</a>
          <a href="?role=Guru" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $active_role == 'Guru' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'; ?>">Guru</a>
          <a href="?role=User" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all <?php echo $active_role == 'User' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'; ?>">Siswa</a>
        </div>

        <!-- Layout Selector (Premium Slips, Mini Cards, Table) -->
        <div class="flex items-center bg-slate-100 p-1 rounded-xl border border-slate-200">
          <button id="btn-format-mini" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-slate-800 shadow-sm flex items-center gap-1">
            <span class="material-icons-round text-sm">apps</span> Mini (A4 Hemat 20-50 Kartu)
          </button>
          <button id="btn-format-premium" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <span class="material-icons-round text-sm">grid_view</span> Slip Premium (Besar)
          </button>
          <button id="btn-format-table" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all text-slate-500 hover:text-slate-800 flex items-center gap-1">
            <span class="material-icons-round text-sm">view_list</span> Tabel Ringkas
          </button>
        </div>

        <!-- Print Trigger -->
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl flex items-center gap-1.5 shadow-md shadow-blue-500/10 hover:shadow-blue-500/25 transition-all">
          <span class="material-icons-round text-base">print</span> Cetak Halaman
        </button>

      </div>
      
    </div>
  </header>

  <!-- PRINT WRAPPER -->
  <main class="max-w-7xl mx-auto px-6 py-8 print-page">
    
    <!-- Empty State -->
    <?php if (empty($users_list)): ?>
      <div class="bg-white border border-slate-200 rounded-3xl p-16 text-center max-w-md mx-auto my-12 shadow-sm">
        <span class="material-icons-round text-5xl text-slate-300 animate-pulse block">account_circle</span>
        <h3 class="text-base font-extrabold text-slate-700 mt-4">Akun Tidak Ditemukan</h3>
        <p class="text-xs text-slate-400 mt-2 leading-relaxed">Belum ada akun pengguna terdaftar untuk filter ini.</p>
        <a href="user" class="no-print mt-6 inline-flex bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition-all">
          Kembali ke Pengguna
        </a>
      </div>
    <?php else: ?>

      <!-- ============================================== -->
      <!-- 1. MINI FORMAT CONTAINER (4 Columns, fits 20-50 per sheet) -->
      <!-- ============================================== -->
      <div id="format-mini-container" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 print-grid-mini">
        
        <?php foreach ($users_list as $u): 
            $lvl = $u['level_akses'];
            $bg_badge = "bg-slate-100 text-slate-700";
            $pw_hint = "Sesuai Pilihan";
            $uid = "-";
            
            if ($lvl == 'Admin') {
                $bg_badge = "bg-blue-50 text-blue-700 border-blue-100";
                $pw_hint = "Pass Admin";
            } elseif ($lvl == 'Guru' || !empty($u['id_guru'])) {
                $bg_badge = "bg-emerald-50 text-emerald-700 border-emerald-100";
                $pw_hint = !empty($u['g_nip']) ? htmlspecialchars($u['g_nip']) : "NIP Guru";
                $uid = !empty($u['g_uid']) ? $u['g_uid'] : "-";
            } elseif ($lvl == 'User' || !empty($u['id_siswa'])) {
                $bg_badge = "bg-indigo-50 text-indigo-700 border-indigo-100";
                $pw_hint = !empty($u['s_nis']) ? htmlspecialchars($u['s_nis']) : "123456";
                $uid = !empty($u['s_uid']) ? $u['s_uid'] : "-";
            }
        ?>
          <div class="page-break-avoid bg-white rounded-xl border border-slate-200 p-3 flex flex-col justify-between h-[120px] shadow-sm card-border-dashed">
            
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-1.5 mb-1.5">
              <div class="flex items-center gap-1.5">
                <img src="../../<?php echo htmlspecialchars($logo_print); ?>" class="h-4 object-contain" alt="Logo" onerror="this.onerror=null; this.src='../../assets/img/asr_edu.png'">
                <span class="text-[7.5px] font-extrabold text-slate-800 uppercase tracking-tighter block leading-none truncate max-w-[80px]"><?php echo htmlspecialchars($company_name); ?></span>
              </div>
              <span class="text-[6.5px] font-extrabold px-1 py-0.5 rounded border <?php echo $bg_badge; ?> uppercase leading-none">
                <?php echo $lvl == 'User' ? 'Siswa' : $lvl; ?>
              </span>
            </div>
            
            <!-- Details -->
            <div class="flex-1 space-y-1">
              <div class="leading-none">
                <span class="text-[6px] text-slate-400 font-bold uppercase tracking-wider block">Nama Lengkap</span>
                <span class="text-[10px] font-extrabold text-slate-850 block truncate leading-tight"><?php echo htmlspecialchars($u['name']); ?></span>
              </div>
              
              <div class="grid grid-cols-2 gap-1 mt-1">
                <div>
                  <span class="text-[6px] text-slate-400 font-bold uppercase tracking-wider block">Username</span>
                  <span class="text-[9px] font-bold text-slate-700 bg-slate-50 px-1 py-0.5 rounded border border-slate-100 block truncate tracking-wide leading-none select-all"><?php echo htmlspecialchars($u['username']); ?></span>
                </div>
                <div>
                  <span class="text-[6px] text-slate-400 font-bold uppercase tracking-wider block">Password</span>
                  <span class="text-[9px] font-extrabold text-slate-800 block truncate leading-none select-all"><?php echo htmlspecialchars($pw_hint); ?></span>
                </div>
              </div>
              
              <div class="mt-1 flex items-center justify-between">
                <div>
                  <span class="text-[6px] text-slate-400 font-bold uppercase tracking-wider block">UID Kartu</span>
                  <span class="text-[9px] font-mono font-bold text-slate-650 block leading-none select-all"><?php echo htmlspecialchars($uid); ?></span>
                </div>
                <?php if ($lvl == 'User' && !empty($u['s_kelas'])): ?>
                  <span class="text-[7px] bg-slate-150 text-slate-600 font-extrabold px-1 rounded">Kls <?php echo htmlspecialchars($u['s_kelas']); ?></span>
                <?php elseif ($lvl == 'Guru' && !empty($u['g_jabatan'])): ?>
                  <span class="text-[7px] bg-slate-150 text-slate-600 font-extrabold px-1 rounded truncate max-w-[65px]"><?php echo htmlspecialchars($u['g_jabatan']); ?></span>
                <?php endif; ?>
              </div>
            </div>
            
            <!-- Footer -->
            <div class="border-t border-slate-100 pt-1 mt-1 flex items-center justify-between text-[6px] text-slate-400 leading-none">
              <span class="truncate max-w-[125px]"><?php echo htmlspecialchars(str_replace(['http://', 'https://'], '', $login_url)); ?></span>
              <span class="font-bold text-slate-300">ASR.EDU</span>
            </div>
            
          </div>
        <?php endforeach; ?>

      </div>


      <!-- ============================================== -->
      <!-- 2. PREMIUM FORMAT CONTAINER (3 Columns, original layout) -->
      <!-- ============================================== -->
      <div id="format-premium-container" class="hidden grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 print-grid-premium">
        
        <?php foreach ($users_list as $u): 
            $lvl = $u['level_akses'];
            $bg_badge = "bg-slate-100 text-slate-700";
            $role_title = "AKSES PENGGUNA";
            $pw_hint = "Password yang Anda buat";
            $uid = "-";
            
            if ($lvl == 'Admin') {
                $bg_badge = "bg-blue-50 text-blue-700 border-blue-100";
                $role_title = "KARTU LOGIN ADMINISTRATOR";
                $pw_hint = "Password Administrator Anda";
            } elseif ($lvl == 'Guru' || !empty($u['id_guru'])) {
                $bg_badge = "bg-emerald-50 text-emerald-700 border-emerald-100";
                $role_title = "KARTU LOGIN GURU / STAFF";
                $pw_hint = !empty($u['g_nip']) ? "Default: " . htmlspecialchars($u['g_nip']) : "NIP Guru / Password Terdaftar";
                $uid = !empty($u['g_uid']) ? $u['g_uid'] : "-";
            } elseif ($lvl == 'User' || !empty($u['id_siswa'])) {
                $bg_badge = "bg-indigo-50 text-indigo-700 border-indigo-100";
                $role_title = "KARTU LOGIN SISWA";
                $pw_hint = !empty($u['s_nis']) ? "Default: " . htmlspecialchars($u['s_nis']) : "123456 / NIS Siswa";
                $uid = !empty($u['s_uid']) ? $u['s_uid'] : "-";
            }
        ?>
          <div class="page-break-avoid bg-white rounded-2xl border border-slate-200 p-5 relative overflow-hidden flex flex-col justify-between shadow-sm card-border-dashed">
            
            <!-- Slip Top Header -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 mb-3">
              <div class="flex items-center gap-2">
                <img src="../../<?php echo htmlspecialchars($logo_print); ?>" class="h-6 object-contain" alt="Logo" onerror="this.onerror=null; this.src='../../assets/img/asr_edu.png'">
                <div>
                  <span class="text-[9px] font-extrabold text-slate-800 uppercase tracking-tight block leading-none"><?php echo htmlspecialchars($company_name); ?></span>
                  <span class="text-[7px] text-slate-400 font-bold tracking-wider uppercase mt-0.5 block leading-none">Absensi Digital</span>
                </div>
              </div>
              <span class="text-[7px] font-extrabold tracking-wider border rounded-md px-2 py-0.5 <?php echo $bg_badge; ?> uppercase">
                <?php echo $lvl == 'User' ? 'Siswa' : $lvl; ?>
              </span>
            </div>

            <!-- Card Subtitle -->
            <span class="text-[8px] font-extrabold text-slate-400 tracking-wider uppercase block mb-3 text-center border-b border-dashed border-slate-100 pb-1">
              <?php echo $role_title; ?>
            </span>

            <!-- Main Body -->
            <div class="flex gap-4 items-start mb-4">
              
              <!-- Details -->
              <div class="flex-1 space-y-2">
                <div>
                  <span class="text-[8px] text-slate-400 font-bold uppercase tracking-wider block">Nama Lengkap</span>
                  <span class="text-xs font-extrabold text-slate-800 block truncate"><?php echo htmlspecialchars($u['name']); ?></span>
                  <?php if ($lvl == 'User' && !empty($u['s_kelas'])): ?>
                    <span class="text-[9px] bg-slate-100 text-slate-600 font-bold px-1.5 py-0.5 rounded mt-0.5 inline-block">Kelas <?php echo htmlspecialchars($u['s_kelas']); ?></span>
                  <?php elseif ($lvl == 'Guru' && !empty($u['g_jabatan'])): ?>
                    <span class="text-[9px] bg-slate-100 text-slate-600 font-bold px-1.5 py-0.5 rounded mt-0.5 inline-block"><?php echo htmlspecialchars($u['g_jabatan']); ?></span>
                  <?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-1.5">
                  <div>
                    <span class="text-[8px] text-slate-400 font-bold uppercase tracking-wider block">Username</span>
                    <span class="text-xs font-bold text-slate-700 bg-slate-50 px-2 py-1 rounded border border-slate-100 block truncate tracking-wide select-all"><?php echo htmlspecialchars($u['username']); ?></span>
                  </div>
                  <div>
                    <span class="text-[8px] text-slate-400 font-bold uppercase tracking-wider block">UID Kartu</span>
                    <span class="text-xs font-mono font-bold text-slate-600 bg-slate-50 px-2 py-1 rounded border border-slate-100 block truncate select-all"><?php echo htmlspecialchars($uid); ?></span>
                  </div>
                </div>

                <div>
                  <span class="text-[8px] text-slate-400 font-bold uppercase tracking-wider block">Password</span>
                  <span class="text-[10px] font-extrabold text-slate-800 tracking-wide select-all"><?php echo htmlspecialchars($pw_hint); ?></span>
                </div>
              </div>

              <!-- QR Code -->
              <div class="text-center flex-shrink-0 flex flex-col items-center gap-1.5 bg-slate-50 border border-slate-100 p-2 rounded-xl">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=<?php echo $login_url_encoded; ?>" 
                     class="w-[70px] h-[70px] object-contain" 
                     alt="QR Login"
                     title="Scan untuk Login">
                <span class="text-[7px] font-extrabold text-slate-400 uppercase tracking-widest leading-none block">SCAN LOGIN</span>
              </div>

            </div>

            <!-- Footer -->
            <div class="border-t border-slate-100 pt-2.5 mt-2 flex items-center justify-between text-[8px] text-slate-400">
              <span class="truncate max-w-[200px]">URL: <span class="text-slate-600 font-medium select-all"><?php echo htmlspecialchars($login_url); ?></span></span>
              <span class="font-bold text-[7px] text-slate-300">Powered by ASR.EDU</span>
            </div>

          </div>
        <?php endforeach; ?>

      </div>


      <!-- ============================================== -->
      <!-- 3. TABLE FORMAT VIEW (Grid Table list) -->
      <!-- ============================================== -->
      <div id="format-table-container" class="hidden bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm print-table">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center no-print">
          <h2 class="font-extrabold text-sm text-slate-800">Daftar Akun Pengguna</h2>
          <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider"><?php echo count($users_list); ?> Akun Terdaftar</span>
        </div>
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-slate-400 font-extrabold uppercase tracking-wider text-[10px]">
              <th class="px-6 py-4">No</th>
              <th class="px-6 py-4">Nama Lengkap</th>
              <th class="px-6 py-4">Role/Level</th>
              <th class="px-6 py-4">UID Kartu</th>
              <th class="px-6 py-4">Username</th>
              <th class="px-6 py-4">Password Default</th>
              <th class="px-6 py-4">Info Lain</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php 
            $no = 1;
            foreach ($users_list as $u):
                $lvl = $u['level_akses'];
                $pw_hint = "Password Rahasia";
                $uid = "-";
                $other_info = "-";
                
                if ($lvl == 'Admin') {
                    $pw_hint = "Password Rahasia Admin";
                } elseif ($lvl == 'Guru' || !empty($u['id_guru'])) {
                    $pw_hint = !empty($u['g_nip']) ? htmlspecialchars($u['g_nip']) : "NIP Guru";
                    $uid = !empty($u['g_uid']) ? htmlspecialchars($u['g_uid']) : "-";
                    $other_info = htmlspecialchars($u['g_jabatan'] ?? 'Guru');
                } elseif ($lvl == 'User' || !empty($u['id_siswa'])) {
                    $pw_hint = !empty($u['s_nis']) ? htmlspecialchars($u['s_nis']) : "123456";
                    $uid = !empty($u['s_uid']) ? htmlspecialchars($u['s_uid']) : "-";
                    $other_info = !empty($u['s_kelas']) ? 'Kelas ' . htmlspecialchars($u['s_kelas']) : 'Siswa';
                }
            ?>
              <tr class="hover:bg-slate-50/50 transition-colors">
                <td class="px-6 py-4 font-bold text-slate-400"><?php echo $no++; ?></td>
                <td class="px-6 py-4 font-extrabold text-slate-850"><?php echo htmlspecialchars($u['name']); ?></td>
                <td class="px-6 py-4">
                  <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider <?php 
                    echo $lvl == 'Admin' ? 'bg-blue-50 text-blue-700' : ($lvl == 'Guru' ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700'); 
                  ?>">
                    <?php echo $lvl == 'User' ? 'Siswa' : $lvl; ?>
                  </span>
                </td>
                <td class="px-6 py-4 font-mono text-slate-600 font-bold"><?php echo htmlspecialchars($uid); ?></td>
                <td class="px-6 py-4 font-bold text-slate-700 tracking-wide"><?php echo htmlspecialchars($u['username']); ?></td>
                <td class="px-6 py-4 font-extrabold text-slate-800"><?php echo htmlspecialchars($pw_hint); ?></td>
                <td class="px-6 py-4 text-slate-500"><?php echo htmlspecialchars($other_info); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php endif; ?>

  </main>

  <!-- jQuery CDN -->
  <script src="https://code.jquery.com/jquery-3.6.3.min.js" crossorigin="anonymous"></script>
  
  <script>
    $(document).ready(function() {
      // Toggle to Mini Card view
      $('#btn-format-mini').click(function() {
        setFormatActive($(this));
        $('#format-mini-container').removeClass('hidden');
        $('#format-premium-container').addClass('hidden');
        $('#format-table-container').addClass('hidden');
      });

      // Toggle to Premium view
      $('#btn-format-premium').click(function() {
        setFormatActive($(this));
        $('#format-premium-container').removeClass('hidden');
        $('#format-mini-container').addClass('hidden');
        $('#format-table-container').addClass('hidden');
      });

      // Toggle to Table view
      $('#btn-format-table').click(function() {
        setFormatActive($(this));
        $('#format-table-container').removeClass('hidden');
        $('#format-premium-container').addClass('hidden');
        $('#format-mini-container').addClass('hidden');
      });

      function setFormatActive(btn) {
        $('#btn-format-mini, #btn-format-premium, #btn-format-table')
          .removeClass('bg-white text-slate-800 shadow-sm')
          .addClass('text-slate-500 hover:text-slate-800');
        btn.removeClass('text-slate-500 hover:text-slate-800').addClass('bg-white text-slate-800 shadow-sm');
      }
    });
  </script>
</body>
</html>

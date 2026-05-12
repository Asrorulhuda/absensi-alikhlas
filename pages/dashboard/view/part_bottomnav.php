<?php
// Determine active page for styling
$current_page = basename($_SERVER['PHP_SELF']);
$dashboard_active = ($current_page == 'index.php' || $current_page == 'dashboard_guru.php') ? 'text-primary' : 'text-secondary';
$scan_active = ($current_page == 'scan2.php') ? 'text-primary' : 'text-secondary';
$profile_active = (strpos($current_page, 'user_update') !== false) ? 'text-primary' : 'text-secondary';
?>

<nav class="navbar navbar-light bg-white navbar-expand fixed-bottom d-xl-none shadow-lg" style="border-top-left-radius: 16px; border-top-right-radius: 16px; z-index: 9999; padding-bottom: 10px;">
    <ul class="navbar-nav nav-justified w-100 align-items-center">
        <!-- Dashboard (All Roles) -->
        <li class="nav-item">
            <a href="<?php echo ($_SESSION['akses'] == 'Guru') ? 'dashboard_guru.php' : 'index.php'; ?>" class="nav-link text-center <?php echo $dashboard_active; ?>">
                <i class="material-icons opacity-10" style="font-size: 24px;">dashboard</i>
                <span class="d-block" style="font-size: 10px; font-weight: 500;">Home</span>
            </a>
        </li>
        
        <?php if ($_SESSION['akses'] == 'Admin') { 
            $siswa_active = (strpos($current_page, 'siswa') !== false) ? 'text-primary' : 'text-secondary';
            $izin_active = (strpos($current_page, 'admin_approval') !== false) ? 'text-primary' : 'text-secondary';
        ?>
            <!-- Admin: Siswa -->
            <li class="nav-item">
                <a href="siswa.php" class="nav-link text-center <?php echo $siswa_active; ?>">
                    <i class="material-icons opacity-10" style="font-size: 24px;">face</i>
                    <span class="d-block" style="font-size: 10px; font-weight: 500;">Siswa</span>
                </a>
            </li>

            <!-- Admin: Scan (Center) -->
            <li class="nav-item">
                <a href="scan2.php" class="nav-link text-center <?php echo $scan_active; ?>">
                    <div class="bg-gradient-primary rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-primary" style="width: 45px; height: 45px; margin-top: -25px; border: 4px solid #f0f2f5;">
                        <i class="material-icons text-white" style="font-size: 24px;">qr_code_scanner</i>
                    </div>
                    <span class="d-block mt-1" style="font-size: 10px; font-weight: 500;">Scan</span>
                </a>
            </li>

            <!-- Admin: Approval -->
            <li class="nav-item">
                <a href="admin_approval.php" class="nav-link text-center <?php echo $izin_active; ?>">
                    <i class="material-icons opacity-10" style="font-size: 24px;">assignment_turned_in</i>
                    <span class="d-block" style="font-size: 10px; font-weight: 500;">Izin</span>
                </a>
            </li>

        <?php } elseif ($_SESSION['akses'] == 'Guru') { 
            $class_active = ($current_page == 'manage_class.php') ? 'text-primary' : 'text-secondary';
        ?>
            <!-- Guru: Kelas (Center) -->
            <li class="nav-item">
                <a href="manage_class.php" class="nav-link text-center <?php echo $class_active; ?>">
                    <div class="bg-gradient-info rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-info" style="width: 45px; height: 45px; margin-top: -25px; border: 4px solid #f0f2f5;">
                        <i class="material-icons text-white" style="font-size: 24px;">school</i>
                    </div>
                    <span class="d-block mt-1" style="font-size: 10px; font-weight: 500;">Kelas</span>
                </a>
            </li>
        <?php } else { 
             // Siswa / Other
             $izin_active = (strpos($current_page, 'user_izin') !== false) ? 'text-primary' : 'text-secondary';
        ?>
            <!-- Siswa: Pengajuan Izin (Center) -->
            <li class="nav-item">
                <a href="user_izin.php" class="nav-link text-center <?php echo $izin_active; ?>">
                    <div class="bg-gradient-success rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-success" style="width: 45px; height: 45px; margin-top: -25px; border: 4px solid #f0f2f5;">
                        <i class="material-icons text-white" style="font-size: 24px;">assignment</i>
                    </div>
                    <span class="d-block mt-1" style="font-size: 10px; font-weight: 500;">Izin</span>
                </a>
            </li>
        <?php } ?>

        <!-- Menu/More (All Roles) -->
        <li class="nav-item">
            <a href="javascript:;" class="nav-link text-center text-secondary" id="bottomNavToggle">
                <i class="material-icons opacity-10" style="font-size: 24px;">menu</i>
                <span class="d-block" style="font-size: 10px; font-weight: 500;">Menu</span>
            </a>
        </li>
    </ul>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('bottomNavToggle');
        const body = document.getElementsByTagName('body')[0];
        const sidenav = document.getElementById('sidenav-main');
        
        if(toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (body.classList.contains('g-sidenav-pinned')) {
                    body.classList.remove('g-sidenav-pinned');
                    // Ensure icon state is updated if needed
                } else {
                    body.classList.add('g-sidenav-pinned');
                    // If sidenav is hidden by default CSS on mobile, we might need to ensure it's visible
                    if(sidenav) {
                        sidenav.style.transform = 'translateX(0)';
                    }
                }
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
             if (body.classList.contains('g-sidenav-pinned') && 
                 !sidenav.contains(e.target) && 
                 !toggleBtn.contains(e.target)) {
                 body.classList.remove('g-sidenav-pinned');
                 if(sidenav && window.innerWidth < 1200) {
                     sidenav.style.transform = ''; // Reset to CSS default
                 }
             }
        });
    });
</script>

<style>
    /* Adjust main content padding to prevent bottom nav from covering content */
    @media (max-width: 1199.98px) {
        body {
            padding-bottom: 70px;
        }
        .fixed-plugin .card {
            bottom: 80px !important;
        }
    }
</style>
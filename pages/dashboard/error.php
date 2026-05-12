<?php
ob_start();
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['akses']) || $_SESSION['akses'] != 'Admin') {
    header('Location: ../../index');
    ob_end_flush();
    exit();
}

$ses_name = $_SESSION['name'];
$_SESSION['pages'] = "Error";

require_once "../../include/db_config.php";
include "control/confignusers_data.php";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/png" href="../../<?php echo $icon_bar; ?>">
    <title>Error - <?php echo $title_bar; ?></title>
    
    <link href="../../assets/css/Roboto.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="../../assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../../assets/css/Material_icon.css" rel="stylesheet">
    <link id="pagestyle" href="../../assets/css/material-dashboard-pro.min.css?v=3.0.6" rel="stylesheet" />
</head>

<body class="g-sidenav-show bg-gray-200">
    
    <?php include 'view/part_sidenav.php'; ?>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        
        <?php include 'view/part_topnav.php'; ?>

        <div class="container-fluid py-4">
            <div class="row min-vh-80 mb-3">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-danger shadow-danger border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">
                                    <i class="material-icons">error_outline</i> Error
                                </h6>
                            </div>
                        </div>

                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="material-icons text-danger" style="font-size: 80px;">warning</i>
                            </div>
                            <h3 class="text-dark">Parameter Tidak Valid</h3>
                            <p class="text-secondary mb-4">
                                Halaman yang Anda akses membutuhkan parameter yang valid.<br>
                                Silakan kembali dan coba lagi.
                            </p>
                            <div class="d-flex justify-content-center gap-3">
                                <a href="siswa" class="btn bg-gradient-primary">
                                    <i class="material-icons text-sm">arrow_back</i>&nbsp; Kembali ke Daftar Siswa
                                </a>
                                <a href="dashboard" class="btn bg-gradient-info">
                                    <i class="material-icons text-sm">dashboard</i>&nbsp; Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php include 'view/part_footer.php'; ?>
            <?php include 'view/part_bottomnav.php'; ?>
        </div>
    </main>

    <?php include 'view/part_theme_config.php'; ?>

    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
    <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), { damping: '0.5' });
        }
    </script>
</body>
</html>

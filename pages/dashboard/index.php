<?php
session_start();
date_default_timezone_set("Asia/Jakarta");

// Validasi akses
if (!in_array($_SESSION['akses'] ?? '', ['Admin', 'User', 'Guru'])) {
    header('location:../../index');
    exit();
}

// Redirect Guru
if ($_SESSION['akses'] == 'Guru') {
    header('location:dashboard_guru.php');
    exit();
}

$ses_name = $_SESSION['name'];
$_SESSION['pages'] = "Dashboard";

require_once "../../include/db_config.php";
include "control/confignusers_data.php";
include "control/dashboard_rekap.php";
?><!DOCTYPE html>
<html lang="en">
<head>
  <!-- Material Dashboard 2 - v3.0.4 
       Copyright 2022 Creative Tim (https://www.creative-tim.com)
       Licensed under MIT -->
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../../<?php echo $icon_bar; ?>">
  
  <!-- PWA Manifest & Meta -->
  <link rel="manifest" href="../../manifest.json">
  <meta name="theme-color" content="#e91e63">

  <title><?php echo $title_bar; ?></title>
  <!--     Fonts and icons     -->
  <link href="../../assets/css/Roboto.css" rel="stylesheet" type="text/css" />
  <!-- Nucleo Icons -->
  <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
  <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
  <!-- Font Awesome Icons -->
  <script src="../../assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
  <!-- Material Icons -->
  <link href="../../assets/css/Material_icon.css" rel="stylesheet">
  <!-- CSS Files -->
  <link id="pagestyle" href="../../assets/css/material-dashboard.css?v=3.0.4" rel="stylesheet" />
  
  <style>
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
        
        /* Ensure enough spacing between stacked cards due to floating icons */
        .mb-4 {
            margin-bottom: 2rem !important;
        }
        
        /* Stats Card Typography for small screens */
        .card-header p.text-sm {
            font-size: 0.8rem !important;
        }
        .card-header h4 {
            font-size: 1.3rem !important;
        }
        
        /* Adjust Chart containers */
        .chart-canvas {
            height: 180px !important; /* Slightly smaller charts on mobile */
        }
        
        /* Navbar adjustments */
        .navbar .container-fluid {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
    }
  </style>
</head>

<body class="g-sidenav-show  bg-gray-200">
  <!-- Sidebar -->
  <?php include 'view/part_sidenav.php';?>
  <!-- End of Sidebar -->
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
	 <?php include 'view/part_topnav.php';?>
	<!-- End Navbar -->
    <div class="container-fluid py-4">
      <?php if($_SESSION['akses'] == 'Admin'): ?>
      <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">person</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Total Siswa</p>
                <h4 class="mb-0"><?php echo $rowcount; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">login</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Presensi hari ini</p>
                <h4 class="mb-0"><?php echo $absensi; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">percent</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Persentase kehadiran </p>
                <h4 class="mb-0"><?php echo $prosentase; ?> %</h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-sm-6">
          <div class="card">
            <div class="card-header p-3 pt-2">
              <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                <i class="material-icons opacity-10">sd_card_alert</i>
              </div>
              <div class="text-end pt-1">
                <p class="text-sm mb-0 text-capitalize">Kartu Invalid</p>
                <h4 class="mb-0"><?php echo $invalid; ?></h4>
              </div>
            </div>
            <hr class="dark horizontal my-0">
            <div class="card-footer p-3">
            </div>
          </div>
        </div>
      </div>
	  
	  
      <div class="row mt-4">
        <div class="col-lg-6 col-md-6 mt-4 mb-4">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-bars" class="chart-canvas" height="200"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 ">Kehadiran 7 Hari Terakhir</h6>
              <p class="text-sm ">Statistik jumlah siswa hadir per hari</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> diperbarui baru saja </p>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-6 mt-4 mb-4">
          <div class="card z-index-2  ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-line" class="chart-canvas" height="200"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 "> Kehadiran per Kelas (Hari Ini) </h6>
              <p class="text-sm "> Top 5 Kelas dengan kehadiran terbanyak </p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> diperbarui baru saja </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php else: ?>
      <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Selamat Datang, <?php echo $ses_name; ?></h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="p-4">
                        <p>Selamat datang di Dashboard Siswa. Di sini Anda dapat melihat rekap presensi dan mengatur akun Anda.</p>
                        <a href="presensi_user.php" class="btn btn-info">
                            <i class="material-icons text-sm">calendar_month</i> Rekap Presensi
                        </a>
                        <a href="user_izin.php" class="btn btn-success ms-2">
                            <i class="material-icons text-sm">assignment</i> Ajukan Izin
                        </a>
                        <a href="user_update.php?id_user=<?php echo base64_encode($_SESSION['id']); ?>" class="btn btn-primary ms-2">
                            <i class="material-icons text-sm">manage_accounts</i> Manajemen Akun
                        </a>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <?php endif; ?>
	    
      <div class="row mb-4  mt-4">
        <?php if($_SESSION['akses'] == 'Admin'): ?>
        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
          <div class="card z-index-2 ">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
              <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
                <div class="chart">
                  <canvas id="chart-pie" class="chart-canvas" height="300"></canvas>
                </div>
              </div>
            </div>
            <div class="card-body">
              <h6 class="mb-0 ">Status Kehadiran Hari Ini</h6>
              <p class="text-sm ">Persentase status kehadiran siswa</p>
              <hr class="dark horizontal">
              <div class="d-flex ">
                <i class="material-icons text-sm my-auto me-1">schedule</i>
                <p class="mb-0 text-sm"> diperbarui baru saja </p>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
        <div class="<?php echo ($_SESSION['akses'] == 'Admin') ? 'col-lg-4 col-md-6' : 'col-12'; ?>">
          <div class="card h-100">
            <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
              <div class="bg-gradient-info shadow-info border-radius-lg pt-4 pb-3">
                <h6 class="text-white text-capitalize ps-3">Ulang Tahun Hari Ini</h6>
              </div>
            </div>
            <div class="card-body p-3">
			  <ul class="list-group">
				<?php 
					// Prepare a delete statement
					$sql = "SELECT * FROM data_siswa
							 where DAY(s_tgl_lahir) = DAY(CURDATE())
							   and MONTH(s_tgl_lahir) = MONTH(CURDATE());";
							   
					$query_ultah = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
                    $ada_ultah = mysqli_num_rows($query_ultah);
					if($ada_ultah > 0){
						while($data_ultah = mysqli_fetch_array($query_ultah)){
							?>
							<li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
							  <div class="d-flex align-items-center">
								<?php 
								echo "<img src='". htmlspecialchars($data_ultah['s_picture'])."' class='avatar avatar-sm me-3 border-radius-lg' alt='user". htmlspecialchars($data_ultah['s_id']) ."'>";
								?>
								<div class="d-flex flex-column">
								<?php
								 $ultah = date_create($data_ultah['s_tgl_lahir']);
								 $ultah = date_format($ultah,"F jS, Y");
								 echo "<h6 class='mb-1 text-dark text-sm'>".htmlspecialchars($data_ultah['s_nama'])."</h6>";
								 echo "<span class='text-xs'>".$ultah."</span>";
								?>
								</div>
							  </div>
							  <div class="d-flex align-items-center text-primary text-gradient text-sm font-weight-bold">
								<i class="material-icons text-sm me-1">cake</i>
							  </div>
							</li> 
					<?php
						}
					}else{
						echo "<li class='list-group-item border-0 ps-0'><p class='text-sm mb-0'><em>No one has a birthday for today.</em></p></li>";
					}
					?>
			  </ul>
			</div>
          </div>
        </div>
      </div>
	  
	  <!-- footer -->
		 <?php include 'view/part_footer.php';?>
	  <!-- footer -->
		 <?php include 'view/part_bottomnav.php';?>
    </div>
  </main>
  <?php include 'view/part_theme_config.php';?>
  
  <!--   Core JS Files   -->
  <script src="../../assets/js/core/popper.min.js"></script>
  <script src="../../assets/js/core/bootstrap.min.js"></script>
  <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
  <script src="../../assets/js/plugins/chartjs.min.js"></script>
  
  <script src="../../assets/js/plugins/jquery/jquery.min.js"></script>

  <?php if($_SESSION['akses'] == 'Admin'): ?>
  <script>
    var ctx = document.getElementById("chart-bars").getContext("2d");

    new Chart(ctx, {
      type: "bar",
      data: {
        labels: <?php echo $chart_labels_json; ?>,
        datasets: [{
          label: "Jumlah Hadir",
          tension: 0.4,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "rgba(255, 255, 255, .8)",
          data: <?php echo $chart_data_json; ?>,
          maxBarThickness: 6
        }, ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              suggestedMin: 0,
              suggestedMax: 50,
              beginAtZero: true,
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
              color: "#fff"
            },
          },
          x: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });


    var ctx2 = document.getElementById("chart-line").getContext("2d");

    new Chart(ctx2, {
      type: "bar",
      data: {
        labels: <?php echo $chart_class_labels_json; ?>,
        datasets: [{
          label: "Jumlah Hadir",
          tension: 0,
          borderWidth: 0,
          borderRadius: 4,
          borderSkipped: false,
          backgroundColor: "rgba(255, 255, 255, .8)",
          data: <?php echo $chart_class_data_json; ?>,
          maxBarThickness: 6

        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false,
          }
        },
        interaction: {
          intersect: false,
          mode: 'index',
        },
        scales: {
          y: {
            grid: {
              drawBorder: false,
              display: true,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
          x: {
            grid: {
              drawBorder: false,
              display: false,
              drawOnChartArea: true,
              drawTicks: false,
              borderDash: [5, 5],
              color: 'rgba(255, 255, 255, .2)'
            },
            ticks: {
              display: true,
              color: '#f8f9fa',
              padding: 10,
              font: {
                size: 14,
                weight: 300,
                family: "Roboto",
                style: 'normal',
                lineHeight: 2
              },
            }
          },
        },
      },
    });
  </script>
  <?php endif; ?>
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>
  <!-- Github buttons -->
  <script async defer src="../../assets/js/buttons_github.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../../assets/js/material-dashboard.min.js?v=3.0.4"></script>
</body>

</html>
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
$_SESSION['pages'] = "Siswa";

require_once "../../include/db_config.php";
include "control/confignusers_data.php";

// Initialize variables
$kelas = "";
$jurusan = "";
$sql = "";
$modal_stat = "";  // ← PENTING: Inisialisasi ini

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kelas = isset($_POST["choices-kelas"]) ? $_POST["choices-kelas"] : "";
    $jurusan = isset($_POST["choices-jurusan"]) ? $_POST["choices-jurusan"] : "";

    if ($jurusan != "") {
        if ($kelas != "") {
            $sql = "SELECT * FROM data_siswa, opsi_jurusan WHERE s_jurusan='$jurusan' AND s_jurusan=j_id AND s_kelas = '$kelas' AND s_status = 'Aktif' ORDER BY s_nama";
        } else {
            $sql = "SELECT * FROM data_siswa, opsi_jurusan WHERE s_jurusan='$jurusan' AND s_jurusan=j_id AND s_status = 'Aktif' ORDER BY s_nama";
        }
    } else {
        if ($kelas != "") {
            $sql = "SELECT * FROM data_siswa, opsi_jurusan WHERE s_kelas= '$kelas' AND s_jurusan=j_id AND s_status = 'Aktif' ORDER BY s_nama";
        } else {
            $sql = "SELECT * FROM data_siswa, opsi_jurusan WHERE s_status = 'Aktif' AND s_jurusan=j_id ORDER BY s_nama DESC";
        }
    }
} else {
    $sql = "SELECT * FROM data_siswa, opsi_jurusan WHERE s_status = 'Aktif' AND s_jurusan = j_id ORDER BY s_id DESC";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="../../<?php echo $icon_bar; ?>">
    <title><?php echo $title_bar; ?></title>

    <link href="../../assets/css/Roboto.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../../assets/css/nucleo-svg.css" rel="stylesheet" />
    <script src="../../assets/js/kit.fontawesome.com_42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="../../assets/css/Material_icon.css" rel="stylesheet">
    <link href="../../assets/css/animate.min.css" rel="stylesheet" />
    <link id="pagestyle" href="../../assets/css/material-dashboard-pro.min.css?v=3.0.6" rel="stylesheet" />
    <link rel="stylesheet" id="pagestyle" href="../../assets/vendor/DataTables_package/jquery.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="../../assets/vendor/DataTables_package/buttons.dataTables.min.css" />
    <link rel="stylesheet" type="text/css" href="../../assets/vendor/DataTables_package/dataTables.bootstrap5.min.css" />
    <style>
        table.dataTable.no-footer { border-bottom: 0 !important; }
    </style>
</head>

<body class="g-sidenav-show bg-gray-200">
    
    <?php include 'view/part_sidenav.php'; ?>

    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        
        <?php include 'view/part_topnav.php'; ?>
        
        <div class="container-fluid py-4">
            <div class="row min-vh-80 mb-3">
                <div class="col-12">
                    <div class="card my-4 h-100" id="data_list">

                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Daftar Siswa</h6>
                                <p class="text-white text-sm ps-3 mb-0">List seluruh siswa aktif</p>
                            </div>
                        </div>

                        <div class="card-body px-0 pb-2">
                            <div class="d-flex justify-content-between align-items-center px-4 mt-4 mb-3">
                                <div class="btn-group">
                                    <button type="button" class="btn bg-gradient-primary btn-sm mb-0" id="btn_add" onClick="location.href='siswa_registration'">
                                        <i class="material-icons text-sm">add</i>&nbsp; Tambah Data Siswa
                                    </button>
                                    <div class="btn-group ms-2">
                                        <button id="btnGroupDrop1" type="button" class="btn bg-gradient-info btn-sm mb-0 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="material-icons text-sm">file_download</i>&nbsp; Export
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="$('#table_siswa').DataTable().buttons(0,2).trigger()">PDF</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="$('#table_siswa').DataTable().buttons(0,1).trigger()">Excel</a></li>
                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="$('#table_siswa').DataTable().buttons(0,0).trigger()">CSV</a></li>
                                        </ul>
                                    </div>
                                    <button type="button" class="btn bg-gradient-success btn-sm mb-0 ms-2" data-bs-toggle="modal" data-bs-target="#importModal">
                                        <i class="material-icons text-sm">file_upload</i>&nbsp; Import
                                    </button>
                                </div>
                            </div>

                            <!-- Filter Section -->
                            <div class="p-4 bg-gray-100 border-radius-lg mb-4 mx-3">
                                <form action="siswa" method="post">
                                    <div class="row align-items-end">
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Kelas</label>
                                            <div class="input-group input-group-outline bg-white">
                                                <select class="form-control" name="choices-kelas" id="choices-kelas">
                                                    <option value="" selected>Pilih Tingkatan</option>
                                                    <?php
                                                    $sql_tingkat = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM opsi_tk_kelas ORDER BY tk_name DESC");
                                                    while ($data_tk = mysqli_fetch_assoc($sql_tingkat)) {
                                                        $selected = ($kelas == $data_tk['tk_name']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($data_tk['tk_name']) . '" ' . $selected . '>' . htmlspecialchars($data_tk['tk_name']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3 mb-3">
                                            <label class="form-label text-xs font-weight-bold text-uppercase text-secondary mb-1">Jurusan</label>
                                            <div class="input-group input-group-outline bg-white">
                                                <select class="form-control" name="choices-jurusan" id="choices-jurusan">
                                                    <option value="" selected>Pilih Kelas/Jurusan</option>
                                                    <?php
                                                    $sql_jurusan = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM opsi_jurusan");
                                                    while ($data_jurusan = mysqli_fetch_assoc($sql_jurusan)) {
                                                        $selected = ($jurusan == $data_jurusan['j_id']) ? 'selected' : '';
                                                        echo '<option value="' . htmlspecialchars($data_jurusan['j_id']) . '" ' . $selected . '>' . htmlspecialchars($data_jurusan['j_short']) . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-auto d-flex align-items-end mb-3">
                                            <button class="btn bg-gradient-info btn-sm mb-0 me-2" type="submit"><i class="material-icons text-sm">filter_list</i>&nbsp; Filter</button>
                                            <a class="btn bg-gradient-default btn-sm mb-0" href="siswa"><i class="material-icons text-sm">restart_alt</i>&nbsp; Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Table -->
                            <div class="row justify-content-center">
                                <div class="table-responsive p-0 w-95">
                                    <div class="dataTable-wrapper dataTable-loading no-footer sortable searchable fixed-columns">
                                        <table id="table_siswa" class="table table-hover table-fixed" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>UID</th>
                                                    <th>Siswa</th>
                                                    <th class="text-center">Gender</th>
                                                    <th class="text-center">Kelas</th>
                                                    <th class="text-center">Umur</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody style="border-style:none;">
                                                <?php
                                                $no = 0;
                                                $s_member = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
                                                while ($d_member = mysqli_fetch_array($s_member)) {
                                                    $id64 = base64_encode($d_member['s_id']);
                                                    $no++;
                                                ?>
                                                    <tr style="border-bottom: 0;">
                                                        <td><?= $no; ?></td>
                                                        <td><?= htmlspecialchars($d_member['s_uid']); ?></td>
                                                        <td>
                                                            <div class="d-flex px-2 py-1">
                                                                <div><img src="<?= htmlspecialchars($d_member['s_picture']); ?>" class="avatar avatar-sm me-3 border-radius-lg" alt="user<?= $d_member['s_id']; ?>"></div>
                                                                <div class="d-flex flex-column justify-content-center">
                                                                    <h6 class="mb-0 text-sm"><?= htmlspecialchars($d_member['s_nama']); ?></h6>
                                                                    <p class="text-xs text-secondary mb-0"><?= htmlspecialchars($d_member['s_nis']); ?></p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="text-center"><?= htmlspecialchars($d_member['s_kelamin']); ?></td>
                                                        <td class="text-center"><?= htmlspecialchars($d_member['s_kelas']); ?> - <?= htmlspecialchars($d_member['j_short']); ?></td>
                                                        <td class="text-center">
                                                            <?php
                                                            $dateOfBirth = $d_member['s_tgl_lahir'];
                                                            $today = date("Y-m-d");
                                                            $diff = date_diff(date_create($dateOfBirth), date_create($today));
                                                            echo $diff->format('%y');
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a href='javascript:;' data-bs-toggle='modal' data-bs-target='#detailModal' data-id='<?= $d_member['s_id']; ?>' id='getDetailUser' title='Detail Record' class='btn btn-info btn-sm mb-0 px-3 me-1'><i class='material-icons text-sm'>visibility</i></a>
                                                            <a href='siswa_update?id_siswa=<?= $id64; ?>' title='Update Record' class='btn btn-warning btn-sm mb-0 px-3 me-1'><i class='material-icons text-sm'>edit</i></a>
                                                            <a href='javascript:;' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='<?= $d_member['s_id']; ?>' id='getDelUser' title='Delete Record' class='btn btn-danger btn-sm mb-0 px-3 me-1'><i class='material-icons text-sm'>delete</i></a>
                                                            <?php if ($d_member['user_stat'] == 0): ?>
                                                                <a href='user_create?id_siswa=<?= $d_member['s_id']; ?>' title='Buat Akun' class='btn btn-success btn-sm mb-0 px-3'><i class='material-icons text-sm'>person_add</i></a>
                                                            <?php else: ?>
                                                                <a href='javascript:;' title='Sudah Punya Akun' class='btn btn-secondary btn-sm mb-0 px-3' disabled style='pointer-events:none;'><i class='material-icons text-sm'>person_off</i></a>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Detail -->
            <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title font-weight-normal text-white">Detail Siswa</h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="spinner-border text-danger justify-content-center" role="status" id="modal-detailloader" style="display: none;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div id="data_detail"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">OK</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Delete -->
            <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title font-weight-normal text-white">Delete Data Siswa</h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close">X</button>
                        </div>
                        <div class="modal-body">
                            <div class="spinner-border text-danger justify-content-center" role="status" id="modal-loader" style="display: none;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div id="data_delete"></div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn bg-gradient-danger" id="modalDelete">Delete</a>
                            <button type="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Notifikasi -->
            <div class="modal fade" id="Modal_notif" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div id="Modal_notif_head" class="modal-header">
                            <h5 class="modal-title font-weight-normal text-white">
                                <?php
                                if ($modal_stat == "Delete Status") {
                                    echo "<i class='far fa-trash-alt'></i>";
                                }
                                if ($modal_stat == "Update Status") {
                                    echo "<i class='far fa-edit'></i>";
                                }
                                ?>
                                &nbsp; <?php echo $modal_stat; ?>
                            </h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="d-flex justify-content-center">
                                <p id="notif_msg" style="font-size:20px;"></p>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <a role="button" class="btn bg-gradient-secondary shadow-secondary" data-bs-dismiss="modal">OK</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import Modal -->
            <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-gradient-success">
                            <h5 class="modal-title text-white">Import Data Siswa (CSV)</h5>
                            <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="siswa_import.php" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="csv_file" class="form-label">Pilih File CSV</label>
                                    <input class="form-control border p-2" type="file" name="csv_file" id="csv_file" accept=".csv" required>
                                </div>
                                <div class="mb-3">
                                    <p class="text-sm">Gunakan template berikut untuk format data yang benar:</p>
                                    <a href="templates/template_siswa.csv" class="btn btn-outline-success btn-sm w-100" download>
                                        <i class="material-icons text-sm">download</i> Download Template CSV
                                    </a>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn bg-gradient-success">Import Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php include 'view/part_footer.php'; ?>
            <?php include 'view/part_bottomnav.php'; ?>
        </div>
    </main>

    <?php include 'view/part_theme_config.php'; ?>

    <!-- Scripts -->
    <script src="../../assets/vendor/DataTables_package/jquery-3.5.1.js"></script>
    <script src="../../assets/vendor/DataTables_package/jquery.dataTables.min.js"></script>
    <script src="../../assets/vendor/DataTables_package/dataTables.buttons.min.js"></script>
    <script src="../../assets/vendor/DataTables_package/jszip.min.js"></script>
    <script src="../../assets/vendor/DataTables_package/pdfmake.min.js"></script>
    <script src="../../assets/vendor/DataTables_package/vfs_fonts.js"></script>
    <script src="../../assets/vendor/DataTables_package/buttons.html5.min.js"></script>
    <script src="../../assets/vendor/DataTables_package/buttons.colVis.min.js"></script>
    <script src="../../assets/js/core/popper.min.js"></script>
    <script src="../../assets/js/core/bootstrap.min.js"></script>
    <script src="../../assets/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="../../assets/js/plugins/smooth-scrollbar.min.js"></script>
    <script src="../../assets/js/plugins/choices.min.js"></script>
    <script async defer src="../../assets/js/buttons_github.js"></script>
    <script src="../../assets/js/material-dashboard-pro.min.js?v=3.0.6"></script>

    <script>
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();

            $(document).on('click', '#getDelUser', function(e) {
                e.preventDefault();
                var uid = $(this).data('id');
                $('#data_delete').html('');
                $('#modalDelete').attr('href', 'control/siswa_del.php?id=' + uid);
                $('#modal-loader').show();

                $.ajax({
                    url: 'view/siswa_del.php',
                    type: 'POST',
                    data: 'id=' + uid,
                    dataType: 'html'
                }).done(function(data) {
                    $('#data_delete').html(data);
                    $('#modal-loader').hide();
                }).fail(function() {
                    $('#data_delete').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
                    $('#modal-loader').hide();
                });
            });

            $(document).on('click', '#getDetailUser', function(e) {
                e.preventDefault();
                var uid = $(this).data('id');
                $('#data_detail').html('');
                $('#modal-detailloader').show();

                $.ajax({
                    url: 'view/siswa_detail.php',
                    type: 'POST',
                    data: 'id=' + uid,
                    dataType: 'html'
                }).done(function(data) {
                    $('#data_detail').html(data);
                    $('#modal-detailloader').hide();
                }).fail(function() {
                    $('#data_detail').html('<i class="glyphicon glyphicon-info-sign"></i> Something went wrong, Please try again...');
                    $('#modal-detailloader').hide();
                });
            });

            $('#table_siswa').DataTable({
                columnDefs: [{ 'orderable': false, 'targets': 6 }],
                buttons: [
                    { extend: 'csvHtml5', title: 'Daftar Siswa', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                    { extend: 'excelHtml5', title: 'Daftar Siswa', exportOptions: { columns: [0, 1, 2, 3, 4, 5] } },
                    {
                        extend: 'pdfHtml5',
                        title: 'Daftar Siswa',
                        exportOptions: { columns: [0, 1, 2, 3, 4, 5] },
                        customize: function(doc) {
                            doc.defaultStyle.fontSize = 11;
                            doc.styles.tableHeader.fontSize = 12;
                            doc.styles.title.fontSize = 16;
                            doc.styles.title.bold = true;
                            doc.content[1].table.widths = ['5%', '20%', '30%', '20%', '20%', '10%'];
                            var now = new Date();
                            var jsDate = now.getDate() + '-' + (now.getMonth() + 1) + '-' + now.getFullYear();
                            doc['footer'] = function(page, pages) {
                                return {
                                    columns: [
                                        { alignment: 'left', text: ['Created on: ', { text: jsDate.toString() }] },
                                        { alignment: 'right', text: ['page ', { text: page.toString() }, ' of ', { text: pages.toString() }] }
                                    ],
                                    margin: 20
                                };
                            };
                            var objLayout = {};
                            objLayout['hLineWidth'] = function(i) { return .5; };
                            objLayout['vLineWidth'] = function(i) { return .5; };
                            objLayout['hLineColor'] = function(i) { return '#aaa'; };
                            objLayout['vLineColor'] = function(i) { return '#aaa'; };
                            objLayout['paddingLeft'] = function(i) { return 4; };
                            objLayout['paddingRight'] = function(i) { return 4; };
                            doc.content[1].layout = objLayout;
                        }
                    }
                ]
            });
        });
    </script>

    <script>
        if (document.getElementById('choices-kelas')) {
            new Choices(document.getElementById('choices-kelas'), { searchEnabled: true });
        }
        if (document.getElementById('choices-jurusan')) {
            new Choices(document.getElementById('choices-jurusan'), { searchEnabled: true });
        }
    </script>

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), { damping: '0.5' });
        }
    </script>

    <?php
    if (isset($_GET['msg'])) {
        $stat_msg = base64_decode($_GET['msg']);

        if ($stat_msg == "300") {
            $modal_stat = "Delete Status";
            echo "<script>
                $(document).ready(function(){
                    $('#Modal_notif_head').addClass('bg-primary');
                    $('#notif_msg').text('Gagal menghapus data Siswa, silahkan ulangi proses!');
                    $('#Modal_notif').modal('show');
                });
            </script>";
        }
        if ($stat_msg == "301") {
            $modal_stat = "Delete Status";
            echo "<script>
                $(document).ready(function(){
                    $('#Modal_notif_head').addClass('bg-success');
                    $('#notif_msg').text('Data Siswa berhasil dihapus');
                    $('#Modal_notif').modal('show');
                });
            </script>";
        }
        if ($stat_msg == "200") {
            $modal_stat = "Update Status";
            $info = base64_decode($_GET['info']);
            echo "<script>
                $(document).ready(function(){
                    $('#Modal_notif_head').addClass('bg-success');
                    $('#notif_msg').text('" . addslashes($info) . "');
                    $('#Modal_notif').modal('show');
                });
            </script>";
        }
    }
    ?>
</body>
</html>

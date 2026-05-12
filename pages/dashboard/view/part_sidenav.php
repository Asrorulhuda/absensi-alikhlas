<?php 
    $ses_page = $_SESSION['pages'];
	$tipe_akses = $_SESSION['akses'];
	if ($tipe_akses == 'Admin'){
        // Count Pending Requests
        $pending_count = 0;
        $sql_count = "SELECT COUNT(*) as count FROM pengajuan_izin WHERE status = 'Pending'";
        if (isset($GLOBALS["___mysqli_ston"])) {
             $result_count = mysqli_query($GLOBALS["___mysqli_ston"], $sql_count);
             if ($result_count) {
                 $row_count = mysqli_fetch_assoc($result_count);
                 $pending_count = $row_count['count'];
             }
        }
?>
 <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="../dashboard">
        <img src="../../<?php echo $icon_dashboard; ?>" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold text-white"><?php echo $company; ?></span>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a <?php if ($ses_page == "Dashboard"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="../dashboard">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
		


		<li class="nav-item">
          <a <?php if ($ses_page == "Invalid"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="invalid">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">warning</i>
            </div>
            <span class="nav-link-text ms-1">Invalid Card</span>
          </a>
        </li>
		<li class="nav-item ">
			<a <?php if ($ses_page == "Siswa" || $ses_page == "Guru"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> data-bs-toggle="collapse" aria-expanded="false" href="#civitas_drop">
				<i class="material-icons opacity-10">supervisor_account</i>
				<span class="nav-link-text ms-1">Civitas <b class="caret"></b></span>
			</a>
			<div class="collapse " id="civitas_drop">
				<ul class="nav nav-sm flex-column">
					<li class="nav-item">
						<a class="nav-link text-white " href="siswa">
						<i class="material-icons opacity-10">face</i>
						<span class="sidenav-normal  ms-2  ps-1">Siswa </span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white " href="guru">
						<i class="material-icons opacity-10">sentiment_satisfied</i>
						<span class="sidenav-normal  ms-2  ps-1">Guru</span>
						</a>
					</li>
				</ul>
			</div>
		</li>
		<li class="nav-item ">
			<a <?php if ($ses_page == "Presensi"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> data-bs-toggle="collapse" aria-expanded="false" href="#presensi_drop">
				<i class="material-icons opacity-10">login</i>
				<span class="nav-link-text ms-1">Presensi <b class="caret"></b></span>
			</a>
			<div class="collapse " id="presensi_drop">
				<ul class="nav nav-sm flex-column">
					<li class="nav-item">
						<a class="nav-link text-white " href="presensi_harian">
						<i class="material-icons opacity-10">today</i>
						<span class="sidenav-normal  ms-2  ps-1">Rekap Harian </span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white " href="presensi_bulanan">
						<i class="material-icons opacity-10">calendar_month</i>
						<span class="sidenav-normal  ms-2  ps-1">Rekap Bulanan </span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white " href="presensi_semester">
						<i class="material-icons opacity-10">calendar_view_month</i>
						<span class="sidenav-normal  ms-2  ps-1">Rekap Semester </span>
						</a>
					</li>
				</ul>
			</div>
		</li>

        <li class="nav-item ">
			<a <?php if ($ses_page == "Manajemen"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> data-bs-toggle="collapse" aria-expanded="false" href="#manage_drop">
				<i class="material-icons opacity-10">settings_applications</i>
				<span class="nav-link-text ms-1">Manajemen <b class="caret"></b></span>
			</a>
			<div class="collapse " id="manage_drop">
				<ul class="nav nav-sm flex-column">
					<li class="nav-item">
						<a class="nav-link text-white " href="manage_kbm.php">
						<i class="material-icons opacity-10">schedule</i>
						<span class="sidenav-normal  ms-2  ps-1">Jadwal KBM </span>
						</a>
					</li>
                    <li class="nav-item">
						<a class="nav-link text-white " href="admin_approval.php">
						<i class="material-icons opacity-10">fact_check</i>
						<span class="sidenav-normal  ms-2  ps-1">Persetujuan Izin 
                            <?php if(isset($pending_count) && $pending_count > 0): ?>
                            <span class="badge bg-gradient-danger ms-2"><?php echo $pending_count; ?></span>
                            <?php endif; ?>
                        </span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white " href="manage_activity.php">
						<i class="material-icons opacity-10">event</i>
						<span class="sidenav-normal  ms-2  ps-1">Kegiatan</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white " href="manage_eskul.php">
						<i class="material-icons opacity-10">sports_soccer</i>
						<span class="sidenav-normal  ms-2  ps-1">Eskul</span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white " href="notification?id=1">
						<i class="material-icons opacity-10">circle_notifications</i>
						<span class="sidenav-normal  ms-2  ps-1">Notification </span>
						</a>
					</li>
				</ul>
			</div>
		</li>
		
		<hr class="horizontal light mt-0 mb-2">
		
		
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs text-white font-weight-bolder opacity-8">Configuration</h6>
        </li>
        <li class="nav-item">
          <a <?php if ($ses_page == "User"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="user">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">manage_accounts</i>
            </div>
            <span class="nav-link-text ms-1">User</span>
          </a>
        </li>
		<li class="nav-item ">
			<a <?php if ($ses_page == "System"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> data-bs-toggle="collapse" aria-expanded="false" href="#system_drop">
				<div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
				  <i class="material-icons opacity-10">desktop_windows</i>
				</div>
				<span class="nav-link-text ms-1">System <b class="caret"></b></span>
			</a>
			<div class="collapse " id="system_drop">
				<ul class="nav nav-sm flex-column">
					<li class="nav-item">
						<a class="nav-link text-white " href="system?id=1">
						<i class="material-icons opacity-10">settings_applications</i>
						<span class="nav-link-text  ms-2  ps-1">App Settings </span>
						</a>
					</li>
					<li class="nav-item">
						<a class="nav-link text-white " href="system_option">
						<i class="material-icons opacity-10">format_list_bulleted</i>
						<span class="nav-link-text  ms-2  ps-1">Opsi Kelas </span>
						</a>
					</li>
				</ul>
			</div>
		</li>
      </ul>
    </div>
  </aside>
   <?php 
   }else if ($tipe_akses == 'Guru'){
	?>
 <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="dashboard_guru.php">
        <img src="../../<?php echo $icon_dashboard; ?>" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold text-white"><?php echo $company; ?></span>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a <?php if ($ses_page == "Dashboard"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="dashboard_guru.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
        
        <li class="nav-item">
          <a <?php if ($ses_page == "Pengajuan Izin"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="guru_izin.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">assignment</i>
            </div>
            <span class="nav-link-text ms-1">Pengajuan Izin/Sakit</span>
          </a>
        </li>

        <li class="nav-item">
          <a <?php if ($ses_page == "Kelas"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="manage_class.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">school</i>
            </div>
            <span class="nav-link-text ms-1">Manajemen Kelas</span>
          </a>
        </li>

        <li class="nav-item">
          <a <?php if ($ses_page == "User"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="user_update?id_user=<?php echo base64_encode($_SESSION['id']);?>">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">manage_accounts</i>
            </div>
            <span class="nav-link-text ms-1">Manajemen Akun</span>
          </a>
        </li>
        
      </ul>
    </div>
  </aside>
   <?php 
   }else{
	?>
 <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3   bg-gradient-dark" id="sidenav-main">
    <div class="sidenav-header">
      <i class="fas fa-times p-3 cursor-pointer text-white opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
      <a class="navbar-brand m-0" href="../dashboard">
        <img src="../../<?php echo $icon_dashboard; ?>" class="navbar-brand-img h-100" alt="main_logo">
        <span class="ms-1 font-weight-bold text-white"><?php echo $company; ?></span>
      </a>
    </div>
    <hr class="horizontal light mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto " id="sidenav-collapse-main">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a <?php if ($ses_page == "Dashboard"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="../dashboard">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">dashboard</i>
            </div>
            <span class="nav-link-text ms-1">Dashboard</span>
          </a>
        </li>
		
        <li class="nav-item">
          <a <?php if ($ses_page == "Presensi"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="presensi_user">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">calendar_month</i>
            </div>
            <span class="nav-link-text ms-1">Rekap Presensi</span>
          </a>
        </li>
        <li class="nav-item">
          <a <?php if ($ses_page == "Pengajuan Izin"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="user_izin.php">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">assignment</i>
            </div>
            <span class="nav-link-text ms-1">Pengajuan Izin</span>
          </a>
        </li>
		<li class="nav-item">
          <a <?php if ($ses_page == "User"){ echo "class='nav-link text-white active bg-gradient-primary'";}else {  echo "class='nav-link text-white'";}?> href="user_update?id_user=<?php echo base64_encode($_SESSION['id']);?>">
            <div class="text-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="material-icons opacity-10">manage_accounts</i>
            </div>
            <span class="nav-link-text ms-1">Manajemen Akun</span>
          </a>
        </li>
		
      </ul>
    </div>
  </aside>

	
	<?php 	
	}
?>
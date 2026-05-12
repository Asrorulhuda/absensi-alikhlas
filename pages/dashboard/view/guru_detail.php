<?php 
 require_once "../../../include/db_config.php";
 
 if (isset($_REQUEST['id'])) {
   
 $id = intval($_REQUEST['id']);
 $sql = "SELECT * FROM data_guru WHERE g_id='$id'";
 $g_list = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
 while ($g_data = mysqli_fetch_array($g_list)){
 
 ?>

<div class="row justify-content-center mt-4 ms-2">
	<div class="col-md-5 col-ms-8">
	   <div class="d-flex justify-content-center animate__animated animate__fadeInDown">
		<img id="picture_modal" src="<?php echo $g_data['g_picture'];?>" alt="Member Avatar" class="rounded-circle z-depth-2" width="200px" height="200px" />
	   </div>
	   <div class="d-flex justify-content-center mb-0">
	       <h5 class="mt-2 text-center"><?php echo $g_data['g_nama'];?>
	   </div>
	   <div class="d-flex justify-content-center mb-1">
	       <p class="mt-0 text-md text-center"><?php echo $g_data['g_nip'];?></p>
	   </div>
	</div>
	<div class="col-md-7 col-ms-8">
			<table class="table table-borderless mb-0">	
				<tr>
				  <td>Jenis Kelamin</td>
				  <td>: <?php echo $g_data['g_kelamin'];?></td>
				</tr>
				<tr>
				  <td>Tanggal Lahir</td>
				  <td>: <?php $tgl=date_create($g_data['g_tgl_lahir']); echo date_format($tgl,"j F Y");?></td>
				</tr>
				<tr>
				  <td>Jabatan</td>
				  <td>: <?php echo $g_data['g_jabatan'];?></td>
				</tr>
				<tr>
				  <td>Kompetensi</td>
				  <td>: <?php echo $g_data['g_kompetensi'];?></td>
				</tr>
				<tr>
				  <td>Tugas Tambahan</td>
				  <td>: <?php echo $g_data['g_tgs_tambahan'];?></td>
				</tr>
				<tr>
				  <td>Email</td>
				  <td>: <?php echo $g_data['g_mail'];?></td>
				</tr>
				<tr>
				  <td>Kontak</td>
				  <td>: <?php echo $g_data['g_contact'];?></td>
				</tr>
				<tr>
				  <td>Alamat</td>
				  <td>: </td>
				</tr>
			</table>
			<div class="col-md-11 offset-md-1 col-sm-10 offset-sm-2">
		      <p><?php echo $g_data['g_alamat'];?></p>
		   </div>
	</div>
	
	
</div>
<?php
 } 
 }
 ?>
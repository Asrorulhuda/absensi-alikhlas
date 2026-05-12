<?php 
 require_once "../../../include/db_config.php";
 
 if (isset($_REQUEST['id'])) {
   
 $id = intval($_REQUEST['id']);
 $sql = "SELECT data_absen.id, data_absen.uid, tanggal, jam_masuk, jam_keluar, status, keterangan, s_picture, s_nama, s_kelas, j_short
				FROM data_absen, data_siswa, opsi_jurusan WHERE data_absen.uid=data_siswa.s_uid  AND s_jurusan=j_id AND data_absen.id ='$id'";
 $s_member = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
 while ($d_member = mysqli_fetch_array($s_member)){
 
 ?>

<div class="row justify-content-center mt-4 ms-2">
	<div class="col-md-10 col-ms-8">
	
	   <div class="d-flex justify-content-center animate__animated animate__fadeInDown">
		<img id="picture_modal" src="<?php echo $d_member['s_picture'];?>" alt="Member Avatar" class="rounded-circle z-depth-2" width="200px" height="200px" />
	   </div>
	   <div class="row justify-content-center mb-0">
	       <h5 class="mt-3 mb-0 text-center"><?php echo $d_member['s_nama'];?></h5>
		   <p class="text-center"><?php echo $d_member['s_kelas']."-". $d_member['j_short'];?></p>
	   </div>
	   <div class="d-flex justify-content-center mb-0">
			<table class="table table-borderless">
			  <tbody>
				<tr>
				  <td class="text-end">Tanggal Presensi:</td>
				  <td> <?php $tgl=date_create($d_member['tanggal']); echo date_format($tgl,"j F Y");?> </td>
				</tr>
				<tr>
				  <td class="text-end">Jam Masuk:</td>
				  <td> <?php echo $d_member['jam_masuk'];?></td>
				</tr>
				<tr>
				  <td class="text-end">Jam Keluar:</td>
				  <td> <?php echo $d_member['jam_keluar'];?></td>
				</tr>
				<tr>
				  <td class="text-end">Keterangan:</td>
				  <td> <?php echo $d_member['keterangan'];?></td>
				</tr>
				<tr>
				  <td class="text-end">Status Kartu:</td>
				  <td> <?php echo $d_member['status'];?></td>
				</tr>
			  </tbody>
			</table>
		   
	   </div>
	</div>
	
</div>
<?php
 } 
 }
 ?>
<?php 
 require_once "../../../include/db_config.php";
 
 if (isset($_REQUEST['id'])) {
   
 $id = intval($_REQUEST['id']);
 $sql = "SELECT * FROM data_siswa, opsi_jurusan WHERE s_jurusan = j_id AND s_id='$id'";
 $s_member = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
 while ($d_member = mysqli_fetch_array($s_member)){
 
 ?>

<div class="row justify-content-center mt-4 ms-2">
	<div class="col-md-10 col-ms-8">
	
	   <div class="d-flex justify-content-center animate__animated animate__fadeInDown">
		<img id="picture_modal" src="<?php echo $d_member['s_picture'];?>" alt="Member Avatar" class="rounded-circle z-depth-2" width="200px" height="200px" />
	   </div>
	   <div class="d-flex justify-content-center mb-0">
	       <h5 class="mt-2 text-center"><?php echo $d_member['s_nama'];?></h5>
	   </div>
	   <div class="d-flex justify-content-center mb-1">
		    <p><?php echo $d_member['s_uid'];?> 
			<?php 
			if ($d_member['s_status'] == "Aktif"){
				echo "<span class='badge badge-success badge-sm'>".$d_member['s_status']."</span>";				
			}else if ($d_member['s_status'] == "Lulus"){
				echo "<span class='badge badge-danger badge-sm'>".$d_member['s_status']."</span>";
			}else{
				echo "<span class='badge badge-primary badge-sm'>".$d_member['s_status']."</span>";
			}
			?>
			</p> 
	   </div>
	   <div class="d-flex justify-content-center mb-0">
			<table class="table table-borderless">
			  <tbody>
				<tr>
				  <td>NIS</td>
				  <td>: <?php echo $d_member['s_nis'];?></td>
				</tr>
				<tr>
				  <td>Tanggal Lahir</td>
				  <td>: <?php $tgl=date_create($d_member['s_tgl_lahir']); echo date_format($tgl,"j F Y");?></td>
				</tr>
				<tr>
				  <td>Kelas</td>
				  <td>: <?php echo $d_member['s_kelas'];?> - <?php echo $d_member['j_name'];?> </td>
				</tr>
				<tr>
				  <td>Phone</td>
				  <td>: <?php echo $d_member['s_phone'];?></td>
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
<?php 
 require_once "../../../include/db_config.php";
 
 if (isset($_REQUEST['id'])) {
   
 $id = intval($_REQUEST['id']);
 $sql = "SELECT * FROM data_siswa,opsi_jurusan WHERE s_jurusan = j_id AND s_id='$id'";
 $s_member = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
 while ($d_member=mysqli_fetch_array($s_member)){
 
 ?>

<div class="d-flex justify-content-center">
   <p>Yakin akan menghapus data siswa berikut ini?</p><br>
</div>
<div class="row justify-content-center mt-4">
	<div class="col-md-5 col-ms-8">
	
	   <div class="d-flex justify-content-center animate__animated animate__fadeInDown">
		<img id="picture_modal" src="<?php echo $d_member['s_picture'];?>" alt="Member Avatar" class="rounded-circle z-depth-2" width="100px" height="100px" />
	   </div>
	   <div class="d-flex justify-content-center mb-0 mt-1">
		   <p class="mt-2 text-center"><span><b><?php echo $d_member['s_nama'];?></b></span>
		   <br>
		   <span><?php echo $d_member['s_uid'];?></span>
		   <br>
		   <span><?php echo $d_member['s_kelas']."-".$d_member['j_short'];?></span></p>
	   </div>
	</div>
</div>
<?php
 } 
 }
 ?>
<?php 
 require_once "../../../include/db_config.php";
 
 if (isset($_REQUEST['id'])) {
   
 $id = intval($_REQUEST['id']);
 $sql = "SELECT * FROM data_guru WHERE g_id='$id'";
 $g_list = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
 while ($g_data=mysqli_fetch_array($g_list)){
 
 ?>

<div class="d-flex justify-content-center">
   <p>Yakin akan menghapus data Guru berikut ini?</p><br>
</div>
<div class="row justify-content-center mt-4">
	<div class="col-md-5 col-ms-8">
	
	   <div class="d-flex justify-content-center animate__animated animate__fadeInDown">
		<img id="picture_modal" src="<?php echo $g_data['g_picture'];?>" alt="Member Avatar" class="rounded-circle z-depth-2" width="100px" height="100px" />
	   </div>
	   <div class="d-flex justify-content-center mb-0 mt-1">
		   <p class="mt-2 text-center"><span><b><?php echo $g_data['g_nama'];?></b></span>
		   <br>
		   <span><?php echo $g_data['g_nip'];?></span>
		   <br>
		   <span><?php echo $g_data['g_jabatan']."-".$g_data['g_kompetensi'];?></span></p>
	   </div>
	</div>
</div>
<?php
 } 
 }
 ?>
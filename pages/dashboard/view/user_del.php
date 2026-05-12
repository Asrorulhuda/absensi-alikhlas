<?php 
 require_once "../../../include/db_config.php";
 
 if (isset($_REQUEST['id'])) {  
 $id = intval($_REQUEST['id']);
 $sql = "SELECT * FROM users WHERE id='$id'";
 $s_member = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
 while ($d_member=mysqli_fetch_array($s_member)){
 
 ?>

<div class="d-flex justify-content-center">
   <p>Are you sure to delete this user data?</p><br>
</div>
<div class="row justify-content-center mt-4">
	<div class="col-md-5 col-ms-8">
	
	   <div class="d-flex justify-content-center animate__animated animate__fadeInDown">
		<img id="picture_modal" src="<?php echo $d_member['picture'];?>" alt="Member Avatar" class="rounded-circle z-depth-2" width="100px" height="100px" />
	   </div>
	   <div class="d-flex justify-content-center mb-0 mt-1">
		   <p class="mt-2 text-center"><span><b><?php echo $d_member['name'];?></b></span>
		   <br>
		   <span><?php echo $d_member['username'];?></span></p>
	   </div>
	</div>
</div>
<?php
 } 
 }
 ?>
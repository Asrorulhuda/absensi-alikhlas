<?php 
 require_once "../../../include/db_config.php";
 
 if (isset($_REQUEST['id'])) {
   
 $id = intval($_REQUEST['id']);
 $sql = "SELECT * FROM users WHERE id='$id'";
 $s_member = mysqli_query($GLOBALS["___mysqli_ston"], $sql);
 while ($d_member = mysqli_fetch_array($s_member)){
 
 ?>
<!-- CSS Files -->
<div class="row justify-content-center mt-4 ms-2">
	<div class="col-md-10 col-ms-8">
	
	   <div class="d-flex justify-content-center animate__animated animate__fadeInDown">
		<img id="picture_modal" src="<?php echo $d_member['picture'];?>" alt="Member Avatar" class="rounded-circle z-depth-2" width="130px" height="130px" />
	   </div>
	   <div class="d-flex justify-content-center mb-0">
	       <h5 class="mt-2 text-center"><?php echo $d_member['name'];?></h5>
	   </div>
	   <div class="row justify-content-center mb-0">
			<div class="col-8 col-sm-12">
				<div class="form-group row">
					<label for="inputPassword" class="col-sm-4 col-form-label">Password</label>
					<div class="col-sm-5">
					  <input class="form-control" type="text" onfocus="focused(this)" 
							   onfocusout="defocused(this)" value="<?= $d_member['username'];?>" name="p_name" />
					</div>
				</div>
			    <div class="row mt-4">	
					<div class="input-group input-group-dynamic">
						<div class="col-sm-4">
							<label class="form-label">Name</label>
						</div>
						<div class="col-sm-7">
							<input class="form-control" type="text" value="<?= $d_member['username'];?>" name="p_name" placeholder="Name" />
					    </div>
					</div>
					<div class="input-group input-group-dynamic">
						<div class="col-sm-4">
							<label class="form-label">Name</label>
						</div>
						<div class="col-sm-7">
							<input class="form-control" type="text" value="<?= $d_member['username'];?>" name="p_name" />
					    </div>
					</div>
					<div class="input-group input-group-dynamic">
						<div class="col-sm-4">
							<label class="form-label">Name</label>
						</div>
						<div class="col-sm-7">
							<input class="form-control" type="text" value="<?= $d_member['username'];?>" name="p_name" />
					    </div>
					</div>
				</div>
			</div>		   
	   </div>
	</div>
	
</div>
<?php
 } 
 }
 ?>
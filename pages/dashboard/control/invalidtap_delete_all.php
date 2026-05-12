<?php
    require_once "../../../include/db_config.php";
	// Prepare a delete statement
	$sql = "TRUNCATE TABLE data_invalid";
	
	if($stmt = mysqli_prepare($link, $sql)){
		// Attempt to execute the prepared statement
		if(mysqli_stmt_execute($stmt)){
			// Records deleted successfully. Redirect to landing page
			//echo '<script language="javascript" type="text/javascript"> alert("Seluruh Data Kartu Invalid berhasil dihapus"); window.location.replace("../invalid"); </script>';
			header('location:../invalid');
		} else{
			echo "Oops! Something went wrong. Please try again later.";
		}
	}

	// Close statement
	mysqli_stmt_close($stmt);

	// Close connection
	mysqli_close($link);
?>
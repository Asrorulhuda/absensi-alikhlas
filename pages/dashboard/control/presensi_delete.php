<?php
	
	 require_once "../../../include/db_config.php";

// Process delete operation after confirmation
if(isset($_GET["id"]) && !empty($_GET["id"])){
	$nama =$_GET["nama"];
	$tgl=$_GET["tgl"];

    // Prepare a delete statement
    $sql = "DELETE FROM data_absen WHERE id = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);

        // Set parameters
        $param_id = trim($_GET["id"]);

        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            // Records deleted successfully. Redirect to landing page
			echo '<script language="javascript" type="text/javascript"> 
								window.location.replace("../presensi_harian?stat_modal=del_ok&nama='.$nama.'&tgl='.$tgl.'");
					  </script>';
        } else{
            echo '<script language="javascript" type="text/javascript"> 
								alert("Gagal Hapus data. Ulangi proses!");
								window.location.replace("../presensi_harian");
					  </script>';
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);

    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter
    if(empty(trim($_GET["id"]))){
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
<?php 
	if (session_status() == PHP_SESSION_NONE) {
		session_start();
	}

	$user_id = "";
	$user_name = "";
	$user_email = "";
	$user_username = "";
	$user_password = "";
	$user_pic = "";

	if (isset($_SESSION['id'])) {
		$ses_id = $_SESSION['id'];
		
		$sql = "SELECT * FROM users WHERE id = ?";
        if ($stmt = mysqli_prepare($GLOBALS["___mysqli_ston"], $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $ses_id);
            if (mysqli_stmt_execute($stmt)) {
                $result = mysqli_stmt_get_result($stmt);
                if ($user_data = mysqli_fetch_array($result)) {
                    $user_id = $user_data["id"];
                    $user_name = $user_data["name"];
                    $user_email = $user_data["email"];
                    $user_username = $user_data["username"];
                    $user_password = $user_data["password"];
                    $user_pic = $user_data["picture"];
                }
            }
            mysqli_stmt_close($stmt);
        }
	}
	
	
	$sql = "SELECT * FROM system_config WHERE id = 1";
    if ($stmt_sys = mysqli_prepare($GLOBALS["___mysqli_ston"], $sql)) {
        if (mysqli_stmt_execute($stmt_sys)) {
            $result_sys = mysqli_stmt_get_result($stmt_sys);
            if ($row = mysqli_fetch_array($result_sys)) {
                $nama_perusahaan = $row["company"];
                $title_bar = $row["title_bar"];
                $icon_bar = $row["icon_bar"];
                $icon_dashboard = $row["icon_dashboard"];
                $company = $row["company"];
                $footer = $row["footer"];
            }
        }
        mysqli_stmt_close($stmt_sys);
    }
?>
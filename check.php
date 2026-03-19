<?php include('includes/mysqli_connect.php'); ?>
<?php include('includes/functions.php'); ?>
<?php 
    if(isset($_GET['email']) && filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
        $e = $_GET['email'];

        $stmt = mysqli_prepare($dbc, "SELECT user_id FROM users WHERE email = ?");
                mysqli_stmt_bind_param($stmt,"s",$e);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);

        if(mysqli_stmt_num_rows($stmt) == 1){
            echo "NO"; // email đã tồn tại
        } else {
            echo "YES"; 
        }   

    // Đóng statement
    mysqli_stmt_close($stmt);
        }
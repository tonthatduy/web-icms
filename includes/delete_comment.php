<?php include('includes/mysqli_connect.php'); ?>
<?php include('includes/functions.php'); ?>
<?php 
    if(isset($_POST['cmt_id']) && filter_var($_POST['cmt_id'],FILTER_VALIDATE_INT)) {
        $cid = $_POST['cmt_id'];
        $stmt = mysqli_prepare($dbc, "DELETE FROM comments WHERE comment_id = ? LIMIT 1");
                mysqli_stmt_bind_param($stmt,"i",$cid);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
    }
?>
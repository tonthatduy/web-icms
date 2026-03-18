 <?php $title = 'Đăng Xuất'; include('includes/header.php') ?>
<?php include('includes/mysqli_connect.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('includes/sidebar-a.php'); ?>
<div id="content">
    <?php 
        if(!isset($_SESSION['first_name'])) {
            redirect_to();
        } else {
            // Neu co thong tin nguoi dung, va da dang nhap, se logout nguoi dung
            $_SESSION = []; // xoa het array cura Session
            session_destroy(); // Destroy session da tao
            setcookie(session_name(),'',time()-36000); // Xoa cookie cua trinh duyet
        }
        echo "<h2>You are now logged out.</h2>";
    ?>
</div>
<?php include('includes/sidebar-b.php'); ?>
<?php include('includes/footer.php'); ?>
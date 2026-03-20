Trải nghiệm AI ngay trong các ứng dụng bạn yêu thích … Dùng Gemini để tạo bản nháp và tinh chỉnh nội dung, đồng thời sử dụng Gemini Pro để khai thác AI thế hệ mới của Google với giá 489.000 ₫ 0 ₫ cho 1 tháng
<?php
    $title = "Delete Users";
    include('../includes/header.php');
    require_once('../includes/mysqli_connect.php');
    require_once('../includes/functions.php');
    include('../includes/sidebar-admin.php');
    // Check to see if has admin access
    admin_access();  
?>
    <div id="content">
    	<?php
    		if(isset($_GET['uid']) && filter_var($_GET['uid'],FILTER_VALIDATE_INT, array('min_range' => 1))) {
        		$uid = $_GET['uid'];

        		if($_SERVER['REQUEST_METHOD'] == 'POST') {
        			if(isset($_POST['delete']) && $_POST['delete'] == 'yes') {
        				// Neu muon xoa ....
        				// Yeu cau phai co ket noi csdl
        				$mysqli = new mysqli('localhost', 'root', '', 'izcms');

        				// Kiem tra xem ket noi co ton tai hay ko
        				check_db_conn();

        				$q = "DELETE FROM users WHERE user_id = ?";

        				if($stmt = $mysqli->prepare($q)) {

        					// Gan tham so cho prepare
        					$stmt->bind_param('i', $uid);

        					// Chay query
        					$stmt->execute() or die("MySQL Error: $q" . $stmt->error);

        					if($stmt->affected_rows == 1) {
        						$message = "<p class='success'>User was deleted successfully.</p>";
        					} else {
        						$message = "<p class='error'>User was NOT deleted due to a system error.</p>";
        					}
        					$stmt->close();
        				} // End if prepare
        			} else {
        				$message = "<p class='warning'>I thought so too, shouldn't be deleted.</p>";
        			}
        		}// END if($_SERVER)

        	} else {
        		// Neu User ID khong ton tai tai dinh huong nguoi dung ve trang manage user
        		redirect_to('admin/manage_users.php');
        	}
    	?>

	<h2> Delete user</h2>
	<?php if(!empty($message)) echo $message; ?>
	   <form action="" method="post">
	   <fieldset>
			<legend>Delete user</legend>
				<label for="delete">Are you sure?</label>
				<div>
					<input type="radio" name="delete" value="no" checked="checked" /> No
					<input type="radio" name="delete" value="yes" /> Yes
				</div>
				<div><input type="submit" name="submit" value="Delete" /></div>
		</fieldset>
	   </form>
    </div><!--end content-->

<?php include('../includes/footer.php'); ?>
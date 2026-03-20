<?php
    $title = "Manage Users";
    include('../includes/header.php');
    require_once('../includes/mysqli_connect.php');
    require_once('../includes/functions.php');
    include('../includes/sidebar-admin.php');
    // Check to see if has admin access
    admin_access();  
?>
<?php
    if(isset($_GET['uid']) && filter_var($_GET['uid'],FILTER_VALIDATE_INT, array('min_range' => 1))) {
        $uid = $_GET['uid'];
    
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $errors = array();
            // Trim all incoming data
            $trimmed = array_map('trim', $_POST);
             
            if(preg_match('/^[\w]{2,10}$/i', $trimmed['first_name'])) {
                $fn = $trimmed['first_name'];
                } else {
                $errors[] = "first_name";
            }

            if(preg_match('/^[\w ]{2,10}$/i', $trimmed['last_name'])) {
                $ln = $trimmed['last_name'];
                } else {
                $errors[] = "last name";
            }

            if(filter_var($trimmed['email'],FILTER_VALIDATE_EMAIL)) {
                $e = $trimmed['email'];
                } else {
                $errors[] = "email";
            }
                   
            if(filter_var($trimmed['user_level'], FILTER_VALIDATE_INT, array('min_range'=>1))) {
                $ul = $trimmed['user_level'];
                } else {
                $errors[] = "user level";
            }
            
            
            if(empty($errors)) {
                // Kiem tra xem email da co trong he thong hay chua
                $q = "SELECT user_id FROM users WHERE email = ? AND user_id != ?";
                if($stmt = mysqli_prepare($dbc, $q)) {
                    
                    // Gan tham so cho cau lenh prepare
                    mysqli_stmt_bind_param($stmt, 'si', $e, $uid);

                    // Cho chay cau lenh prepare
                    mysqli_stmt_execute($stmt);

                    // Luu lai ket qua cua cau lenh prepare
                    mysqli_stmt_store_result($stmt);

                    if(mysqli_stmt_num_rows($stmt) == 0) {
                        // Email available, run query de update csdl
                        $query = "UPDATE users SET 
                                    first_name = ?, 
                                    last_name = ?, email = ?, 
                                    user_level =? 
                                    WHERE user_id = ? LIMIT 1";
                        if($upd_stmt = mysqli_prepare($dbc, $query)) {

                            // Gan tham so
                            mysqli_stmt_bind_param($upd_stmt, 'sssii', $fn, $ln, $e, $ul, $uid);

                            // Cho chay cau lenh
                            mysqli_stmt_execute($upd_stmt) or die("Mysqli Error: $query ". mysqli_stmt_error($upd_stmt));

                            if(mysqli_stmt_affected_rows($upd_stmt) == 1) {
                                $message = "<p class='success'>User info updated successfully.</p>";
                            } else {
                                $message = "<p class='error'>User info was NOT updated.</p>";
                            }
                        }
                    } else {
                        $message = "<p class='error'>Please use another email address. This email is already in the system.</p>";
                    }

                }// END if($STMT)

                }// end empty($errors) 
            
            } // END main IF POST
        
        } else {
            // Invialid UID, redirect to index page.
        redirect_to('admin/manage_users.php');
    }// End main IF
?>
    <?php 
        // Truy xuat csdl de hien thi thong tin nguoi dung
        if($user = fetch_user($uid)) { // Neu user ton tai, thi hien thi noi dung cua user
    ?>

<div id="content">
    <h2>Edit user: <?php echo $user['first_name'] ." ". $user['last_name'];?> </h2>
    <?php if(isset($message)) {echo $message;}?>

<form action="" method="post">        
<fieldset>
    <legend>User Info</legend>
    <div>
        <label for="first-name">First Name
            <?php if(isset($errors) && in_array('first_name',$errors)) echo "<p class='warning'>Please enter your first name.</p>";?>
        </label> 
        <input type="text" name="first_name" value="<?php if(isset($user['first_name'])) echo strip_tags($user['first_name']); ?>" size="20" maxlength="40" tabindex='1' />
    </div>
    
    <div>
        <label for="last-name">Last Name
            <?php if(isset($errors) && in_array('last name',$errors)) echo "<p class='warning'>Please enter your last name.</p>";?>
        </label> 
        <input type="text" name="last_name" value="<?php if(isset($user['last_name'])) echo strip_tags($user['last_name']); ?>" size="20" maxlength="40" tabindex='1' />
    </div>

    <div>
        <label for="email">Email
        <?php if(isset($errors) && in_array('email',$errors)) echo "<p class='warning'>Please enter a valid email.</p>";?>
        </label> 
        <input type="text" name="email" value="<?php if(isset($user['email'])) echo $user['email']; ?>" size="20" maxlength="40" tabindex='3' />
    </div>

    <div>
        <label for="User Level">User Level:
            <?php if(isset($errors) && in_array('user level',$errors)) echo "<p class='warning'>Please pick a user level.</p>";?>
        </label>
        <select name="user_level">
        <?php
            // Set up array for roles
            $roles = array(1 => 'Registered Member', 2 => 'Moderator', 3 => 'Super Mod', 4 => 'Admin');
            foreach ($roles as $key => $role) {
                echo "<option value='{$key}'";
                    if($key == $user['user_level']) {echo "selected='selected'";}
                echo ">".$role."</option>";
            }
        ?>
        </select>
    </div>
</fieldset>

<div><input type="submit" name="submit" value="Save Changes" /></div>
<?php } else {
    echo "<p class='error'>No user found.</p>";
} ?>
</div><!--end content-->

    
<?php 
    include('../includes/footer.php'); 
?>
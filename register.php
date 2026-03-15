<?php $title = 'Register'; include('includes/header.php') ?>
<?php include('includes/mysqli_connect.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('includes/sidebar-a.php'); ?>
<div id="content">
    <?php 
    if($_SERVER['REQUEST_METHoD'] == 'POST') {
        // Bắt đầu xử lý form
        $errors = [];
        // Mặc định cho các trường nhập liệu là FALSE
         $fn = $ln = $e = $p = FALSE;

        if(preg_match('/^[\w\'.-]{2.20}$/i', trim($_POST['first_name']))) {
            $fn = mysqli_real_escape_string($dbc,trim($_POST['first_name']));
        } else {
             $errors[] = 'first _name';
        }

          if(preg_match('/^[\w\'.-]{2.20}$/i', trim($_POST['last_name']))) {
            $fn = mysqli_real_escape_string($dbc,trim($_POST['last_name']));
        } else {
            $errors[]  = 'last_name';
        }

        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $e = mysqli_real_escape_string($dbc,$_POST['email']);
        } else {
            $errors[] = 'email';
        }

        if(preg_match('/^[\w\'.-]{4.20}$/', trim($_POST['password1']))) {
            if($_POST['password1'] == $_POST['password2']) {
                // Nếu mật khẩu 1 phù hợp với mật khẩu hai thì lưu vào csdl
                $p = mysqli_real_escape_string($dbc, trim($_POST['password1']));
            } else {

                $errors[] = "password not match";
            }
        } else {
            $errors[] = 'pasword';
        }

        if( $fn && $ln && $e && $p ) {
           $stmt =mysqli_prepare($dbc,"SELECT u.user_id FROM users as u WHERE email = ?");
                mysqli_stmt_bind_param($stmt,'s',$e);
        }
    } // END MAIN IF
    
    ?>

    <h2>Register</h2>
    <form action="register.php">
        <fieldset>
            <legend>Register</legend>
            <div>
                <label for="First Name">First Name <span class="required">*</span></label>
                <input type="text" name="first_name" size="20" maxlength="20" value="" tabindex="1">
            </div>

            <div>
                <label for="Last Name">Last Name <span class="required">*</span></label>
                <input type="text" name="last_name" size="20" maxlength="40" value="" tabindex="2">
            </div>

            <div>
                <label for="Email">Email<span class="required">*</span></label>
                <input type="text" name="last_name" size="20" maxlength="80" value="" tabindex="3">
            </div>

            <div>
                <label for="password">Password<span class="required">*</span></label>
                <input type="password" name="password1" size="20" maxlength="20" value="" tabindex="4">
            </div>

             <div>
                <label for="email">Confirm Password<span class="required">*</span></label>
                <input type="password" name="password2" size="20" maxlength="20" value="" tabindex="5">
            </div>
        </fieldset>
        <p><input type="submit" name="submit" value="Register"></p>
    </form>
</div>

<?php include('includes/sidebar-b.php'); ?>
<?php include('includes/footer.php'); ?>
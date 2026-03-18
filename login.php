<?php $title = 'Đăng Nhập'; include('includes/header.php') ?>
<?php include('includes/mysqli_connect.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('includes/sidebar-a.php'); ?>
<div id="content">
    <?php
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Bắt đầu xử lý form. tạo biến $errors
            $errors = [];

            //Validate Email
            if(isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $e = mysqli_real_escape_string($dbc, $_POST['email']);
            } else {
                $errors[] = 'email';
            }

            //Validate Password
            // if(isset($_POST['password']) && preg_match('/^[\w\'.-]{4,20}$/', $_POST['password'])) {
            //     $p = mysqli_real_escape_string($dbc, $_POST['password']);
            // } else {
            //     $errors[]='password';
            // }

            if(!empty($_POST['password'])) {
                $p = $_POST['password'];
            } else {
                $errors[] = 'password';
            }

            if(empty($errors)) {
                // bat dau truy van CSDL de lay thong tin nguoi dung
                    // Dùng prepared statement
                $stmt = mysqli_prepare($dbc, "SELECT user_id, first_name, pass, user_level FROM users WHERE email = ? AND active IS NULL LIMIT 1");
                mysqli_stmt_bind_param($stmt, "s", $e);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);

                if(mysqli_num_rows($result) == 1 ) {
                    // Neu tim that thong tin nguoi dung CSDL, se chuyen huong nguoi dung ve trang thich hop
                    $user = mysqli_fetch_assoc($result);
                      // So sánh password
                    if(password_verify($p, $user['pass'])) {
                    // Bắt đầu session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['user_level'] = $user['user_level'];

                    redirect_to();
                    } else {
                         $message = "<p class='error'>Sai mật khẩu.</p>";
                    }
                } else {
                    $message = "<p class='error'>The email or password do not match those on file. Or you have not activated your account.</p>";
                }
            } else {
                $message = "<p class='error'>Please fill in all the required fields.</p>";
            }
        }

    //    var_dump(password_verify('123456', '$2y$10$hOdIsp7DauTi.WQlz5zkLe0hi11m7IMJBIVDYVeKIHf5D3Y1FQ31e'));
    ?>

    <h2>Đăng Nhập</h2>
    <?php if(!empty($message)) echo $message; ?>
    <form action="" id="login" method="post">
        <fieldset>
            <legend>Login</legend>
            <div>
                <label for="email">Email: 
                    <?php if(isset($errors) && in_array('email', $errors)) echo "<span class ='warning'>Please enter your email.</span>"; ?>
                </label>
                <input type="text" name="email" id="email" value="<?php if(isset($_POST['email'])) {echo htmlentities($_POST['email']);} ?>" size="20" maxlength="80" tabindex="1">
            </div>

             <div>
                <label for="pass">Password: 
                    <?php if(isset($errors) && in_array('password', $errors)) echo "<span class ='warning'>Please enter your Password.</span>"; ?>
                </label>
                <input type="password" name="password" id="password" value="" size="20" maxlength="20" tabindex="2">
            </div>
        </fieldset>
        <div><input type="submit" name="submit" value="Login"></div>            
    </form>
    <p><a href="retrieve_password.php">Forgot password?</a></p>                
</div>
<?php include('includes/sidebar-b.php'); ?>
<?php include('includes/footer.php'); ?>
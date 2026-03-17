<?php $title = 'Register'; include('includes/header.php') ?>
<?php include('includes/mysqli_connect.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('includes/sidebar-a.php'); ?>
<div id="content">
    <?php 
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Bắt đầu xử lý form
        $errors = [];
        // Mặc định cho các trường nhập liệu là FALSE
         $fn = $ln = $e = $p = FALSE;

        if(preg_match('/^[\w\'.-]{2,20}$/i', trim($_POST['first_name']))) {
            $fn = mysqli_real_escape_string($dbc,trim($_POST['first_name']));
        } else {
             $errors[] = 'first_name';
        }

        if(preg_match('/^[\w\'.-]{2,20}$/i', trim($_POST['last_name']))) {
            $ln = mysqli_real_escape_string($dbc,trim($_POST['last_name']));
        } else {
            $errors[]  = 'last_name';
        }

        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $e = mysqli_real_escape_string($dbc,$_POST['email']);
        } else {
            $errors[] = 'email';
        }

        if(preg_match('/^[\w\'.-]{4,20}$/', trim($_POST['password1']))) {
            if($_POST['password1'] == $_POST['password2']) {
                // Nếu mật khẩu 1 phù hợp với mật khẩu hai thì lưu vào csdl
                $p = mysqli_real_escape_string($dbc, trim($_POST['password1']));
            } else {
                
            $errors[] = "password not match";
            }
        } else {
            $errors[] = 'password';
        }

        if( $fn && $ln && $e && $p ) {
           $stmt =mysqli_prepare($dbc,"SELECT u.user_id FROM users as u WHERE email = ?");
                mysqli_stmt_bind_param($stmt,'s',$e);
                mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 0) {
                // Lúc này email vẫn còn trống, chi phép người dùng đăng ký
                // Tạo ra một chuỗi Activation Key
                $a = bin2hex(random_bytes(32));

                //Chèn giá trị vào CSDL
                $stmt = mysqli_prepare($dbc,"INSERT INTO users (first_name, last_name, email, pass, active,user_level, registration_date) 
                                            VALUES (?,?,?,?,?,0,NOW())");
                $hashed_password =  password_hash($p, PASSWORD_BCRYPT);                           

                        mysqli_stmt_bind_param($stmt,"sssss", $fn,$ln,$e,$hashed_password,$a);
                        mysqli_stmt_execute($stmt);

                if(mysqli_affected_rows($dbc) == 1) {
                    // Nếu điền thông tin thành công, thì gửi email kích hoạt cho người dùng
                    $body= "Cảm ơn bạn đã đăng ký ở trang izCMS, Một Email kích hoạt đã được gửi tới địa chỉ email mà bạn cung cấp. PHiền bạn click vào đường link để kích hoạt tài khoản \n\n";
                    $body .= BASE_URL . "admin/activate.php?x=". urlencode($e) ."&y={$a}";
                    if(mail($_POST['email'],'Kích hoạt tài khoản tại icCMS', $body, 'FROM: localhost')) {
                        $message = "<p class='success'> Tài khoản của bạn đã được đăng ký thành công. Email đã được gửi tới địa chỉ của bạn. Bạn phải nhấn vào link để kích hoạt tài khoản trước khi sư dụng nó </p>";
                    } else {
                        $message = "<p class='warning'>Không thể gửi được email cho bạn. Rất xin lỗi về sự bất tiện này</p>"; 
                    }
                } else {
                    $message ="<p class='warning'>Sorry, your order could not be processed due to a system error.</p>";
                }
            } else {
                // Email da ton tai phai dang ky bang email khac
                $message = "<p class='warning'>The email was already used previously. Please use another email address.</p>";
            }
        } else {
            // Neu một trong các trường bị thiếu giá trị
            $message = "<p class='warning'>Please fill in all the required fields. </p>";
        }
    } // END MAIN IF

    ?>

    <h2>Register</h2>
    <?php 
    if(!empty($message)) echo $message; ?>
    <form action="register.php" method="POST">
        <fieldset>
            <legend>Register</legend>
            <div>
                <label for="First Name">First Name <span class="required">*</span>
                    <?php if(isset($errors) && in_array('first_name',$errors)) echo "<span class='warning'>Please enter your first name</span>"; ?>
                </label>
                <input type="text" name="first_name" size="20" maxlength="20" value="<?php if(isset($_POST['first_name'])) echo $_POST['first_name']  ?>" tabindex="1">
            </div>

            <div>
                <label for="Last Name">Last Name <span class="required">*</span>
                    <?php if(isset($errors) && in_array('last_name',$errors)) echo "<span class='warning'>Please enter your last name</span>" ?>
                </label>
                <input type="text" name="last_name" size="20" maxlength="40" value="<?php if(isset($_POST['last_name'])) echo $_POST['last_name']  ?>" tabindex="2">
            </div>

            <div>
                <label for="Email">Email<span class="required">*</span>
                    <?php if(isset($errors) && in_array('email',$errors)) echo "<span class='warning'>Please enter your email</span>" ?>
                </label>
                <input type="text" name="email" size="20" maxlength="80" value="<?php if(isset($_POST['email'])) echo htmlentities($_POST['email'], ENT_COMPAT, 'UTF-8'); ?>" tabindex="3">
            </div>

            <div>
                <label for="password">Password<span class="required">*</span>
                <?php if(isset($errors) && in_array('password',$errors)) echo "<span class='warning'>Please enter your password</span>" ?>
                </label>
                <input type="password" name="password1" size="20" maxlength="20" value="<?php if(isset($_POST['password1'])) echo $_POST['password1']  ?>" tabindex="4">
            </div>

             <div>
                <label for="email">Confirm Password<span class="required">*</span>
                <?php if(isset($errors) && in_array('password not match',$errors)) echo "<span class='warning'>Your confirmed password does </span>" ?>
                </label>
                <input type="password" name="password2" size="20" maxlength="20" value="<?php if(isset($_POST['password2'])) echo $_POST['password2']  ?>" tabindex="5">
            </div>
        </fieldset>
        <p><input type="submit" name="submit" value="Register"></p>
    </form>
</div>

<?php include('includes/sidebar-b.php'); ?>
<?php include('includes/footer.php'); ?>
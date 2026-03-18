 <?php $title = 'Đổi Mật Khẩu'; include('includes/header.php') ?>
<?php include('includes/mysqli_connect.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('includes/sidebar-a.php'); ?>
<div id="content">
    <?php
    
    $title = "Quên Mật Khẩu";
    $errors = [];
    $success = false;
    
      if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $email = trim($_POST['email'] ?? '');
        //Bat dau xu ly form
        if(empty($email) || !filter_var($email,FILTER_VALIDATE_EMAIL)) {
             $errors[] = 'Vui lòng nhập địa chỉ email hợp lệ.';

        } else {
            //Kiem tra cong CSDL de xem email co ton tai hay khong
            $stmt = mysqli_prepare($dbc, "SELECT user_id FROM users WHERE email = ? LIMIT 1");
                    mysqli_stmt_bind_param($stmt, "s", $email);
                    mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);

            if(!$user) {
                // Không lộ thông tin: luôn thông báo chung chung để tránh email enumerati
                $errors[] = 'Nếu email này tồn tại trong hệ thống, bạn sẽ nhận được hướng đẫn đặt lại mật khẩu.';
                // Ghi chú: Đặt $success = true ở đây nếu muốn ẩn hoàn toàn thông tin
                // $success = true;
            } else {
                $uid = (int) $user['user_id'];
                
                $temp_pass = bin2hex(random_bytes(8)); // 16 ký tự
                $hashed_pass = password_hash($temp_pass,PASSWORD_BCRYPT);

                $stmt = mysqli_prepare($dbc, "UPDATE users SET pass = ? WHERE user_id = ? LIMIT 1");
                mysqli_stmt_bind_param($stmt,'si', $hashed_pass,$uid);
                mysqli_stmt_execute($stmt);
                $affected = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);

                if ($affected === 1) {
                // --- 2e. Gửi email ---
                $subject = 'Mật khẩu tạm thời của bạn';
                $body    = "Mật khẩu của bạn đã được đặt lại tạm thời thành: {$temp_pass}\n\n"
                         . "Vui lòng đăng nhập và đổi mật khẩu ngay sau đó.\n\n"
                         . "Nếu bạn không yêu cầu điều này, hãy liên hệ với chúng tôi ngay.";
                $headers = "From: no-reply@yourdomain.com\r\n"
                         . "Reply-To: no-reply@yourdomain.com\r\n"
                         . "X-Mailer: PHP/" . phpversion();
 
                if (mail($email, $subject, $body, $headers)) {
                    $success = true;
                } else {
                    $errors[] = 'Không thể gửi email. Vui lòng thử lại sau.';
                    // Rollback: có thể xóa temp pass hoặc log lỗi tại đây
                }
            } else {
                $errors[] = 'Đã xảy ra lỗi hệ thống. Vui lòng thử lại.';
            }
            }
        
        }
      }
    ?>
    <h2>Lấy lại mật khẩu</h2>
 
    <?php if (!empty($errors)): ?>
        <div class="messages messages--error" role="alert">
            <?php foreach ($errors as $error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
 
    <?php if ($success): ?>
        <div class="messages messages--success" role="status">
            <p class="success">
                Yêu cầu đã được xử lý. Nếu email tồn tại, bạn sẽ nhận được hướng dẫn trong vài phút.
            </p>
        </div>
    <?php else: ?>
        <form id="forgot-password" action="" method="post" novalidate>
            <fieldset>
                <legend>Nhập email để lấy lại mật khẩu</legend>
                <div>
                    <label for="email">Email:</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
                        size="40"
                        maxlength="80"
                        tabindex="1"
                        required
                        autocomplete="email"
                    />
                </div>
            </fieldset>
            <div>
                <input type="submit" name="submit" value="Lấy lại mật khẩu" />
            </div>
        </form>
    <?php endif; ?>
</div>
<?php include('includes/sidebar-b.php'); ?>
<?php include('includes/footer.php'); ?>
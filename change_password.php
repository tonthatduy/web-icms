<?php
// -------------------------------------------------------------------------
// 1. INCLUDES & KHỞI TẠO
// -------------------------------------------------------------------------
include('includes/mysqli_connect.php');
include('includes/functions.php');
include('includes/header.php');

$title   = 'Đổi mật khẩu';
$errors  = [];
$success = false;

// Kiểm tra đăng nhập 
is_logged_in();

$uid = (int) $_SESSION['user_id']; // Ép kiểu int để đảm bảo an toàn

// -------------------------------------------------------------------------
// 2. XỬ LÝ FORM (chỉ chạy khi POST)
// -------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cur_password = trim($_POST['cur_password'] ?? '');
    $password1    = trim($_POST['password1']    ?? '');
    $password2    = trim($_POST['password2']    ?? '');

    // --- 2a. Validate định dạng mật khẩu hiện tại ---
    if (empty($cur_password)) {
        $errors[] = 'Vui lòng nhập mật khẩu hiện tại.';
    } else if (!preg_match('/^\w{4,20}$/', $cur_password)) {
        $errors[] = 'Mật khẩu hiện tại không đúng định dạng (4–20 ký tự).';
    } else {
        // --- 2b. Xác minh mật khẩu hiện tại với DB (Prepared Statement) ---
        $stmt = mysqli_prepare($dbc, "SELECT pass FROM users WHERE user_id = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 'i', $uid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        // Dùng password_verify() để so sánh — tương thích với bcrypt từ forgot_password.php
        if (!$user || !password_verify($cur_password, $user['pass'])) {
            $errors[] = 'Mật khẩu hiện tại không chính xác.';
        } else {
            // --- 2c. Validate mật khẩu mới ---
            if (empty($password1) || !preg_match('/^\w{4,20}$/', $password1)) {
                $errors[] = 'Mật khẩu mới không hợp lệ (4–20 ký tự, chỉ gồm chữ, số, dấu gạch dưới).';
            } else if ($password1 !== $password2) {
                $errors[] = 'Mật khẩu mới và xác nhận mật khẩu không khớp.';
            } else if ($password1 === $cur_password) {
                $errors[] = 'Mật khẩu mới không được trùng với mật khẩu hiện tại.';
            } else {
                // --- 2d. Cập nhật DB với mật khẩu mới (Prepared Statement + bcrypt) ---
                $new_hash = password_hash($password1, PASSWORD_BCRYPT);

                $stmt = mysqli_prepare($dbc, "UPDATE users SET pass = ? WHERE user_id = ? LIMIT 1");
                mysqli_stmt_bind_param($stmt, 'si', $new_hash, $uid);
                mysqli_stmt_execute($stmt);
                $affected = mysqli_stmt_affected_rows($stmt);
                mysqli_stmt_close($stmt);

                if ($affected === 1) {
                    $success = true;
                } else {
                    $errors[] = 'Không thể cập nhật mật khẩu. Vui lòng thử lại.';
                }
            }
        }
    }
} // END xử lý POST

// -------------------------------------------------------------------------
// 3. RENDER HTML
// -------------------------------------------------------------------------
include('includes/sidebar-a.php');
?>

<div id="content">
    <h2>Đổi mật khẩu</h2>

    <?php if ($success): ?>
        <div class="success" role="status">
            <p class="success">Mật khẩu của bạn đã được cập nhật thành công.</p>
            <p><a href="index.php">Quay về trang chủ</a></p>
        </div>
    <?php else: ?>

        <?php if (!empty($errors)): ?>
            <div class="error" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p class="error"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" novalidate>
            <fieldset>
                <legend>Đổi mật khẩu</legend>

                <div>
                    <label for="cur_password">Mật khẩu hiện tại</label>
                    <input
                        type="password"
                        name="cur_password"
                        id="cur_password"
                        size="20"
                        maxlength="40"
                        tabindex="1"
                        autocomplete="current-password"
                        required
                    />
                </div>

                <div>
                    <label for="password1">Mật khẩu mới</label>
                    <input
                        type="password"
                        name="password1"
                        id="password1"
                        size="20"
                        maxlength="40"
                        tabindex="2"
                        autocomplete="new-password"
                        required
                    />
                </div>

                <div>
                    <label for="password2">Xác nhận mật khẩu mới</label>
                    <input
                        type="password"
                        name="password2"
                        id="password2"
                        size="20"
                        maxlength="40"
                        tabindex="3"
                        autocomplete="new-password"
                        required
                    />
                </div>
            </fieldset>

            <div>
                <input type="submit" name="submit" value="Cập nhật mật khẩu" tabindex="4" />
            </div>
        </form>

    <?php endif; ?>
</div><!-- #content -->

<?php
include('includes/sidebar-b.php');
include('includes/footer.php');
?>
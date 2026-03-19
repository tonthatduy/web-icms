<?php
// Include theo thứ tự đúng: functions và db trước
include('../includes/functions.php');
include('../includes/mysqli_connect.php');
include('../includes/header.php');
include('../includes/sidebar-admin.php');

admin_access();

$messages = '';
$errors = [];
$page = [];

// Kiểm tra pid hợp lệ
if (!isset($_GET['pid']) || !filter_var($_GET['pid'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
    redirect_to('admin/view_pages.php');
}

$pid = (int) $_GET['pid'];

// Xử lý form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate page_name
    if (empty($_POST['page_name'])) {
        $errors[] = 'page_name';
    } else {
        $page_name = strip_tags($_POST['page_name']);
    }

    // Validate category
    if (isset($_POST['category']) && filter_var($_POST['category'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
        $cat_id = (int) $_POST['category'];
    } else {
        $errors[] = 'category';
    }

    // Validate position
    if (isset($_POST['position']) && filter_var($_POST['position'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
        $position = (int) $_POST['position'];
    } else {
        $errors[] = 'position';
    }

    // Validate content
    if (empty($_POST['content'])) {
        $errors[] = 'content';
    } else {
        $content = $_POST['content'];
    }

    if (empty($errors)) {
        // Dùng Prepared Statement - an toàn, không cần escape thủ công
        $stmt = mysqli_prepare($dbc, "UPDATE pages SET page_name=?, cat_id=?, position=?, content=?, user_id=1, post_on=NOW() WHERE page_id=? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "siisi", $page_name, $cat_id, $position, $content, $pid);
        // ↑ s=page_name, i=cat_id, i=position, i=content... 
        // Ở đây content là string nên: "siisi" không phải "siiii"
        
        if (mysqli_stmt_execute($stmt)) {
            if (mysqli_stmt_affected_rows($stmt) == 1) {
                $messages = "<p class='success'>The page was edited successfully.</p>";
            } else {
                $messages = "<p class='warning'>No changes were made.</p>";
            }
        } else {
            $messages = "<p class='warning'>The page could not be edited due to a system error.</p>";
        }
        mysqli_stmt_close($stmt);

    } else {
        $messages = "<p class='warning'>Please fill in all the required fields.</p>";
    }
}

// Lấy thông tin page từ DB để hiển thị form
$stmt = mysqli_prepare($dbc, "SELECT * FROM pages WHERE page_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "i", $pid);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 1) {
    $page = mysqli_fetch_assoc($result);
} else {
    $messages = "<p class='warning'>The page does not exist.</p>";
}
mysqli_stmt_close($stmt);

// Lấy danh sách categories
$cats_result = mysqli_query($dbc, "SELECT cat_id, cat_name FROM categories ORDER BY position ASC");

// Lấy số lượng pages để tạo dropdown position
$count_result = mysqli_query($dbc, "SELECT COUNT(page_id) FROM pages");
list($pages_count) = mysqli_fetch_array($count_result, MYSQLI_NUM);
?>

<div id="content">
    <h2>Edit Page: <?php echo isset($page['page_name']) ? htmlspecialchars($page['page_name']) : ''; ?></h2>
    <?php if (!empty($messages)) echo $messages; ?>

    <form id="edit_page" action="" method="post">
        <fieldset>
            <legend>Edit a Page</legend>

            <!-- Page Name -->
            <div>
                <label for="page_name">Page Name: <span class="required">*</span></label>
                <?php if (in_array('page_name', $errors)) echo "<p class='warning'>Please fill in the page name.</p>"; ?>
                <input type="text" name="page_name" id="page_name"
                    value="<?php echo isset($page['page_name']) ? htmlspecialchars($page['page_name']) : ''; ?>"
                    size="20" maxlength="80" tabindex="1" />
            </div>

            <!-- Category -->
            <div>
                <label for="category">Category: <span class="required">*</span></label>
                <?php if (in_array('category', $errors)) echo "<p class='warning'>Please pick a category.</p>"; ?>
                <select name="category" id="category">
                    <option value="">Select Category</option>
                    <?php while ($cat = mysqli_fetch_assoc($cats_result)): ?>
                        <option value="<?php echo $cat['cat_id']; ?>"
                            <?php if (isset($page['cat_id']) && $page['cat_id'] == $cat['cat_id']) echo "selected"; ?>>
                            <?php echo htmlspecialchars($cat['cat_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Position -->
            <div>
                <label for="position">Position: <span class="required">*</span></label>
                <?php if (in_array('position', $errors)) echo "<p class='warning'>Please pick a position.</p>"; ?>
                <select name="position" id="position">
                    <?php for ($i = 1; $i <= $pages_count + 1; $i++): ?>
                        <option value="<?php echo $i; ?>"
                            <?php if (isset($page['position']) && $page['position'] == $i) echo "selected"; ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Content -->
            <div>
                <label for="content">Page Content: <span class="required">*</span></label>
                <?php if (in_array('content', $errors)) echo "<p class='warning'>Please fill in the content.</p>"; ?>
                <textarea name="content" id="content" cols="50" rows="20"><?php
                    echo isset($page['content']) ? htmlspecialchars($page['content']) : '';
                ?></textarea>
            </div>

        </fieldset>
        <p><input type="submit" name="submit" value="Save Changes"></p>
    </form>
</div>

<?php
include('../includes/footer.php');
?>
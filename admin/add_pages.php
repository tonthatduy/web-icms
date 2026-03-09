<?php include('../includes/header.php'); ?>
<?php include('../includes/mysqli_connect.php'); ?>
<?php include('../includes/sidebar-admin.php'); ?>

<?php
        if($_SERVER['REQUEST_METHOD'] == 'POST') { // Gia tri ton tai, xu ly form
           $errors = [];
           if(empty($_POST['page_name'])) {
            $errors[] = 'page_name';
           } else {
                $page_name = mysqli_real_escape_string($dbc, strip_tags($_POST['page_name']));
           }

           if(isset($_POST['category']) && filter_var($_POST['category'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))){
                $cat_id = $_POST['category'];
            } else {
                $errors[] =  "category";
            }

            if(isset($_POST['position']) && filter_var($_POST['position'], FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))){
                $position = $_POST['position'];
            } else {
                $errors[] =  "position";
            }

            if(empty($_POST['content'])) {
                $errors[] = 'content';
            } else {
                $content = mysqli_real_escape_string($dbc, $_POST['content']);
            }

            if(empty($errors)) {
                // Nếu không có lỗi xảy ra, bắt đầu chèn dữ liệu vào cở sở dữ liệu
                $q = "INSERT INTO pages (user_id, cat_id,page_name, content, position, post_on) 
                        VALUES (1, {$cat_id},'$page_name', '$content', $position, NOW())";
                $r = mysqli_query($dbc, $q) or die("Query ($q) \n<br/> MYSQL Error: " . mysqli_error($dbc));
                if(mysqli_affected_rows($dbc) == 1) {
                    $messages = "<p class='success'>The apge was added successfully.</p>";
                } else {
                    $messages ="<p class='warning'>The page could not be added due to a system error</p>";
                }
            } else {
                $messages = "<p class='warning'>Please fill in all the required fields</p>";
            }
        } // END main IF submit condition
?>
<div id="content">
    <h2>Create a pages</h2>
        <?php if(!empty($messages)) {echo $messages;} ?>
            <form id="login" action="" method="post">
                <fieldset>
                    <legend>
                        Add a Page
                    </legend>
                    <div>
                        <label for="page">Page Name: <span class ="required">*</span>
                     <?php 
                    if(isset($errors) && in_array('page_name', $errors)) {
                        echo "<p class='warning'> Please fill in the page name</p>";
                    }
                    ?>
                    </label>
                        <input type="text" name="page_name" id="page_name" value="" size="20" maxlength="80" tabindex="1" />
                    </div>

                    <div>
                        <label for="category">All categories: <span class="required">*</span>
                      <?php 
                    if(isset($errors) && in_array('category', $errors)) {
                        echo "<p class='warning'> Please pick a category</p>";
                    }
                    ?>
                    </label>

                        <select name="category" id="">
                            <option value="">Select Category</option>
                        </select>
                    </div>

                    <div>
                        <label for="position">Position: <span class="required">*</span>
                      <?php 
                    if(isset($errors) && in_array('position', $errors)) {
                        echo "<p class='warning'> Please pick a position</p>";
                    }
                    ?>
                    </label>
                        <select name="position" id="">
                            <option value="">Select position</option>
                        </select>
                    </div>

                    <div>
                        <label for="page-content">Page Content: <span class="required">*</span>
                       <?php 
                    if(isset($errors) && in_array('content', $errors)) {
                        echo "<p class='warning'> Please fill in the content</p>";
                    }
                    ?>
                    </label>
                        <textarea name="content" cols="50" rows="20" ></textarea>
                    </div>
                </fieldset>
                <p><input type="submit" name="submit" value="Add Page"></p>
            </form>
          
</div> <!--end content -->

<?php include('../includes/sidebar-b.php'); ?>
<?php include('../includes/footer.php'); ?>


<?php include('../includes/header.php'); ?>
<?php include('../includes/mysqli_connect.php'); ?>
<?php include('../includes/sidebar-admin.php'); ?>
<?php include('../includes/functions.php'); ?>



<?php
    // Xac nhan bien Get ton tai va thuoc loai du lieu cho phep
    if(isset($_GET['cid']) && filter_var($_GET['cid'], FILTER_VALIDATE_INT,  array('options' => array('min_range' => 1)))) {
        $cid = $_GET['cid'];
    } else {
        redirect_to('admin/admin.php');
    }

        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Gia tri tri ton tai, xu ly form
            $errors = [];
            // Kiem tra ten cua category
            if(empty($_POST['category'])) {
                $errors[] = "category";
            } else {
                $cat_name = mysqli_real_escape_string($dbc, strip_tags($_POST['category']));
            }
            // Check position ton` tai,  so nguyen , >= 1;

            // Kiem tra position cua category
            $position = filter_input(INPUT_POST, 'position', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

            if($position === false) {
                $errors[] =  "position";
            }

            if(empty($errors)) {
                // Neu khong co loi xay ra thi chen du lieu vao
                $q = "UPDATE categories SET cat_name = '$cat_name', position = $position WHERE cat_id = {$cid} LIMIT 1 "; 
            $r = mysqli_query($dbc, $q);
            confirm_query($r,$q);

                if(mysqli_affected_rows($dbc) == 1)  {
                $messages = "<p class='success'> The category was edited successfully.</p>";
                } else {
                $messages = "<p class='warning'>Could not edit the category due to a system error.</p>";
                }
            } else {
                $messages = "<p class='warning'>Please fill all the required fields</p>";
            }
       
        } // END main IF submit condition
?>
<div id="content">
    <?php 
    $q = "SELECT cat_name, position FROM categories WHERE cat_id = {$cid}";
    $r = mysqli_query($dbc, $q);
    confirm_query($r,$q);
    if(mysqli_num_rows($r) == 1) {
        // Neu category ton tai trong database, dua vao CID, xuat du lieu ra ngoai trinh duyet
        list($cat_name, $position) = mysqli_fetch_array($r, MYSQLI_NUM);
    } else {
        // Neu CID khong hop le se khong the hien thi category
        $messages = "<p class='warning'>The category does not exist.</p>";
    }
    ?>

    <h2>Edit category <?php if(isset($cat_name)) echo $cat_name; ?></h2>
        <?php if(!empty($messages)) {echo $messages;} ?>
            <form action="" method="post" id="edit_cat">
                <fieldset>
                    <legend>Edit category</legend>
                    <div>
                        <label for="category">Category Name: <span class="required">*</span>
                    <?php 
                    if(isset($errors) && in_array('category', $errors)) {
                        echo "<p class='warning'> Please fill in the category name</p>";
                    }
                    ?>
                    
                    
                    </label>
                        <input type="text" name="category" id ="category" value="<?php if(isset($cat_name)) echo $cat_name; ?>"  size="20" maxlength ="150" tabindex ="1" />
                    </div>
                    <div>
                        <label for="position">Position: <span class="required">*</span>
                      <?php 
                    if(isset($errors) && in_array('position', $errors)) {
                        echo "<p class='warning'> Please pick a position</p>";
                    }
                    ?>
                    
                    </label>
                        <select name="position" tabindex ="2">
                            <?php 
                            $q ="SELECT count(cat_id) AS count FROM categories";
                            $r = mysqli_query($dbc,$q) or die("Query ($q) \n<br/> MYSQL Error: " . mysqli_error($dbc));
                            if(mysqli_num_rows($r) == 1) {
                                list($num) = mysqli_fetch_array($r, MYSQLI_NUM);
                                for ($i =1; $i <= $num + 1; $i++) {  // tao vong for de ra option, cong them 1 gia tri cho position
                                    echo "<option value='{$i}'";
                                    if(isset($position) && ($position== $i)) echo "selected = 'selected' ";
   
                                    echo ">".$i."</option>";
                                }
                            }
                                ?>
                        </select>
                    </div>
                </fieldset>
                <p><input type="submit" name="submit" value="Edit Category"></p>
            </form>
          
</div>

<?php include('../includes/sidebar-b.php'); ?>
<?php include('../includes/footer.php'); ?>
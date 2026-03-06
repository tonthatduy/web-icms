<?php include('../includes/header.php'); ?>
<?php include('../includes/mysqli_connect.php'); ?>
<?php include('../includes/sidebar-admin.php'); ?>

<div id="content">
            <h2>Create a category</h2>
<?php
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Gia tri tri ton tai, xu ly form
            $errors = [];
            
            if(empty($_POST['category'])) {
                $errors[] = "category";
            } else {
                $cat_name = $_POST['category'];
            }
            // Check position ton` tai,  so nguyen , >= 1;

            $position = filter_input(INPUT_POST, 'position', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

            if($position === false) {
                $errors[] =  "position";
            }

            // if(isset($_POST['position']) && filter_var($_POST['position'], FILTER_VALIDATE_INT, array('min_range' => 1))){
            //     $position = $_POST['position'];
            // } else {
            //     $errors[] =  "position";
            // }

            if(empty($errors)) {
                // Neu khong co loi xay ra thi chen du lieu vao
                $q = "INSERT INTO categories (user_id, cat_name, position) VALUES (1,'$cat_name',$position)"; 
            $r = mysqli_query($dbc, $q) or die("Query ($q) \n<br/> MYSQL Error: " . mysqli_error($dbc));
                if(mysqli_affected_rows($dbc) == 1)  {
                echo "<p> The category was added successfully.</p>";
                } else {
                echo "<p>Could not added to the database due to a system error.</p>";
                }
            } else {
                echo "Please fill all the required fields";
            }
       
        } // END main IF submit condition
?>
            <form action="" method="post" id="add_cat">
                <fieldset>
                    <legend>Add category</legend>
                    <div>
                        <label value="<?php if(isset($_POST['category'])) echo $_POST['category']; ?>" for="category">Category Name: <span class="required">*</span>
                    <?php 
                    if(isset($errors) && in_array('category', $errors)) {
                        echo "<p class='warning'> Please fill in the category name</p>";
                    }
                    ?>
                    
                    
                    </label>
                        <input type="text" name="category" id ="category" value="" size="20" maxlength ="150" tabindex ="1" />
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
                            <option value="0"> Option</option>
                        </select>
                    </div>
                </fieldset>
                <p><input type="submit" name="submit" value="Add Category"></p>
            </form>
          
</div>

<?php include('../includes/sidebar-b.php'); ?>
<?php include('../includes/footer.php'); ?>
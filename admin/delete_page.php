<?php include('../includes/functions.php'); ?>
<?php include('../includes/mysqli_connect.php'); ?>
<?php include('../includes/header.php'); ?>
<?php include('../includes/sidebar-admin.php'); ?>
<div id ="content">
    <?php 
    if(isset($_GET['pid'], $_GET['pn']) && filter_var($_GET['pid'], FILTER_VALIDATE_INT,  array('options' => array('min_range' => 1)))) {
        $pid = $_GET['pid'];
        $page_name = $_GET['pn'];
        // neu cid va cat_name ton tai, thi se xoa page khoi csdl
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Xu ly form
            if(isset($_POST['delete']) && ($_POST['delete'] == 'yes')) {
                //Neu muon delete page
                $q = "DELETE FROM pages WHERE page_id = {$pid} LIMIT 1";
                $r = mysqli_query($dbc,$q);
                confirm_query($r,$q);
                if(mysqli_affected_rows($dbc) == 1) {
                    // Xoa thanh cong bao cho nguoi dung biet
                    $messages = "<p class='success'>The page was deleted successfully.</p>";
                } else {
                    $messages = "<p class='warning'>The page was not deleted due to a system error.</p>";
                }
            } else {
                // Ko muon delete page
                $messages = "<p class='warning'> I thought so too! shouldn't be deleted.</p>";
            }
        }
    } else {
        // Neu PID va page_name khong ton tai, hoac khong dung dinh dang mong muon
        redirect_to('admin/view_pages.php');
    }
    ?>
    <h2> Delete Pages: <?php if(isset($page_name)) echo htmlentities($page_name, ENT_COMPAT, 'UTF-8')?></h2>
    <?php if(!empty($messages)) echo $messages; ?> 
        <form action="" method="POST">
            <fieldset>
                <legend>Delete Page</legend>
                <label for="delete">Are you sure?</label>
                <div>
                    <input type="radio" name="delete" value="no" checked="checked" /> NO
                    <input type="radio" name="delete" value="yes" /> YES
                </div>
                <div>
                    <input type="submit" name="submit" value="Delete" onclick="return confirm('Are you sure?');" />
                </div>
            </fieldset>
        </form>    
</div> <!--End content -->

<?php include('../includes/footer.php'); ?>
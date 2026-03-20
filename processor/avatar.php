<?php 
session_start();
?>
<?php include('../includes/mysqli_connect.php'); ?>
<?php include('../includes/functions.php'); ?>

<?php
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_FILES['image'])) {

        $errors = [];

        $allowed = ['image/jpeg','image/jpg','image/png','image/x-png'];

        if($_FILES['image']['error'] == 0) {

            if(in_array(strtolower($_FILES['image']['type']), $allowed)) {

                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $rename = uniqid(rand(), true) . '.' . $ext;


                if(!move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/images/" . $rename)) {
                    $errors[] = "<p class='errors'>Server problem</p>";
                } else {
                    echo "Upload thành công!";
                }

            } else {
                $errors[] = "<p class='error'>File không hợp lệ. Chỉ chấp nhận JPG hoặc PNG.</p>";
            }

        } else {
            $errors[] = "<p class='error'>Lỗi upload file.</p>";
        }

        print_r($errors);
    }

    // Check for an error
    if($_FILES['image']['error'] > 0) {
        $errors[] = "<p class='error'>The file could not be uploaded because: <strong>";

        // Print the message based on the error
        switch ($_FILES['image']['error']) {
            case 1:
                $errors[] .= "The file exceeds the upload_max_filesize setting in php.ini";
                break;
                
            case 2:
                $errors[] .= "The file exceeds the MAX_FILE_SIZE in HTML form";
                break;
             
            case 3:
                $errors[] .= "The was partially uploaded";
                break;
            
            case 4:
                $errors[] .= "NO file was uploaded";
                break;

            case 6:
                $errors[] .= "No temporary folder was available";
                break;

            case 7:
                $errors[] .= "Unable to write to the disk";
                break;

            case 8:
                $errors[] .= "File upload stopped";
                break;
            
            default:
                $errors[] .= "a system error has occured.";
                break;
        } // END of switch

        $errors[] .= "</strong></p>";
    } // END of error IF
    // Xoa file da duoc upload va ton tai trong thu muc tam
    if(isset($_FILES['image']['tmp_name']) && is_file($_FILES['image']['tmp_name']) && file_exists($_FILES['image']['tmp_name'])) {
        unlink($_FILES['image']['tmp_name']);
    }           
}// END main if

if(empty($errors)) {
    //Update CSDL
    $avatar = 'uploads/images/' . $rename;
    $stmt = mysqli_prepare($dbc,"UPDATE users SET avatar = ?
                            WHERE user_id = ? LIMIT 1 ");
            mysqli_stmt_bind_param($stmt,"si",$avatar,$_SESSION['user_id']);
            mysqli_stmt_execute($stmt);

    if(mysqli_stmt_execute($stmt)) {
        // Update thanh cong, chuyen huong ngoai dung ve trong edit_profile
            redirect_to('edit_profile.php');
            exit;
    }
}

report_error($errors);
if(!empty($message)) echo $message;
?>
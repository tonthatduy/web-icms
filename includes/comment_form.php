<?php 
$errors = [];

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate name
    if(empty($_POST['name'])) {
        $errors[] = 'name';
    } else {
        $name = mysqli_real_escape_string($dbc,strip_tags($_POST['name']));
    }

    // Validate Email
    if(isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $e = mysqli_real_escape_string($dbc, strip_tags($_POST['email']));
    } else {
        $errors[] = 'email';    
    }

    // Validate Comment
    if(empty($_POST['comment'])) {
        $errors[] = 'comment';
    } else {
        $comment = mysqli_real_escape_string($dbc,$_POST['comment']);
    }

    // Validate captcha question
    if(isset($_POST['captcha']) && trim($_POST['captcha']) != $_SESSION['q']['answer']) {
        $errors[] = "wrong";
    }

    if(!empty($_POST['url'])) {
        redirect_to('thankyou.html');
        
    }

    if(!empty($_POST['question'])) {
        $errorsp[] = "delete";
    } 

    if(empty($errors)) {
        $stmt = mysqli_prepare($dbc, "INSERT INTO comments (page_id,author,email,comment,comment_date) 
                                      VALUES (?,?,?,?,NOW())");
                mysqli_stmt_bind_param($stmt,"isss",$pid,$name,$e,$comment);

        if(mysqli_stmt_execute($stmt)) {
            if(mysqli_stmt_affected_rows($stmt) == 1) {
                //Success
                $message = "<p class='success'>Thank you for your comment</p>"; 
            } else { // No mactch was made
                $message = "<p class='error'>Your comment could,not be posted due to a system error.</p>";
            }
        } else {
            $message ="<p class='error'>The comment could not be add due to a system error</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
            $message = "<p class='error'>Please try again</p>";
    }
} // End main IF

?>
<?php
// Hien thi comment tu csdl
   $stmt = mysqli_prepare($dbc, "SELECT c.comment_id, c.author, c.comment, DATE_FORMAT(c.comment_date, '%b %d %y') AS date 
                              FROM comments AS c 
                              WHERE page_id = ?");
        mysqli_stmt_bind_param($stmt,"i",$pid);
        mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) > 0 ) {
    // Neu co comment de hien thi ra trinh duyet
    echo "<ol id ='disscuss'>";
    while(list($cmt_id,$author,$comment,$date) = mysqli_fetch_array($result, MYSQLI_NUM)) {
        echo "<li class='comment-wrap'>
                <p class='author'>{$author}</p>
                <p class='comment-sec'>{$comment}</p>";
            if(is_admin()) echo "<a id='{$cmt_id}' class='remove'>Delete</a>";
        echo    "<p class='date'>{$date}</p>
            </li>";
    } //End While
    echo "</ol>";
} else { 
    //New ko co comment, thi se bao ra trinh duyet
    echo "<h2> Be the first to leave a comment.</h2>";
} // End If Mysqli_num_rows
?>
<?php if(!empty($message)) echo $message; ?>
<form id="comment-form" action="" method="POST">
    <fieldset>
        <legend>Leave a Comment</legend>
        <div>
            <label for="name">Name: <span class="required">*</span>
            <?php if(isset($errors) && in_array('name',$errors)) {
                echo "<span class='warning'>Please enter your name.</span>";
            }
            ?>        
            </label>
            <input type="text" name="name" id="name" value="<?php if(isset($_POST['name'])) {echo htmlentities($_POST['name'], ENT_COMPAT, 'UTF-8');} ?>" size="20" maxlength="80" tabindex="1">
        </div>

        <div>
            <label for="email">Email: <span class="required">*</span>
            <?php if(isset($errors) && in_array('email',$errors)) {
                echo "<span class='warning'>Please enter your email.</span>";
            }
            ?>  
            </label>
            <input type="text" name="email" id="email" value="<?php if(isset($_POST['email'])) {echo htmlentities($_POST['email']);} ?>" size="20" maxlength="80" tabindex="2">
        </div>

        <div>
            <label for="comment">Your Comment: <span class="required">*</span>
            <?php if(isset($errors) && in_array('comment',$errors)) {
                echo "<span class='warning'>Please enter your comment.</span>";
            }
            ?> 
            </label>
            <div id="comment"><textarea id="editor" name="comment" rows="10" cols="50" tabindex="3"><?php if(isset($_POST['comment'])) {echo htmlentities($_POST['comment']);} ?></textarea> </div>
        </div>

        <div>
            <label for="captcha">"Hãy điền vào giá trị số cho câu hỏi sau: " <?php echo captcha(); ?><span class="required">*</span>
            <?php if(isset($errors) && in_array('wrong',$errors)) {
                echo "<span class='warning'>Please give a correct answer.</span>";
            }
            ?> 
            </label>
            <input type="text" name="captcha" id="captcha" value="" size="20" maxlength="5" tabindex="4">
        </div>

        <div class="">
            <label for="question">"Hãy xoá giá trị ở trường dưới, trước khi comment"
                <?php if(isset($errors) && in_array('delete',$errors)) {
                    echo "<span class='warning'>Hãy xoá đi giá trị trong ô bên dưới</span>";
                    }
                ?> 

            </label>
            <input type="text" name="question" id="question" value="Xoá đi giá trị này" size="20" maxlength="40">
        </div>

        <div class="website">
            <label for="website">"Nếu bạn nhìn thấy trường này, thì ĐỪNG điền gì vào hết"</label>
            <input type="text" name="url" id="url" value="" size="20" maxlength="20">
        </div>
    </fieldset>
    <div><input type="submit" name="submit" value="Post Comment"></div>
</form>
<?php 
    include('includes/mysqli_connect.php'); 
    include('includes/functions.php'); 

      // Kiểm tra pid hợp lệ
    if($pid = validate_id($_GET['pid'])) {
        // Neu PID hop le thi tien hanh try van csdl
        $result = get_page_by_id($dbc,$pid);
        $posts = []; // Tao mot array trong de luu gia tri vao su dung sau nay cho phan noi dung
       
        if(mysqli_num_rows($result) > 0) {
        // Neu co post de hien thi ra trnh duyet.
            $pages = mysqli_fetch_assoc($result);
            $title = $pages['page_name'];
            $posts[] = array(
                'page_name' => $pages['page_name'], 
                'content' => $pages['content'], 
                'author' => $pages['name'], 
                'post_on' => $pages['date'],
                'aid' => $pages['user_id']
                );    
        } else {
        echo "<p>Thre are currenlty no post in this category.</p>";
        }
    } else {
        // Neu pid khong hop le, thi chuyen huong nguoi dung ve trang chu
        redirect_to();
    }

    include('includes/header.php'); 
    include('includes/sidebar-a.php'); 
?>

<div id="content">
    <?php 
    foreach($posts as $post) {
        echo "
            <div class='post'> 
                <h2>{$post['page_name']}</h2>
                <p>{$post['content']}</p>
                <p class='meta'><strong>Posted By: </strong><a href='author.php?aid={$post['aid']}'>{$post['author']}</a> | <strong>On: </strong> {$post['post_on']}</p>
            </div>
        ";
    } // End Foreach
    ?>         
</div>

<?php 
    include('includes/sidebar-b.php'); 
    include('includes/footer.php'); 
?>
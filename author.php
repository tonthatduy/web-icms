<?php $title = 'Author Page'; include('includes/header.php') ?>
<?php include('includes/mysqli_connect.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('includes/sidebar-a.php'); ?>
<div id="content">

    <?php 
        if($aid = validate_id($_GET['aid'])) {
                 // Đặt số trang muốn hiển thị ra trình duyệt
            $display = 4;
                // Xác định vị trí bắt đầu
           $start = isset($_GET['s']) && filter_var($_GET['s'],FILTER_VALIDATE_INT,['options' => ['min_range' => 1]]) ? $_GET['s'] : 0;
                
                // neu author id ton tai, thi ae truy van csdl
                $stmt = mysqli_prepare($dbc, 
                        "SELECT p.page_id, p.page_name, p.content,
                        DATE_FORMAT(p.post_on, '%b, %d, %y') AS date,
                        CONCAT_WS(' ', u.first_name, u.last_name) AS name, u.user_id
                        FROM pages AS p
                        INNER JOIN users AS u
                        USING (user_id)
                        WHERE u.user_id = ?
                        ORDER BY date ASC LIMIT ?, ?
                        ");
                mysqli_stmt_bind_param($stmt, "iii",$aid,$start,$display);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                if(mysqli_num_rows($result) > 0) {
                    // Co gia tri tra ve, hien thi ra trinh duyet
                    while($author = mysqli_fetch_assoc($result)) {
                        echo "
                            <div class='post'> 
                                <h2><a href='single.php?pid={$author['page_id']}'>{$author['page_name']}</a></h2>
                                <p>".the_excrept($author['content'])." ... <a href='single.php?pid={$author['page_id']}'>Read more</a></p>
                                <p class='meta'><strong>Posted By: </strong><a href='author.php?aid={$author['user_id']}'>{$author['name']} </a> | <strong>On: </strong> {$author['date']}</p>
                            </div>
                            ";
                        } // End While

                        //Phan trang cho phan author
                    echo pagination($aid,$display);
                } else {
                 echo "<p class='warning'>The author you are typing to view is no longer available.</p>";
                }
        } else {
            redirect_to();
        }
    ?>
   

</div>

<?php include('includes/sidebar-b.php'); ?>
<?php include('includes/footer.php'); ?>
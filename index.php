<?php include('includes/header.php'); ?>
<?php include('includes/mysqli_connect.php'); ?>
<?php include('includes/functions.php'); ?>
<?php include('includes/sidebar-a.php'); ?>

<div id="content">
    <?php 
     // Kiểm tra cid hợp lệ
    if (isset($_GET['cid']) && filter_var($_GET['cid'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
    
        $cid = (int) $_GET['cid'];

        $stmt = mysqli_prepare($dbc, 
            "SELECT p.page_name, p.page_id, p.content, 
            DATE_FORMAT(p.post_on, '%b, %d, %Y') AS date, 
            CONCAT_WS(' ', u.first_name, u.last_name) AS name, u.user_id 
            FROM pages AS p 
            INNER JOIN users AS u 
            USING (user_id) 
            WHERE p.cat_id = ? 
            ORDER BY date ASC LIMIT 0, 10");

        mysqli_stmt_bind_param($stmt,"i", $cid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) > 0) {
        // Neu co post de hien thi ra trnh duyet.
        while($pages = mysqli_fetch_assoc($result)) {
            echo "
                <div class='post'> 
                    <h2><a href='single.php?pid={$pages['page_id']}'>{$pages['page_name']}</a></h2>
                    <p>".the_excrept($pages['content'])." ... <a href='single.php?pid={$pages['page_id']}'>Read more</a></p>
                    <p class='meta'><strong>Posted By: </strong><a href='author.php?aid={$pages['user_id']}'>{$pages['name']}</a> | <strong>On: </strong> {$pages['date']}</p>
                </div>
            ";
        } // End While Loop
        } else {
        echo "<p>Thre are currenlty no post in this category.</p>";
        }
        mysqli_stmt_close($stmt);
    } else if (isset($_GET['pid']) && filter_var($_GET['pid'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {

        $pid = (int) $_GET['pid'];

        $stmt = mysqli_prepare($dbc, 
                                "SELECT p.page_name, p.content, DATE_FORMAT(p.post_on, '%b, %d, %y') AS date, CONCAT_WS(' ', u.first_name, u.last_name) AS name, u.user_id, COUNT(c.comment_id) AS count \n"

                                . "FROM users AS u\n"

                                . "INNER JOIN pages AS p\n"

                                . "USING(user_id)\n"

                                . "LEFT JOIN comments AS c \n"

                                . "ON p.page_id = c.page_id\n"

                                . "WHERE p.page_id = ?\n"

                                . "GROUP BY p.page_name\n"

                                . "ORDER BY date ASC");
        mysqli_stmt_bind_param($stmt,"i",$pid);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if(mysqli_num_rows($result) > 0) {
            // Neu co ket qua tra ve, hien thi ra trinh duyet
            while($pages = mysqli_fetch_assoc($result)) {
                echo "
                    <div class='post'> 
                        <h2><a href='single.php?pid=$pid'>{$pages['page_name']}</a></h2>
                        <p class='comments'><a href='single.php?pid=$pid#disscuss'>{$pages['count']}</a></p>
                        <p>".the_excrept($pages['content'])." ... <a href='single.php?pid=$pid'>Read more</a></p>
                        <p class='meta'><strong>Posted By: </strong><a href='author.php?aid={$pages['user_id']}'>{$pages['name']} </a> | <strong>On: </strong> {$pages['date']}</p>
                    </div>
            ";
            } // End While
        } else {
            // New khong co ket qua, hoac ID khong ton tai hoac khong hop le
            echo "<p class='warning'>The article you are viewing is not avaible</p>";
        }

    } else {
    ?>
        <h2>Welcome To izCMS</h2>
            <div>
                <p>
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                    Omnis quam fuga consectetur, sapiente iste labore eligendi
                    accusantium nam saepe deserunt dicta quo nemo! Numquam,
                    nobis, explicabo error quo cum, eum hic sequi aspernatur et
                    nam expedita aliquid sint illum rerum! Consequatur
                    voluptates, fugiat repudiandae magni, repellat ad expedita
                    harum, suscipit explicabo soluta similique nihil ut odio
                    ullam. Eius officia alias quia atque officiis sint
                    consectetur deleniti error commodi. Omnis amet fugit
                    tenetur, totam iusto nobis corporis nemo quidem voluptatum
                    architecto illum ipsa earum in quisquam! Placeat minus qui
                    deserunt dignissimos laboriosam totam modi quam nobis.
                    Provident expedita perferendis exercitationem vero.
                </p>

                <p>
                    Lorem ipsum dolor, sit amet consectetur adipisicing elit.
                    Saepe impedit consequatur neque officia harum aperiam quas
                    ullam placeat nisi qui nostrum vero officiis, esse
                    doloremque optio dolor repudiandae laborum cum nesciunt
                    dolorum numquam quisquam voluptatibus fuga? Ab eos dolore
                    perspiciatis. Excepturi, laboriosam nobis ab asperiores
                    autem deleniti, nam rem laborum neque ullam at expedita modi
                    dolores. Obcaecati quibusdam recusandae expedita. Neque
                    exercitationem atque ratione vero dolorem assumenda id
                    repellendus minima animi ipsam dignissimos, placeat odio
                    unde magni ullam suscipit beatae. Debitis, cum. Voluptatum
                    debitis cumque iste et, enim sequi minima eos, aut voluptate
                    officia dolores sint magnam nihil quasi velit.
                </p>

                <p>
                    Lorem ipsum, dolor sit amet consectetur adipisicing elit.
                    Fugit magni quo quae voluptatibus recusandae velit sequi
                    ipsam laboriosam consectetur magnam suscipit accusantium hic
                    dolor qui laborum cumque cupiditate voluptates quaerat,
                    culpa, amet nobis voluptas rerum exercitationem doloribus?
                    Delectus quasi autem dignissimos ipsum molestias doloremque
                    ullam, repellat voluptatibus placeat, veniam minus.
                </p>
            </div>
            <?php } ?>
</div>

<?php include('includes/sidebar-b.php'); ?>
<?php include('includes/footer.php'); ?>
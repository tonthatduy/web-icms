<?php
    // Xac dinh hang so cho dia chi tuyet doi
    define('BASE_URL', 'http://localhost/icms/');
    // Kiem tra xem ket qua tra ve co dung hay khong?
    function confirm_query($result, $query) {
        global $dbc;
        if(!$result) {
            die("Query ($query) \n<br/> MYSQL Error: " . mysqli_error($dbc));
        }
    }

    function redirect_to($page = 'index.php') {
        $url = BASE_URL . $page;    
    header ("Location: $url");
        exit();
    }

    function the_excrept($text) {
        return substr($text,0,strrpos($text,' '));
    }

    function validate_id($id) {
        if (isset($id) && filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            $val_id = (int) $id;
            return $val_id;
        } else {
            return NULL;
        }
    } // End Validate

    function get_page_by_id($dbc,$id) {
        $stmt = mysqli_prepare($dbc, 
            "SELECT p.page_name, p.page_id, p.content, 
            DATE_FORMAT(p.post_on, '%b, %d, %Y') AS date, 
            CONCAT_WS(' ', u.first_name, u.last_name) AS name, u.user_id 
            FROM pages AS p 
            INNER JOIN users AS u 
            USING (user_id) 
            WHERE p.page_id = ? 
            ORDER BY date ASC LIMIT 1");

        mysqli_stmt_bind_param($stmt,"i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        return $result;

    }
?>
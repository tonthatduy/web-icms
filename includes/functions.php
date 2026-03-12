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

    // Tái định hướng người dùng về trang mặc định là index
    function redirect_to($page = 'index.php') {
        $url = BASE_URL . $page;    
    header ("Location: $url");
        exit();
    }

    // Cắt chữ để hiển thị thành đoạn văn ngắn
    function the_excrept($text) {
        $sanitized = htmlentities($text, ENT_COMPAT, 'UTF-8');
        if(strlen($sanitized) > 400) {
            $cutString = substr($sanitized, 0, 400);
            $words = substr($sanitized, 0, strrpos($cutString, ' '));
            return $words;
        } else {
            return $sanitized;
        }
    } // End the Excrept

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

    //tao paragraph tu CSDL
    function the_content($text) {
        $sanitized = htmlentities($text, ENT_COMPAT, 'UTF-8');
       return str_replace(array("\r\n", "\n"), array("<p>","</p>"),$sanitized);
    }

    // Question Captcha
    function captcha() {
        $qna = [
            1 => array('question' => 'Mot cong mot', 'answer' => 2),
            2 => array('question' => 'ba tru hai', 'answer' => 1),
            3 => array('question' => 'ba nhan nam', 'answer' => 15),
            4 => array('question' => 'sau chia hai', 'answer' => 3),
            5 => array('question' => 'nang bach tuyet va .... chu lun', 'answer' => 7),
            6 => array('question' => 'Alibaba va ... ten cuop', 'answer' => 40),
            7 => array('question' => 'an mot qua khe, tra ... cuc vang', 'answer' => 1),
            8 => array('question' => 'may tui ... gang, mang di ma dung', 'answer' => 3)
        ];
        $rand_key = array_rand($qna); // Lay ngau nhien mot trong cac array
        $_SESSION['q'] = $qna[$rand_key];
        return $question = $qna[$rand_key]['question'];
    } // End Function Captcha
?>
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

    // Kiểm tra xem người dùng đã đăng nhập hay chưa
    function is_logged_in() {
        if(!isset($_SESSION['user_id'])) {
            redirect_to('login.php');
        }
    } // End is Loggin

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

    function pagination($aid, $display =4) {
        global $dbc;
        global $start;

        if(isset($_GET['p']) && filter_var($_GET['p'],FILTER_VALIDATE_INT,['options' =>['min_range'=> 1]])) {
                $page = $_GET['p'];
        }  else {
                // Neu biến p không có, sẽ truy vấn CSDL để tìm xem có bao nhiêu page để hiển thị
                $q = "SELECT COUNT(page_id) FROM pages";
                $r = mysqli_query($dbc,$q);
                confirm_query($r,$q);
                list($record) = mysqli_fetch_array($r, MYSQLI_NUM);

                if($record > $display) {
                    $page = ceil($record/$display);
                } else {
                    $page = 1;
                }
        }

         
        $output = "<ul class = 'pagination'> " ;
        if($page > 1) {
            $current_page = ($start/$display) + 1;


            // Nếu không phải ở trang đầu (hoặc 1) thì sẽ hiển thị trang trước

            if($current_page != 1) {
                $output .= "<li><a href='author.php?aid={$aid}&s=".($start - $display)."&p={$page}'>Previous</a></li>";
            }

            // Hiển thị những phần số còn lại của trang
            for($i = 1; $i <= $page ; $i++) {
                if($i != $current_page) {
                    $output .= "<li><a href='author.php?aid={$aid}&s=".($display * ($i - 1))."&p={$page}'>{$i}</a></li>";
                } else {
                    $output .= "<li class='current'>{$i}</li>";
                }
            } // End FOR loop
            
            // Neeus khoong phải trang cuối thì hiển thị trang kế
            if($current_page != $page) {
                $output .= "<li><a href='author.php?aid={$aid}&s=".($start + $display)."&p={$page}'>Next</a></li>";
            }
        } // EN pagination section
        $output .= "</ul>";

        return $output;
    } // End pagination

    //
    function clean_email($value) {
        $suspects = ['to:', 'bcc', 'cc','content-type:','mime-version:','multipart-mixed:','content-transfer-endcoding:'];
        foreach($suspects as $s) {
           if(strpos($value, $s) !== FALSE) {
                return '';
           }
           // Trả về giá trị cho dấu xuống hàng
           $value = str_replace(['\n', '\r', '$0a', '$0d'], '', $value);
           return trim($value);
        }
    }
?>
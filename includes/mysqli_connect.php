<?php
    // Ket noi vs CSDL
    $dbc = mysqli_connect('localhost','root','','izcms');

    //Neu ket noi ko thanh cong, thi bao loi ra

    if(!$dbc) {
        trigger_error("Could not connect to DB: " . mysqli_connect_error());
    } else {
        // Dat phuong thuc ket noi la utf-8
        mysqli_set_charset($dbc, 'utf8');
    }
?>
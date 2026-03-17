<?php
session_start();
?>

<!Doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>izCMS <?php echo (isset($title)) ? $title: "My Home page";?></title>
        <link rel="stylesheet" href="/icms/css/style.css" />
    </head>
    <body>
        <div id="container">
            <div id="header">
                <h1>
                    <a href="index.php">izCMS</a>
                </h1>
                <p class="slogan">THE iz Content Management System</p>
            </div>
            <div id="navigation">
                <ul>
                    <li>
                        <a href="index.php">Home</a>
                    </li>
                    <li>
                        <a href="#">About</a>
                    </li>
                    <li>
                        <a href="#">Services</a>
                    </li>
                    <li>
                        <a href="contact.php">Contact us</a>
                    </li>
                </ul>

                <p class="greeting">Xin chào bạn hiền</p>
            </div>

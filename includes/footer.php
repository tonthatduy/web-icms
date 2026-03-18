<div id="footer">
    <ul class ="footer-links">
        <?php 
       
        if(isset($_SESSION['user_level'])) {
            // Neu co SESSION
            switch($_SESSION['user_level']) {
                case 0: // Registered users access
                echo "
                    <li><a href='".BASE_URL."edit_profile.php'>User Profile</a></li>
                    <li><a href='".BASE_URL."change_password.php'>Change Password</a></li>
                    <li><a href='#'>Personal Message</a></li>
                    <li><a href='".BASE_URL."logout.php'>Log Out</a></li>
                ";
                break;
                
                case 2: // Admin access
                echo "
                    <li><a href='".BASE_URL."edit_profile.php'>User Profile</a></li>
                    <li><a href='".BASE_URL."change_password.php'>Change Password</a></li>
                    <li><a href='#'>Personal Message</a></li>
                    <li><a href='".BASE_URL."admin/admin.php'>Admin CP</a></li>
                    <li><a href='".BASE_URL."logout.php'>Log Out</a></li>
                ";
                break;
                
                default:
                echo "
                    <li><a href='".BASE_URL."register.php'>Register</a></li>
                    <li><a href='".BASE_URL."login.php'>Login</a></li>
                ";
                break;
                
            }
            
        } else {
            // Neu khong co $_SESSION
            echo "
                    <li><a href='register.php'>Home</a></li>
                    <li><a href='register.php'>Register</a></li>
                    <li><a href='login.php'>Login</a></li>
                ";
        }
       ?>
    </ul>
</div>
</div>
</div>
</body>
</html>
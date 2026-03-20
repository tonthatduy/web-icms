<?php
    $title = "Manage Users";
    include('../includes/header.php');
    require_once('../includes/mysqli_connect.php');
    require_once('../includes/functions.php');
    include('../includes/sidebar-admin.php');
    // Check to see if has admin access
    admin_access();  
?>
<div id="content">
<h2>Manage Users</h2>
    <table>
<thead>
	<tr>
		<th><a href="manage_users.php?sort=fn">First Name</a></th>
		<th><a href="manage_users.php?sort=ln">Last Name</a></th>
		<th><a href="manage_users.php?sort=e">Email</a></th>
        <th><a href="manage_users.php?sort=ul">User Level</a></th>
        <th>Edit User</th>
        <th>Delete User</th>
	</tr>
</thead>
<tbody>
    <?php 
        // Kiem tra xem bien sort ton tai hay khong?
        $sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'fn';

        // Sap xep thu tu cua bang bang bien
        $order_by = sort_table_users($sort);
        
        // Lay thong tin nguoi dung tu CSDL
        $users = fetch_users($order_by);

        // In ket qua ra trinh duyet
        foreach ($users as $user) {
            echo "
                <tr>
                    <td>" .$user['first_name']."</td>
                    <td>".$user['last_name']."</td>
                    <td>".$user['email']."</td>
                    <td>".$user['user_level']."</td>
                    <td><a class='edit' href='edit_user_2.php?uid=".urlencode($user['user_id'])."'>Edit</a></td>
                    <td><a class='delete' href='delete_user.php?uid=".urlencode($user['user_id'])."'>Delete</a></td>
                <tr>";
            } // End foreach  
    ?>
   </tbody>
</table>
</div>
    
<?php include('../includes/footer.php'); ?>
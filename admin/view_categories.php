<?php include('../includes/header.php'); ?>
<?php include('../includes/mysqli_connect.php'); ?>
<?php include('../includes/functions.php'); ?>
<?php include('../includes/sidebar-admin.php'); ?>
<div id ="content">
    <h2>Manage Categories</h2>
    <table>
        <thead>
            <tr>
                <th>
                    <a href="view_categories.php?sort=cat">Categories</a>
                </th>
                <th>
                    <a href="view_categories.php?sort=pos">Position</a>
                </th>
                <th>
                    <a href="view_categories.php?sort=by">Posted By</a>
                </th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Sap xep theo thu tu cua table head
            if(isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'cat' :
                        $order_by = 'cat_name';
                        break;
                    case 'pos' :
                        $order_by = 'position';
                        break;
                    case 'by'  :
                        $order_by = 'name';
                        break;

                    default: 
                        $order_by = 'position';
                        break;
                } // End Switch

            } else {
                $order_by = 'position';
            }
            
            // End isset ($_Get['sort'])
            // Truy xuat csdl de hien thi categories
            $q = "SELECT c.cat_id, c.cat_name, c.position, c.user_id, CONCAT_WS(' ', first_name, last_name) AS name"; 
            $q .= " FROM categories AS c ";
            $q .= " JOIN users AS u ";
            $q .= " USING(user_id) ";
            $q .= " ORDER BY {$order_by} ASC";
            $r = mysqli_query($dbc,$q);
                confirm_query($r, $q);
                while($cats = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                    echo " 
                    <tr>
                        <td>{$cats['cat_name']}</td>
                        <td>{$cats['position']}</td>
                        <td>{$cats['name']}</td>
                        <td><a href='edit_category.php?cid={$cats['cat_id']}' class='edit'>Edit</a></td>
                        <td><a href='delete_category.php?cid={$cats['cat_id']}&cat_name={$cats['cat_name']}' class='delete'>Delete</a></td>
                    </tr>
                 ";
                }
            ?>
           
        </tbody>
    </table>
</div> <!--End content -->


<?php include('../includes/footer.php'); ?>
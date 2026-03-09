<div id="content-container">
    <div id="section-navigation">
                <ul class="navi">
                  <?php 
                  $q = "SELECT c.cat_name FROM categories AS c ORDER BY position ASC";
                  $r = mysqli_query($dbc,$q) or die("Query ($q) \n<br/> MYSQL Error: " . mysqli_error($dbc));
                  while ($cats = mysqli_fetch_array($r, MYSQLI_ASSOC)) {
                    echo "<li><a href ='index.php'>".$cats['cat_name']."</a></li>";
                  }
                  ?>
                </ul>
    </div> 
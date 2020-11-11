<?php
/**
* Plugin Name: CRUD Basic 
* Plugin URI: https://www.narasak.com/
* Description: ตัวอย่างการสร้างปลั๊กอินพื้นฐาน สามารถใช้งานได้จริง และยังสามารถนำไปพัฒนาต่อยอดได้ด้วย
* Version: 1.0
* Author: Pawin Khenphukhiaw
**/
register_activation_hook( __FILE__, 'createTable'); 
function createTable() { 
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name = $wpdb->prefix . 'usertable';
  $sql = "CREATE TABLE `$table_name` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(220) DEFAULT NULL,
  `email` varchar(220) DEFAULT NULL,
  PRIMARY KEY(user_id)
  ) $charset_collate;
  ";

  if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
  }
}

// create menu
add_action('admin_menu', 'addMainPageContent');
function addMainPageContent() {
  add_menu_page('CRUD Basic', 'CRUD Basic', 'manage_options', __FILE__, 'crudMainPage', 'dashicons-schedule');
  add_submenu_page(__FILE__, 'Add WPuser' , 'Add WPuser','manage_options','add-userself','insert_data_func');
}

// use script and style css
function add_style_pwtbl_func() {
	wp_register_style('pwtbl_style', plugins_url('style.css',__FILE__ ));
	wp_register_script('pwtbl_script', plugins_url('pwtbl.js' , __FILE__ ));

	wp_enqueue_style('pwtbl_style');
	wp_enqueue_script( 'pwtbl_script' );
}
add_action( 'admin_init','add_style_pwtbl_func');


// crud section
function crudMainPage() {
  global $wpdb;
  $table_name = $wpdb->prefix . 'usertable';
  if (isset($_POST['newsubmit'])) {
    $name = $_POST['newname'];
    $email = $_POST['newemail'];
    $data = "INSERT INTO $table_name(name,email) VALUES('$name','$email')";
    $wpdb->query($data);
    echo "<script>location.replace('admin.php?page=crud-advance/main_file.php');</script>";
  }
  if (isset($_POST['uptsubmit'])) {
    $id = $_POST['uptid'];
    $name = $_POST['uptname'];
    $email = $_POST['uptemail'];
    $data = "UPDATE $table_name SET name='$name',email='$email' WHERE user_id='$id'";
    $wpdb->query($data);
    echo "<script>location.replace('admin.php?page=crud-advance/main_file.php');</script>";
  }
  if (isset($_GET['del'])) {
    $del_id = $_GET['del'];
    $data = "DELETE FROM $table_name WHERE user_id='$del_id'";
    $wpdb->query($data);
    echo "<script>location.replace('admin.php?page=crud-advance/main_file.php');</script>";
  }
  ?>
  <div class="wrap">
    <h2>CRUD Basic</h2>
    <table class="wp-list-table widefat striped">
      <thead>
        <tr>
          <th>User ID</th>
          <th>ชื่อ-สกุล</th>
          <th>ที่อยู่อีเมลล์</th>
          <th>ดำเนินการ</th>
        </tr>
      </thead>
      <tbody>
        <form action="" method="post">
          <tr>
            <td><input type="text" value="AUTO_GENERATED" disabled></td>
            <td><input type="text" id="newname" name="newname"></td>
            <td><input type="text" id="newemail" name="newemail"></td>
            <td><button id="btnn-1" name="newsubmit" type="submit">INSERT</button></td>
          </tr>
        </form>
        <?php
          $result = $wpdb->get_results("SELECT * FROM $table_name");
          foreach ($result as $print) {
            echo "
              <tr>
                <td>$print->user_id</td>
                <td>$print->name</td>
                <td>$print->email</td>
                <td><a href='admin.php?page=crud-advance/main_file.php&upt=$print->user_id'><button type='button' id='btnn-2'>UPDATE</button></a> <a href='admin.php?page=crud-advance/main_file.php&del=$print->user_id'><button type='button' id='btnn-3'>DELETE</button></a></td>
              </tr>
            ";
          }
        ?>
      </tbody>  
    </table>
    <br>
    <br>
    <?php
      if (isset($_GET['upt'])) {
        $upt_id = $_GET['upt'];
        $result = $wpdb->get_results("SELECT * FROM $table_name WHERE user_id='$upt_id'");
        foreach($result as $print) {
          $name = $print->name;
          $email = $print->email;
        }
        echo "
        <table class='wp-list-table widefat striped'>
          <thead>
            <tr>
              <th width='25%'>User ID</th>
              <th width='25%'>Name</th>
              <th width='25%'>Email Address</th>
              <th width='25%'>Actions</th>
            </tr>
          </thead>
          <tbody>
            <form action='' method='post'>
              <tr>
                <td width='25%'>$print->user_id <input type='hidden' id='uptid' name='uptid' value='$print->user_id'></td>
                <td width='25%'><input type='text' id='uptname' name='uptname' value='$print->name'></td>
                <td width='25%'><input type='text' id='uptemail' name='uptemail' value='$print->email'></td>
                <td width='25%'><button id='btnn-2' name='uptsubmit' type='submit'>UPDATE</button> <a href='admin.php?page=crud-advance/main_file.php'><button type='button' id='btnn-4'>CANCEL</button></a></td>
              </tr>
            </form>
          </tbody>
        </table>";
      }
    ?>
  </div>
  <?php
}

// create shortcuts
add_shortcode('crud-pw','showData');
// show data
function showData(){
  global $wpdb;
  $table_name = $wpdb->prefix."usertable";
  $result = $wpdb->get_results("SELECT * FROM $table_name");
  echo "<div class='wrap'>
  <table class='wp-list-table widefat striped'>
  <tr>
          <th>User ID</th>
          <th>ชื่อ-สกุล</th>
          <th>ที่อยู่อีเมลล์</th>
  </tr>";
  foreach ($result as $print) {
    echo "<tr>
            <td>$print->user_id</td>
            <td>$print->name</td>
            <td>$print->email</td>
          </tr>
    ";
  }
  echo "  </table>
  </div>";
}

// create user
function insert_data_func(){
  if(isset($_POST['create-save'])){
    wp_create_user_func($_POST['user_name'], $_POST['password']);
  }
  
  echo "<div class='create-user'><h3>Add Users</h3>
    <form action='' method='post' enctype='multipart/form-data'>
      <input type='text' name='user_name' placeholder='Username'><br>
      <input type='text' name='password' placeholder='Password'><br>
      <input type='submit' name='create-save' value='Insert'>
    </form></div>"; 

  $all_users = get_users('orderby=ID');
  echo "<div class='wrap'>
  <table class='wp-list-table widefat striped'>";
  echo "<tr><th width='10%'>User ID</th><th>Display Name</th></tr>";
  foreach ($all_users as $user) {
    echo '<tr><td>' . esc_html($user->ID) . '</td><td>' . esc_html($user->display_name) . '</td></tr>';
  }
  echo "</table></div>";

}

function wp_create_user_func($username, $password) {
  $user_login = wp_slash( $username );
  $user_pass  = $password;
  $userdata = compact( 'user_login', 'user_pass' );
  return wp_insert_user( $userdata );
}

?>

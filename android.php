<?php
$servername = "localhost";
$username = "root";
$password = "0505";
$dbname = "test";
$tablename = "dani_login";

// Create connection
$conn =mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($conn,'utf8');
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$input_id = htmlspecialchars($_POST['ID']);
$input_pw = htmlspecialchars($_POST['PW']);


$select_sql = "SELECT * FROM dani_login WHERE ID='$input_id'";
$result_set = mysqli_query($conn, $select_sql);
$row = mysqli_fetch_array($result_set);

$db_id = $row[1];
$db_pw = $row[2];

if(($row[1]==$input_id && $row[2]==$input_pw ) ){
  echo "Success Login";
  setcookie("id", $db_id, time() + (86400 * 30), "/");
  setcookie("pw", $db_pw, time() + (86400 * 30), "/");

}else{
  echo "One more time to login Or You have to sign up.";
}


$conn->close();
?>

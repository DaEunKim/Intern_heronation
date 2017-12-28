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

$input_id = htmlspecialchars($_GET['ID']);
$input_pw = htmlspecialchars($_GET['PW']);


$select_sql = "SELECT * FROM dani_login WHERE ID='$input_id'";
$result_set = mysqli_query($conn, $select_sql);
$row = mysqli_fetch_array($result_set);

$db_id = $row[1];
$db_pw = $row[2];

if(($row[1]==$input_id && $row[2]==$input_pw ) ){
  echo "로그인 성공 ";
  setcookie("id", $db_id, time() + (86400 * 30), "/");
  setcookie("pw", $db_pw, time() + (86400 * 30), "/");

}else{
  echo "다시 로그인해보던지 회원가입을 해라";
}



$conn->close();
?>

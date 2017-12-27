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

$db_ID = htmlspecialchars($_GET['ID']);
$db_PW = htmlspecialchars($_GET['PW']);

$select_sql = "SELECT * FROM dani_login WHERE ID='$db_ID'";

$result_set = mysqli_query($conn, $select_sql);
$row = mysqli_fetch_array($result_set);
$arrlength = count($row);


if($row[1]==$db_ID && $row[2]==$db_PW){
  echo "로그인 성공";
  // echo $_GET["ID"];
  // echo $_GET["PW"];

  $cookie_name = $row[1];
  $cookie_value = $row[2];
  setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");

  if(!isset($_COOKIE[$cookie_name])) {
      echo "Cookie named '" . $cookie_name . "' is not set!";
  } else {
      echo "Cookie '" . $cookie_name . "' is set!";
      echo "Value is: " . $_COOKIE[$cookie_name];
  }

}else{
  echo "다시 로그인해보던지 회원가입을 해라";
}

$conn->close();
?>

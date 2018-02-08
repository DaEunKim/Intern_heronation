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
$db_PW_Check = htmlspecialchars($_GET['PW_Check']);

if($db_PW == $db_PW_Check){
  $insert_sql = "INSERT INTO dani_login (ID, PW) VALUES ('$db_ID', '$db_PW');";
  $result_set = mysqli_query($conn, $insert_sql);

  if($result_set){
    echo "회원가입 성공";
  }else{
    echo "중복된 아이디";
  }
}
else{
  echo "다시해 비번 안맞아";
}

mysqli_close($conn);

?>

<?php
$servername = "localhost";
$username = "root";
$password = "0505";
$dbname = "test";
$tablename = "dani_test";

// Create connection
$conn =mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// print_r($_GET);
$db_index = (int)($_GET['index']);
$db_companyID = (int)($_GET['companyID']);
$db_countryID = (int)($_GET['countryID']);
$db_ID = htmlspecialchars($_GET['ID']);
$db_PW = htmlspecialchars($_GET['PW']);

$sql = "SELECT * FROM dani_test";
$result = $conn->query($sql);
$insertsql = "INSERT INTO $tablename VALUES($db_index, $db_companyID, $db_countryID, '$db_ID', '$db_PW')";
if(mysqli_query($conn, $insertsql)){
  echo "index=" . $db_index . "&companyID=" . $db_companyID .
  "&countryID=" . $db_countryID . "&ID=" . $db_ID . "&PW=" . $db_PW;

  // echo "<script>location.href='http://heronation.net/test/dani/dani.html?index=" . $db_index . "&companyID=" . $db_companyID .
  // "&countryID=" . $db_countryID . "&ID=" . $db_ID . "&PW=" . $db_PW . "';</script>";
  // echo "success";
}

$conn->close();

?>

<?
include_once ("../header.php");
$conn=$classDB->getConn();
if(!$conn){
  echo "DB Connect Fail";
  die();
}

if(isset($_POST['UserSizeListID'])){
  $UserSizeListID = $_POST['UserSizeListID'];
}else{
  echo "POST_ERROR";
}

// $UserSizeListID = 2343;

$clickUpdate_query = "UPDATE UserSizeList SET UpdatedDate = NOW() WHERE PKey = $UserSizeListID";

$clickUpdate_sql = mysqli_query($conn, $clickUpdate_query);

if($clickUpdate_sql){
  echo "update_success";
}else{
  echo "update_fail";
}

mysqli_close($conn);
?>

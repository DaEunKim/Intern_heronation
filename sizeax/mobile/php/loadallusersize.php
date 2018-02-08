<?
@include_once("../../header.php");
$conn=$classDB->getConn();
if(!$conn){
  echo "DB Connect Fail";
  die();
}

if(isset($_POST['UserPKey']) && isset($_POST['CategoryID'])){
  $UserPKey = $_POST['UserPKey'];
  $CategoryID = $_POST['CategoryID'];

}else{
  echo "POST_ERROR";
}

  // $UserPKey = 0;
  // $CategoryID = 16;

$loadUserSizeList_query = "SELECT * FROM UserSizeList
  WHERE UserID = $UserPKey AND Status != 0 and CategoryID = $CategoryID
  ORDER BY UpdatedDate DESC";

$loadUserSizeList_sql = mysqli_query($conn,$loadUserSizeList_query);

$loadUserSizeList_result = array();
$loadUserSizeList_count = 0;
while($loadUserSizeList_row = mysqli_fetch_array($loadUserSizeList_sql)){
  $loadUserSizeList_result[$loadUserSizeList_count] = array(
    $loadUserSizeList_row["PKey"],
    $loadUserSizeList_row["Name"]
  );
  $loadUserSizeList_count++;
}

echo json_encode($loadUserSizeList_result,JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
?>

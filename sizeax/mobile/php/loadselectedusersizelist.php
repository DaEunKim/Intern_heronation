<?
@include_once("../../header.php");
$conn=$classDB->getConn();
if(!$conn){
  echo "DB Connect Fail";
  die();
}

if(isset($_POST['UserPKey']) && isset($_POST['UserSizeListID'])){
  $UserPKey = $_POST['UserPKey'];
  $UserSizeListID = $_POST['UserSizeListID'];

}else{
  echo "POST_ERROR";
}

  // $UserPKey = 2070;
  // $UserSizeListID = 2308;

$SelectedUserSizeList_query =
  "select UserSize.UserSizeListID as UserSizeListID, UserSize.SizeTypeID as SizeTypeID,
                          UserSize.Size as Size, UserSizeList.Name as UserSizeListName, SizeType.NameKR as SizeTypeName,
                          UserSizeList.Priority as UserPriority, UserSizeList.Status, UserSizeList.CategoryID as CategoryID
                          from UserSize
                          inner join UserSizeList on UserSize.UserSizeListID = UserSizeList.PKey
                          inner join SizeType on UserSize.SizeTypeID = SizeType.PKey
                          where UserSizeList.UserID = $UserPKey and UserSizeList.Status!=0 and UserSize.Size!=0 and UserSizeList.PKey = $UserSizeListID
                          order by UserSizeList.Priority, UserSizeList.UpdatedDate desc, UserSize.SizeTypeID";

$selectedUserSizeList_sql = mysqli_query($conn, $SelectedUserSizeList_query);

$selectedUserSizeList_result = array();

$selectedUserSizeList_count = 0;
while($selectedUserSizeList_row = mysqli_fetch_array($selectedUserSizeList_sql)){
  $selectedUserSizeList_result[$selectedUserSizeList_count] = array(
    $selectedUserSizeList_row['UserSizeListID'],
    $selectedUserSizeList_row['SizeTypeID'],
    $selectedUserSizeList_row['SizeTypeName'],
    $selectedUserSizeList_row['UserSizeListName'],
    $selectedUserSizeList_row['Size'],
    $selectedUserSizeList_row['UserPriority'],
    $selectedUserSizeList_row['CategoryID']
  );
  $selectedUserSizeList_count++;
}

echo json_encode($selectedUserSizeList_result,JSON_UNESCAPED_UNICODE);

mysqli_close($conn);
?>

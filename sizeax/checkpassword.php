<?
/*
 *  user의 id를 이용해 db에서 password hash 값을 가져와서 비교하는 코드
 */
  include_once("./header.php");

  if (isset($_POST['UserID']) && isset($_POST['UserPassword'])) {
    $userId = $_POST['UserID'];
    $userPassword = $_POST['UserPassword'];
    $conn = $classDB->getConn();
    if (!$conn) {
      die("server error");
    }
    //받아온 userid의 비밀번호 hash값을가져오는 쿼리
    $query = "SELECT PWD from User where binary(ID)='$userId'";
    if ($result = mysqli_query($conn, $query)) {
      if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result);
        $getPassword = $row['PWD'];
        if(password_verify($userPassword,$getPassword)){
          //비밀번호 검사
          echo "collect";
        } else echo "incollect";
      } else echo "id error";
    } else echo "query error";
  } else echo "no id"
?>

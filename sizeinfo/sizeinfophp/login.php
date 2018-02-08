<?
include 'connectdb.php';

$id = isset($_POST["ID"]) ? $_POST["ID"] : 'mapl003@naver.com';
$pw = isset($_POST["PW"]) ? $_POST["PW"] : '0';

comparePassword();

function comparePassword() {
  global $id, $pw, $conn;

  $selectQuery = "select PWD from User where ID = '$id';";
  $SQL = mysqli_query($conn, $selectQuery);

  if(mysqli_num_rows($SQL) == 1)
    $selectPWD = mysqli_fetch_row($SQL)[0];
  else {
    echo "fail";
    return false;
  }

  $salt = "hell";
  $pw = $pw.$salt;

  if(password_verify($pw, $selectPWD)) {
    echo "success";
    return true;
  } else {
    echo "비밀번호가 맞지 않습니다.";
    return false;
  }

}

?>

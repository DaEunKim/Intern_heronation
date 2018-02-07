<?
include 'connectdb.php';

$id = isset($_POST["ID"]) ? $_POST["ID"] : '0';
$pw = isset($_POST["PW"]) ? $_POST["PW"] : '0';
$cpw = isset($_POST["CPW"]) ? $_POST["CPW"] : '0';
$name = isset($_POST["Name"]) ? $_POST["Name"] : '0';

$passwordHash = "";
$insertUserQuery = "";
$insertUserSQL = "";

if(checkInfo()) {
  encryption();
  insertQuery();
  echo "Success";
}

function insertQuery() {
  global $id, $passwordHash, $name, $insertUserQuery, $insertUserSQL, $conn;

  $insertUserQuery = "insert into User(PKey, ID, Email, PWD, Name, Status) values(null, '$id', '$id', '$passwordHash', '$name', 10)";
  $insertUserSQL = mysqli_query($conn, $insertUserQuery) or die(mysqli_error($conn));

}

function encryption() {
  global $pw, $passwordHash;

  // $salt = MD5($pw)."hell";
  $salt = "hell";
  $pw = $pw.$salt;
  // echo $pw;
  $passwordHash = password_hash($pw, PASSWORD_DEFAULT);

}

function checkInfo() {

  if(!checkInsert()) {
    return false;
  } else if(!checkEmail()) {
    return false;
  } else if(!checkDuplicateID()) {
    return false;
  } else if(!checkPassword()) {
    return false;
  } else return true;

}

function checkInsert() {
  global $id, $pw, $cpw, $name;

  if($id != '0' && $pw != '0' && $cpw != '0' && $name != '0') return true;
  else {
    echo "정보를 모두 입력해주세요.";
    return false;
  };
}

function checkDuplicateID() {
  global $id, $conn;

  $selectIDQuery = "select id from User where ID = '$id'";
  $selectIDSQL = mysqli_query($conn, $selectIDQuery);
  $checkDuplicateID = mysqli_num_rows($selectIDSQL);

  if($checkDuplicateID > 0) {
    echo "중복된 이메일입니다.";
    return false;
  }
  else return true;

}

function checkDuplicateName() {
  global $name, $conn;

  $selectNameQuery = "select id from User where Name = '$name'";
  $selectNameSQL = mysqli_query($conn, $selectNameQuery);
  $checkDuplicateName = mysqli_num_rows($selectNameSQL);

  if($checkDuplicateName > 0) return true;
  else return false;

}

function checkPassword() {
  global $pw, $cpw;

  $checkCPW = strcmp($pw, $cpw);
  $checkLen = strlen($pw);

  $pattern = '/^.*(?=^.{8,15}$)(?=.*\d)(?=.*[a-zA-Z]).*$/';
  $checkPreg = preg_match($pattern, "$pw");

  if($checkCPW) {
    echo "비밀번호가 다릅니다.";
    return false;
  } else if(!$checkPreg) {
    echo "비밀번호는 영문/숫자를 조합하여 8자 이상 입력해주세요.";
    return false;
  } else return true;
}

function checkEmail() {
  global $id;

  $checkEmail = filter_var($id, FILTER_VALIDATE_EMAIL);

  if($checkEmail) return true;
  else {
    echo "잘못된 이메일 형식입니다.";
    return false;
  }
}

?>

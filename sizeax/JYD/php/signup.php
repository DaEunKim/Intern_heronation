<?
include $_SERVER["DOCUMENT_ROOT"]."/database/databaseConnection.php";
$classDB = new ClassDB();
$conn=$classDB->getConn();

//암호화
$id = isset($_POST['ID']) ? $_POST['ID'] : "0";
$password_base = isset($_POST['PW']) ? $_POST['PW'] : "0"; //길이는 72byte까지
$ADNotification = isset($_POST['ADNotification']) ? $_POST['ADNotification'] : "0";
// echo "email : $email, password : $password";

const PASSWORD_COST = ['cost'=>12];// 가중치. 높을수록 강력하게 암호화 가능. 기본값은 10
$password_hash = password_hash($password_base,PASSWORD_BCRYPT,PASSWORD_COST); //암호화. hash의 크기는 60byte

//암호 검증
// $password ="test1123";
// if(password_verify($password,$hash)) echo $hash;
// else echo "incollect";

// 아이디 검사 쿼리.
$result = mysqli_query($conn,"SELECT * FROM User WHERE binary(ID)='$id'");
$user_data = mysqli_fetch_array($result);

// 혹시 몰라서 하나 이상으로 했다. 사실 있으면 무조건 1개여야 무결성 유지되는거임.
if(mysqli_num_rows($result)>=1){
  //있으면 중복처리.
  echo "dumpEmail";
}else{
  if($id != "0" && $password_base != "0") {
    $query = "INSERT INTO User(CompanyID, CountryID, ID, PWD, Email,  Level, CreatedDate, UpdatedDate, AccessTerms, PersonalData, PositionInformation, ADNotification)
    VALUES(0,0, '$id', '$password_hash', '$id', 20, Now(), Now(), 1, 1, 1, $ADNotification)";
    mysqli_query($conn, $query);
    $getPKey = "select PKey from User where ID = '$id'";

    $res = mysqli_query($conn, $getPKey);
    $result = mysqli_fetch_row($res);

    echo $result[0];
  } else {

    echo "Fail";
  }
}




// echo json_encode($totalResult, JSON_UNESCAPED_UNICODE);
mysqli_close($conn);
?>

<?
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASSWORD = "0505";
$DB_NAME = "heronation";

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
mysqli_set_charset($conn,'utf8');
if(!$conn){
  echo '서버와의 통신이 원할하지 않습니다.잠시후 다시 시도해주세요';
}
?>

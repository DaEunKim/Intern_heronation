<?
//암호화
$password ="$2y$12rsKUOAlEsGDDQmuBC5kpgOMYJsd8XISaWmK6rwRqTG1BZYz/LxbKW1111111111111"; //길이는 72byte까지
const PASSWORD_COST = ['cost'=>12];// 가중치. 높을수록 강력하게 암호화 가능. 기본값은 10
$hash = password_hash($password,PASSWORD_BCRYPT,PASSWORD_COST); //암호화. hash의 크기는 60byte

//암호 검증
$password ="$2y$12rsKUOAlEsGDDQmuBC5kpgOMYJsd8XISaWmK6rwRqTG1BZYz/LxbKW1111111111112";
if(password_verify($password,$hash)) echo $hash;
else echo "incollect";
?>

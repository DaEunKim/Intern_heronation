<?
$saveId = '';
$savePWD = '';
$isAutoLogin = 0;
$isPersonal = 1;
setcookie("loginSuccessZeyo", "", time()-1, "/");
setcookie("loginNonMemberZeyo", "", time()-1, "/");

if(!isset($_COOKIE['isAutoLoginZeyo'])){
    setcookie("isAutoLoginZeyo", 0, time() + (86400 * 365), "/");
    $isAutoLogin = 0;
}
else{
    if($_COOKIE['isAutoLoginZeyo'] == 1){
        $isAutoLogin = 1;
        $saveId = isset($_COOKIE['userIdZeyo']) ? $_COOKIE['userIdZeyo'] : "";
        $savePWD = isset($_COOKIE['userPasswordZeyo']) ? $_COOKIE['userPasswordZeyo'] : "";
    }
    else{
        $isAutoLogin = 0;
        $saveId = isset($_COOKIE['userIdZeyo']) ? $_COOKIE['userIdZeyo'] : "";
    }
}

if(isset($_COOKIE['isPersonalUserZeyo']) && $_COOKIE['isPersonalUserZeyo'] == 0){
    $isPersonal = 0;
}
?>

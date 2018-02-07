<?
include 'connectdb.php';

if(isset($_POST['userID']) && isset($_POST['userPWD'])){
    $userID = $_POST['userID'];
    $userPWD = $_POST['userPWD'];
    $isAutoLogin = $_POST['isAutoLogin'];
    $isPersonalUser = $_POST['isPersonalUser'];

    if($conn!=null){
        if($isPersonalUser){
            $userlevel = 20;
        }
        else{
            $userlevel = 10;
        }

        // 기업, 개인회원 구분 로그인
        // $query = 'SELECT PKey from User where Email="'.$userID.'" and PWD="'.$userPWD.'" and Level='.$userlevel.';';

        // 기업, 개인회원 구분 안하고 로그인
        $query = 'SELECT PKey from User where BINARY Email="'.$userID.'" and BINARY PWD="'.$userPWD.'";';

        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_array($result);

        if(mysqli_num_rows($result) == 1){ // login success

            $getPKey = $row['PKey'];


            if($isAutoLogin == 1){ // autologin checked
                setcookie("userPKeyZeyo", $getPKey, time() + (86400 * 30), "/"); // 86400 = 1 day
                setcookie("isAutoLoginZeyo", $isAutoLogin, time() + (86400 * 365), "/");
                setcookie("isPersonalUserZeyo", $isPersonalUser, time() + (86400 * 365), "/");
                setcookie("userIdZeyo", $userID, time() + (86400 * 30), "/");
                setcookie("loginSuccessZeyo", 1, time() + (86400 * 30), "/");
                setcookie("userPasswordZeyo", $userPWD, time() + (86400 * 30), "/");
            }
            else{
                setcookie("userPKeyZeyo", $getPKey, time() + (3600), "/"); // 86400 = 1 day
                setcookie("isAutoLoginZeyo", $isAutoLogin, time() + (3600), "/");
                setcookie("isPersonalUserZeyo", $isPersonalUser, time() + (3600), "/");
                setcookie("userIdZeyo", $userID, time() + (3600), "/");
                setcookie("loginSuccessZeyo", 1, time() + (3600), "/");
            }
            echo $getPKey;
            exit();
        }
        else {  // login fail
            setcookie("loginSuccessZeyo", "", time()-1, "/");
            setcookie("userPasswordZeyo", "", time()-1, "/");
            // echo $userPWD;
            echo "LOGIN_FAIL";
            exit();
        }
        mysqli_close($conn);
    }
    else {
        echo "DB_CONNECT_FAIL";
        exit();
    }
}
else{
    echo "LOGIN_FAIL";
    exit();
}
?>

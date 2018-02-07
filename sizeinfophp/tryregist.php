<?
include 'connectdb.php';

// 이메일 중복 검사
$userInfoTable = mysqli_query($conn, 'SELECT * from User Where User.Email = "'.$_POST["userEmail"].'"; ' );
$numOfUserInfo = mysqli_num_rows($userInfoTable);

// 연락처 중복 검사
// if(isset($_POST["phoneNum1"]) && !empty($_POST["phoneNum1"])){
//     $concatPhoneNum = $_POST['phoneNum1'].'-'.$_POST['phoneNum2'].'-'.$_POST['phoneNum3'];
//
//     $userPhoneTable = mysqli_query($conn, 'SELECT Phone from User Where User.Phone = "'.$concatPhoneNum.'"; ' );
//     $numOfUserPhoneNums = mysqli_num_rows($userPhoneTable);
//     if($numOfUserPhoneNums != 0){
//         echo "이미 가입된 유저 연락처입니다."; exit();
//     }
// }

// 중복되는 이메일이 없을 경우
if($numOfUserInfo == 0){
    if(isset($_POST["companyName"]) && !empty($_POST["companyName"])){
        $businessNum = $_POST["businessNum1"].$_POST["businessNum2"].$_POST["businessNum3"];
        // 기업명 중복 검사
        $companyInfoTable = mysqli_query($conn, 'SELECT * from Company Where Company.Name = "'.$_POST["companyName"].'" or Company.BusinessNumber = '.$businessNum.'; ' );
        $numOfCompanyInfo = mysqli_num_rows($companyInfoTable);

        if($numOfCompanyInfo != 0){
            echo "이미 등록된 기업입니다.";
            exit();
        }
    }

    $instUserQuery = 'INSERT INTO User set
        User.Email = "'.$_POST["userEmail"].'",
        User.Name = "'.$_POST["userName"].'",
        User.PWD = "'.$_POST["userPWD"].'" ';


    if(isset($_POST["phoneNum1"]) && !empty($_POST["phoneNum1"])){
        $instUserQuery = $instUserQuery.', User.Phone = "'.$concatPhoneNum.'"';
    }


    if($_POST["isPersonalUser"]){   // 개인회원 가입
        $instUserQuery = $instUserQuery.', User.Level = 20';
    }
    else{   // 기업회원 가입
        $instUserQuery = $instUserQuery.', User.Level = 10';
    }
    $instUserInfo = mysqli_query($conn, $instUserQuery);
    if($instUserInfo){
        $userID = mysqli_insert_id($conn);

        if(isset($_POST["companyName"]) && !empty($_POST["companyName"])){

            $instCompanyInfoHistoryQuery = 'INSERT INTO CompanyInfoHistory set
                CompanyInfoHistory.CompanyID = 0,
                CompanyInfoHistory.UserID = '.$userID.',
                CompanyInfoHistory.State = 0,
                CompanyInfoHistory.CreatedDate = Now()';

            $instCompanyInfoHistoryQuery = $instCompanyInfoHistoryQuery.', CompanyInfoHistory.Name = "'.$_POST["companyName"].'"';
            if(isset($_POST["businessNum1"]) && !empty($_POST["businessNum1"])){
                $businessNum = $_POST["businessNum1"].$_POST["businessNum2"].$_POST["businessNum3"];
                $instCompanyInfoHistoryQuery = $instCompanyInfoHistoryQuery.', CompanyInfoHistory.BusinessNumber = "'.$businessNum.'"';
            }

            $instCompanyInfoHistory = mysqli_query($conn, $instCompanyInfoHistoryQuery);
            if($instCompanyInfoHistory){
            }
            else{
                echo "INSERT_CompanyInfoHistory_FAIL";
                echo "\n".$instCompanyInfoHistoryQuery;
                exit();
            }

        }
        echo "REGIST_SUCCESS";

    }
    else{
        echo "INSERT_USER_FAIL";
        echo "\n".$instUserQuery;
        exit();
    }
}
else{
    echo "이미 가입된 E-MAIL입니다.";
}


?>

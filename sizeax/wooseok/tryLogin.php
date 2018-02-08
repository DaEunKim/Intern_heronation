<?
    include $_SERVER["DOCUMENT_ROOT"]."/database/databaseConnection.php";

	$loginId=$_POST['UserId'];
	$login_pwd=$_POST['UserPassword'];

	if(empty($loginId) || empty($login_pwd)){
		// 둘중 하나라도 비어있으면 입력 오류 처리함.
		echo "id_pw_empty";
	}else{
		// 데이터가 다 들어와있으면 디비 활성화.
		// 리소스를 조금이라도 줄이고자.. (의미 없을지도)
		$classDB = new ClassDB();
		$conn=$classDB->getConn();
		if($conn){
			// 아이디/비번을 동시에 만족하며, 사용자 레벨이 20 이상인 사람을 찾는다.
			// 사용자 레벨이 적을수록 관리자에 가까워진다. 모르면 반드시 질문할것.
			$result = mysqli_query($conn,"select *  from User where binary(ID)='$loginId' and PWD='$login_pwd'");
			$user_data = mysqli_fetch_array($result);


			// 한명만 존재해야 로그인 성공이다.
			// 만약 여러개가 존재한다면, 에러처리하는걸로.
			if(mysqli_num_rows($result)==1){
				echo 'success';
			}else{
				// 로그인 실패 이벤트.
				echo "fail";
			}
			mysqli_close($conn);
		}else{
			echo "server_connect_fail";
		}
	}
?>

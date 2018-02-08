<?
	/*
		상품에 접근 한 유저의 정보를 db에 저장하는 코드
		sizeax에서 ajax를 통해 접근
	*/
	include_once("./header.php");

	if(isset($_POST['UserPKey'])&&isset($_POST['TrackingPKey'])){
		$UserPKey = $_POST['UserPKey'];
		$TrackingPKey = $_POST['TrackingPKey'];
  }else{
    echo "POST_ERROR";
  }
    $conn = $classDB->getConn();
		if(!$conn) die("커넥션 실패");

		//Product PKey를 가져오기 위한 쿼리
		$update_query = "UPDATE CollectedData SET UserID = $UserPKey where PKey = $TrackingPKey";
			if($result = mysqli_query($conn, $update_query)){
				echo "update_succeed";

			}else{

				echo "insert error";

			}


	mysqli_close($conn);
?>

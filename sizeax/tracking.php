<?
	/*
		상품에 접근 한 유저의 정보를 db에 저장하는 코드
		sizeax에서 ajax를 통해 접근
	*/
	include_once("./header.php");

	if(isset($_POST['UserPKey'])&&isset($_POST['CompanyID'])&&isset($_POST['ProductKey'])
		&&isset($_POST['URL'])&&isset($_POST['PageName'])&&isset($_POST['Local'])&&isset($_POST['Browser'])){

		$UserPKey = $_POST['UserPKey'];
		$CompanyID = $_POST['CompanyID'];
		$ProductKey = $_POST['ProductKey'];
	     $IP = $_SERVER['REMOTE_ADDR'];
		$URL = $_POST['URL'];
		$PageName = $_POST['PageName'];
		$Local = $_POST['Local'];
		$Browser = $_POST['Browser'];

		$conn = $classDB->getConn();

		if(!$conn) die("커넥션 실패");

		//Product PKey를 가져오기 위한 쿼리
		$product_query = "select PKey from Product where CompanyID=$CompanyID and ProductKey = '$ProductKey'";
		if($product_result = mysqli_query($conn, $product_query)){
			$product_row = mysqli_fetch_array($product_result);
			$ProductPKey = $product_row['PKey'];

			$query = "insert into CollectedData(UserID, CompanyID, ProductID, CountryID, ProductKey, IP, URL, PageName, Local, Browser, ConnectedDate)
								values($UserPKey, $CompanyID, $ProductPKey, 0, '$ProductKey', '$IP', '$URL', '$PageName', '$Local', '$Browser', Now())";
			if($result = mysqli_query($conn, $query)){
				$latest_tracking_PKey=mysqli_insert_id($conn);
				$data = array("message"=>'saved_success', "pkey"=>$latest_tracking_PKey);
				// echo "save succeed";
				echo json_encode($data);
			}else{
				$data=array("message"=>'insert error');
				// echo "insert error";
				echo json_encode($data);
			}
		}else{
			echo "product query error";
		}
	}else{
		echo "error page";
	}
	mysqli_close($conn);
?>

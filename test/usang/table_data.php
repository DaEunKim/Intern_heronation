<?php
$productNum = $_POST['number'];

function dbConnect(){
  $dbid = "root";
  $dbpw = "0505";
  $dbname = "heronation";
  $dbhost = "localhost";

  $conn = mysqli_connect($dbhost, $dbid, $dbpw, $dbname);
  mysqli_set_charset($conn,'utf8');

  return $conn;
}

$connect = dbConnect();

$query = "SELECT Product.Name as ProductName, ProductSizeList.Name as SizeName, SizeType.NameKR as SizeType, ProductSize.Size as Size
FROM Product
INNER JOIN ProductSizeList ON Product.Pkey= ProductSizeList.ProductID
INNER JOIN ProductSize ON ProductSizeList.PKey = ProductSize.ProductSizeListID
INNER JOIN SizeType ON ProductSize.SizeTypeID = SizeType.PKey
WHERE Product.Pkey = $productNum ";

if($query == true){
  $result = mysqli_query($connect, $query);
  $arr = array();
  $i=0;
  while($row = mysqli_fetch_array($result)){
    $arr[$i] = array(
      'productName' => $row[0],
      'sizeName' => $row[1],
      'sizeType' => $row[2],
      'size' => $row[3]
    );
    $i++;
  }
  echo json_encode($arr, JSON_UNESCAPED_UNICODE);
}


?>

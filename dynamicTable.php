<?php
$servername = "localhost";
$username = "root";
$password = "0505";
$dbname = "heronation";
$tablename = "Product";

// Create connection
$conn =mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($conn,'utf8');
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// print_r($_GET);
$db_Product_PKey = (int)($_GET['Product_PKey']);


// $sql = "select Product.Name from heronation.Product Where Product.PKey = " .$db_Product_PKey. ";";
$Product_Name = "Product.Name";
$Category_Name = "Category.NameKR";
$ProductSizeList_Name = "ProductSizeList.Name";
$SizeType_Name = "SizeType.NameKR";
$ProductSize_Size = "ProductSize.Size";

$sql = "select " .$Product_Name.", ".$Category_Name.", ".$ProductSizeList_Name. "," .$SizeType_Name. ", " .$ProductSize_Size. "
from heronation.Product
inner join heronation.Category on Product.CategoryID = Category.PKey
inner join heronation.ProductSizeList on ProductSizeList.ProductID = Product.PKey
inner join heronation.ProductSize on ProductSize.ProductSizeListID = ProductSizeList.PKey
inner join heronation.SizeType on ProductSize.SizeTypeID = SizeType.PKey
Where Product.PKey = ". $db_Product_PKey. ";";

if(mysqli_query($conn, $sql)){

  $result = mysqli_query($conn, $sql);
  $jsonArr = array();

  $i = 0;
  while($rs= mysqli_fetch_array($result)){
      $jsonArr[$i] = array(
        'Product' => $rs[0],
        'Category' => $rs[1],
        'ProductSizeList' => $rs[2],
        'SizeType' => $rs[3],
        'Size' => $rs[4]
      );
      $i++;
    }
    echo json_encode($jsonArr, JSON_UNESCAPED_UNICODE);
  }
    //
    // print_r("Name : " .$rs['Name'] ."<br>");
    // print_r("NameKR : " .$rs['NameKR'] ."<br>");
    // print_r("Size : " .$rs['Size'] ."<br>");

    // print_r("2 : " .$rs[2]."<br>");
    // print_r("3 : " .$rs[3]. "<br>");
    // print_r("4 : " .$rs[4]. "<br>");

$conn->close();

?>

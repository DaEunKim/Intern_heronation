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
  while($rs = mysqli_fetch_array($result)) {
    // echo $rs;
    print_r($rs);
  }
}

$conn->close();

?>

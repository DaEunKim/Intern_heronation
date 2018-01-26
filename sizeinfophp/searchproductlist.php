<?
include $_SERVER["DOCUMENT_ROOT"]."/sizeinfo/sizeinfophp/connectdb.php";

if(isset($_COOKIE["userPKeyZeyo"])){
    $userID = $_COOKIE["userPKeyZeyo"];
}
/*========== 리스트 개수 때문에 전체 상품 보여줌 page 처리 끝나고 주석해제 ================*/
// $productListQuery = 'SELECT Product.PKey as ProductPKey, Product.CreatedDate as ProductCreatedDate,
// Category.NameKR as CategoryName, Product.Name as ProductName
// from Product
// left join User on User.PKey = Product.UserID
// left join Category on Product.CategoryID = Category.PKey
// where Product.UserID = '.$userID.' ';

// $productListQuery = 'SELECT Product.PKey as ProductPKey, Product.CreatedDate as ProductCreatedDate,
// Category.NameKR as CategoryName, Product.Name as ProductName
// from Product
// left join Category on Product.CategoryID = Category.PKey';

// $strAndFlag = 0;


if(isset($_GET['productName']) || isset($_GET['categoryPKey'])){
    if(isset($_GET['productName'])) {
        $productName = $_GET['productName'];
        $productListQuery = $productListQuery.' and Product.Name LIKE "%'.$productName.'%"';
        // $strAndFlag = 1;
        // {echo $productListQuery; exit();}
    }
    if(isset($_GET['categoryPKey'])){
        $categoryPKey = $_GET['categoryPKey'];
        // if($strAndFlag){
        $productListQuery = $productListQuery.' and Product.CategoryID = '.$categoryPKey.'';
        // }
        // else{
        //     $productListQuery = $productListQuery.'Product.CategoryID = '.$categoryPKey.'';
        //     $strAndFlag = 1;
        // }
    }
    $productListQuery = $productListQuery." order by Product.PKey desc";
}
// else if(isset($_GET['productID'])){
//     $productID = $_GET['productID'];
// }
else{
    /*========== 리스트 개수 때문에 전체 상품 보여줌 page 처리 끝나고 주석해제 ================*/
    // $productListQuery =
    // 'SELECT Product.PKey as ProductPKey, Product.CreatedDate as ProductCreatedDate,
    // Category.NameKR as CategoryName, Product.Name as ProductName
    // from Product
    // left join User on User.PKey = Product.UserID
    // left join Category on Product.CategoryID = Category.PKey
    // where Product.UserID = '.$userID.'
    // order by Product.PKey desc;';

  $sortMethod;

  $i = (int)($_POST['pageNo'])*10;
  $productListQuery =
  "SELECT Product.CreatedDate as ProductCreatedDate,
  Category.NameKR as CategoryName, Product.PKey as ProductPKey, Product.Name as ProductName
  from Product
  left join Category on Product.CategoryID = Category.PKey";
  // order by Product.PKey desc limit $i, 10";
  // if(isset($productListQuery) ) {
  //   $productListQuery .=" order by Product.Pkey limit $i, 10";
  // }
  if(isset($_POST['regisDate'])){
    if(($_POST['regisDate'])==0){
      $productListQuery .= " order by Product.CreatedDate";
    }
    else{
      $productListQuery .= " order by Product.CreatedDate desc";
    }
  }
  $productListQuery .= " limit $i, 10";


  //   if(isset($_POST['regisDate']) ){
  //
  //
  //   }
    // if(isset($_POST['categoryPKey'])){
    //   $productListQuery .=" where Product.CategoryID = $_POST['categoryPKey']";
    // }
    // if(isset($_POST['regisDate']) ){
    //   $productListQuery .=" order by date";
    // }

  // }

}
// echo $productListQuery;
if($productTable = mysqli_query($conn, $productListQuery)){

  $jsonArr = array();
  $i = 0;
  while($productRow = mysqli_fetch_array($productTable)){
    $jsonArr[$i] = array(
      'date' => $productRow[0],
      'CategoryName' => $productRow[1],
      'ProductPKey' => $productRow[2],
      'ProductName' => $productRow[3]
    );
    $i++;
  }
  echo json_encode($jsonArr, JSON_UNESCAPED_UNICODE);
}


?>

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


  $showDataNum = 10;
  $i = (int)($_POST['pageNo']) * $showDataNum;

  $productListQuery =
  "SELECT Product.CreatedDate as ProductCreatedDate,
  Category.NameKR as CategoryName, Product.PKey as ProductPKey, Product.Name as ProductName
  from Product
  left join Category on Product.CategoryID = Category.PKey";

  if(isset($_POST['categoryPKey']) && $_POST['categoryPKey']!="sortingMethod" && $_POST['categoryPKey']!="totalCategory"){
    $categoryID = $_POST['categoryPKey'];
    $productListQuery .= " where Product.CategoryID = $categoryID";
  }

  if(isset($_POST['regisDate'])){
    if($_POST['regisDate']==0){
      $productListQuery .= " order by Product.CreatedDate desc";
    }
    else{
      $productListQuery .= " order by Product.CreatedDate";
    }
  }

  $rowcount=0;
  if($result = mysqli_query($conn, $productListQuery)){
    $rowcount = mysqli_num_rows($result);
  }

  $productListQuery .= " limit $i, $showDataNum";
}

if($productTable = mysqli_query($conn, $productListQuery)){
  $jsonArr = array();
  $i = 0;
  while($productRow = mysqli_fetch_array($productTable)){
    $jsonArr[$i] = array(
      'date' => $productRow[0],
      'CategoryName' => $productRow[1],
      'ProductPKey' => $productRow[2],
      'ProductName' => $productRow[3],
      'rowcount' => $rowcount
    );
    $i++;
  }
  echo json_encode($jsonArr, JSON_UNESCAPED_UNICODE);
}


?>

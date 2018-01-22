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
$productListQuery = 'SELECT Product.PKey as ProductPKey, Product.CreatedDate as ProductCreatedDate,
Category.NameKR as CategoryName, Product.Name as ProductName
from Product
left join Category on Product.CategoryID = Category.PKey';
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

    $i = (int)($_POST['pageNo'])*10;

    $productListQuery =
    'SELECT Product.CreatedDate as ProductCreatedDate,
    Category.NameKR as CategoryName, Product.PKey as ProductPKey, Product.Name as ProductName
    from Product
    left join Category on Product.CategoryID = Category.PKey
    order by Product.PKey desc limit '.$i.', 10;';
    // where Product.CreatedDate > "2017-09-26"
}

// echo $productListQuery;
$productTable = mysqli_query($conn, $productListQuery);
while($productRow = mysqli_fetch_array($productTable)){
    echo
    '<tr>
        <td><input type="checkbox" name="sizeInfoList_chk"></td>

        <td>'.date("Y.m.d", strtotime($productRow["ProductCreatedDate"])).'</td>
        <td>'.$productRow["CategoryName"].'</td>
        <td class="searchlist_productPKey">'.$productRow["ProductPKey"].'</td>
        <td class="updateProductInfo">'.$productRow["ProductName"].'</td>
        <td style="width:180px" id="download_button">
            <button type="submit" id="downloadImg">이미지파일</button>
            <button type="submit" id="downloadHTML">HTML소스</button>
        </td>
    </tr>';
}
?>

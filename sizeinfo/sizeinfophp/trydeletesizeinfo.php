<?
include "connectdb.php";

$numOfDelProduct = count($_POST["delProductID"]);
for($delIter=0; $delIter<$numOfDelProduct; $delIter++){

    // 남길정보 조인 지워야함
    $delProductSize = mysqli_query($conn,
        'DELETE Product, ProductSizeList, ProductSize, ProductSpecViewList, ProductInfo, MaterialInfo, ProductThumbnail, WashingMethod, ProductSpecInfo, ProductAdditionalInfo, ProductAdditionalItem
        from Product
        left join ProductSizeList on ProductSizeList.ProductID = Product.Pkey
        left join ProductSize on ProductSize.ProductSizeListID = ProductSizeList.PKey
        left join ProductSpecViewList on ProductSpecViewList.ProductID = Product.PKey
        left join ProductInfo on ProductInfo.ProductSpecID = ProductSpecViewList.PKey
        left join MaterialInfo on MaterialInfo.ProductInfoID = ProductInfo.PKey
        left join ProductThumbnail on ProductThumbnail.ProductSpecID = ProductSpecViewList.PKey
        left join WashingMethod on WashingMethod.ProductSpecID = ProductSpecViewList.PKey
        left join ProductSpecInfo on ProductSpecInfo.ProductID = Product.PKey
        left join ProductAdditionalInfo on ProductAdditionalInfo.ProductID = Product.PKey
        left join ProductAdditionalItem on ProductAdditionalItem.ProductAdditionalInfoID = ProductAdditionalInfo.PKey
        where Product.PKey='.$_POST["delProductID"][$delIter].'
        ;' );
    if($delProductSize){
    }
    else {echo 'Product_DELETE_FAIL'; exit();}
}

echo 'Product_DELETE_SUCCESS';
?>

<?
include 'connectdb.php';

$postData = $_POST['postData'];
$productID = $postData["currentProductPKey"];

$userID = 0;
if(isset($_COOKIE["userPKeyZeyo"]) && isset($_COOKIE["loginSuccessZeyo"])){
    $userID = $_COOKIE["userPKeyZeyo"];
}


// for($i=0; $i < count($sizeTypeID); $i++){
//     echo $i.":".$sizeTypeID[$i]."\n";
//     echo $i.":".$sizeTypeListPriority[$i]."\n";
//
// }
// exit();


// echo $postData["isNecessarySize"][0];
// exit();

// ProductSpecTableName의 항목
$ProductSpecTable = mysqli_query($conn, 'SELECT * from ProductSpecTable;' );
$ProductSpecTableName = mysqli_fetch_array($ProductSpecTable);

// 선택된 카테고리 admin 설정 불러오기
$customSpecInfoTable = mysqli_query($conn, 'SELECT * from CustomizedSpecInfo where CategoryID = '.$postData["categoryID"].'' );
$customSpecInfoRow = mysqli_fetch_array($customSpecInfoTable);

// CustomizedSpecInfo IsHide와 IsDelete에 따라 각 항목의 IsDelete 판별
$newIsInfoDelete = sprintf("%06b", $customSpecInfoRow["IsInfoDelete"]) | sprintf("%06b", $customSpecInfoRow["IsInfoHide"]);
$newIsThumbnailDelete = sprintf("%06b", $customSpecInfoRow["IsThumbnailDelete"]) | sprintf("%06b", $customSpecInfoRow["IsThumbnailHide"]);
$newIsWashingMethodDelete = sprintf("%06b", $customSpecInfoRow["IsWashingMethodDelete"]) | sprintf("%06b", $customSpecInfoRow["IsWashingMethodHide"]);


// productPKey가 빈값이 아니면 (카테고리 변경없이 update일 경우)
if($productID && $postData["isCategoryReload"]==0){

    $categoryTable = mysqli_query($conn, 'SELECT CategoryID from Product where Product.PKey = '.$productID.';' );
    $categoryRow = mysqli_fetch_array($categoryTable);
    $pastCategoryID = $categoryRow["CategoryID"];

    // Theme Color update
    $themeColorTable = mysqli_query($conn, 'SELECT * from ProductSpecInfo where ProductID = '.$productID.';' );
    $themeColorRow = mysqli_num_rows($themeColorTable);

    // 테마컬러가 존재할 경우
    if($themeColorRow == 1){
        $updtProductSpecInfo = mysqli_query($conn,
            'UPDATE ProductSpecInfo set
            BorderColor = "'.$postData['themeColor'].'"
            where ProductID = '.$productID.'
            ;' );
        if($updtProductSpecInfo){
        }
        else { echo 'ProductSpecInfo_UPDATE_FAIL'; exit(); }
    }
    // 테마컬러가 없을 경우
    else{
        $instProductSpecInfo = mysqli_query($conn,
            'INSERT into ProductSpecInfo set
            ProductID = '.$productID.',
            BorderColor = "'.$postData['themeColor'].'"
            ;' );
        if($instProductSpecInfo){
        }
        else { echo 'ProductSpecInfo_INSERT_FAIL'; exit(); }
    }

    // 미리보기 order, isHide 변경
    $productSpectViewTable = mysqli_query($conn, 'SELECT PKey from ProductSpecViewList
        where ProductID= '.$productID.' order by PKey asc;' );
    $numOfproductSpectView = mysqli_num_rows($productSpectViewTable);
    $productSpectViewRow = mysqli_fetch_array($productSpectViewTable);
    $productSpectViewPkey = $productSpectViewRow["PKey"];

    for ($viewListIter = 0; $viewListIter < $numOfproductSpectView; $viewListIter++){
        $ProductSpecViewListIsHide = ($postData["isShow"][$viewListIter] == 1 || $postData["isShow"][$viewListIter] == '1') ? 0 : 1 ;

        $updtPSViewList = mysqli_query($conn,
            'UPDATE ProductSpecViewList set
                Num = '.$postData["order"][$viewListIter].',
                IsHide = '.$ProductSpecViewListIsHide.'
                where PKey = '.$productSpectViewPkey.' ;' );
        if($updtPSViewList){
            $productSpectViewPkey++;
        }
        else {
            echo 'ProductSpecViewList_UPDATE_FAIL0'; exit();
        }
    }

    // 이름이나 상단 카테고리 변경시
    if($postData["productNameChanged"]){
        $updtProduct = mysqli_query($conn,
                'UPDATE Product set
                CategoryID = '.$postData["categoryID"].',
                Name = "'.$postData["productName"].'",
                UpdatedDate = Now()
                where PKey = '.$productID.'
                ;' );
        if($updtProduct){

        }
        else { echo 'Product_UPDATE_FAIL'; exit(); }
    }


    // 소재 정보 변경 && checked
    if($postData["materialChanged"] && $postData["isShow"][0]){

        // updt ProductInfo
        $updtPSViewListQuery = 'UPDATE ProductSpecViewList
        left join ProductSpecTable on ProductSpecTable.PKey = ProductSpecViewList.ProductSpecTableID
        left join ProductInfo on ProductInfo.ProductSpecID = ProductSpecViewList.PKey
        set
        ProductSpecViewList.UpdatedDate = Now()';

        // 값이 존재하면 query에 append
        if(isset($postData["warrantyPKey"])) { $updtPSViewListQuery = $updtPSViewListQuery.', ProductInfo.WarrantyID = "'.$postData["warrantyPKey"].'"';}
        if(isset($postData["warrantyInput"])) { $updtPSViewListQuery = $updtPSViewListQuery.', ProductInfo.Warranty = "'.$postData["warrantyInput"].'"';}
        if(isset($postData["productCountryPKey"])) { $updtPSViewListQuery = $updtPSViewListQuery.', ProductInfo.CountryID = "'.$postData["productCountryPKey"].'"';}
        if(isset($postData["productCountryInput"])) { $updtPSViewListQuery = $updtPSViewListQuery.', ProductInfo.Country = "'.$postData["productCountryInput"].'"';}
        if(isset($postData["productColor"])) { $updtPSViewListQuery = $updtPSViewListQuery.', ProductInfo.Color = "'.$postData["productColor"].'"';}
        if(isset($postData["productManufacturer"])) { $updtPSViewListQuery = $updtPSViewListQuery.', ProductInfo.Manufacturer="'.$postData["productManufacturer"].'"';}
        if(isset($postData["productionDate"])) { $updtPSViewListQuery = $updtPSViewListQuery.', ProductInfo.ProductionDate = "'.$postData["productionDate"].'"';}
        $updtPSViewListQuery = $updtPSViewListQuery.'where ProductSpecViewList.ProductID = '.$productID.' and ProductSpecTable.PKey=1;';

        $updtPSViewList = mysqli_query($conn, $updtPSViewListQuery);
        if($updtPSViewList){
            updtMaterialInfoFunc($postData, $conn, $productID);
            updtAdditionalInfoFunc($postData, $conn, $productID, 1);
        }
        else {echo 'ProductInfo_UPDATE_FAIL1'; exit();}
    }   // 소재 정보 변경 END

    // 상품 이미지 정보 변경 && checked
    if($postData["imageChanged"] && $postData["isShow"][1]){
        updtThumbnailFunc($postData, $conn, $productID);
    }

    // 제품 사이즈 정보 변경 && checked
    if($postData["sizeChanged"] && $postData["isShow"][2]){
        $updateInfoTable = mysqli_query($conn,
            'SELECT Product.CategoryID as ProductCategoryID, ProductSizeList.Pkey as ProductSizeListPkey
            from ProductSpecViewList
            left join ProductSpecTable on ProductSpecTable.PKey = ProductSpecViewList.ProductSpecTableID
            left join Product on Product.PKey = ProductSpecViewList.ProductID
            left join ProductSizeList on ProductSizeList.ProductID = Product.PKey
            where ProductSpecViewList.ProductID='.$productID.' and ProductSpecTable.PKey=3 ;' );


        $pastNumOfSizeList = mysqli_num_rows($updateInfoTable);
        $updateInfoRow = mysqli_fetch_array($updateInfoTable);

        $updtPSViewList = mysqli_query($conn,
            'UPDATE ProductSpecViewList
            left join ProductSpecTable on ProductSpecTable.PKey = ProductSpecViewList.ProductSpecTableID
            set
            ProductSpecViewList.UpdatedDate = Now()
            where ProductID = '.$productID.' and ProductSpecTable.PKey=3
            ;' );


        if($updtPSViewList){

            $numOfSizeList = count($postData["productSizeList"]);
            $numOfSize = count($postData["productSize"]);
            $sizeListIter=0;
            $sizeIndex=0;

            do{
                // 같은 수까지 사이즈 리스트 update
                if($sizeListIter < $pastNumOfSizeList){
                    $updtProductSizeList = mysqli_query($conn,
                        'UPDATE ProductSizeList set
                        Name = "'.$postData["productSizeList"][$sizeListIter].'",
                        UpdatedDate = Now()
                        where ProductSizeList.PKey = '.$updateInfoRow["ProductSizeListPkey"].'
                        ;' );
                    if($updtProductSizeList){
                        $sizeIter=0;
                        $productSizeTable = mysqli_query($conn,
                            'SELECT ProductSize.PKey as ProductSizePKey from ProductSize
                            where ProductSize.ProductSizeListID='.$updateInfoRow["ProductSizeListPkey"].' ;');
                        $productSizeRow = mysqli_fetch_array($productSizeTable);

                        do {
                            // 같은 수까지 사이즈 update
                            if(!empty($postData["productSize"][$sizeIndex])){
                                $updtProductSize = mysqli_query($conn,
                                    'UPDATE ProductSize set
                                    Size = '.$postData["productSize"][$sizeIndex].',
                                    IsNecessary = '.$postData["isNecessary"][$sizeIndex].',
                                    UpdatedDate = Now()
                                    where ProductSize.PKey = '.$productSizeRow["ProductSizePKey"].'
                                    ;' );
                            }
                            else{   // size가 null일 경우
                                $updtProductSize = mysqli_query($conn,
                                    'UPDATE ProductSize set
                                    Size = NULL,
                                    IsNecessary = '.$postData["isNecessary"][$sizeIndex].',
                                    UpdatedDate = Now()
                                    where ProductSize.PKey = '.$productSizeRow["ProductSizePKey"].'
                                    ;' );
                            }

                            if($updtProductSize){
                                $productSizeRow = mysqli_fetch_array($productSizeTable);
                                $sizeIter++;
                                $sizeIndex++;
                            }
                            else {echo 'ProductSize_UPDATE_FAIL'; exit();}

                        } while ($sizeIter < ($numOfSize/$numOfSizeList));

                        $updateInfoRow = mysqli_fetch_array($updateInfoTable);
                    }
                    else {echo 'ProductSizeList_UPDATE_FAIL'; exit();}
                }
                else{   // 사이즈 리스트 증가분 insert
                    $sizeTypeTable = mysqli_query($conn, 'SELECT SizeType.PKey as SizeTypePKey,
                        ProductSize.Priority as ProductSizePriority
                        from ProductSizeList
                        left join ProductSize on ProductSize.ProductSizeListID = ProductSizeList.PKey
                        left join SizeType on SizeType.PKey = ProductSize.SizeTypeID
                        where ProductSizeList.ProductID = "'.$productID.'"
                        group by SizeType.PKey, ProductSize.Priority
                        order by ProductSize.Priority asc;' );
                    while($sizeTypeRow = mysqli_fetch_array($sizeTypeTable)){
                        $sizeTypeID[] = $sizeTypeRow["SizeTypePKey"];
                        $sizeTypeListPriority[] = $sizeTypeRow["ProductSizePriority"];
                    }

                    $instProductSizeList = mysqli_query($conn,
                        'INSERT into ProductSizeList set
                        ProductID = '.$productID.',
                        Name = "'.$postData["productSizeList"][$sizeListIter].'",
                        CreatedDate = Now()
                        ;' );
                    if($instProductSizeList){
                        $productSizeListID = mysqli_insert_id($conn);
                        $sizeIter=0;

                        do {
                            // 사이즈 insert
                            if(!empty($postData["productSize"][$sizeIndex])){
                                $instProductSizeQuery = 'INSERT into ProductSize set
                                ProductSizeListID = '.$productSizeListID.',
                                SizeTypeID = '.$sizeTypeID[$sizeIter].',
                                Size = '.$postData["productSize"][$sizeIndex].',
                                Priority = '.$sizeTypeListPriority[$sizeIter].',
                                IsNecessary = '.$postData["isNecessary"][$sizeIndex].',
                                CreatedDate = Now();';
                            }
                            else{   // size가 null일 경우
                                $instProductSizeQuery = 'INSERT into ProductSize set
                                ProductSizeListID = '.$productSizeListID.',
                                SizeTypeID = '.$sizeTypeID[$sizeIter].',
                                Size = NULL,
                                Priority = '.$sizeTypeListPriority[$sizeIter].',
                                IsNecessary = '.$postData["isNecessary"][$sizeIndex].',
                                CreatedDate = Now();';
                            }
                            $instProductSize = mysqli_query($conn, $instProductSizeQuery);
                            if($instProductSize){
                                $sizeIter++;
                                $sizeIndex++;
                            }
                            else {echo 'ProductSize_INSERT_FAIL'; echo "\n".$instProductSizeQuery; exit();}

                        } while ($sizeIter < ($numOfSize/$numOfSizeList));

                    }
                    else {echo 'ProductSizeList_INSERT_FAIL'; exit();}
                }
                $sizeListIter++;
            } while($sizeListIter < $numOfSizeList);

            do{ // 남아있는 소재정보 삭제
                if($updateInfoRow["ProductSizeListPkey"])
                {
                    $delProductSize = mysqli_query($conn,
                        'DELETE ProductSizeList, ProductSize from ProductSizeList
                        left join ProductSize on ProductSize.ProductSizeListID = ProductSizeList.PKey
                        where ProductSize.ProductSizeListID='.$updateInfoRow["ProductSizeListPkey"].'
                        ;' );
                    if($delProductSize){
                    }
                    else {echo 'ProductSizeList_DELETE_FAIL'; exit();}
                }
            } while($updateInfoRow = mysqli_fetch_array($updateInfoTable));

        }
        else {echo 'ProductSpecViewList_UPDATE_FAIL3'; exit();}
    }

    // 세탁 및 관리방법 변경 && checked
    if($postData["laundryChanged"]  && $postData["isShow"][3]){

        // isShow를 isHide로 바꿈
        $WashingMethodIsHide = bitChangeFunc($postData["isShowWashingMethod"]);

        $updtWashingMethodQuery =
            'UPDATE ProductSpecViewList
            left join ProductSpecTable on ProductSpecTable.PKey = ProductSpecViewList.ProductSpecTableID
            left join WashingMethod on WashingMethod.ProductSpecID = ProductSpecViewList.PKey
            set
            ProductSpecViewList.UpdatedDate = Now(),
            WashingMethod.IsHide = conv('.$WashingMethodIsHide.', 2, 10)';

        // 값이 존재하면 query에 append
        if(isset($postData["washingMethod"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.WashingMethod=conv('.$postData["washingMethod"].', 2, 10)';}
        if(isset($postData["detergentWaterPKey"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.DetergentWaterID = '.$postData["detergentWaterPKey"].'';}
        if(isset($postData["detergentDryPKey"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.DetergentDryID = '.$postData["detergentDryPKey"].'';}
        if(isset($postData["detergentOxygenPKey"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.DetergentOxygenID = '.$postData["detergentOxygenPKey"].'';}
        if(isset($postData["detergentChlorinePKey"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.DetergentChlorineID = '.$postData["detergentChlorinePKey"].'';}
        if(isset($postData["waterTemperature"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.WaterTemperature = '.$postData["waterTemperature"].'';}
        if(isset($postData["ironMethod"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.Iron = conv('.$postData["ironMethod"].', 2, 10)';}
        if(isset($postData["ironTemperatureMin"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.IronTemperatureMin = '.$postData["ironTemperatureMin"].'';}
        if(isset($postData["ironTemperatureMax"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.IronTemperatureMax = '.$postData["ironTemperatureMax"].'';}
        if(isset($postData["dryCondition"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.DryCondition = conv('.$postData["dryCondition"].', 2, 10)';}
        if(isset($postData["dryMethod"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.DryMethod = '.$postData["dryMethod"].'';}
        if(isset($postData["handDryCondition"])) { $updtWashingMethodQuery = $updtWashingMethodQuery.', WashingMethod.HandDryCondition = '.$postData["handDryCondition"].'';}

        $updtWashingMethodQuery = $updtWashingMethodQuery.' where ProductSpecViewList.ProductID = '.$productID.' and ProductSpecTable.PKey=4
        ;';

        $updtWashingMethod = mysqli_query($conn, $updtWashingMethodQuery);


        if($updtWashingMethod){
        }
        else {echo 'WashingMethod_UPDATE_FAIL4'; echo '\n'.$updtWashingMethodQuery; exit();}
    }

    // 의류치수 측정방법 변경 && checked
    if($postData["guideChanged"] && $postData["isShow"][4]){
        updtGuideFunc($postData, $conn, $productID);
    }

    // AdditionalInfo가 존재 && checked
    if(isset($postData["additionalChanged"]) && $postData["isShow"][5]){
        // AdditionalInfo changed
        if($postData["additionalChanged"]){
            updtAdditionalInfoFunc($postData, $conn, $productID, 6);
        }
    }
}

else{   // 새제품등록 insert
    // 카테고리 변경시 기존 product 삭제후 새로 등록
    if($productID && $postData["isCategoryReload"] == 1){
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
            where Product.PKey='.$productID.'
            ;' );
        if($delProductSize){
        }
        else {echo 'Product_DELETE_FAIL'; exit();}
    }

    $userInfoTable = mysqli_query($conn, 'SELECT CompanyID from User where PKey = '.$userID.'');
    $numOfUserInfo = mysqli_num_rows($userInfoTable);

    $instProductQuery = 'INSERT into Product set
        UserID = '.$userID.',
        CategoryID = '.$postData["categoryID"].',
        Name = "'.$postData["productName"].'",
        CreatedDate = Now()';

    if($numOfUserInfo){
        $userInfoRow = mysqli_fetch_array($userInfoTable);
        if(isset($userInfoRow["CompanyID"]) && !empty($userInfoRow["CompanyID"])){
            $instProductQuery = $instProductQuery.', CompanyID='.$userInfoRow["CompanyID"].' ';
        }
    }

    $instProduct = mysqli_query($conn, $instProductQuery);
    if($instProduct){
        $productID = mysqli_insert_id($conn);
    }
    else { echo 'Product_INSERT_FAIL'; echo "\n".$instProductQuery; exit(); }

    $instProductSpecInfo = mysqli_query($conn,
        'INSERT into ProductSpecInfo set
        ProductID = '.$productID.',
        BorderColor = "'.$postData['themeColor'].'"
        ;' );
    if($instProductSpecInfo){
    }
    else { echo 'ProductSpecInfo_INSERT_FAIL'; exit(); }

    // 새 제품 등록 카테고리 설정 불러오기
    $customSpecInfoTable = mysqli_query($conn, 'SELECT * from CustomizedSpecInfo where CategoryID = '.$postData["categoryID"].'' );
    $customSpecInfoRow = mysqli_fetch_array($customSpecInfoTable);

    // 새 제품 등록 카테고리 뷰 리스트 설정 불러오기
    $viewListTable = mysqli_query($conn,
        'SELECT *, CategorySpecViewList.IsHide as CategorySpecViewListIsHide,
        CategorySpecViewList.IsDelete as CategorySpecViewListIsDelete
        from SpecViewList
        left join CategorySpecViewList on CategorySpecViewList.SpecViewListID = SpecViewList.PKey
        where CategorySpecViewList.CategoryID = '.$postData["categoryID"].'
        order by SpecViewList.PKey asc;');
    $numOfViewList = mysqli_num_rows($viewListTable);


    // 카테고리의 뷰 리스트 수만큼 ProductSpecViewList생성
    for ($viewListIter = 0; $viewListIter < $numOfViewList; $viewListIter++){
        $viewListRow = mysqli_fetch_array($viewListTable);

        // CategorySpecViewList의 isdelete ishide 둘중 하나라도 1이면
        // ProductSpecViewList의 isdelete = 1
        $productSpecViewListIsDelete = 0;
        if($viewListRow["CategorySpecViewListIsHide"] == 1 || $viewListRow["CategorySpecViewListIsDelete"] == 1) $productSpecViewListIsDelete = 1;

        // 6번째 ProductSpecViewList 생성시(기타) ProductSpecTableID = 0
        $productSpecTableID = $viewListIter+1;
        if($viewListIter == 5) $productSpecTableID = 0;

        $specViewListID = $viewListIter+1;

        $ProductSpecViewListIsHide = ($postData["isShow"][$viewListIter] == 1 || $postData["isShow"][$viewListIter] == '1') ? 0 : 1 ;

        $instPSViewListQuery =
            'INSERT into ProductSpecViewList set
            SpecViewListID = '.$specViewListID.',
            ProductID = '.$productID.',
            ProductSpecTableID = '.$productSpecTableID.',
            Name = "'.$viewListRow["Name"].'",
            Description = "'.$viewListRow["Description"].'",
            X = "'.$viewListRow["X"].'",
            Y = "'.$viewListRow["Y"].'",
            Width = "'.$viewListRow["Width"].'",
            Height = "'.$viewListRow["Height"].'",
            Num = '.$postData['order'][$viewListIter].',
            IsHide = '.$ProductSpecViewListIsHide.',
            IsDelete = '.$productSpecViewListIsDelete.',
            UpdatedDate = Now()';

        // inst ProductSpecViewList
        $instPSViewList = mysqli_query($conn, $instPSViewListQuery);

        if($instPSViewList){
            $productSpecID = mysqli_insert_id($conn);
        }
        // else {echo 'ProductSpecViewList_INSERT_FAIL'; exit();}
        else {echo $instPSViewListQuery; exit();}


        switch ($viewListIter) {
            case 0: // ProductInfo
            {
                instAdditionalInfoFunc($postData, $conn, $productID, 1);

                $instProductInfoQuery = 'INSERT into ProductInfo set
                ProductSpecID = '.$productSpecID.',
                MaterialName = "'.$customSpecInfoRow["MaterialName"].'",
                MaterialDesc = "'.$customSpecInfoRow["MaterialDesc"].'",
                ColorName = "'.$customSpecInfoRow["ColorName"].'",
                ColorDesc = "'.$customSpecInfoRow["ColorDesc"].'",
                CountryName = "'.$customSpecInfoRow["CountryName"].'",
                CountryDesc = "'.$customSpecInfoRow["CountryDesc"].'",
                ManufacturerName = "'.$customSpecInfoRow["ManufacturerName"].'",
                ManufacturerDesc = "'.$customSpecInfoRow["ManufacturerDesc"].'",
                ProductionDateName = "'.$customSpecInfoRow["ProductionDateName"].'",
                ProductionDateDesc = "'.$customSpecInfoRow["ProductionDateDesc"].'",
                WarrantyName = "'.$customSpecInfoRow["WarrantyName"].'",
                WarrantyDesc = "'.$customSpecInfoRow["WarrantyDesc"].'",
                IsDelete = conv('.$newIsInfoDelete.', 2, 10),
                IsHide = conv(0, 2, 10)';

                // 값이 존재하면 query에 append
                if(isset($postData["warrantyPKey"])) { $instProductInfoQuery = $instProductInfoQuery.', ProductInfo.WarrantyID = "'.$postData["warrantyPKey"].'"';}
                if(isset($postData["warrantyInput"])) { $instProductInfoQuery = $instProductInfoQuery.', ProductInfo.Warranty = "'.$postData["warrantyInput"].'"';}
                if(isset($postData["productCountryPKey"])) { $instProductInfoQuery = $instProductInfoQuery.', ProductInfo.CountryID = "'.$postData["productCountryPKey"].'"';}
                if(isset($postData["productCountryInput"])) { $instProductInfoQuery = $instProductInfoQuery.', ProductInfo.Country = "'.$postData["productCountryInput"].'"';}
                if(isset($postData["productColor"])) { $instProductInfoQuery = $instProductInfoQuery.', ProductInfo.Color = "'.$postData["productColor"].'"';}
                if(isset($postData["productManufacturer"])) { $instProductInfoQuery = $instProductInfoQuery.', ProductInfo.Manufacturer="'.$postData["productManufacturer"].'"';}
                if(isset($postData["productionDate"])) { $instProductInfoQuery = $instProductInfoQuery.', ProductInfo.ProductionDate = "'.$postData["productionDate"].'"';}

                // inst ProductInfo
                $instProductInfo = mysqli_query($conn, $instProductInfoQuery);

                if($instProductInfo){
                    $productInfoID = mysqli_insert_id($conn);

                    if(isset($postData["materialName"])){
                        $numOfMaterials = count($postData["materialName"]);
                        for($materialIter=0; $materialIter<$numOfMaterials ; $materialIter++){
                            if($postData["isShow"][$viewListIter]){   // ProductInfo checked
                                $instMaterialInfoQuery = 'INSERT into MaterialInfo set
                                ProductInfoID = '.$productInfoID.',
                                Name = "'.$postData["materialName"][$materialIter].'",
                                Ratio = '.$postData["materialRatio"][$materialIter].'
                                ;';
                            }
                            else{   // ProductInfo unchecked
                                $instMaterialInfoQuery = 'INSERT into MaterialInfo set
                                ProductInfoID = '.$productInfoID.'
                                ;';
                            }

                            // inst MaterialInfo
                            $instMaterialInfo = mysqli_query($conn, $instMaterialInfoQuery);
                            if($instMaterialInfo){

                            }
                            else {echo 'MaterialInfo_INSERT_FAIL'; echo "\n".$instMaterialInfoQuery; exit();}
                        }
                    }
                }
                else {echo 'ProductInfo_INSERT_FAIL'; echo "\n".$instProductInfoQuery; exit();}
            }
                break;

            case 1: // ProductThumbnail
            {
                // inst ProductThumbnail
                $instProductThumbnail = mysqli_query($conn,
                    'INSERT into ProductThumbnail set
                    ProductSpecID = '.$productSpecID.',
                    IsDelete = conv('.$newIsThumbnailDelete.', 2, 10),
                    IsHide = conv(0, 2, 10);
                    ;' );
                if($instProductThumbnail){

                }
                else {echo 'ProductThumbnail_INSERT_FAIL';exit();}
            }
                break;

            case 2: // ProductSize
            {
                instProductSizeFunc($postData, $conn, $productID);
            }
                break;

            case 3: // WashingMethod
            {
                $WashingMethodIsHide = bitChangeFunc($postData["isShowWashingMethod"]);

                $instWashingMethodQuery = 'INSERT into WashingMethod set
                    ProductSpecID = '.$productSpecID.',
                    WashingMethodName = "'.$customSpecInfoRow["WashingMethodName"].'",
                    WashingMethodDesc = "'.$customSpecInfoRow["WashingMethodDesc"].'",
                    DryCleaningName = "'.$customSpecInfoRow["DryCleaningName"].'",
                    DryCleaningDesc = "'.$customSpecInfoRow["DryCleaningDesc"].'",
                    IronName = "'.$customSpecInfoRow["IronName"].'",
                    IronDesc = "'.$customSpecInfoRow["IronDesc"].'",
                    DryMethodName = "'.$customSpecInfoRow["DryMethodName"].'",
                    DryMethodDesc = "'.$customSpecInfoRow["DryMethodDesc"].'",
                    BleachName = "'.$customSpecInfoRow["BleachName"].'",
                    BleachDesc = "'.$customSpecInfoRow["BleachDesc"].'",
                    IsHide = conv('.$WashingMethodIsHide.', 2, 10),
                    IsDelete = conv('.$newIsWashingMethodDelete.', 2, 10)';

                // 값이 존재하면 query에 append
                if(isset($postData["washingMethod"])) { $instWashingMethodQuery = $instWashingMethodQuery.', WashingMethod=conv('.$postData["washingMethod"].', 2, 10)';}
                if(isset($postData["detergentWaterPKey"])) { $instWashingMethodQuery = $instWashingMethodQuery.', DetergentWaterID = '.$postData["detergentWaterPKey"].'';}
                if(isset($postData["detergentDryPKey"])) { $instWashingMethodQuery = $instWashingMethodQuery.', DetergentDryID = '.$postData["detergentDryPKey"].'';}
                if(isset($postData["detergentOxygenPKey"])) { $instWashingMethodQuery = $instWashingMethodQuery.', DetergentOxygenID = '.$postData["detergentOxygenPKey"].'';}
                if(isset($postData["detergentChlorinePKey"])) { $instWashingMethodQuery = $instWashingMethodQuery.', DetergentChlorineID = '.$postData["detergentChlorinePKey"].'';}
                if(isset($postData["waterTemperature"])) { $instWashingMethodQuery = $instWashingMethodQuery.', WaterTemperature = '.$postData["waterTemperature"].'';}
                if(isset($postData["ironMethod"])) { $instWashingMethodQuery = $instWashingMethodQuery.', Iron = conv('.$postData["ironMethod"].', 2, 10)';}
                if(isset($postData["ironTemperatureMin"])) { $instWashingMethodQuery = $instWashingMethodQuery.', IronTemperatureMin = '.$postData["ironTemperatureMin"].'';}
                if(isset($postData["ironTemperatureMax"])) { $instWashingMethodQuery = $instWashingMethodQuery.', IronTemperatureMax = '.$postData["ironTemperatureMax"].'';}
                if(isset($postData["dryCondition"])) { $instWashingMethodQuery = $instWashingMethodQuery.', DryCondition = conv('.$postData["dryCondition"].', 2, 10)';}
                if(isset($postData["dryMethod"])) { $instWashingMethodQuery = $instWashingMethodQuery.', DryMethod = '.$postData["dryMethod"].'';}
                if(isset($postData["handDryCondition"])) { $instWashingMethodQuery = $instWashingMethodQuery.', HandDryCondition = '.$postData["handDryCondition"].'';}

                // inst WashingMethod
                $instWashingMethod = mysqli_query($conn, $instWashingMethodQuery);
                if($instWashingMethod){

                }
                else {echo 'WashingMethod_INSERT_FAIL'; echo "\n".$instWashingMethodQuery; exit();}
            }
                break;

            case 4: // MeansureGuide
            {

            }
                break;

            case 5: // 기타
            {
                if(isset($postData["additionalChanged"])){
                    instAdditionalInfoFunc($postData, $conn, $productID, 6);
                }
            }
                break;
        }
    }
}
echo $productID;


function instAdditionalInfoFunc($postData, $conn, $productID, $additionalSpecViewListID){

    $additionalETCTable = mysqli_query($conn,
        'SELECT
        AdditionalCustomizedSpecInfo.PKey as AdditionalID,
        AdditionalCustomizedSpecInfo.Name as AdditionalCustomizedSpecInfoName,
        AdditionalCustomizedSpecInfo.Description as AdditionalCustomizedSpecInfoDescription,
        AdditionalCustomizedSpecInfo.Num as AdditionalNum,
        AdditionalCustomizedSpecInfo.InputTypeID as AdditionalInputTypeID,
        AdditionalCustomizedSpecInfo.IsFlexible as AdditionalIsFlexible,
        CategoryAdditionalOption.IsDelete as CategoryAdditionalOptionIsDelete,
        CategoryAdditionalOption.IsHide as CategoryAdditionalOptionIsHide,
        CustomizedSpecItem.PKey as ItemID,
        CustomizedSpecItem.IsInputType as ItemIsInputType,
        CustomizedSpecItem.Name as ItemName,
        CustomizedSpecItem.Description as ItemDescription,
        CustomizedSpecItem.ValueInt as ItemValueInt,
        CustomizedSpecItem.ValueStr as ItemValueStr,
        CustomizedSpecItem.Num as ItemNum
        from Category
        left join CategoryAdditionalOption on CategoryAdditionalOption.CategoryID = Category.PKey
        inner join AdditionalCustomizedSpecInfo
        on (AdditionalCustomizedSpecInfo.PKey = CategoryAdditionalOption.AdditionalCustomizedSpecInfoID
          and AdditionalCustomizedSpecInfo.SpecViewListID = '.$additionalSpecViewListID.')
        left join CustomizedSpecItem on CustomizedSpecItem.AdditionalCustomizedSpecInfoID = AdditionalCustomizedSpecInfo.PKey
        where Category.PKey = '.$postData["categoryID"].' and (CategoryAdditionalOption.IsDelete = 0 and CategoryAdditionalOption.IsHide = 0)
        order by AdditionalCustomizedSpecInfo.PKey asc, CustomizedSpecItem.PKey asc;' );

    $numOfAdditional = mysqli_num_rows($additionalETCTable);

    if($numOfAdditional != 0){
        switch ($additionalSpecViewListID) {
            case 1:{ // material
                $additionalInput = $postData["materialAdditional"];
            }
                break;
            case 6:{ // etc
                $additionalInput = $postData["etcAdditional"];
            }
                break;
        }

        $lastAdditionalID = 0;
        $additionalIter = 0;
        while($additionalRow = mysqli_fetch_array($additionalETCTable)){
            // AdditionalCustomizedSpecInfo.PKey가 다를 경우(다른 항목일 경우)
            if($lastAdditionalID != $additionalRow["AdditionalID"]){
                // IsDelete 판별
                $ProductAdditionalInfoIsDelete = $additionalRow["CategoryAdditionalOptionIsDelete"] ||  $additionalRow["CategoryAdditionalOptionIsHide"];

                $instProductAdditionalInfoQuery =
                'INSERT INTO ProductAdditionalInfo set
                ProductAdditionalInfo.InputTypeID = "'.$additionalRow["AdditionalInputTypeID"].'",
                ProductAdditionalInfo.ProductID = "'.$productID.'",
                ProductAdditionalInfo.SpecViewListID = '.$additionalSpecViewListID.',
                ProductAdditionalInfo.Name = "'.$additionalRow["AdditionalCustomizedSpecInfoName"].'",
                ProductAdditionalInfo.Description = "'.$additionalRow["AdditionalCustomizedSpecInfoDescription"].'",
                ProductAdditionalInfo.IsHide = 0,
                ProductAdditionalInfo.IsDelete = 0';
                if(!empty($additionalRow["AdditionalIsFlexible"])) {
                    $instProductAdditionalInfoQuery = $instProductAdditionalInfoQuery.', ProductAdditionalInfo.IsFlexible = '.$additionalRow["AdditionalIsFlexible"].' ';
                }
                else{
                    $instProductAdditionalInfoQuery = $instProductAdditionalInfoQuery.', ProductAdditionalInfo.IsFlexible = 0';
                }


                if(!empty($additionalRow["AdditionalNum"])) {
                    $instProductAdditionalInfoQuery = $instProductAdditionalInfoQuery.', ProductAdditionalInfo.Num = '.$additionalRow["AdditionalNum"].' ';
                }
                else{
                    $instProductAdditionalInfoQuery = $instProductAdditionalInfoQuery.', ProductAdditionalInfo.Num = 0 ';
                }


                // insert ProductAdditionalInfo
                $instProductAdditionalInfo = mysqli_query($conn, $instProductAdditionalInfoQuery);
                if($instProductAdditionalInfo){
                    $productAdditionalInfoID = mysqli_insert_id($conn);
                }
                else{
                    echo 'ProductAdditionalInfo_INSERT_FAIL';
                    echo "\n".$instProductAdditionalInfoQuery;
                    exit();
                }
            }

            $instProductAdditionalItemQuery =
            'INSERT INTO ProductAdditionalItem set
            ProductAdditionalItem.ProductAdditionalInfoID = "'.$productAdditionalInfoID.'"
            ';

            // 빈 값이면 AdditionalCustomizedSpecInfo의 값 쿼리에 추가
            if(empty($additionalRow["ItemIsInputType"]) || $additionalRow["AdditionalInputTypeID"] == 1){
                $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.IsInputType = 4';  // valueStr
            }
            else{
                $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.IsInputType = "'.$additionalRow["ItemIsInputType"].'"';
            }

            if(empty($additionalRow["ItemName"])){
                $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.Name = NULL';
            }
            else{
                $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.Name = "'.$additionalRow["ItemName"].'"';
            }

            if(empty($additionalRow["ItemDescription"])){
                $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.Description = NULL';
            }
            else{
                $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.Description = "'.$additionalRow["ItemDescription"].'"';
            }

            if(empty($additionalInput["additionalInfoVal"][$additionalIter])){
                //IsInputType 체크
                if($additionalRow["AdditionalInputTypeID"] == 1 || $additionalRow["ItemIsInputType"] != 2){
                    if(!empty($additionalRow["ItemValueStr"])){
                        $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.ValueStr = "'.$additionalRow["ItemValueStr"].'"';
                    }
                }
                else{
                    if(!empty($additionalRow["ItemValueInt"])){
                        $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.ValueInt = "'.$additionalRow["ItemValueInt"].'"';
                    }
                }
            }
            else{
                //IsInputType 체크
                if($additionalRow["AdditionalInputTypeID"] == 1 || $additionalRow["ItemIsInputType"] != 2){
                    $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.ValueStr = "'.$additionalInput["additionalInfoVal"][$additionalIter].'"';
                }
                else{
                    $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.ValueInt = "'.$additionalInput["additionalInfoVal"][$additionalIter].'"';
                }
            }

            if(empty($additionalInput["additionalInfoChecked"][$additionalIter])){
                $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.IsChecked = 0';
            }
            else{
                $instProductAdditionalItemQuery=$instProductAdditionalItemQuery.', ProductAdditionalItem.IsChecked = "'.$additionalInput["additionalInfoChecked"][$additionalIter].'"';
            }

            // insert ProductAdditionalItem
            $instProductAdditionalItem = mysqli_query($conn, $instProductAdditionalItemQuery);
            if($instProductAdditionalItem){
                $lastAdditionalID = $additionalRow["AdditionalID"];
            }
            else{
                echo 'ProductAdditionalItem_INSERT_FAIL';
                echo "\n".$instProductAdditionalItemQuery;
                exit();
            }
            $additionalIter++;
        }
    }
}

function updtAdditionalInfoFunc($postData, $conn, $productID, $additionalSpecViewListID){

    $additionalETCTable = mysqli_query($conn,
        'SELECT
        ProductAdditionalInfo.PKey as AdditionalID,
        ProductAdditionalInfo.Name as AdditionalCustomizedSpecInfoName,
        ProductAdditionalInfo.Description as AdditionalCustomizedSpecInfoDescription,
        ProductAdditionalInfo.Num as AdditionalNum,
        ProductAdditionalInfo.InputTypeID as AdditionalInputTypeID,
        ProductAdditionalInfo.IsFlexible as AdditionalIsFlexible,
        ProductAdditionalItem.PKey as ItemID,
        ProductAdditionalItem.IsInputType as ItemIsInputType,
        ProductAdditionalItem.Name as ItemName,
        ProductAdditionalItem.Description as ItemDescription,
        ProductAdditionalItem.ValueInt as ItemValueInt,
        ProductAdditionalItem.ValueStr as ItemValueStr,
        ProductAdditionalItem.Num as ItemNum
        from ProductAdditionalInfo
        inner join ProductAdditionalItem on ProductAdditionalItem.ProductAdditionalInfoID = ProductAdditionalInfo.PKey
        where ProductAdditionalInfo.ProductID = '.$productID.'
        and (ProductAdditionalInfo.IsDelete = 0 and ProductAdditionalInfo.IsHide = 0)
        and ProductAdditionalInfo.SpecViewListID = '.$additionalSpecViewListID.'
        order by ProductAdditionalInfo.PKey asc, ProductAdditionalItem.PKey asc;' );

    $numOfAdditional = mysqli_num_rows($additionalETCTable);

    if($numOfAdditional != 0){
        switch ($additionalSpecViewListID) {
            case 1:{ // material
                $additionalInput = $postData["materialAdditional"];
            }
                break;
            case 6:{ // etc
                $additionalInput = $postData["etcAdditional"];
            }
                break;
        }

        $additionalIter = 0;
        while($additionalRow = mysqli_fetch_array($additionalETCTable)){

            $updtProductAdditionalItemQuery =
            'UPDATE ProductAdditionalItem set
            ProductAdditionalItem.Name = "'.$additionalRow["ItemName"].'"
            ';


            // 입력 값 쿼리에 추가
            if($additionalInput["additionalInfoVal"][$additionalIter] == null){
                $updtProductAdditionalItemQuery=$updtProductAdditionalItemQuery.', ProductAdditionalItem.Valueint = NULL';
                $updtProductAdditionalItemQuery=$updtProductAdditionalItemQuery.', ProductAdditionalItem.ValueStr = NULL';
            }
            else{
                //IsInputType 체크
                if($additionalRow["AdditionalInputTypeID"] == 1 || $additionalRow["ItemIsInputType"] != 2){
                    $updtProductAdditionalItemQuery=$updtProductAdditionalItemQuery.', ProductAdditionalItem.ValueStr = "'.$additionalInput["additionalInfoVal"][$additionalIter].'"';
                }
                else{
                    $updtProductAdditionalItemQuery=$updtProductAdditionalItemQuery.', ProductAdditionalItem.ValueInt = "'.$additionalInput["additionalInfoVal"][$additionalIter].'"';
                }
            }


            if($additionalInput["additionalInfoChecked"][$additionalIter] == 0 ||
            !empty($additionalInput["additionalInfoChecked"][$additionalIter])){
                $updtProductAdditionalItemQuery=$updtProductAdditionalItemQuery.', ProductAdditionalItem.IsChecked = "'.$additionalInput["additionalInfoChecked"][$additionalIter].'"';
            }


            $updtProductAdditionalItemQuery = $updtProductAdditionalItemQuery.' where ProductAdditionalItem.PKey = '.$additionalRow["ItemID"].'';
            // update ProductAdditionalItem
            $updtProductAdditionalItem = mysqli_query($conn, $updtProductAdditionalItemQuery);
            if($updtProductAdditionalItem){

            }
            else{
                echo 'ProductAdditionalItem_UPDATE_FAIL';
                echo "\n".$updtProductAdditionalItemQuery;
                exit();
            }
            $additionalIter++;
        }
    }
}

function instProductSizeFunc($postData, $conn, $productID){
    $numOfSizeList = count($postData["productSizeList"]);
    $numOfSize = count($postData["productSize"]);
    $sizeIndex = 0;

    for($sizeListIter=0; $sizeListIter<$numOfSizeList ; $sizeListIter++){
        $instProductSizeListQuery =
        'INSERT into ProductSizeList set
        ProductID = '.$productID.',
        CreatedDate = Now()';

        // 값이 존재하면 query에 append
        if(isset($postData["productSizeList"][$sizeListIter])) { $instProductSizeListQuery = $instProductSizeListQuery.', Name = "'.$postData["productSizeList"][$sizeListIter].'"';}

        // inst ProductSizeList
        $instProductSizeList = mysqli_query($conn, $instProductSizeListQuery);
        if($instProductSizeList){
            $productSizeListID = mysqli_insert_id($conn);

            // ProductSize.SizeTypeID에 저장할 SizeTypePKey, SizeTypeListPriority 배열에 저장
            $sizeTypeTable = mysqli_query($conn, 'SELECT SizeType.PKey as SizeTypePKey,
                    SizeType.NameKR as SizeTypeName,
                    SizeTypeList.Priority as SizeTypeListPriority
                    from SizeTypeList
                    left join SizeType on SizeType.PKey = SizeTypeList.SizeTypeID
                    where SizeTypeList.CategoryID='.$postData["categoryID"].';' );
            while($sizeTypeRow = mysqli_fetch_array($sizeTypeTable)){
                $sizeTypeID[] = $sizeTypeRow["SizeTypePKey"];
                $sizeTypeListPriority[] = $sizeTypeRow["SizeTypeListPriority"];
            }

            // inst ProductSize
            for($sizeIter=0; $sizeIter < ($numOfSize/$numOfSizeList); $sizeIter++, $sizeIndex++){
                if(!empty($postData["productSize"][$sizeIndex])){
                    $instProductSizeQuery =
                        'INSERT into ProductSize set
                        ProductSizeListID = '.$productSizeListID.',
                        SizeTypeID = '.$sizeTypeID[$sizeIter].',
                        Priority = '.$sizeTypeListPriority[$sizeIter].',
                        Size = '.$postData["productSize"][$sizeIndex].',
                        IsNecessary = '.$postData["isNecessary"][$sizeIndex].',
                        CreatedDate = Now();';
                }
                else{   // ProductSize value가 null일 경우
                    // echo "121212";
                    $instProductSizeQuery =
                        'INSERT into ProductSize set
                        ProductSizeListID = '.$productSizeListID.',
                        SizeTypeID = '.$sizeTypeID[$sizeIter].',
                        Priority = '.$sizeTypeListPriority[$sizeIter].',
                        IsNecessary = 0,
                        CreatedDate = Now();';
                }
                $instProductSize = mysqli_query($conn, $instProductSizeQuery);
                if($instProductSize){

                }
                else {echo 'ProductSize_INSERT_FAIL'; echo "\n".$instProductSizeQuery; exit();}
            }
        }
        else {echo 'ProductSizeList_INSERT_FAIL2';exit();}
    }
}


function updtMaterialInfoFunc($postData, $conn, $productID){
    $updateInfoTable = mysqli_query($conn,
        'SELECT ProductSpecViewList.PKey as ProductSpecViewListPKey, ProductInfo.PKey as ProductInfoPKey, MaterialInfo.PKey as MaterialInfoPKey
        from ProductSpecViewList
        left join ProductSpecTable on ProductSpecTable.PKey = ProductSpecViewList.ProductSpecTableID
        left join ProductInfo on ProductInfo.ProductSpecID = ProductSpecViewList.PKey
        left join MaterialInfo on MaterialInfo.ProductInfoID = ProductInfo.PKey
        where ProductSpecViewList.ProductID='.$productID.'
        and ProductSpecTable.PKey=1;' );
    $pastNumOfMaterials = mysqli_num_rows($updateInfoTable);
    $updateInfoRow = mysqli_fetch_array($updateInfoTable);
    $productInfoID = $updateInfoRow["ProductInfoPKey"];

    //소재명 비율 항목이 존재할 경우에만 update
    if(isset($postData["materialName"])){
        $numOfMaterials = count($postData["materialName"]);
        // update MaterialInfo
        // 수정하려는 소재의 수가 db에 있는 소재의 수와 같거나 많을 경우
        if($pastNumOfMaterials <= $numOfMaterials){
            $materialIter=0;
            do{ // 같은 수까지 소재 정보 update
                if($materialIter < $pastNumOfMaterials){
                    $updtMaterialInfo = mysqli_query($conn,
                        'UPDATE MaterialInfo set
                        Name = "'.$postData["materialName"][$materialIter].'",
                        Ratio = '.$postData["materialRatio"][$materialIter].'
                        where MaterialInfo.PKey = '.$updateInfoRow["MaterialInfoPKey"].'
                        ;' );
                    if($updtMaterialInfo){
                        $updateInfoRow = mysqli_fetch_array($updateInfoTable);
                    }
                    else {echo 'MaterialInfo_UPDATE_FAIL6'; exit();}
                }
                else{   // 소재 추가시
                    $instMaterialInfo = mysqli_query($conn,
                        'INSERT into MaterialInfo set
                        ProductInfoID = '.$productInfoID.',
                        Name = "'.$postData["materialName"][$materialIter].'",
                        Ratio = '.$postData["materialRatio"][$materialIter].'
                        ;' );
                    if($instMaterialInfo){
                    }
                    else {echo 'MaterialInfo_INSERT_FAIL7'; exit();}
                }
                $materialIter++;
            } while($materialIter < $numOfMaterials);
        }
        else{   // 소재 삭제시
            $materialIter=0;
            do{ // 같은 수까지 소재정보 update
                $updtMaterialInfo = mysqli_query($conn,
                    'UPDATE MaterialInfo set
                    Name = "'.$postData["materialName"][$materialIter].'",
                    Ratio = '.$postData["materialRatio"][$materialIter].'
                    where MaterialInfo.PKey = '.$updateInfoRow["MaterialInfoPKey"].'
                    ;' );
                if($updtMaterialInfo){
                    $updateInfoRow = mysqli_fetch_array($updateInfoTable);
                    $materialIter++;
                }
                else {echo 'MaterialInfo_UPDATE_FAIL8'; exit();}

            } while($materialIter < $numOfMaterials);

            do{    // 남아있는 소재정보 삭제
                $delMaterialInfo = mysqli_query($conn,
                    'DELETE from MaterialInfo
                    where MaterialInfo.PKey = '.$updateInfoRow["MaterialInfoPKey"].'
                    ;' );
                if($delMaterialInfo){
                }
                else {echo 'MaterialInfo_DELETE_FAIL1'; exit();}
            } while($updateInfoRow = mysqli_fetch_array($updateInfoTable));
        }
    }

    //소재명 비율 항목이 존재하지 않으면 해당 Product의 MaterialInfo 삭제
    else{


        do{    // 남아있는 소재정보 삭제
            if(isset($updateInfoRow["MaterialInfoPKey"]) && !empty($updateInfoRow["MaterialInfoPKey"])){
            $delMaterialInfoQuery ='DELETE from MaterialInfo
                where MaterialInfo.PKey = '.$updateInfoRow["MaterialInfoPKey"].' ';
            $delMaterialInfo = mysqli_query($conn, $delMaterialInfoQuery);
            if($delMaterialInfo){
            }
            else {echo 'MaterialInfo_DELETE_FAIL2'; echo "\n".$delMaterialInfoQuery; exit();}
            }
        } while($updateInfoRow = mysqli_fetch_array($updateInfoTable));
    }
}


function updtThumbnailFunc($postData, $conn, $productID){

    // 카테고리 리로드시 설정값 다시 불러옴
    if($postData["isCategoryReload"]){

        $customSpecInfoTable = mysqli_query($conn, 'SELECT * from CustomizedSpecInfo where CategoryID = '.$postData["categoryID"].'' );
        $customSpecInfoRow = mysqli_fetch_array($customSpecInfoTable);
        // CustomizedSpecInfo IsHide와 IsDelete에 따라 각 항목의 IsDelete 판별
        $newIsThumbnailDelete = sprintf("%06b", $customSpecInfoRow["IsThumbnailDelete"]) | sprintf("%06b", $customSpecInfoRow["IsThumbnailHide"]);


        $updtPSViewListQuery = 'UPDATE ProductSpecViewList
            left join ProductSpecTable on ProductSpecTable.PKey = ProductSpecViewList.ProductSpecTableID
            left join ProductThumbnail on ProductThumbnail.ProductSpecID = ProductSpecViewList.PKey
            set
            ProductSpecViewList.UpdatedDate = Now(),
            ProductThumbnail.IsDelete = conv('.$newIsThumbnailDelete.', 2, 10),
            ProductThumbnail.IsHide = conv(0, 2, 10)
            where ProductSpecViewList.ProductID = '.$productID.' and ProductSpecTable.PKey=2
            ;';
    }
    else{
        $updtPSViewListQuery = mysqli_query($conn,
            'UPDATE ProductSpecViewList
            left join ProductSpecTable on ProductSpecTable.PKey = ProductSpecViewList.ProductSpecTableID
            set
            ProductSpecViewList.UpdatedDate = Now()
            where ProductSpecViewList.ProductID = '.$productID.' and ProductSpecTable.PKey=2
            ;' );
        if($updtPSViewList){
        }
        else {echo 'ProductSpecViewList_UPDATE_FAIL2'; exit();}
    }

    $updtPSViewList = mysqli_query($conn, $updtPSViewListQuery);
    if($updtPSViewList){
    }
    else {echo 'ProductSpecViewList_UPDATE_FAIL2'; exit();}
}

function updtGuideFunc($postData, $conn, $productID){
    $updtPSViewList = mysqli_query($conn,
        'UPDATE ProductSpecViewList
        left join ProductSpecTable on ProductSpecTable.PKey = ProductSpecViewList.ProductSpecTableID
        set
        ProductSpecViewList.UpdatedDate = Now()
        where ProductSpecViewList.ProductID = '.$productID.' and ProductSpecTable.PKey=5
        ;' );
    if($updtPSViewList){
    }
    else {echo 'ProductSpecViewList_UPDATE_FAIL5'; exit();}
}


// not 연산
function bitChangeFunc($binaryStr){
    for($iter=0; $iter < strlen($binaryStr); $iter++){
        if($binaryStr[$iter] == 1){
            $binaryStr[$iter]=0;
        }
        else{
            $binaryStr[$iter]=1;
        }
    }
    return $binaryStr;
}
?>

<?

// GET productPKey가 존재할 경우(update) Product의 name과 description을 가져온다
if(isset($_GET["productPKey"]) && !isset($_GET['categoryID'])){

    // material additional inpo
    $materialAdditionalTable = mysqli_query($conn,
        'SELECT ProductAdditionalInfo.InputTypeID as InputTypeID,
        ProductAdditionalInfo.PKey as AdditionalID,
        ProductAdditionalInfo.Name as AdditionalName,
        ProductAdditionalInfo.Description as AdditionalDescription,
        ProductAdditionalItem.Name as ItemName,
        ProductAdditionalItem.Name as ItemDescription,
        ProductAdditionalItem.IsInputType as ItemIsInputType,
        ProductAdditionalItem.ValueInt as ItemValueInt,
        ProductAdditionalItem.ValueStr as ItemValueStr,
        ProductAdditionalItem.IsChecked as ItemIsChecked
        from ProductAdditionalInfo
        left join ProductAdditionalItem on ProductAdditionalItem.ProductAdditionalInfoID = ProductAdditionalInfo.PKey
        where ProductAdditionalInfo.ProductID = '.$productPKey.'
        and (ProductAdditionalInfo.IsDelete = 0 and ProductAdditionalInfo.IsHide = 0)
        and ProductAdditionalInfo.SpecViewListID = 1
        order by ProductAdditionalInfo.PKey asc, ProductAdditionalItem.PKey asc;' );

    $lastMaterialAdditionalID = 0;
    $numOfMaterialAdditional = 0;
    while($materialAdditionalRow = mysqli_fetch_array($materialAdditionalTable)){

        // ProductAdditionalInfo.PKey 가 다른것이면
        if($lastMaterialAdditionalID != $materialAdditionalRow["AdditionalID"]){
            // ProductAdditionalInfo name, discription 저장
            $materialAdditionalName[] = $materialAdditionalRow["AdditionalName"];
            $materialAdditionalDescription[] = $materialAdditionalRow["AdditionalDescription"];

            // ProductAdditionalInfo inputtypeid, inputtype 저장
            switch($materialAdditionalRow["InputTypeID"]){
                case 1:{
                    $materialAdditionalInputID[] = 1;
                    $materialAdditionalInputType[] = "text";
                }
                    break;
                case 2:{
                    $materialAdditionalInputID[] = 2;
                    $materialAdditionalInputType[] = "checkbox";
                }
                    break;
                case 3:{
                    $materialAdditionalInputID[] = 3;
                    $materialAdditionalInputType[] = "radio";
                }
                    break;
                case 4:{
                    $materialAdditionalInputID[] = 4;
                    $materialAdditionalInputType[] = "select";
                }
                    break;
            }

            $numOfMaterialAdditional++;
        }

        // ProductAdditionalItem name, description, value 저장
        $materialItemName[$numOfMaterialAdditional-1][] = $materialAdditionalRow["ItemName"];
        $materialItemDescription[$numOfMaterialAdditional-1][]=$materialAdditionalRow["ItemDescription"];
        $materialItemIsChecked[$numOfMaterialAdditional-1][]=$materialAdditionalRow["ItemIsChecked"];
        if($materialAdditionalRow["ItemIsInputType"] == 2){
            $materialItemValue[$numOfMaterialAdditional-1][] = $materialAdditionalRow["ItemValueInt"];
        }
        else if($materialAdditionalRow["ItemIsInputType"] == 4){
            $materialItemValue[$numOfMaterialAdditional-1][] = $materialAdditionalRow["ItemValueStr"];
        }
        else{
            $materialItemValue[$numOfMaterialAdditional-1][] = null;
        }

        $lastMaterialAdditionalID = $materialAdditionalRow["AdditionalID"];
    }

    // additional info
    $additionalETCTable = mysqli_query($conn,
        'SELECT ProductAdditionalInfo.InputTypeID as InputTypeID,
        ProductAdditionalInfo.PKey as AdditionalID,
        ProductAdditionalInfo.Name as AdditionalName,
        ProductAdditionalInfo.Description as AdditionalDescription,
        ProductAdditionalItem.Name as ItemName,
        ProductAdditionalItem.Name as ItemDescription,
        ProductAdditionalItem.IsInputType as ItemIsInputType,
        ProductAdditionalItem.ValueInt as ItemValueInt,
        ProductAdditionalItem.ValueStr as ItemValueStr,
        ProductAdditionalItem.IsChecked as ItemIsChecked
        from ProductAdditionalInfo
        left join ProductAdditionalItem on ProductAdditionalItem.ProductAdditionalInfoID = ProductAdditionalInfo.PKey
        where ProductAdditionalInfo.ProductID = '.$productPKey.'
        and (ProductAdditionalInfo.IsDelete = 0 and ProductAdditionalInfo.IsHide = 0)
        and ProductAdditionalInfo.SpecViewListID = 6
        order by ProductAdditionalInfo.PKey asc, ProductAdditionalItem.PKey asc;' );

    $lastAdditionalID = 0;
    $numOfAdditional = 0;
    while($additionalRow = mysqli_fetch_array($additionalETCTable)){

        // ProductAdditionalInfo.PKey 가 다른것이면
        if($lastAdditionalID != $additionalRow["AdditionalID"]){
            // ProductAdditionalInfo name, discription 저장
            $additionalName[] = $additionalRow["AdditionalName"];
            $additionalDescription[] = $additionalRow["AdditionalDescription"];


            // ProductAdditionalInfo inputtypeid, inputtype 저장
            switch($additionalRow["InputTypeID"]){
                case 1:{
                    $additionalInputID[] = 1;
                    $additionalInputType[] = "text";
                }
                    break;
                case 2:{
                    $additionalInputID[] = 2;
                    $additionalInputType[] = "checkbox";
                }
                    break;
                case 3:{
                    $additionalInputID[] = 3;
                    $additionalInputType[] = "radio";
                }
                    break;
                case 4:{
                    $additionalInputID[] = 4;
                    $additionalInputType[] = "select";
                }
                    break;
            }

            $numOfAdditional++;
        }

        // ProductAdditionalItem name, description, value 저장
        $itemName[$numOfAdditional-1][] = $additionalRow["ItemName"];
        $itemDescription[$numOfAdditional-1][] = $additionalRow["ItemDescription"];
        $itemIsChecked[$numOfAdditional-1][] = $additionalRow["ItemIsChecked"];
        if($additionalRow["ItemIsInputType"] == 2){
            $itemValue[$numOfAdditional-1][] = $additionalRow["ItemValueInt"];
        }
        else if($additionalRow["ItemIsInputType"] == 4){
            $itemValue[$numOfAdditional-1][] = $additionalRow["ItemValueStr"];
        }
        else{
            $itemValue[$numOfAdditional-1][] = null;
        }

        $lastAdditionalID = $additionalRow["AdditionalID"];
    }
}
else{
    $materialAdditionalTable = mysqli_query($conn,
        'SELECT Category.NameKR as CategoryName,
        AdditionalCustomizedSpecInfo.InputTypeID as InputTypeID,
        AdditionalCustomizedSpecInfo.PKey as AdditionalID,
        AdditionalCustomizedSpecInfo.Name as AdditionalName,
        AdditionalCustomizedSpecInfo.Description as AdditionalDescription,
        CustomizedSpecItem.Name as ItemName,
        CustomizedSpecItem.Name as ItemDescription,
        CustomizedSpecItem.IsInputType as ItemIsInputType,
        CustomizedSpecItem.ValueInt as ItemValueInt,
        CustomizedSpecItem.ValueStr as ItemValueStr
        from Category
        left join CategoryAdditionalOption on CategoryAdditionalOption.CategoryID = Category.PKey
        inner join AdditionalCustomizedSpecInfo
        on (AdditionalCustomizedSpecInfo.PKey = CategoryAdditionalOption.AdditionalCustomizedSpecInfoID
          and AdditionalCustomizedSpecInfo.SpecViewListID = 1)
        left join InputType on InputType.PKey = AdditionalCustomizedSpecInfo.InputTypeID
        left join CustomizedSpecItem on CustomizedSpecItem.AdditionalCustomizedSpecInfoID = AdditionalCustomizedSpecInfo.PKey
        where Category.PKey = '.$categoryID.' and (CategoryAdditionalOption.IsDelete = 0 and CategoryAdditionalOption.IsHide = 0)
        order by AdditionalCustomizedSpecInfo.PKey asc, CustomizedSpecItem.PKey asc;' );

    $lastMaterialAdditionalID = 0;
    $numOfMaterialAdditional = 0;
    while($materialAdditionalRow = mysqli_fetch_array($materialAdditionalTable)){

        // AdditionalCustomizedSpecInfo.PKey 가 다른것이면
        if($lastMaterialAdditionalID != $materialAdditionalRow["AdditionalID"]){
            // AdditionalCustomizedSpecInfo name, discription 저장
            $materialAdditionalName[] = $materialAdditionalRow["AdditionalName"];
            $materialAdditionalDescription[] = $materialAdditionalRow["AdditionalDescription"];


            // AdditionalCustomizedSpecInfo inputtypeid, inputtype 저장
            switch($materialAdditionalRow["InputTypeID"]){
                case 1:{
                    $materialAdditionalInputID[] = 1;
                    $materialAdditionalInputType[] = "text";
                }
                    break;
                case 2:{
                    $materialAdditionalInputID[] = 2;
                    $materialAdditionalInputType[] = "checkbox";
                }
                    break;
                case 3:{
                    $materialAdditionalInputID[] = 3;
                    $materialAdditionalInputType[] = "radio";
                }
                    break;
                case 4:{
                    $materialAdditionalInputID[] = 4;
                    $materialAdditionalInputType[] = "select";
                }
                    break;
            }

            $numOfMaterialAdditional++;
        }

        // CustomizedSpecItem name, description, value 저장
        $materialItemName[$numOfMaterialAdditional-1][] = $materialAdditionalRow["ItemName"];
        $materialItemDescription[$numOfMaterialAdditional-1][] = $materialAdditionalRow["ItemDescription"];
        if($materialAdditionalRow["ItemIsInputType"] == 2){
            $materialItemValue[$numOfMaterialAdditional-1][] = $materialAdditionalRow["ItemValueInt"];
        }
        else if($materialAdditionalRow["ItemIsInputType"] == 4){
            $materialItemValue[$numOfMaterialAdditional-1][] = $materialAdditionalRow["ItemValueStr"];
        }
        else{
            $materialItemValue[$numOfMaterialAdditional-1][] = null;
        }

        $lastMaterialAdditionalID = $materialAdditionalRow["AdditionalID"];
    }


    // 치수이미지에 추가될 정보
    $thumbnailAdditionalTable = mysqli_query($conn,
        'SELECT Category.NameKR as CategoryName,
        AdditionalCustomizedSpecInfo.InputTypeID as InputTypeID,
        AdditionalCustomizedSpecInfo.PKey as AdditionalID,
        AdditionalCustomizedSpecInfo.Name as AdditionalName,
        AdditionalCustomizedSpecInfo.Description as AdditionalDescription,
        CustomizedSpecItem.Name as ItemName,
        CustomizedSpecItem.Name as ItemDescription,
        CustomizedSpecItem.IsInputType as ItemIsInputType,
        CustomizedSpecItem.ValueInt as ItemValueInt,
        CustomizedSpecItem.ValueStr as ItemValueStr
        from Category
        left join CategoryAdditionalOption on CategoryAdditionalOption.CategoryID = Category.PKey
        inner join AdditionalCustomizedSpecInfo
        on (AdditionalCustomizedSpecInfo.PKey = CategoryAdditionalOption.AdditionalCustomizedSpecInfoID
          and AdditionalCustomizedSpecInfo.SpecViewListID = 2)
        left join InputType on InputType.PKey = AdditionalCustomizedSpecInfo.InputTypeID
        left join CustomizedSpecItem on CustomizedSpecItem.AdditionalCustomizedSpecInfoID = AdditionalCustomizedSpecInfo.PKey
        where Category.PKey = '.$categoryID.' and (CategoryAdditionalOption.IsDelete = 0 and CategoryAdditionalOption.IsHide = 0)
        order by AdditionalCustomizedSpecInfo.PKey asc, CustomizedSpecItem.PKey asc;' );

    $lastThumbnailAdditionalID = 0;
    $numOfThumbnailAdditional = 0;
    while($thumbnailAdditionalRow = mysqli_fetch_array($thumbnailAdditionalTable)){

        // AdditionalCustomizedSpecInfo.PKey 가 다른것이면
        if($lastThumbnailAdditionalID != $thumbnailAdditionalRow["AdditionalID"]){
            // AdditionalCustomizedSpecInfo name, discription 저장
            $thumbnailAdditionalName[] = $thumbnailAdditionalRow["AdditionalName"];
            $thumbnailAdditionalDescription[] = $thumbnailAdditionalRow["AdditionalDescription"];


            // AdditionalCustomizedSpecInfo inputtypeid, inputtype 저장
            switch($thumbnailAdditionalRow["InputTypeID"]){
                case 1:{
                    $thumbnailAdditionalInputID[] = 1;
                    $thumbnailAdditionalInputType[] = "text";
                }
                    break;
                case 2:{
                    $thumbnailAdditionalInputID[] = 2;
                    $thumbnailAdditionalInputType[] = "checkbox";
                }
                    break;
                case 3:{
                    $thumbnailAdditionalInputID[] = 3;
                    $thumbnailAdditionalInputType[] = "radio";
                }
                    break;
                case 4:{
                    $thumbnailAdditionalInputID[] = 4;
                    $thumbnailAdditionalInputType[] = "select";
                }
                    break;
            }

            $numOfThumbnailAdditional++;
        }

        // CustomizedSpecItem name, description, value 저장
        $thumbnailItemName[$numOfThumbnailAdditional-1][] = $thumbnailAdditionalRow["ItemName"];
        $thumbnailItemDescription[$numOfThumbnailAdditional-1][] = $thumbnailAdditionalRow["ItemDescription"];
        if($thumbnailAdditionalRow["ItemIsInputType"] == 2){
            $thumbnailItemValue[$numOfThumbnailAdditional-1][] = $thumbnailAdditionalRow["ItemValueInt"];
        }
        else if($thumbnailAdditionalRow["ItemIsInputType"] == 4){
            $thumbnailItemValue[$numOfThumbnailAdditional-1][] = $thumbnailAdditionalRow["ItemValueStr"];
        }
        else{
            $thumbnailItemValue[$numOfThumbnailAdditional-1][] = null;
        }

        $lastThumbnailAdditionalID = $thumbnailAdditionalRow["AdditionalID"];
    }

    // 기타에 추가될 정보
    $additionalETCTable = mysqli_query($conn,
        'SELECT Category.NameKR as CategoryName,
        AdditionalCustomizedSpecInfo.InputTypeID as InputTypeID,
        AdditionalCustomizedSpecInfo.PKey as AdditionalID,
        AdditionalCustomizedSpecInfo.Name as AdditionalName,
        AdditionalCustomizedSpecInfo.Description as AdditionalDescription,
        CustomizedSpecItem.Name as ItemName,
        CustomizedSpecItem.Name as ItemDescription,
        CustomizedSpecItem.IsInputType as ItemIsInputType,
        CustomizedSpecItem.ValueInt as ItemValueInt,
        CustomizedSpecItem.ValueStr as ItemValueStr
        from Category
        left join CategoryAdditionalOption on CategoryAdditionalOption.CategoryID = Category.PKey
        inner join AdditionalCustomizedSpecInfo
        on (AdditionalCustomizedSpecInfo.PKey = CategoryAdditionalOption.AdditionalCustomizedSpecInfoID
          and AdditionalCustomizedSpecInfo.SpecViewListID = 6)
        left join InputType on InputType.PKey = AdditionalCustomizedSpecInfo.InputTypeID
        left join CustomizedSpecItem on CustomizedSpecItem.AdditionalCustomizedSpecInfoID = AdditionalCustomizedSpecInfo.PKey
        where Category.PKey = '.$categoryID.' and (CategoryAdditionalOption.IsDelete = 0 and CategoryAdditionalOption.IsHide = 0)
        order by AdditionalCustomizedSpecInfo.PKey asc, CustomizedSpecItem.PKey asc;' );

    $lastAdditionalID = 0;
    $numOfAdditional = 0;
    while($additionalRow = mysqli_fetch_array($additionalETCTable)){

        // AdditionalCustomizedSpecInfo.PKey 가 다른것이면
        if($lastAdditionalID != $additionalRow["AdditionalID"]){
            // AdditionalCustomizedSpecInfo name, discription 저장
            $additionalName[] = $additionalRow["AdditionalName"];
            $additionalDescription[] = $additionalRow["AdditionalDescription"];


            // AdditionalCustomizedSpecInfo inputtypeid, inputtype 저장
            switch($additionalRow["InputTypeID"]){
                case 1:{
                    $additionalInputID[] = 1;
                    $additionalInputType[] = "text";
                }
                    break;
                case 2:{
                    $additionalInputID[] = 2;
                    $additionalInputType[] = "checkbox";
                }
                    break;
                case 3:{
                    $additionalInputID[] = 3;
                    $additionalInputType[] = "radio";
                }
                    break;
                case 4:{
                    $additionalInputID[] = 4;
                    $additionalInputType[] = "select";
                }
                    break;
            }

            $numOfAdditional++;
        }

        // CustomizedSpecItem name, description, value 저장
        $itemName[$numOfAdditional-1][] = $additionalRow["ItemName"];
        $itemDescription[$numOfAdditional-1][] = $additionalRow["ItemDescription"];
        if($additionalRow["ItemIsInputType"] == 2){
            $itemValue[$numOfAdditional-1][] = $additionalRow["ItemValueInt"];
        }
        else if($additionalRow["ItemIsInputType"] == 4){
            $itemValue[$numOfAdditional-1][] = $additionalRow["ItemValueStr"];
        }
        else{
            $itemValue[$numOfAdditional-1][] = null;
        }

        $lastAdditionalID = $additionalRow["AdditionalID"];
    }
}
?>

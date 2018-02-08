$(document).ready(function(){

    // searchproductlist.php
    $(document).on("click", '#sizeInfoSearch_submit', function(){
        // alert("검색버튼.");
        var productName = $("#searchName").val();
        var categoryPKey = $("#sizeInfoSearch_selectItem option:selected").val();
        var searchStartDate = $("#searchStartDate").val();
        var searchEndDate = $("#searchEndDate").val();
        var sendGetParam = "searchmain.html?";
        if(productName) sendGetParam += "productName="+productName+"&";
        if(categoryPKey!=0) sendGetParam += "categoryPKey="+categoryPKey+"&";
        if(searchStartDate) sendGetParam += "searchStartDate="+searchStartDate+"&";
        if(searchEndDate) sendGetParam += "searchEndDate="+searchEndDate+"&";
        location.href = sendGetParam;
    });
    $(document).on("click", '.updateProductInfo', function(){
        productPKey = $(this).siblings(".searchlist_productPKey").text();
        location.href = "insertsizeinfo.html?productPKey="+productPKey;
    });


    // searchmain.html
    $(document).on("click", '#sizeInfoList_delSelectedItem', function(){

        // 체크된 ProductPkey들을 배열로 저장
        var chkedProductID = new Array;
         $("input[name=sizeInfoList_chk]:checked").parent().siblings(".searchlist_productPKey").each(function(index){
            chkedProductID.push($(this).text());
        });

        // 체크된 항목이 없으면
        if(chkedProductID.length == 0){
            alert("삭제할 대상이 없습니다.");
        }
        else{
            $.ajax({
                type:'post',
                url: './sizeinfophp/trydeletesizeinfo.php',
                data: {
                    delProductID:chkedProductID
                },
                success:function(data){
                    if(data == 'Product_DELETE_SUCCESS'){
                        alert("항목을 삭제했습니다.")
                        location.reload();
                    }
                    else{
                        alert(data);
                    }
                }
            });  //ajax닫음
        }
    });

    $(document).on("click", '#sizeInfoList_instSizeInfo', function(){
        location.href = "insertsizeinfo.html";
    });
    $(document).on("click", '#downloadImg', function(){
        alert("이미지다운로드버튼.");
    });
    $(document).on("click", '#downloadHTML', function(){
        alert("html소스다운로드버튼.");
    });

    $('#sizeInfoList_checkAll').click(function(){
        if($('#sizeInfoList_checkAll').prop("checked")){
            $("input[name=sizeInfoList_chk]").prop("checked", true);
        }
        else{
            $("input[name=sizeInfoList_chk]").prop("checked", false);
        }
    });

});

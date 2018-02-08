var passwordFlag = 0;
var passwordFlagConfirm = 0;
var emailFlag = 0;

// 이메일 유효성 검사
function validateEmail(email) {
  // var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
  // var re =  /^[A-Za-z0-9_\.\-]+@[A-Za-z0-9\-]+\.[A-Za-z0-9\-]+/;
  var re =  /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
  return re.test(email);
}
function fn_press_han(obj)
{
    //좌우 방향키, 백스페이스, 딜리트, 탭키에 대한 예외
    if(event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 37 || event.keyCode == 39
    || event.keyCode == 46 ) return ;
    //obj.value = obj.value.replace(/[\a-zㄱ-ㅎㅏ-ㅣ가-힣]/g, '');
    obj.value = obj.value.replace(/[\ㄱ-ㅎㅏ-ㅣ가-힣]/g, '');
}


// 모두 동의 누르면 모두 체크
$("#allPermission").click(function() {

  if ($("#allPermission").prop("checked")) {

    $("#permissionCheck1").prop('checked', true);
    $("#permissionCheck2").prop('checked', true);
    $("#permissionCheck3").prop('checked', true);
    $("#permissionCheck4").prop('checked', true);

  } else {

    $("#permissionCheck1").prop('checked', false);
    $("#permissionCheck2").prop('checked', false);
    $("#permissionCheck3").prop('checked', false);
    $("#permissionCheck4").prop('checked', false);
  }

});

// 반대로 모두 체크 되어있으면 모두 동의도 자동 체크

$('.permissionCheckbox').click(function(){
  var permissionCheckboxFlag = true;
  for(var i = 1; i<5; i++){
    permissionCheckboxFlag &= $("#permissionCheck"+i).prop('checked');
  }
  console.log(permissionCheckboxFlag);
  if(permissionCheckboxFlag){
    $("#allPermission").prop("checked",true);
  }else{
    $("#allPermission").prop("checked",false);
  }
});
// 약관 텍스트 클릭하는 경우
var permissionFlag = 0;
$(".permissionTextBox").click(function(){
  $("#permissionInfoBoxTitle").text($(this).text());
  permissionFlag = $(this).attr("value");
  $("#permissionInfoBox").show();
});

// 약관창에서 확인 버튼 클릭하는 경우
$("#permissionInfoBoxCloseBtn").click(function(){
  $("#permissionInfoBox").hide();
  $("#permissionCheck"+permissionFlag).prop('checked',true);

});

// 회원가입 시에 Email 에 입력할때 비동기로 유효성 검사를 실행함
$(document).on("input", "#emailBox", function() {

  checkEmailFlag = validateEmail($(this).val());
  if (!checkEmailFlag) {
    $("#checkEmail").html("메일 형식이 올바르지 않습니다.");
    $("#checkEmail").css("color", "red");
    $("#emaildiv").css("outline-color", "red");
    emailFlag = 0;
  } else {
    $("#checkEmail").html("");
    $("#emaildiv").css("outline-color", "#dfdfdf");
    emailFlag = 1;
  }

});

// 비밀번호와 비밀번호 확인 같은지 확인 + 길이 조건
$(document).on("input", "#pw2", function() {

  if ($(this).val() != $("#pw").val()) {

    $("#checkPW2").css("outline-color", "red");
    passwordFlagConfirm = 0;

  } else {
    $("#checkPW2").css("outline-color", "#dfdfdf");
    passwordFlagConfirm = 1;
  }


});

// 비밀번호와 비밀번호 확인 같은지 확인
$(document).on("input", "#pw", function() {

  if ($(this).val() != $("#pw2").val()) {

    $("#checkPW2").css("outline-color", "red");
    passwordFlagConfirm = 0;

  } else {
    $("#checkPW2").css("outline-color", "#dfdfdf");
    passwordFlagConfirm = 1;
  }


});

// 비밀번호 형식확인
$(document).on("input", "#pw", function() {
  console.log($(this).val().length);
  if (!chkPwd($.trim($('#pw').val())) || $(this).val().length >20) {

    $("#checkPW").css("outline-color", "red");
    passwordFlag = 0;
  } else {
    $("#checkPW").css("outline-color", "#dfdfdf");
    passwordFlag = 1;
  }

});

// 가입버튼
$(".signupButton").click(function() {
// alert(localStorage.getItem("email"));
 if($("#permissionCheck1").prop('checked')==false||
    $("#permissionCheck2").prop('checked')==false||
    $("#permissionCheck3").prop('checked')==false){

  alert("약관에 동의해주세요.");

}else if(passwordFlag == 1 && passwordFlagConfirm == 1 && emailFlag == 1 ) {

    $.ajax({
          type: "POST",
          url: "http://heronation.net/sizeax/JYD/php/signup.php",
          data: {'ID': $('#emailBox').val(),
                 'PW': $('#pw').val(),
                  'ADNotification': $("#permissionCheck4").prop('checked')
                },
          success: function(result){

            if(result == "dumpEmail"){
                alert('중복된 이메일입니다.');
            }

            else if(result == "Fail") {
                alert('다시 시도해주세요.');
            }
            else {

              // 스토리지에 UserID는 존재하지 않는데, UserPKey가 존재하는 경우
              // UserSizeList에 해당하는 UserPKey 값을 로그인한 UserPKey로 바꿔준다.
              if(!localStorage.getItem("UserID") && localStorage.getItem("UserPKey")){
                $.ajax({
                  type:'POST',
                  url:'http://heronation.net/sizeax/CHS/php/updateusersizelistId.php',
                  data : {
                    tempUserPKey:localStorage.getItem("UserPKey"), // 임시 pkey
                    UserPKey:result // 등록된 pkey
                  },
                  success: function(response){
                    if(response == "update_success"){
                      console.log("uslID 교체 완료");
                    }else{
                      console.log("uslID 교체 실패");
                    }
                  }
                });
              }



                localStorage.setItem("UserPKey", result);
                localStorage.setItem("UserID", $("#emailBox").val());
                localStorage.setItem("UserPassword", $("#pw").val());

                // alert("가입이 완료되었습니다.\n" + localStorage.getItem("UserPKey") + "\n" + localStorage.getItem("UserID")
                //           + "\n" + localStorage.getItem("UserPassword"));

                $(".content").load("./LYH/lyh.html", {
                    CompanyID: CompanyID,
                    ProductKey: ProductKey,
                    UserPKey: localStorage.getItem("UserPKey")
                });
                $(".loginbutton").show();
                $(".backButton").hide();
            }
          }
        });

  } else {

    alert("이메일/비밀번호를 제대로 입력해주세요.\n비밀번호는 8~20자, 영문/숫자를 조합해야합니다.");

  }

});

// 취소버튼
$(".signupCancelButton").click(function() {

});


//비밀번호 형식 확인 8~20자 영문 숫자 조합
function chkPwd(str) {

  var reg_pwd = /^.*(?=.{8,20})(?=.*[0-9])(?=.*[a-zA-Z]).*$/;

  if (!reg_pwd.test(str)) {

    return false;

  }

  return true;

}


$(function(){
  console.log($("#permissionCheck1").prop('checked'));
  console.log($("#permissionCheck2").prop('checked'));
  console.log($("#permissionCheck3").prop('checked'));

});

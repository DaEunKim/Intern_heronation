jQuery.ajaxSetup({cache:false});

$(function() {
    if(localStorage.getItem("UserID")){
        document.getElementById("loginButton").innerHTML=localStorage.getItem("UserID");    }
    // 로그아웃 되어있을 떄 버튼을 누르면 로그인 페이지를 load 함
    else {
        document.getElementById("loginButton").innerHTML="LOGIN";
    }
});

$(document).delegate(".textInputBoxLeft", "click", function(){
    $(this).siblings('.textInputBoxRight').focus();
});

function loginView() {
        // 로그인 되어있을 때
        if(localStorage.getItem("UserID")){
            // 모달창 띄우기
            backgroundFlag = false;
            $("#heroBackground").css({
                'z-index':10
            });
            $("#logoutContainer").show();
        }
        // 로그아웃 되어있을 떄 버튼을 누르면 로그인 페이지를 load 함
        else {
            $(".content").load("./login/loginContainer.html");
        }
        $("#loginButton").hide();
        if($("#loginButton").text()=="LOGIN"){
        $(".backButton").show();
      }
}


function logout() {
    UserPKey = 0;
    localStorage.clear();
    document.getElementById("loginButton").innerHTML="LOGIN";
    opacityRemove();
    $(".content").load("./LYH/lyh.html",{
      CompanyID: CompanyID,
      ProductKey: ProductKey,
      UserPKey: UserPKey
    });

    alert('로그아웃 되었습니다.');
    $("#loginButton").show();
    $(".backButton").hide();


}

function myPage() {
    $('.content').load("http://heronation.net/sizeax/login/updatePassword.html");

    // $('.loginBackingContainer').show();

    $("#heroBackground").css('z-index', '0');
    // $(".backgroundOpacity").css('background-color', 'rgba(255, 0, 0, 1)');
    // $(".test").removeAttr('style');
    $("#logoutContainer").hide();
    $(".snsDiv").hide();

}



function checkPasswordFormat(str) {
    var reg_pwd = /^.*(?=.{8,20})(?=.*[0-9])(?=.*[a-zA-Z]).*$/;
    if (!reg_pwd.test(str))
        return false;
    return true;
}


function passwordNullCheck() {
    if (loginForm.originalPassword.value == "") {
        alert('기존 비밀번호 란을 입력하세요.');
        return false;
    }
    else if (loginForm.newPassword.value == "") {
        alert('새 비밀번호 란을 입력하세요.');
        return false;
    }
    else if (loginForm.newPasswordCheck.value == "") {
        alert('새 비밀번호 확인란을 입력하세요.');
        return false;
    }
    else if(loginForm.originalPassword.value == loginForm.newPassword.value){
        alert('새로운 비밀번호를 입력하세요.');
        return false;
    }
    else if ( !checkPasswordFormat($.trim(loginForm.newPassword.value)) || loginForm.newPassword.value.length >20 ) {
        alert('비밀번호는 8~20자, 영문/숫자를 조합해야합니다.');
    }
    else if (loginForm.newPasswordCheck.value != loginForm.newPassword.value) {
        alert('새 비밀번호 일치 여부를 확인하세요.');
        return false;
    }
    else {
        var userID = localStorage.getItem("UserID");
        var originalPassword = $('input[name=originalPassword]').val();
        var newPassword = $('input[name=newPassword]').val();

        var checkPassword =  $.post("http://heronation.net/sizeax/php/updatePassword.php", {
            userID: userID,
            originalPassword: originalPassword,
            newPassword: newPassword
        });

        checkPassword.done(function(result){
            console.log(result);
            if(result == 'success'){
                alert('비밀번호가 변경 되었습니다.');
                localStorage.setItem('UserPassword', newPassword);
                $(".content").load("./LYH/lyh.html",{
                  CompanyID: CompanyID,
                  ProductKey: ProductKey,
                  UserPKey: UserPKey
                });
                $("#loginButton").show();
            }
            else if(result == 'originalPassword'){
                alert('기존 비밀번호가 일치하지않습니다.');
            }
            else if(result == 'dbFail') { // DB Update 실패
                alert('서버 환경이 불안정합니다.')
            }
            else if(result == 'server_connect_fail'){
                alert('서버 연결 실패');
            }
        });
        checkPassword.fail(function(response) {
            //비동기 실패시 코드.
            alert('서버 환경이 불안정합니다.');
        });
    }
}

function nullCheck() {
    if (typeof console === "undefined" || typeof console.log === "undefined") {
        console = {};
    }

    if(localStorage.getItem("UserID")){
        alert("이미 로그인 되어있습니다.")
        return false;
    }
    else{
        if(loginForm.userId.value == ""){
            alert("아이디를 입력해주세요");
            return false;
        }
        else if(loginForm.userPassword.value == ""){
            alert("비밀번호를 입력해주세요");
            return false;
        }
        else {
            $.ajax({
                type: 'post',
                url: 'http://heronation.net/sizeax/php/tryLogin.php',
                data: $("#loginForm").serialize(),

                success: function (result) {
                    console.log("result: " + result);
                    if (result.indexOf("fail") == -1) {
                        // 임시
                        UserPKey = result;


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
                        localStorage.setItem("UserID", $('input[name=userId]').val());
                        localStorage.setItem("UserPassword", $('input[name=userPassword]').val());
                        document.getElementById("loginButton").innerHTML="LOGOUT";

                        // alert($('input[name=userId]').val());

                        // trackingUserPKey 수정 하는 ajax 추가//
                        // 단, trackingUserPKey가 없으면 실행 x -> 그냥 trackingUser 실행//
                        console.log("trackingPKey2 : "+trackingPKey);
                        if(trackingPKey==""){
                          console.log("asdf1221321");
                          if(trackingFlag){
                            trackingUser();
                            trackingFlag=false;
                          }
                        }else{
                            console.log("adfsafasf");
                            $.ajax({
                              type:'POST',
                              url:'http://heronation.net/sizeax/updatetracking.php',
                              data:{
                                UserPKey:UserPKey,
                                TrackingPKey:trackingPKey
                              },
                              success:function(response){
                                printData(response);
                                trackingFlag = false;
                              }
                            });
                          }


                        alert("로그인에 성공했습니다.");
                        $(".content").load("./LYH/lyh.html",{
                          CompanyID: CompanyID,
                          ProductKey: ProductKey,
                          UserPKey: UserPKey
                        });
                        $("#loginButton").show();
                        $(".backButton").hide();

                    } else if (result == "id_pw_empty"){
                        alert('11');
                    }
                    else {
                        $("#heroBackground").css('opacity', '0.3');
                        $("#heroBackground").css({
                            'z-index':10
                        });
                        $("#findContainer").show();
                        return false;
                    }
                },

                complete: function(data) {

                },
                error: function(xhr, status, error) {
                    alert("에러발생");
                }
            });
        }
    }
}

function outContainer() {
    $(".content").load("http://heronation.net/sizeax/LYH/lyh.html",{
      CompanyID: CompanyID,
      ProductKey: ProductKey,
      UserPKey: UserPKey
    });
    $("#loginButton").show();
}

function loginBacking() {
    $('.mainContainer').load("http://heronation.net/sizeax/login/loginContainer.html");
    $('.loginBackingContainer').hide();
    $(".backgroundOpacity").css('opacity', '');
    $(".backButton").show();

}

function findPassword() {
  $('.loginDiv').load("http://heronation.net/sizeax/login/findPassword.html");
  $('.loginBackingContainer').show();
  console.log("asdfsadfdsfsaf");
  $("#heroBackground").css('opacity', '');
  $("#heroBackground").css('z-index', '0');
  // $(".backgroundOpacity").css('background-color', 'rgba(255, 0, 0, 1)');
  // $(".test").removeAttr('style');
  $("#findContainer").hide();
  $(".snsDiv").hide();
  $(".backButton").hide();
}

function sendCheckEmail() {
    $.ajax({
        type: 'post',
        url: 'http://heronation.net/sizeax/php/sendCheckEmail.php',
        data: $("#sendCheckForm").serialize(),

        success: function (result) {
            console.log("result: " + result);
            if (result == "success") {
                alert('임시비밀번호가 발송되었습니다.');
            }
            else if (result == "notselect"){
                alert('존재하지 않는 이메일입니다. 다시 확인해주세요');
            }
            else {
                alert('서버가 불안정합니다.');
            }
        },

        complete: function(data) {

        },
        error: function(xhr, status, error) {
            alert("서버가 불안정합니다.");
        }
    });
}


function opacityRemove() {
    $("#heroBackground").css('opacity', '');
    $("#heroBackground").css('z-index', '0');
    // $(".backgroundOpacity").css('background-color', 'rgba(255, 0, 0, 1)');
    // $(".test").removeAttr('style');
    $("#findContainer").hide();
    $("#logoutContainer").hide();
    $("#loginButton").show();
    console.log("FTP TEST");

}

// $("#findDiv_YesBtn").click(function(){
//   $(".loginButton").hide();
// });

function join() {
    $(".content").load("http://heronation.net/sizeax/JYD/signup.html").show();
    $(".backButton").hide();

}

function joinCancel() {
  // $(".content").load("./LYH/lyh.html", {
  //     CompanyID: CompanyID,
  //     ProductKey: ProductKey,
  //     UserPKey: UserPKey
  // });
  $(".content").load("./login/loginContainer.html");
  // $(".loginbutton").show();
    $(".backButton").show();
}

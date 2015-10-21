$(function (){
    $(document).on("userDataUpdate", function(){
        $("#username").text(userData.username);
        $("#email").text(userData.email);
    });
});

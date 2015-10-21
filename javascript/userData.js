console.log("userData.js loading");

var userData = {
   username:"anon",
   email:"example@test.net",
   updateUserData: function (){
      $.post("/php/session_json.php", null, function(data){
         console.log("userDataUpdate occuring");
         console.log(data.username);
         userData.username = data.username;
         userData.email = data.email;
         $(document).trigger("userDataUpdate");
      }, "json");  
  }
};

$(function (){
    $(document).on("cookieUpdate", userData.updateUserData);
    if(!(typeof($.cookie("session_id")) == "undefined")){
        userData.updateUserData();
    }
});

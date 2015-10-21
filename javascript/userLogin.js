$(document).ready(function(){    
    $("form").submit(function (event){
        $.post("/php/login_user.php", $(this).serializeArray(),
            function(data){
                $(document).trigger("cookieUpdate");
            }, "text");
        event.preventDefault();
    });
});

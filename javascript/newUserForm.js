$(document).ready(function(){
    $("form").submit(function (event){
        $.post("/php/create_new_user.php", $(this).serializeArray(),
            function(data){
                console.log(data);
            }, "text");
        console.log($(this).serializeArray());
        event.preventDefault();
    });      
});

$(document).ready(function(){
    $("div.navbaritem[dropdown]").mouseenter(function(){
        $("div.navbaritemdropdown[name='" + $(this).attr("dropdown") + "']")
            .addClass("dropdowntransition");
    });

    $("div.navbaritem[dropdown]").mouseleave(function(){
        $("div.navbaritemdropdown[name='" + $(this).attr("dropdown") + "']")
            .removeClass("dropdowntransition");
    });

    $("div.navbaritemdropdown").mouseenter(function(){
       $("div.navbaritemdropdown[name='" + $(this).attr("name") + "']")
           .addClass("dropdowntransition");
    });

    $("div.navbaritemdropdown").mouseleave(function(){
       $("div.navbaritemdropdown[name='" + $(this).attr("name") + "']")
           .removeClass("dropdowntransition");
    });
});

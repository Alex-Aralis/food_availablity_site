$(document).ready(function(){
    $("div.navbaritem").click(function(){
        $(this).toggleClass("selectednavbaritem");

        $(this).siblings().removeClass("selectednavbaritem");
    });

    $("div.navbaritem[openupshutter][openupgroup]").click(function(){
        console.log($(this).attr("openupgroup"));
        console.log($(this).attr("openupshutter"));

        //close all non selected navbar containing shutters
        $("div.openupgroup[name='" + $(this).attr("openupgroup") + 
            "'] > div.openupshutter:not([name='" + $(this).attr("openupshutter") + "'])")
            .removeClass("openuptransition"); 

        //toggle selected navbar shutter
        $("div.openupshutter[name='" + $(this).attr("openupshutter") + "']")
            .toggleClass("openuptransition");
       
        //open lower shadow padding on selected grouping
        $("div.openupgroup[name='" + $(this).attr("openupgroup") + "'] > div.openupgrouppadding")
            .addClass("openuppaddingtransition");
  
        //if the only open shutter is being closed, close the lower shadow padding.
        $("div.openupgroup[name='" + $(this).attr("openupgroup") + 
            "']:not(:has(div.openupshutter.openuptransition)) > div.openupgrouppadding")
            .removeClass("openuppaddingtransition");
    });

/*
    $("div.navbaritem[openupshutter]").mouseleave(function(){
        $("div.openupshutter[name='" + $(this).attr("openupshutter") + "']")
            .removeClass("openuptransition");

        $("div.openupgroup[name='" + $(this).attr("openupgroup") + "']")
            .removeClass("openuppaddingtransition");
    });

    $("div.openupshutter").mouseenter(function(){
       $("div.openupshutter[name='" + $(this).attr("name") + "']")
           .addClass("openuptransition");
    });

//    $("div.openupshutter").mouseleave(function(){
//       $("div.openupshutter[name='" + $(this).attr("name") + "']")
//           .removeClass("openuptransition");
//    });

    $("div.openuplimiter").mouseleave(function(){
        $("div.openupshutter.openuptransition")
            .removeClass("openuptransition");
    });*/
});

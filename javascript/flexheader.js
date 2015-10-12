$(document).ready(function(){
    $("div.navbaritem[link]").click(function(){
        $("div.navbaritem.selectednavbaritem")
            .removeClass("selectednavbaritem");

        //close all openupshutters
        $("div.openupshutter.openuptransition")
            .removeClass("openuptransition");

        //close all grouppadding
        $("div.openupgrouppadding.openuppaddingtransition")
            .removeClass("openuppaddingtransition");
    });

    $("div.navbaritem[openupshutter][openupgroup]").click(function(){
        console.log($(this).attr("openupgroup"));
        console.log($(this).attr("openupshutter"));

        //toggle current navbaritem
        $(this).toggleClass("selectednavbaritem");

        //unselect all not clicked navbaritems on the current
        //navbar
        $(this).siblings().removeClass("selectednavbaritem");

        //unselect all lower level navbaritems
        $("div.openupgroup[name='" + $(this).attr("openupgroup") + 
            "'] div.navbar > div.navbaritem.selectednavbaritem")
            .removeClass("selectednavbaritem");
       
        //close all non selected navbar containing shutters inside of all
        //lower levels of grouping
        $("div.openupgroup[name='" + $(this).attr("openupgroup") + 
            "'] div.openupshutter:not([name='" + $(this).attr("openupshutter") + "'])")
            .removeClass("openuptransition"); 

        //toggle selected navbar shutter
        $("div.openupshutter[name='" + $(this).attr("openupshutter") + "']")
            .toggleClass("openuptransition");
       
        //open lower shadow padding on selected grouping
        $("div.openupgroup[name='" + $(this).attr("openupgroup") + "'] > div.openupgrouppadding")
            .addClass("openuppaddingtransition");
     
        //if the only open shutter is being closed, close the lower shadow padding.
        //on all lower openupgroup levels
        $("div.openupgroup[name='" + $(this).attr("openupgroup") + 
            "']:not(:has(div.openupshutter.openuptransition)) div.openupgrouppadding")
            .removeClass("openuppaddingtransition");
    });
});

$(document).ready(function(){

    $("div.navbaritem[link]").click(function(){
        $.post($(this).attr("link"), {}, function(data, status){
            $(".ajax-container").text(data);
        }, 'text');

        colapseOpenUps();
    });

    $("div.navbaritem[openupshutter][openupgroup]").click(function(){
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
        
        //close all non current group paddings on lower levels.
        $("div.openupgroup[name='" + $(this).attr("openupgroup") + 
            "'] > div.openupgroup div.openupgrouppadding.openuppaddingtransition")
            .removeClass("openuppaddingtransition"); 
 
        //closes all paddings in sibling groups
        //important for nonhomogeneous group pointers in navbars/openupcontent
        $("div.openupgroup[name='" + $(this).attr("openupgroup") + 
            "'] ~ div.openupgroup div.openupgrouppadding.openuppaddingtransition")
            .removeClass("openuppaddingtransition");

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
  
    $("div.main").click(function (){
       colapseOpenUps();
    });
  


    function colapseOpenUps(){
        //unselect all navbaritems
        $("div.navbaritem.selectednavbaritem")
            .removeClass("selectednavbaritem");

        //close all openupshutters
        $("div.openupshutter.openuptransition")
            .removeClass("openuptransition");

        //close all grouppadding
        $("div.openupgrouppadding.openuppaddingtransition")
            .removeClass("openuppaddingtransition");
    }
});

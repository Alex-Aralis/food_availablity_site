$(document).ready(function(){

    $("div.navbaritem[link]").click(function(){
        var path = $(this).attr("link");
        $.post(path, {}, function(data, status){
            console.log(path); 
            //for html docs
            if(/^.+\.html$/.test(path) || /^<!DOCTYPE html>/.test(data)){
                $("div.swapper-new").html("<iframe src='" + window.location.origin + path + "'class='swapper-iframe'>Your browser doesn't support iframes. :( </iframe>");
            }else{ //assuming to be a text doc
                //inserting sent data into the new swapper-item
                $("div.swapper-new").html("<pre class='text-data'></pre>");
                $("pre.text-data").text(data);
            }
            //setting up animation initail state
            $("div.swapper-current").addClass("swapper-current-prep");
            $("div.swapper-new").addClass("swapper-new-prep");

            //swapper-current animation
            $("div.swapper-current-prep").animate({right:"100%"}, 1000, "swing", function (){
                //resetting the old current windows content and style
                $("div.swapper-current-prep").html("").css("right", "")

                    //removing switch class swapper-current, swapper-new
                    .removeClass("swapper-current").addClass("swapper-new")
            
                    //removing swapper-current-prep
                    .removeClass("swapper-current-prep");

            });
        
            //moves new-swapper into position, then 
            $("div.swapper-new-prep").animate({left:"0"}, 1000, "swing", function (){
                //swapping swapper-current and swapper-new classes
                $("div.swapper-new-prep").removeClass("swapper-new").addClass("swapper-current")
 
                    //reset left
                    .css({left: ""})

                    //removing prep-class
                    .removeClass("swapper-new-prep");
            });

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

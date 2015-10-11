<?php 
require_once "dir_array.php";

$file_openups = '';

function write_openups($dir_array, $PWD){
    global $file_openups;
    $needsOpenup = false;

    //creating the navbar
    $file_openups .= "<div class='openupshutter' name='$PWD/'>";
    $file_openups .= "<div class='openupcontent'>";  
    $file_openups .=  "<div class='navbar'>";
    foreach($dir_array as $filename => $subarray){
        if(is_null($subarray)){
            //if not a directory
            $file_openups .=  "<div class='navbaritem'> $filename </div>";
        }else{
            //if a directory
            $needsOpenup = true;
            $file_openups .=  "<div class='navbaritem' openupgroup='$PWD/' openupshutter='$PWD/$filename/'> $filename </div>";
        }
    }
    $file_openups .=  "</div>";
    $file_openups .=  "</div>";
    $file_openups .=  "</div>";

    //create the openups linked to the navbar
    if($needsOpenup){
        $file_openups .= "<div class='openupgroup shadowbox' name='$PWD/'>";
        foreach($dir_array as $filename => $subarray){
//            $file_openups .= "<div class='openupshutter' name='$filename'>";
//            $file_openups .= "<div class='openupcontent'>";
            if(!is_null($subarray)){
                write_openups($subarray, "$PWD/$filename");
            }
//            $file_openups .= "</div>";
//            $file_openups .= "</div>";
        }    
        $file_openups .=  "<div class='openuptopshadow'></div>";
        $file_openups .=  "<div class='openupgrouppadding'>";
        $file_openups .=  "<div class='openuppaddingshadow'></div>";
        $file_openups .=  "</div>";
        $file_openups .=  "</div>";
    }
}

write_openups(dir_to_array($_SERVER['DOCUMENT_ROOT']), '');
?>

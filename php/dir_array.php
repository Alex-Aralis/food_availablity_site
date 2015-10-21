<?php
function dir_to_array($dir){
    if(false === is_dir($dir)){
        return false;
    }

    $dh = opendir($dir);
    $filetree = [];
    $tmp = '';

    while(false !== ($filename = readdir($dh))){
        if($filename === '..' || $filename === '.'){
            continue;
        }

        if(false !== ($tmp = dir_to_array($dir . '/' . $filename))){
            $filetree[$filename] = $tmp;
        }else{
            $filetree[$filename] = null;
        }
    }
  
    closedir($dir);

    return $filetree;
}
?>

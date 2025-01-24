<?php
$dir = 'inc/';
$inc = scandir($dir);
if($inc){
    foreach($inc as $include){
        if($include != '' && $include != '.' && $include != '..')
        
            require_once($dir.$include);
    }
}
?>
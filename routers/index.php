<?php
if($_SERVER['REQUEST_METHOD'] == 'GET' || $_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(isset($_GET['route']))
        $action = $_GET['route'];
    else{
        $action=null;
    }
    switch($action){
        case 'register':
            require('controllers/register-form.php');
        break;
        case 'login':
            require('controllers/login-form.php');
        break;
        case 'upload':
            require('controllers/upload-form.php');
        break;
        
        case 'logout':
            require('controllers/logout.php');
        break;
        default:
            require('controllers/index.php');
        break;
    }
}
?>

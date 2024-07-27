<?php
 header('Access-Control-Allow-Origin: http://localhost:8081');
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Access-Control-Allow-Credentials: true');
 require '../vendor/autoload.php';

 $app = new \Slim\App;

// Include route files
require './userList.php';
require './orderList.php';
require './menu.php';
require './order.php';
require './adminDashboard.php';
require './userOrder.php';
require './profile.php';
require './register.php'; 
require './login.php'; 


$app->run();
?>
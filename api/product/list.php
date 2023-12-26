<?php 

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../config/config.php';
include_once '../../class/products.php';


$database = new Database();
$db = $database->getConnection();
$productObj = new Products($db);

$productTable = 'products';

$requertMethod = $_SERVER['REQUEST_METHOD'];

if ($requertMethod == 'GET') {
    $getListProducts = $productObj->getListProduct($productTable);
    echo $getListProducts;
}else{
    $data = [
        'status' => 405,
        'message' => $requertMethod. ' Method now allowed',
    ];
    header("HTTP/1.0 405 Method now allowed");
    echo json_encode($data);
}

?>
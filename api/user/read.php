<?php 

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


include_once '../../config/config.php';
include_once '../../class/users.php';


$database = new Database();
$db = $database->getConnection();
$userObj = new Users($db);

$userTable = 'users';

$requertMethod = $_SERVER['REQUEST_METHOD'];

if ($requertMethod == 'GET') {
    if (isset($_GET['id'])) {
        $userId = $_GET['id'];
        $getUserDetail = $userObj->getDetailUser($userTable, $userId);
    } else {
        $data = [
            'status' => 404,
            'message' => $requertMethod. ' No User Found',
        ];
    }
    echo $getUserDetail;
}else{
    $data = [
        'status' => 405,
        'message' => $requertMethod. ' Method now allowed',
    ];
    header("HTTP/1.0 405 Method now allowed");
    echo json_encode($data);
}

?>
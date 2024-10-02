<?php

require_once "../controllers/EventCategoryController.php";
require_once "../database/config.php";

$controller = new EventCategoryController($conn);
$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case "GET":
       if (!empty($_GET["id"])) {
        $id = intval($_GET["id"]);
        $controller->getEventCategoryById($id);
       } else {
        $controller->getAllEventCategory();
       }
       break;
    case "POST":
        $controller->createEventCategory();
        break;
    case "PUT":
        if(!empty($_GET['id'])) {
            $id = intval($_GET["id"]);
            
            parse_str(file_get_contents("php://input"), $_PUT);

            $controller->updateEventCategory($id);
        } else {
            header("HTTP/1.0 400 Bad Request");
            echo json_encode(array('message' => 'ID is required for PUT request'));
        }
        break;
    case "DELETE":
        $id = intval($_GET["id"]);
        $controller->deleteEventCategory($id);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

?>
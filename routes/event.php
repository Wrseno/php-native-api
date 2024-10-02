<?php

require_once "../controllers/EventController.php";
require_once "../database/config.php";

$controller = new EventController($conn);
$request_method = $_SERVER["REQUEST_METHOD"];

switch ($request_method) {
    case "GET":
       if (!empty($_GET["id"])) {
        $id = intval($_GET["id"]);
        $controller->getEventById($id);
       } else {
        $controller->getAllEvent();
       }
       break;
    case "POST":
        $controller->createEvent();
        break;
    case "PUT":
        if(!empty($_GET['id'])) {
            $id = intval($_GET["id"]);
            
            parse_str(file_get_contents("php://input"), $_PUT);

            $controller->updateEvent($id);
        } else {
            header("HTTP/1.0 400 Bad Request");
            echo json_encode(array('message' => 'ID is required for PUT request'));
        }
        break;
    case "DELETE":
        $id = intval($_GET["id"]);
        $controller->deleteEvent($id);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
        break;
}

?>
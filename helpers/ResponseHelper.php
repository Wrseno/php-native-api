<?php

function response($success, $message, $data = null, $error = null) {
   header('Content-Type: application/json');
   $response = ([
        "success" => $success,
        "message" => $message,
        "data" => $data,
        "error" => $error,
    ]);

    echo json_encode($response);
    exit();
}

?>
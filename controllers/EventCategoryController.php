<?php

require_once "../database/config.php";
require_once "../helpers/ResponseHelper.php";

class EventCategoryController {
    private $conn;

    public function __construct($conn) {
        if (!$conn) {
            response(false, 'Database connection failed');
        }
        $this->conn = $conn;
    }

    public function getAllEventCategory() {
        $query = "SELECT * FROM eventcategories";
        $data = array();

        $stmt = $this->conn->query($query);

        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $data[] = $row;
            }
            response(true, 'Get List EventCategory Successfully', $data);
        } else {
            response(false, 'Get List EventCategory Failure', null, [
                'code' => 500,
                'message' => 'Internal server error: ' . $this->conn->errorInfo()[2]
            ]);
        }
    }

    public function getEventCategoryById($id = 0) {
        if ($id != 0) {
            $query = "SELECT * FROM eventcategories WHERE categories_id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_OBJ);
                response(true, 'Get EventCategory Successfully', $data);
            } else {
                response(false, 'EventCategory not found', null, [
                    'code' => 404,
                    'message' => 'The requested resource could be not found'
                ]);
            }
        } else {
            response(false, 'Invalid ID', null, [
                'code' => 400,
                'message' => 'Bad request: ID is required'
            ]);
        }
    }

    public function createEventCategory() {
        $arrcheckpost = array(
            'name' => '', 
        );

        $count = count(array_intersect_key($_POST, $arrcheckpost));
        
        if ($count == count($arrcheckpost)) {
            $query = "INSERT INTO eventcategories (name) VALUES (?)";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute([
                $_POST['name'], 
            ])) {
                $insert_id = $this->conn->lastInsertId();

                $result_stmt = $this->conn->prepare("SELECT * FROM eventcategories WHERE categories_id = ?");
                $result_stmt->execute([$insert_id]);
                $new_data = $result_stmt->fetch(PDO::FETCH_OBJ);

                response(true, 'EventCategory Added Successfully', $new_data);
            } else {
                response(false, 'Failed to Add EventCategory', null, [
                    'code' => 500,
                    'message' => 'Internal server error: ' . $this->conn->errorInfo()[2]
                ]);
            }
        } else {
            response(false, 'Missing Parameters', null, [
                'code' => 400,
                'message' => 'Bad request: Missing required parameters'
            ]);
        }
    }

    public function updateEventCategory($id) {
        parse_str(file_get_contents('php://input'), $_PUT);

        $arrcheckpost = array(
            'name' => '', 
        );
        
        $count = count(array_intersect_key($_PUT, $arrcheckpost));
        
        if ($count == count($arrcheckpost)) {
            $query = 'UPDATE eventcategories SET name = ? WHERE categories_id = ?';
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute([
                $_PUT['name'], 
                $id
            ])) {
                $query = "SELECT * FROM eventcategories WHERE categories_id = ?";
                $result_stmt = $this->conn->prepare($query);
                $result_stmt->execute([$id]);
                $updated_data = $result_stmt->fetch(PDO::FETCH_OBJ);

                response(true, 'EventCategory Updated Successfully', $updated_data);
            } else {
                response(false, 'Failed to Update EventCategory', null, [
                    'code' => 500,
                    'message' => 'Internal server error: ' . $this->conn->errorInfo()[2]
                ]);
            }
        } else {
            response(false, 'Missing Parameters', null, [
                'code' => 400,
                'message' => 'Bad request: Missing required parameters'
            ]);
        }
    }

    public function deleteEventCategory($id) {
        $stmt = $this->conn->prepare('DELETE FROM eventcategories WHERE categories_id = ?');

        if ($stmt->execute([$id])) {
            response(true, 'EventCategory Deleted Successfully');
        } else {
            response(false, 'Failed to Delete EventCategory', null, [
                'code' => 500,
                'message' => 'Internal server error: ' . $this->conn->errorInfo()[2]
            ]);
        }
    }
}
?>

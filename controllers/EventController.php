<?php

require_once "../database/config.php";
require_once "../helpers/ResponseHelper.php";

class EventController {
    private $conn;

    public function __construct($conn) {
        if (!$conn) {
            response(false, 'Database connection failed');
        }
        $this->conn = $conn;
    }

    public function getAllEvent() {
        $query = "SELECT * FROM event";
        $data = array();

        $stmt = $this->conn->query($query);

        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $data[] = $row;
            }
            response(true, 'Get List Event Successfully', $data);
        } else {
            response(false, 'Get List Event Failure', null, [
                'code' => 500,
                'message' => 'Internal server error: ' . $this->conn->errorInfo()[2]
            ]);
        }
    }

    public function getEventById($id = 0) {
        if ($id != 0) {
            $query = "SELECT * FROM event WHERE event_id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_OBJ);
                response(true, 'Get Event Successfully', $data);
            } else {
                response(false, 'Event not found', null, [
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

    public function createEvent() {
        $arrcheckpost = array(
            'user_id' => '', 
            'categories_id' => '', 
            'event_name' => '', 
            'poster' => '',
            'date' => '',
            'location' => '',
            'max_quota' => '',
            'status' => '',
            'description' => '',
            'start_time' => '',
            'end_time' => '',
        );

        $count = count(array_intersect_key($_POST, $arrcheckpost));
        
        if ($count == count($arrcheckpost)) {
            $query = "INSERT INTO event (user_id, categories_id, event_name, poster, 
            date, location, max_quota, status, description, start_time, end_time) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute([
                $_POST['user_id'], 
                $_POST['categories_id'], 
                $_POST['event_name'], 
                $_POST['poster'],
                $_POST['date'],
                $_POST['location'],
                $_POST['max_quota'],
                $_POST['status'],
                $_POST['description'],
                $_POST['start_time'],
                $_POST['end_time'],
            ])) {
                $insert_id = $this->conn->lastInsertId();

                $result_stmt = $this->conn->prepare("SELECT * FROM event WHERE event_id = ?");
                $result_stmt->execute([$insert_id]);
                $new_data = $result_stmt->fetch(PDO::FETCH_OBJ);

                response(true, 'Event Added Successfully', $new_data);
            } else {
                response(false, 'Failed to Add Event', null, [
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

    public function updateEvent($id) {
        parse_str(file_get_contents('php://input'), $_PUT);

        $arrcheckpost = array(
            'user_id' => '', 
            'categories_id' => '', 
            'event_name' => '', 
            'poster' => '',
            'date' => '',
            'location' => '',
            'max_quota' => '',
            'status' => '',
            'description' => '',
            'start_time' => '',
            'end_time' => '',
        );
        
        $count = count(array_intersect_key($_PUT, $arrcheckpost));
        
        if ($count == count($arrcheckpost)) {
            $query = 'UPDATE event SET user_id = ?, categories_id = ?, event_name = ?, poster = ?, date = ?, location = ?, max_quota = ?, status = ?, description = ?, start_time = ?, end_time = ? WHERE event_id = ?';
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute([
                $_PUT['user_id'], 
                $_PUT['categories_id'], 
                $_PUT['event_name'], 
                $_PUT['poster'],
                $_PUT['date'],
                $_PUT['location'],
                $_PUT['max_quota'],
                $_PUT['status'],
                $_PUT['description'],
                $_PUT['start_time'],
                $_PUT['end_time'],
                $id
            ])) {
                $query = "SELECT * FROM event WHERE event_id = ?";
                $result_stmt = $this->conn->prepare($query);
                $result_stmt->execute([$id]);
                $updated_data = $result_stmt->fetch(PDO::FETCH_OBJ);

                response(true, 'Event Updated Successfully', $updated_data);
            } else {
                response(false, 'Failed to Update Event', null, [
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

    public function deleteEvent($id) {
        $stmt = $this->conn->prepare('DELETE FROM event WHERE event_id = ?');

        if ($stmt->execute([$id])) {
            response(true, 'Event Deleted Successfully');
        } else {
            response(false, 'Failed to Delete Event', null, [
                'code' => 500,
                'message' => 'Internal server error: ' . $this->conn->errorInfo()[2]
            ]);
        }
    }
}
?>

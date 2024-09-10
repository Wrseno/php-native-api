<?php

require_once "../database/config.php";
require_once "../helpers/ResponseHelper.php";

class MahasiswaController {
    private $conn;

    public function __construct($conn) {
        if (!$conn) {
            response(false, 'Database connection failed');
        }
        $this->conn = $conn;
    }

    public function getAllMahasiswa() {
        $query = "SELECT * FROM tbl_mahasiswa";
        $data = array();

        $stmt = $this->conn->query($query);

        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
                $data[] = $row;
            }
            response(true, 'Get List Mahasiswa Successfully', $data);
        } else {
            response(false, 'Get List Mahasiswa Failure', null, [
                'code' => 500,
                'message' => 'Internal server error: ' . $this->conn->errorInfo()[2]
            ]);
        }
    }

    public function getMahasiswaById($id = 0) {
        if ($id != 0) {
            $query = "SELECT * FROM tbl_mahasiswa WHERE id = ? LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$id]);

            if ($stmt->rowCount() > 0) {
                $data = $stmt->fetch(PDO::FETCH_OBJ);
                response(true, 'Get Mahasiswa Successfully', $data);
            } else {
                response(false, 'Mahasiswa not found', null, [
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

    public function createMahasiswa() {
        $arrcheckpost = array(
            'nim' => '', 
            'nama' => '', 
            'jk' => '', 
            'alamat' => '',
            'jurusan' => ''
        );

        $count = count(array_intersect_key($_POST, $arrcheckpost));
        
        if ($count == count($arrcheckpost)) {
            $query = "INSERT INTO tbl_mahasiswa (nim, nama, jk, alamat, jurusan) VALUES (?,?,?,?,?)";
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute([
                $_POST['nim'], 
                $_POST['nama'], 
                $_POST['jk'], 
                $_POST['alamat'], 
                $_POST['jurusan']
            ])) {
                $insert_id = $this->conn->lastInsertId();

                $result_stmt = $this->conn->prepare("SELECT * FROM tbl_mahasiswa WHERE id = ?");
                $result_stmt->execute([$insert_id]);
                $new_data = $result_stmt->fetch(PDO::FETCH_OBJ);

                response(true, 'Mahasiswa Added Successfully', $new_data);
            } else {
                response(false, 'Failed to Add Mahasiswa', null, [
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

    public function updateMahasiswa($id) {
        parse_str(file_get_contents('php://input'), $_PUT);

        $arrcheckpost = array(
            'nim' => '',
            'nama' => '',
            'jk' => '',
            'alamat' => '',
            'jurusan' => ''
        );
        
        $count = count(array_intersect_key($_PUT, $arrcheckpost));
        
        if ($count == count($arrcheckpost)) {
            $query = 'UPDATE tbl_mahasiswa SET nim = ?, nama = ?, jk = ?, alamat = ?, jurusan = ? WHERE id = ?';
            $stmt = $this->conn->prepare($query);

            if ($stmt->execute([
                $_PUT['nim'],
                $_PUT['nama'],
                $_PUT['jk'],
                $_PUT['alamat'],
                $_PUT['jurusan'],
                $id
            ])) {
                $query = "SELECT * FROM tbl_mahasiswa WHERE id = ?";
                $result_stmt = $this->conn->prepare($query);
                $result_stmt->execute([$id]);
                $updated_data = $result_stmt->fetch(PDO::FETCH_OBJ);

                response(true, 'Mahasiswa Updated Successfully', $updated_data);
            } else {
                response(false, 'Failed to Update Mahasiswa', null, [
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

    public function deleteMahasiswa($id) {
        $stmt = $this->conn->prepare('DELETE FROM tbl_mahasiswa WHERE id = ?');

        if ($stmt->execute([$id])) {
            response(true, 'Mahasiswa Deleted Successfully');
        } else {
            response(false, 'Failed to Delete Mahasiswa', null, [
                'code' => 500,
                'message' => 'Internal server error: ' . $this->conn->errorInfo()[2]
            ]);
        }
    }
}
?>

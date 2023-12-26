<?php

class Users
{
    private $con;

    public function __construct($db)
    {
        $this->con = $db;
    }

    public function getDetailUser($table, $id)
    {
        try {
            if (!empty($id)) {
                $stmt = $this->con->prepare("SELECT id, full_name, email, phone_number FROM $table WHERE id = ?");
                
                $stmt->bind_param('i', $id);
                
                $stmt->execute();
                
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $data = [
                        'status' => 200,
                        'message' => 'Single record fetch successfully',
                        'data'  => $row,
                    ];
                    header("HTTP/1.0 200 OK");
                } else {
                    $data = [
                        'status' => 404,
                        'message' => 'No User found',
                    ];
                    header("HTTP/1.0 404 No User found");
                }

                $stmt->close();
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'User Id is required',
                ];
                header("HTTP/1.0 404 User Id is required");
            }

            return json_encode($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
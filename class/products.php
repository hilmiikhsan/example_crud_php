<?php

class Products
{
    private $con;

    public function __construct($db)
    {
        $this->con = $db;
    }

    public function createProduct($table, $post)
    {
        if (!empty($post)) {
            $requiredFields = ['name', 'price', 'stock', 'description'];
            foreach ($requiredFields as $field) {
                if (empty($post[$field])) {
                    $data = [
                        'status' => 422,
                        'message' => 'All fields are required',
                    ];
                    header("HTTP/1.0 422 Unprocessable Entity");
                    return json_encode($data);
                }
            }

            if ($post['price'] <= 0 || $post['stock'] <= 0) {
                $data = [
                    'status' => 422,
                    'message' => 'Price or stock must be greater than 0',
                ];
                header("HTTP/1.0 422 Unprocessable Entity");
                return json_encode($data);
            }

            $query = "INSERT INTO $table (name, price, stock, description) VALUES(?, ?, ?, ?)";

            $stmt = $this->con->prepare($query);
            $stmt->bind_param(
                'sdss', 
                $post['name'],
                $post['price'],
                $post['stock'],
                $post['description']
            );

            $result = $stmt->execute();
            $stmt->close();

            if ($result) {
                $data = [
                    'status' => 201,
                    'message' => 'Product created successfully.',
                ];
                header("HTTP/1.0 201 OK");
            } else {
                $data = [
                    'status' => 500,
                    'message' => 'Internal server error',
                ];
                header("HTTP/1.0 500 Internal Server Error");
            }
        } else {
            $data = [
                'status' => 500,
                'message' => 'Something went wrong',
            ];
            header("HTTP/1.0 500 Internal Server Error");
        }

        return json_encode($data);
    }

    public function getListProduct($table)
    {
        $query  = "SELECT id, name, price, stock, description FROM $table";
        
        $result = $this->con->query($query);
        
        if ($result) {
            
            if ($result->num_rows > 0) {
                
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                foreach ($rows as &$row) {
                    $row['id'] = (int)$row['id'];
                    $row['price'] = (int)$row['price'];
                    $row['stock'] = (int)$row['stock'];
                }
                
                $data = [
                    'status' => 200,
                    'message' => 'Product record fetch successfully',
                    'data'  => $rows,
                ];
                
                header("HTTP/1.0 20 OK");
            }else{
                $data = [
                    'status' => 400,
                    'message' => 'No Product found',
                ];
                header("HTTP/1.0 400 No Product found");
            }
        }else{
            $data = [
                'status' => 500,
                'message' => 'Internal server error',
            ];
            header("HTTP/1.0 500 Internal server error");
        }
        return json_encode($data);
    }

    public function getDetailProduct($table, $id)
    {
        try {
            if (!empty($id)) {
                $stmt = $this->con->prepare("SELECT id, name, price, stock, description FROM $table WHERE id = ?");
                
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
                        'message' => 'No Product found',
                    ];
                    header("HTTP/1.0 404 No Product found");
                }

                $stmt->close();
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'Product Id is required',
                ];
                header("HTTP/1.0 404 Product Id is required");
            }

            return json_encode($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateProduct($table, $post, $getId) {
        if (!empty($post)) {
            if (isset($getId['id']) && !empty($getId['id'])) {
                $id = $getId['id'];
                $name = $post['name'];
                $price = $post['price'];
                $stock = $post['stock'];
                $description = $post['description'];

                if ($price <= 0 || $stock < 0) {
                    $data = [
                        'status' => 400,
                        'message' => 'Invalid price or stock value',
                    ];
                    header("HTTP/1.0 400 Bad Request");
                    return json_encode($data);
                }
    
                $stmt = $this->con->prepare("UPDATE $table SET name=?, price=?, stock=?, description=? WHERE id=?");
    
                if ($stmt) {
                    $stmt->bind_param('ssdsi', $name, $price, $stock, $description, $id);
    
                    $result = $stmt->execute();
    
                    if ($result) {
                        $data = [
                            'status' => 200,
                            'message' => 'Product updated successfully.',
                        ];
                        header("HTTP/1.0 200 OK");
                    } else {
                        $data = [
                            'status' => 500,
                            'message' => 'Internal Server Error',
                        ];
                        header("HTTP/1.0 500 Internal Server Error");
                    }
    
                    $stmt->close();
                } else {
                    $data = [
                        'status' => 500,
                        'message' => 'Internal Server Error',
                    ];
                    header("HTTP/1.0 500 Internal Server Error");
                }
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'Product Id is not found',
                ];
                header("HTTP/1.0 404 Not Found");
            }
        } else {
            $data = [
                'status' => 400,
                'message' => 'Invalid Request',
            ];
            header("HTTP/1.0 400 Bad Request");
        }
    
        return json_encode($data);
    }

    public function deleteProductId($table, $id)
    {
        try {
            if (!empty($id)) {
                $checkStmt = $this->con->prepare("SELECT id FROM $table WHERE id = ? LIMIT 1");
                $checkStmt->bind_param('i', $id);
                $checkStmt->execute();
                $checkStmt->store_result();

                if ($checkStmt->num_rows > 0) {
                    $deleteStmt = $this->con->prepare("DELETE FROM $table WHERE id = ? LIMIT 1");
                    $deleteStmt->bind_param('i', $id);
                    $result = $deleteStmt->execute();

                    if ($result) {
                        $data = [
                            'status' => 200,
                            'message' => 'Record deleted successfully',
                        ];
                        header("HTTP/1.0 200 OK");
                    } else {
                        $data = [
                            'status' => 500,
                            'message' => 'Internal server error',
                        ];
                        header("HTTP/1.0 500 Internal server error");
                    }

                    $deleteStmt->close();
                } else {
                    $data = [
                        'status' => 404,
                        'message' => 'Product not found',
                    ];
                    header("HTTP/1.0 404 Not found");
                }

                $checkStmt->close();
            } else {
                $data = [
                    'status' => 404,
                    'message' => 'Product Id is required',
                ];
                header("HTTP/1.0 404 Not found");
            }

            return json_encode($data);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
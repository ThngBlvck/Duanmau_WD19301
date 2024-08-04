<?php

namespace App\Models;

class Product extends BaseModel
{
    protected $table = 'products';
    protected $id = 'id';

    public function getAllProduct()
    {

        return $this->getAll();
    }
    public function getOneProduct($id)
    {
        return $this->getOne($id);
    }

    public function createProduct($data)
    {
        return $this->create($data);
    }
    public function updateProduct($id, $data)
    {
        return $this->update($id, $data);
    }

    public function deleteProduct($id)
    {
        return $this->delete($id);
    }
    public function getAllProductByStatus()
    {
        $result = [];
        try {
        // $sql = "SELECT * FROM $this->table WHERE status=" . self::STATUS_ENABLE;
        $sql = "SELECT products.* FROM products INNER JOIN categories ON products.category_id=categories.id WHERE products.status= " .  self::STATUS_ENABLE . " AND categories.status=" . self::STATUS_ENABLE;
        $result = $this->_conn->MySQLi()->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    } catch (\Throwable $th) {
        error_log('Lỗi khi hiển thị tất cả dữ liệu: ' . $th->getMessage());
        return $result;
    }
    }

    public function getOneProductByName(string $name)
    {
        return $this->getOneByName($name);
    }

    public function getAllProductJoinCategorty()
    {
        $result = [];
        try {
            // $sql = "SELECT * FROM $this->table";
            $sql = "SELECT products.*,categories.name AS category_name 
            FROM products 
            INNER JOIN categories 
            ON products.category_id=category_id;";
            $result = $this->_conn->MySQLi()->query($sql);
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            error_log('Lỗi khi hiển thị tất cả dữ liệu: ' . $th->getMessage());
            return $result;
        }
    }
    public function getAllProductByCategoryAndStatus(int $id)
    {
        $result = [];
        try {
            // $sql = "SELECT * FROM $this->table WHERE $this->id=?";
            $sql = "SELECT products.*, categories.name AS category_name FROM products
            INNER JOIN categories ON products.category_id = categories.id
            WHERE products.status = " . self::STATUS_ENABLE . "
            AND categories.status = " . self::STATUS_ENABLE . "
            AND products.category_id = ?";

            $conn = $this->_conn->MySQLi();
            $stmt = $conn->prepare($sql);

            $stmt->bind_param('i', $id);
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (\Throwable $th) {
            error_log('Lỗi khi hiển thị chi tiết dữ liệu: ' . $th->getMessage());
            return $result;
        }
    }
}

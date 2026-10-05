<?php
require_once __DIR__ . "/../core/db_class.php";

class Product extends Database
{

    public function addBrand($name)
    {
        $sql = "INSERT INTO brands (brand_name) VALUES (?)";
        return $this->execute($sql, [$name]);
    }

    public function getAllBrands()
    {
        $sql = "SELECT * FROM brands ORDER BY brand_name ASC";
        return $this->fetchAll($sql);
    }

    public function getBrandById($id)
    {
        $sql = "SELECT * FROM brands WHERE brand_id = ?";
        return $this->fetchOne($sql, [$id]);
    }

    public function updateBrand($id, $name)
    {
        $sql = "UPDATE brands SET brand_name = ? WHERE brand_id = ?";
        return $this->execute($sql, [$name, $id]);
    }


    public function addCategory($name)
    {
        $sql = "INSERT INTO categories (cat_name) VALUES (?)";
        return $this->execute($sql, [$name]);
    }

    public function getAllCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
        return $this->fetchAll($sql);
    }

    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM categories WHERE cat_id = ?";
        return $this->fetchOne($sql, [$id]);
    }

    public function updateCategory($id, $name)
    {
        $sql = "UPDATE categories SET cat_name = ? WHERE cat_id = ?";
        return $this->execute($sql, [$name, $id]);
    }
}
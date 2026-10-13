<?php
require_once __DIR__ . "/../classes/ProductClass.php";

class ProductController
{
    private $product;

    public function __construct()
    {
        $this->product = new Product();
    }

   
    public function addBrand($name)  
    { 
        return $this->product->addBrand($name); 
    }
    public function getAllBrands()          
    { 
        return $this->product->getAllBrands(); 
        }
    public function getBrandById($id)       
    { 
        return $this->product->getBrandById($id);
    }
    public function updateBrand($id, $n)    
    { 
        return $this->product->updateBrand($id, $n); 
    }

    
    public function addCategory($name)      
    { 
        return $this->product->addCategory($name); 
    }
    public function getAllCategories()      
    { 
        return $this->product->getAllCategories(); 
    }
    public function getCategoryById($id)    
    { 
        return $this->product->getCategoryById($id); 
    }
    public function updateCategory($id, $n) 
    { 
        return $this->product->updateCategory($id, $n); 
    }


    public function addProduct($cat, $brand, $title, $price, $desc, $image, $keywords)
    {
        return $this->product->addProduct($cat, $brand, $title, $price, $desc, $image, $keywords);
    }

    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords)
    {
        return $this->product->updateProduct($id, $cat, $brand, $title, $price, $desc, $image, $keywords);
    }

    public function getProductById($id)      { return $this->product->getProductById($id); }
    public function getAllProducts()         { return $this->product->getAllProducts(); }
    public function getFeaturedProducts($n = 6) { return $this->product->getFeaturedProducts($n); }
    public function getProductsByCategory($id)  { return $this->product->getProductsByCategory($id); }
    public function getProductsByBrand($id)     { return $this->product->getProductsByBrand($id); }
    public function searchProducts($q)          { return $this->product->searchProducts($q); }
}
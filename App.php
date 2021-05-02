<?php

class App
{
    public static function main()
    {
        $products = self::getProducts();
        self::renderData($products);
    }

    private static function getProducts()
    {
        require_once "products.php";
        return $products;
    }

    private static function renderData($products)
    {
        echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}

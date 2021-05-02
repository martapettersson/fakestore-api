<?php

class App
{
    private static $limit = 20;
    private static $errors = array();

    public static function main()
    {
        try {
            self::$limit = self::getLimit() ?? self::$limit;
        } catch (Exception $error) {
            array_push(self::$errors, array("Limit" => $error->getMessage()));
        }

        $products = self::getProducts();

        // Om det uppstår fel ska felmeddelande renderas, annars renderas produkter
        if (self::$errors) self::renderProducts(self::$errors);
        else self::renderProducts($products);
    }

    /**
     * En klassmetod för att hämta limit
     */
    private static function getLimit()
    {
        $limit = self::getQuery("limit");

        if ($limit && (!is_numeric($limit) || $limit > 20)) {
            throw new Exception("Limit must be a number between 1-20!");
        } else if ($limit == 0 && strlen($limit)) {
            throw new Exception("Limit must be more than 0. Try between 1-20!");
        }

        return $limit;
    }

    /**
     * En klassmetod för att hämta och filtrera query
     */
    private static function getQuery($var)
    {
        if (isset($_GET[$var])) {
            $query = filter_var($_GET[$var], FILTER_SANITIZE_STRING);
            return $query;
        }
    }

    /**
     * En klassmetod för att hämta produkter
     */
    private static function getProducts()
    {
        require_once "products.php";
        shuffle($products);

        $productsArr = array();

        for ($i = 0; $i < self::$limit; $i++) {
            array_push($productsArr, $products[$i]);
        }
        return $productsArr;
    }

    /**
     * En klassmetod för att rendera produkter
     */
    private static function renderProducts($products)
    {
        echo json_encode($products, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}

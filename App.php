<?php

class App
{
    private static $limit = 20;
    private static $category = null;
    private static $errors = array();

    public static function main()
    {
        try {
            self::$limit = self::getLimit() ?? self::$limit;
        } catch (Exception $error) {
            array_push(self::$errors, array("Limit" => $error->getMessage()));
        }

        try {
            self::$category = self::getCategory();
        } catch (Exception $error) {
            array_push(self::$errors, array("Category" => $error->getMessage()));
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
     * En klassmetod för att hämta category
     */
    private static function getCategory()
    {
        $category = self::getQuery("category");

        if ($category && !($category == "bass" || $category == "drums" || $category == "guitars" || $category == "keyboards")) {
            throw new Exception("Woops! Category must be bass, drums, guitars or keyboards!");
        }

        return $category;
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

        // Om en limit är satt ska det antalet produkter returneras, annars 20 st
        for ($i = 0; $i < self::$limit; $i++) {
            array_push($productsArr, $products[$i]);
        }

        // Om speciell category är vald ska endast dessa produkter returneras
        if (self::$category) {
            $categoryArr = array();

            foreach ($productsArr as $product) {
                if (self::$category == $product["category"]) {
                    array_push($categoryArr, $product);
                }
            }
            return $categoryArr;
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

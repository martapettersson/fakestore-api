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

        // Om det uppstår fel ska felmeddelande renderas, annars renderas produkter
        if (self::$errors) self::renderData(self::$errors);
        else {
            $products = self::getProducts();
            self::renderData($products);
        }
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

        $productsArr = array();

        // Om man satt limit och valt en kategori ska endast dessa produkter returneras
        if (self::$category && self::$limit) {
            $categoryArr = array();
            foreach ($products as $product) {
                if ($product["category"] == self::$category)
                    array_push($categoryArr, $product);
            }
            shuffle($categoryArr);

            // $limit ska vara vald limit via GET-request om den inte överskrider max antal produkter i vald kategori
            // annars ska $limit vara antal produkter i vald kategori
            $numberOfProducts = count($categoryArr);
            $limit = self::$limit <= $numberOfProducts ? self::$limit : $numberOfProducts;

            for ($i = 0; $i < $limit; $i++) {
                array_push($productsArr, $categoryArr[$i]);
            }
            return $productsArr;
        }

        // Om ingen kategori är vald men en limit är satt ska det antalet produkter returneras, annars 20 st
        if (!self::$category && self::$limit) {
            shuffle($products);
            for ($i = 0; $i < self::$limit; $i++) {
                array_push($productsArr, $products[$i]);
            }
            return $productsArr;
        }

        // Om specifik category är vald ska endast dessa produkter returneras
        if (self::$category) {
            for ($i = 0; $i < self::$limit; $i++) {
                if ($products[$i]["category"] == self::$category)
                    array_push($productsArr, $products[$i]);
            }
            return $productsArr;
        }
    }

    /**
     * En klassmetod för att rendera data
     */
    private static function renderData($data)
    {
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}

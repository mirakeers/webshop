<?php
require_once './model/Product.php';
require_once './dao/Verbinding/DatabaseFactory.php';

class ProductDAO {

    private static function getVerbinding() {
        return DatabaseFactory::getDatabase();
    }

    public static function getProducten() {
        $resultaat = self::getVerbinding()->voerSqlQueryUit("SELECT * FROM Product");
        $resultatenArray = array();
        for ($index = 0; $index < $resultaat->num_rows; $index++) {
            $databaseRij = $resultaat->fetch_array();
            $nieuw = self::converteerRijNaarObject($databaseRij);
            $resultatenArray[$index] = $nieuw;
        }
        return $resultatenArray;
    }
    
    public static function getProductenByProperties($productCategorieIds, $minPrijsInclBtw, $maxPrijsInclBtw, $kleurIds) {
        $queryarray = array(); 
        $querystring = "SELECT p.* FROM Product p JOIN ProductVariatie pv ON p.productId = pv.productId";
        $querystring .= " WHERE p.productCategorieId IN (";
        foreach($productCategorieIds as $i => $categorieId) {
            array_push($queryarray, $categorieId);
            if($i < count($productCategorieIds) - 1){
                $querystring .= "?,";
            } else {
                $querystring .= "?)";
            }
        }
        $querystring .= " AND prijsExclBtw * (1 + btwPercentage) BETWEEN ? AND ?";
        array_push($queryarray, $minPrijsInclBtw, $maxPrijsInclBtw);
        
        $querystring.= " AND pv.kleurId IN(";
        foreach($kleurIds as $i => $kleurId) {
            array_push($queryarray, $kleurId);
            if($i < count($kleurIds) - 1){
                $querystring .= "?,";
            } else {
                $querystring .= "?)";
            }
        }
        $resultaat = self::getVerbinding()->voerSqlQueryUit($querystring, $queryarray);
        $resultatenArray = array();
        for ($index = 0; $index < $resultaat->num_rows; $index++) {
            $databaseRij = $resultaat->fetch_array();
            $nieuw = self::converteerRijNaarObject($databaseRij);
            $resultatenArray[$index] = $nieuw;
        }
        $productIds = array();
        foreach($resultatenArray as $i => $product) {
            if(in_array($product->getProductId(), $productIds)) {
                unset($resultatenArray[$i]);
            } else {
                array_push($productIds, $product->getProductId());
            }
        }
        return $resultatenArray;
    }

    public static function getProductById($id) {
        $resultaat = self::getVerbinding()->voerSqlQueryUit("SELECT * FROM Product WHERE productId=?", array($id));
        if ($resultaat->num_rows == 1) {
            $databaseRij = $resultaat->fetch_array();
            return self::converteerRijNaarObject($databaseRij);
        } else {
            //Er is waarschijnlijk iets mis gegaan
            return false;
        }
    }

    public static function insert($product) {
        return self::getVerbinding()->voerSqlQueryUit("INSERT INTO Product(naam, beschrijving, prijsExclBtw, btwPercentage, productCategorieId) VALUES ('?','?','?','?','?')", array($product->getNaam(), $product->getBeschrijving(), $product->getPrijsExclBtw(), $product->getBtwPercentage(), $product->getProductCategorieId()));
    }

    public static function deleteById($id) {
        return self::getVerbinding()->voerSqlQueryUit("DELETE FROM Product where productId=?", array($id));
    }

    public static function delete($product) {
        return self::deleteById($product->getProductId());
    }

    public static function update($product) {
        return self::getVerbinding()->voerSqlQueryUit("UPDATE Product SET naam='?', beschrijving='?', prijsExclBtw='?', btwPercentage='?', productCategorieId='?' WHERE productId=?", array($product->getNaam(), $product->getBeschrijving(), $product->getPrijsExclBtw(), $product->getBtwPercentage(), $product->getProductCategorieId(), $product->getProductId()));
    }

    protected static function converteerRijNaarObject($databaseRij) {
        return new Product($databaseRij['productId'], $databaseRij['naam'], $databaseRij['beschrijving'], $databaseRij['prijsExclBtw'], $databaseRij['btwPercentage'], $databaseRij['productCategorieId']);
    }

}

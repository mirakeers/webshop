<?php
    require_once './model/ProductVariatie.php';
    require_once './dao/FotoDAO.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ProductVariatieDAO
 *
 * @author Myra
 */
class ProductVariatieStubDAO {
    static function getProductVariaties() {
        $pv1 = new ProductVariatie(1, 1, "white", FotoDAO::getFotosByProductVariatieId(1));
        $pv2 = new ProductVariatie(2, 1, "kaki", FotoDAO::getFotosByProductVariatieId(2));
        $pv3 = new ProductVariatie(3, 1, "gray", FotoDAO::getFotosByProductVariatieId(3));
        $pv4 = new ProductVariatie(4, 2, "pink", FotoDAO::getFotosByProductVariatieId(4));
        $pv5 = new ProductVariatie(5, 2, "white", FotoDAO::getFotosByProductVariatieId(5));
        $pv6 = new ProductVariatie(6, 3, "blue", FotoDAO::getFotosByProductVariatieId(6));
        
        $resultaat = array($pv1, $pv2, $pv3, $pv4, $pv5, $pv6);
        return $resultaat;
    }
    static function getProductVariatiesByProductId($productId) {
        $productvariaties = self::getProductVariaties();
        $resultaat = array();
        foreach ($productvariaties as $index => $val) {
            if($val->getProductId() == $productId) {
                array_push($resultaat, $val);
            }
        }
        return $resultaat;
    }
}

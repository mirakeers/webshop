<?php
    require_once './model/Foto.php';

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FotoDAO
 *
 * @author Myra
 */
class FotoStubDAO {
    static function getFotos() {
        $foto1 = new Foto(1, 1, "img/products/1Shirt/white/full/product.jpg", "img/products/1Shirt/white/thumbs/product.jpg", "product");
        $foto2 = new Foto(2, 1, "img/products/1Shirt/white/full/model.jpg", "img/products/1Shirt/white/thumbs/model.jpg", "model");
        $foto3 = new Foto(3, 2, "img/products/1Shirt/kaki/full/product.jpg", "img/products/1Shirt/kaki/thumbs/product.jpg", "product");
        $foto4 = new Foto(4, 2, "img/products/1Shirt/kaki/full/model.jpg", "img/products/1Shirt/kaki/thumbs/model.jpg", "model");
        $foto5 = new Foto(5, 3, "img/products/1Shirt/gray/full/product.jpg", "img/products/1Shirt/gray/thumbs/product.jpg", "product");
        $foto6 = new Foto(6, 3, "img/products/1Shirt/gray/full/model.jpg", "img/products/1Shirt/gray/thumbs/model.jpg", "model");
        $foto7 = new Foto(7, 4, "img/products/2Hemd/pink/full/product.jpg", "img/products/2Hemd/pink/thumbs/product.jpg", "product");
        $foto8 = new Foto(8, 4, "img/products/2Hemd/pink/full/model.jpg", "img/products/2Hemd/pink/thumbs/model.jpg", "model");
        $foto9 = new Foto(9, 5, "img/products/2Hemd/white/full/product.jpg", "img/products/2Hemd/white/thumbs/product.jpg", "product");
        $foto10 = new Foto(10, 5, "img/products/2Hemd/white/full/model.jpg", "img/products/2Hemd/white/thumbs/model.jpg", "model");
        $foto11 = new Foto(11, 6, "img/products/3Pants/blue/full/product.jpg", "img/products/3Pants/blue/thumbs/product.jpg", "product");
        $foto12 = new Foto(12, 6, "img/products/3Pants/blue/full/model.jpg", "img/products/3Pants/white/thumbs/model.jpg", "model");
        
        $resultaat = array($foto1, $foto2, $foto3, $foto4, $foto5, $foto6, $foto7, $foto8, $foto9, $foto10, $foto11, $foto12);
        return $resultaat;
    }
    static function getFotosByProductVariatieId($productVariatieId) {
        $fotos = self::getFotos();
        $resultaat = array();
        foreach ($fotos as $index => $val) {
            if($val->getProductVariatieId() == $productVariatieId) {
                array_push($resultaat, $val);
            }
        }
        return $resultaat;
    }
    static function getFotoByProductVariatieIdAndPrioriteit($productVariatieId, $priotiteit) {
        $fotos = self::getFotos();
        $resultaat = array();
        foreach ($fotos as $index => $val) {
            if($val->getProductVariatieId() == $productVariatieId) {
                array_push($resultaat, $val);
            }
        }
        return $resultaat;
    }
}

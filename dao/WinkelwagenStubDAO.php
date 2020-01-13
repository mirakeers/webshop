<?php
    require_once './model/WinkelwagenItem.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of WinkelwagenDAO
 *
 * @author Myra
 */
class WinkelwagenStubDAO {
    public static function getWinkelwagenItems() {
        $item1 = new WinkelwagenItem(4, 3);
        $item2 = new WinkelwagenItem(10, 1);
        $resultaat = array($item1, $item2);
        return $resultaat;
    }
    public static function getTotaalAantalItems() {
        $resultaat = 0;
        $items = self::getWinkelwagenItems();
        foreach ($items as $item) {
           $resultaat += $item->getAantal();
        }
        return $resultaat;
    }
    
    public static function getTotaalPrijsExclBtw() {
        $resultaat = 0;
        $items = self::getWinkelwagenItems();
        foreach ($items as $item) {
           $resultaat += $item->getTotaalPrijsExclBtw();
        }
        return $resultaat;
    }
    public static function getTotaalBtw() {
        $resultaat = 0;
        $items = self::getWinkelwagenItems();
        foreach ($items as $item) {
           $resultaat += $item->getTotaalBtw();
        }
        return $resultaat;
    }
    public static function getTotaalPrijsInclBtw() {
        $resultaat = 0;
        $items = self::getWinkelwagenItems();
        foreach ($items as $item) {
           $resultaat += $item->getTotaalPrijsInclBtw();
        }
        return $resultaat;
    }
}

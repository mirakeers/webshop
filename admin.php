<?php
    require_once './dao/ProductDAO.php';
    require_once './dao/ProductVariatieDAO.php';
    require_once './dao/FotoDAO.php';
    require_once './dao/KleurDAO.php';
    require_once './dao/WinkelwagenDAO.php';
    require_once './model/Product.php';
    require_once './model/ProductVariatie.php';
    require_once './model/Foto.php';
    require_once './model/Kleur.php';
    
    if (isFormulierIngediend()) {
        $productId = $_POST["productId"];
        $fotos = FotoDAO::getFotosByProductId($productId);
        $productvariaties = ProductVariatieDAO::getProductVariatiesByProductId($productId);
        $product = ProductDAO::getProductById($productId);
        foreach ($fotos as $foto) { FotoDAO::delete($foto); }
        foreach ($productvariaties as $productvariatie) { ProductVariatieDAO::delete($productvariatie); }
        ProductDAO::deleteById($productId);
        //delete foto-directory
        rrmdir('img/products/' . $productId);
    }
    function rrmdir($dir) { 
        if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (is_dir($dir."/".$object))
                        rrmdir($dir."/".$object);
                    else
                        unlink($dir."/".$object); 
                } 
            }
            rmdir($dir); 
        } 
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Salt och Peppar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/detail.js"></script>
</head>

    
<body class="admin">
    <header>
        <div>
            <a id="toadmin" href=index.php>To webshop</a>
            <h1 class="col-lg-8 col-lg-offset-2">Salt&amp;Peppar | admin zone</h1>
        </div>
    </header>
    <main>
        <div class="container">            
            <div class="row buttons-top">
                <a class="button inverted" href=producttoevoegen.php>+ New Product</a>
                <a class="button inverted" href=kleurtoevoegen.php>+ New Color</a>
                <a class="button inverted" href=categorietoevoegen.php>+ New Category</a>
            </div>
            
            <h2>Product list</h2>
            
            
            <?php
                $products = ProductDAO::getProducten();
                foreach ($products as $product) {
            ?>
            <div class="row productitem">
                <div class="col-lg-6">
                    <h3>#<?php echo str_pad($product->getProductId(), 4, '0', STR_PAD_LEFT)?>: <?php echo $product->getNaam()?></h3>
                    <h4 class="price">€<?php echo number_format($product->getPrijsInclBtw(), 2)?> incl. BTW</h4>
                    <h5>€<?php echo number_format($product->getPrijsExclBtw(), 2)?> excl. BTW</h5>
                    <p><?php echo mb_convert_encoding($product->getBeschrijving(), "UTF-8")?></p>
                    <form action="admin.php " method ="post">
                        <input type="hidden" name="productId" value="<?php echo $product->getProductId()?>"/>
                        <input type="hidden" name="postcheck" value="true"/>
                        <input type="submit" class="button" value="Delete Item">
                    </form>
                </div>       
                <div class="col-lg-6">
                    <div class="imagebox">
                        <?php
                        $fotos = FotoDAO::getFotosByProductId($product->getProductId());
                        foreach ($fotos as $foto) {
                        ?>
                        <img src="<?php echo $foto->getLocatieFull();?>">
                        <?php
                        }
                        ?>
                    </div>
                    
                    <h4>Available colors:</h4>
                    <ul class="colors">
                        <?php
                        $productvariaties = ProductVariatieDAO::getProductVariatiesByProductId($product->getProductId());
                        foreach ($productvariaties as $productvariatie) {
                            $kleur = KleurDAO::getKleurById($productvariatie->getKleurId());
                        ?>
                            <li title="<?php echo $kleur->getNaam()?>"><div class="colordot" style="background-color: <?php echo $kleur->getKleurcode()?>"></div><?php echo ucwords($kleur->getNaam())?></li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <?php 
                }
            ?>
        </div>
        
    </main>
        

</body>
</html>



<?php
function isFormulierIngediend() {
    return isset($_POST['postcheck']);
}
?>
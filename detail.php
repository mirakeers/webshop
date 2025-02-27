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
    $productId = $_GET['productId'];
    $startKleurId = $_GET['kleurId'];
    $product = ProductDAO::getProductById($productId);
    $productvariaties = ProductVariatieDAO::getProductVariatiesByProductId($productId);
    $startProductVariatieId = $productvariaties[0]->getProductVariatieId();

    if (isFormulierIngediend()) {
        WinkelwagenDAO::vermeerderAantalItems($_POST["productVariatieId"], $_POST["aantal"]);
        header("Location:detail.php?productId=" . $productId . '&kleurId=' . ProductVariatieDAO::getProductVariatieById($_POST["productVariatieId"])->getKleurId());
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
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/detail.js"></script>    
    <script src="js/winkelwagenicon.js"></script>
</head>

    
<body>
    <header>
        <div>
            <a id="toadmin" href=admin.php>To admin zone</a>
            <h1>Salt&amp;Peppar</h1>
            <div id="cart">
                <div class="dropdown">
                    
                    <?php include './winkelwagenpreview.php' ?>
                    
                    </div>
                <div class="amount"><?php echo $aantalItems?></div>
                <a href=winkelwagen.php class="overlay" title="<?php if($aantalItems >0) {echo $aantalItems . " items in cart";} else {echo "No items in shopping cart";}?>">Shopping cart</a>
            </div>
        </div>
    </header>
    <main>
        <div class="container cf">
            <div>
                <a class="button btn-mb" href=index.php#productid<?php echo $productId?>>&larr; Back to overview</a>
            </div>
            <div class="imagethumbnails">
                <?php
                    foreach ($productvariaties  as $pvIndex => $productvariatie) {
                        $kleur = KleurDAO::getKleurById($productvariatie->getKleurId());
                        if($productvariatie->getKleurId() == $startKleurId) {
                            $startProductVariatieId = $productvariatie->getProductVariatieId();
                        }
                ?>
                        <div data-color="<?php echo $kleur->getNaam() ?>" <?php if($kleur->getKleurId() == $startKleurId) {echo "class='visible'";}?>>
                    <?php
                        $fotos = FotoDAO::getFotosByProductVariatieId($productvariatie->getProductVariatieId());
                        foreach ($fotos  as $fotoIndex => $foto) {
                    ?>
                            <a href=# <?php if($fotoIndex == 0) {echo "class='current'";}?>><img src="<?php echo $foto->getLocatieThumb()?>" data-fotoId="<?php echo $foto->getFotoId()?>"></a>
                        <?php
                        }
                        ?>
                        </div>
                    <?php
                    }
                    ?>
            </div>
            <div class="product-img">
                <?php
                    foreach ($productvariaties  as $pvIndex => $productvariatie) {
                        $kleur = KleurDAO::getKleurById($productvariatie->getKleurId());
                        $fotos = FotoDAO::getFotosByProductVariatieId($productvariatie->getProductVariatieId());
                        foreach ($fotos  as $fotoIndex => $foto) {
                ?>
                            <img src="<?php echo $foto->getLocatieFull()?>" <?php if($kleur->getKleurId() == $startKleurId && $fotoIndex == 0) {echo "class='visible'";}?> data-color="<?php echo $kleur->getNaam()?>" data-fotoId="<?php echo $foto->getFotoId()?>">
                        <?php
                        }
                        ?>
                    <?php
                    }
                    ?>
            </div>
            <div class="product-information">
                <h2><?php echo $product->getNaam()?></h2>
                <h3 class="price-big">€<?php echo number_format($product->getPrijsInclBtw(), 2)?></h3>
                <h4 class="price-small">€<?php echo number_format($product->getPrijsExclBtw(), 2)?> excl. BTW</h4>
                <p><?php echo mb_convert_encoding($product->getBeschrijving(), "UTF-8");?></p>
                <h5>Color</h5>
                <ul class="colors">
                    <?php
                    foreach ($productvariaties  as $pvIndex => $productvariatie) {
                        $kleur = KleurDAO::getKleurById($productvariatie->getKleurId());
                    ?>
                    <li <?php if($kleur->getKleurId() == $startKleurId) {echo "class='current'";}?> title="<?php echo $kleur->getNaam();?>" data-productVariatieId="<?php echo $productvariatie->getProductVariatieId()?>"><a href="#" style="background-color: <?php echo $kleur->getKleurcode();?>"><?php echo ucwords($kleur->getNaam())?></a></li>
                    <?php
                    }
                    ?>
                </ul>
                <h5>Amount</h5>
                <div class="counter">
                    <a class="less" href=#>-</a>
                    <div class="number">1</div>
                    <a class="more" href=#>+</a>
                </div>
                <form id="addToCart" action="detail.php?productId=<?php echo $productId?>" method ="post">
                        <input type="hidden" name="postcheck" value="true"/>
                        <input type="hidden" id="productVariatieId" name="productVariatieId" value="<?php echo $startProductVariatieId?>"/>
                        <input type="hidden" id="aantal" name="aantal" value="1"/>
                        <input type=submit class="button" value="Add to cart">
                </form>
            </div>
        </div>
        
    </main>
    <div id="itemAddedDialog" title="Item added">
        Your item was successfully added to your cart!
    </div>

</body>
</html>
<?php
    function isFormulierIngediend() {
        return isset($_POST['postcheck']);
    }
?>
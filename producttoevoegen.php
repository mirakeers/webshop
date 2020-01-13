<?php
    require_once './dao/ProductDAO.php';
    require_once './dao/ProductCategorieDAO.php';
    require_once './dao/ProductVariatieDAO.php';
    require_once './dao/KleurDAO.php';
    require_once './dao/FotoDAO.php';
    require_once './model/Product.php';
    require_once './model/ProductCategorie.php';
    require_once './model/ProductVariatie.php';
    require_once './model/Kleur.php';
    require_once './model/Foto.php';
    include_once './validatiebibliotheek.php';
    $toonFormulier = true;
    
    $productNaamVal = $prijsExclBtwVal = $btwPercentageVal = $beschrijvingVal = '';
    $productNaamErr = $prijsExclBtwErr = $btwPercentageErr = $beschrijvingErr = $kleurIdsErr = '';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Salt och Peppar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery-3.1.1.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/producttoevoegen.js"></script>
    <script src="js/discard.js"></script>
</head>

    
<body class="admin">
    <header>
        <div>
            <a id="toadmin" href=index.php>To webshop</a>
            <h1 class="col-lg-8 col-lg-offset-2">Salt&amp;Peppar | admin zone</h1>
        </div>
    </header>
    <main>
        <div class="container cf producttoevoegen">
            
        <?php
        if (isFormulierIngediend()) {
            
            $productNaamErr = errRequiredVeld("naam");
            $prijsExclBtwErr = errVeldIsNumeriek("prijsExclBtw");
            $btwPercentageErr = errVeldIsPercentage("btwPercentage");
            $beschrijvingErr = errRequiredVeld("beschrijving");
            $kleurIdsErr = errAtLeastOneSelected("kleurIds");
            
            if(isFormulierValid()) {
                $toonFormulier = false;
                
                $naam = $_POST['naam'];
                $beschrijving = $_POST['beschrijving'];
                $prijsExclBtw = $_POST['prijsExclBtw'];
                $btwPercentage = $_POST['btwPercentage'];
                $productCategorieId = $_POST['productCategorieId'];
                
                ProductDAO::insert(new Product(0, $naam, $beschrijving, $prijsExclBtw, $btwPercentage, $productCategorieId));
                $productId = end(ProductDAO::getProducten())->getProductId();
                
                $kleurIds = $_POST['kleurIds'];
                $aantalfotos = 0;
                $kleurString = "";
                        
                foreach ($kleurIds as $index => $kleurId) {
                    $kleur = KleurDAO::getKleurById($kleurId);
                    
                    ProductVariatieDAO::insert(new ProductVariatie(0, $productId, $kleurId));
                    $variatieId = end(ProductVariatieDAO::getProductVariaties())->getProductVariatieId();
                    
                    $pathToFullFolder = 'img/products/' . $productId . '/' . $kleur->getNaam() . '/full';
                    $pathToThumbsFolder = 'img/products/' . $productId . '/' . $kleur->getNaam() . '/thumbs';
                    
                    if (!is_dir($pathToFullFolder)) {
                        mkdir($pathToFullFolder, 0777, true);
                    }
                    if (!is_dir($pathToThumbsFolder)) {
                        mkdir($pathToThumbsFolder, 0777, true);
                    }
                    $fulls_target_dir = $pathToFullFolder . '/';
                    $thumbs_target_dir = $pathToThumbsFolder . '/';
                    
                    
                    $counter = 1;
                    while(isset($_FILES['v' . ($index + 1) . '-file' . $counter])) {
                        $uploadedImage = $_FILES['v' . ($index + 1) . '-file' . $counter];
                        
                        if($uploadedImage['type'] == 'image/jpeg') {
                            $img = imagecreatefromjpeg($uploadedImage['tmp_name']);    
                        } else if($uploadedImage['type'] == 'image/png') {
                            $img = imagecreatefrompng($uploadedImage['tmp_name']);
                        } else if($uploadedImage['type'] == 'image/gif') {
                            $img = imagecreatefromgif($uploadedImage['tmp_name']);
                        }
                        list($width, $height) = getimagesize($uploadedImage['tmp_name']); 
                        $thumbnailImage = imagecreatetruecolor($width, $width * 1.1); 
                        
                        $thumb_target_file = $thumbs_target_dir . 'picture' . $counter . '.' . pathinfo($uploadedImage['name'], PATHINFO_EXTENSION);

                        imagecopyresampled($thumbnailImage, $img, 0, 0, 0, 0, $width, $width*1.1, $width, $width*1.1); 
                        imagejpeg($thumbnailImage, $thumb_target_file, 100);
                        
                        $full_target_file = $fulls_target_dir . 'picture' . $counter . '.' . pathinfo($uploadedImage['name'], PATHINFO_EXTENSION);
                        move_uploaded_file($uploadedImage['tmp_name'], $full_target_file);
                        
                        $fotoLabelId = $_POST['v' . ($index+1) . '-fotoLabelId' . $counter];
                        
                        FotoDAO::insert(new Foto(0, $full_target_file, $thumb_target_file, $variatieId, $fotoLabelId, $counter));
                        $aantalfotos++;
                        $counter++;
                    }
                                 
                    $kleurString .= ucwords(KleurDAO::getKleurById($kleurId)->getNaam());
                    if($index < count($kleurIds) - 1) {
                        $kleurString .= ", ";
                    }
                }
            ?>
                <div class="text-center">
                    <h3>The product is added to the shop's database and is now available for sale.</h3>
                    <p>A summary of the new product:</p>
                    <table class="table text-left">
                        <tr><th><strong>Name of the product:</strong></th><td><?php echo $naam?></td></tr>
                        <tr><th><strong>Price, BTW exclusive:</strong></th><td>€<?php echo number_format($prijsExclBtw, 2)?></td></tr>
                        <tr><th><strong>BTW Percentage:</strong></th><td><?php echo number_format($btwPercentage, 2)?></td></tr>
                        <tr><th><strong>Category:</strong></th><td><?php echo ucwords(ProductCategorieDAO::getProductCategorieById($productCategorieId)->getNaam())?></td></tr>
                        <tr><th><strong>Description:</strong></th><td><?php echo $beschrijving?></td></tr>
                        <tr><th><strong>Colors:</strong></th><td><?php echo $kleurString?></td></tr>
                        <tr><th><strong>Photos:</strong></th><td><?php echo $aantalfotos?> photos added</td></tr>
                    </table>
                    <a class="button inverted btn-mb" href=admin.php>&larr; Back to admin page</a>
                </div>
        <?php
            } else {
                $productNaamVal = getVeldWaarde("naam");
                $prijsExclBtwVal = getVeldWaarde("prijsExclBtw");
                $btwPercentageVal = getVeldWaarde("btwPercentage");
                $beschrijvingVal = getVeldWaarde("beschrijving");
            }
        }
        ?>    
        <?php
        if ($toonFormulier) {
        ?>
            <div class="row">
                <h2 class="text-center">Add new Product</h2>
            </div>
            
            <form action="producttoevoegen.php" method="POST" enctype="multipart/form-data">
                
                
                
                <div class="step <?php if(!isFormulierIngediend() || !isStep1Valid()) {echo 'visible';}?>" id="step1">
                    <h3 class="text-center">Step 1: General information</h3>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="naam">Product name</label>
                                    <input type="text" name="naam" id="naam" class="validation-notempty <?php if(isFormulierIngediend()) { if(empty($productNaamErr)) { echo 'valid';} else { echo 'notvalid';}}?>" value="<?php echo $productNaamVal ?>">
                                    <div class="errorMessage <?php if(!empty($productNaamErr)) { echo 'visible';}?>">
                                        <div class="staartje"></div>
                                        <div class="message"><?php echo $productNaamErr ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="prijsExclBtw">Price (BTW exclusive)</label>
                                    <input type="text" name="prijsExclBtw" id="prijsExclBtw"  class="validation-notempty validation-numeric <?php if(isFormulierIngediend()) { if(empty($prijsExclBtwErr)) { echo 'valid';} else { echo 'notvalid';}}?>" value="<?php echo $prijsExclBtwVal ?>">
                                    <div class="errorMessage <?php if(!empty($prijsExclBtwErr)) { echo 'visible';}?>">
                                        <div class="staartje"></div>
                                        <div class="message"><?php echo $prijsExclBtwErr ?></div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <label for="btwPercentage">BTW percentage (e.g. 0.21) </label>
                                    <input type="text" name="btwPercentage" id="btwPercentage" class="validation-notempty validation-numeric validation-percentage <?php if(isFormulierIngediend()) { if(empty($btwPercentageErr)) { echo 'valid';} else { echo 'notvalid';}}?>" value="<?php echo $btwPercentageVal ?>">
                                    <div class="errorMessage <?php if(!empty($btwPercentageErr)) { echo 'visible';}?>">
                                        <div class="staartje"></div>
                                        <div class="message"><?php echo $btwPercentageErr ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label for="productCategorieId">Product category</label>
                                    <a href=# class="refresh refresh-productCategories"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Refresh</a>
                                    <select name="productCategorieId" id="productCategorieId">
                                        <?php  include './get_productcategoriesAsOptions.php'; ?>
                                    </select>
                                    <a id="trigger-categorieToevoegenDialog" href=# class="button small pull-right">Add new category...</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <label for="beschrijving">Description</label>
                            <textarea name="beschrijving" id="beschrijving" rows="10" cols="50" class="<?php if(isFormulierIngediend()) { if(empty($beschrijvingErr)) { echo 'valid';} else { echo 'notvalid';}}?> validation-notempty"><?php echo $beschrijvingVal ?></textarea>
                            <div class="errorMessage <?php if(!empty($beschrijvingErr)) { echo 'visible';}?>">
                                <div class="staartje"></div>
                                <div class="message"><?php echo $beschrijvingErr ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top: 3em;">
                        <div class="col-lg-12 text-center">
                            <a href=# class="button inverted discard">Discard</a>
                            <a href=# class="button inverted nextstep disabled">Ready, to step 2</a>
                        </div>
                    </div>
                </div>
                
                
                <div class="step <?php if(isFormulierIngediend() && isStep1Valid() && !isStep2Valid()) {echo 'visible';}?>" id="step2">
                    <h3 class="text-center">Step 2: Color variations</h3>
                    <div class="row">
                        <div class="col-lg-6 col-lg-offset-3 variation">
                            <h4>Which colors will this product be available in?</h4>
                            <a href=# class="refresh refresh-kleuren"><span class="glyphicon glyphicon-refresh" aria-hidden="true"></span> Refresh</a>
                            <fieldset id="kleuren" class=" validation-1OrMoreSelected <?php if(isFormulierIngediend()) { echo 'notvalid';}?>">
                                <?php  include './get_kleurenAsCheckboxes.php'; ?>
                            </fieldset>
                            <div class="errorMessage <?php if(!empty($kleurIdsErr)) { echo 'visible';}?>">
                                <div class="staartje"></div>
                                <div class="message">Please select at least 1 color variation.</div>
                            </div>
                            <a href=# id="trigger-kleurToevoegenDialog" class="button small pull-right">Add new color...</a>
                        </div>
                        <div class="col-lg-6 col-lg-offset-3" style="padding:0">
                            
                        </div>
                    </div>
                    <div class="row" style="margin-top: 3em;">
                        <div class="col-lg-12 text-center">
                            <a href=# class="button inverted previousstep">Back</a>
                            <a href=# class="button inverted discard">Discard</a>
                            <a href=# class="button inverted nextstep disabled">Ready, to step 3</a>
                        </div>
                    </div>
                </div>
                
                <div class="step" id="step3">
                    <h3 class="text-center">Step 3: Add photos</h3>
                    <div id="variationlist">
                        <div class="row">
                        </div>
                    </div>
                    <div class="row" style="margin-top: 3em;">
                        <div class="col-lg-12 text-center">
                            <a href=# class="button inverted previousstep">Back</a>
                            <a href=# class="button inverted discard">Discard</a>
                            <input type="hidden" name="postcheck" value="true"/>
                            <input type="submit" class="button inverted nextstep disabled" style="margin-left: 20px;" value="Ready, add product">
                        </div>
                    </div>
                </div>
                
            </form>
        <?php
        }
        ?>
        </div>
    </main>
    
    
    <div id="categorieToevoegenDialog" title="Add new category">
        <div style="text-align:center">
            <label for="categorieNaam">Category name</label><br>
            <input type="text" id="categorieNaam">
        </div>
    </div>
    <div id="kleurToevoegenDialog" title="Add new color">
        <div style="text-align:center">
            <label for="kleurNaam">Color name</label><br>
            <input type="text" name="kleurNaam"  id="kleurNaam" value="<?php echo $kleurNaamVal; ?>"><br>
            <label for="kleurCode">Pick a color</label><br>
            <input type="color" name="kleurCode"  id="kleurCode" value="#FF0000">
        </div>
    </div>

</body>
</html>
<?php
function isFormulierValid() {
    return isStep1Valid()&&isStep2Valid();
}
function isStep1Valid() {
    global $productNaamErr, $prijsExclBtwErr, $btwPercentageErr, $beschrijvingErr;
    $allErr = $productNaamErr . $prijsExclBtwErr . $btwPercentageErr . $beschrijvingErr;
    if (empty($allErr)) {
        return true;
    } else {
        return false;
    }
}
function isStep2Valid() {
    global $kleurIdsErr;
    if(empty($kleurIdsErr)) {
        return true;
    } else {
        return false;
    }
}
function isFormulierIngediend() {
    return isset($_POST['postcheck']);
}
?>
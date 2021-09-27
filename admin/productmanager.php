<?php
require_once('../includes/config.php');
require_once('../includes/mysqli-connect.php');
require_once('../includes/functions.php');
require_once('../includes/sessioncheck.php');

$hasError = false; 
$messages = array();

// Delete from the cart

if( isset($_GET['delete']) ){
	$deleteID = $_GET['delete'];
}

// DB

$query_products = "SELECT * FROM product";
$res = mysqli_query($connection, $query_products);
$products = mysqli_fetch_all($res, MYSQLI_ASSOC);
$products_number = 0;

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="../images/favicon.png">
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <title>Admin - Productmanager</title>
</head>
<body>
<div class="content_container">

    <!-- NAVIGATION NAV -->

    <div>
        <a href="../index.php"><img class="logo" src="../images/logo.png" alt="logo_limmat"></a>
    </div>

    <nav>
        <ul class="navigation">
            <li><a href="" class="nav_active">product manager</a></li>
            <li><a href="product_edit.php">hinzufügen</a></li>
            <li id="admin_login"><a href="../includes/logout.php" class="" >Logout</a></li>

        </ul>
    </nav>

    <div class="header_title">
        <img src="../images/titles/admin.png" alt="">
    </div>

    <!-- PRODUCT LIST -->

    <section>
        <div class="products_container">
            <?php if(count($products)  >0 ){ ?>
                <?php foreach($products as $product){ ?>
                    <div class="items">
                        <img src="../<?=IMAGEFOLDER?>/product/<?=$product['product_image']?>" alt="limmat_product_<?=$product['product_name']?>">
                        <h4><?php echo $product['product_name'];?></h4>
                        <p class="product_desc"><?=$product['product_desc']?></p>
                        <div class="line"></div>
                        <div class="kauf_container">
                        <a href="productmanager.php?delete=<?=$product['IDproduct']?>" class="kauf_button">löschen</a>
                        <span class="product_price">CHF <?=$product['product_price']?></span>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
    </section>


</div>


<!-- FOOTER -->

<section class="footer">
	<div class="footer_container"></div>
</section>

</body>
</html>
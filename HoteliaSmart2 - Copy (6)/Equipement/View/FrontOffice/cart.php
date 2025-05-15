<?php
session_start();
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/../../Controller/equipementController.php');
require_once(__DIR__ . '/../../Controller/commandeController.php');

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $response = array('success' => false);
    
    if ($_POST['action'] === 'add') {
        $equipId = $_POST['equipId'];
        if (!in_array($equipId, $_SESSION['cart'])) {
            $_SESSION['cart'][] = $equipId;
            $response['success'] = true;
            $response['message'] = 'Equipment added to cart';
        } else {
            $response['message'] = 'Equipment already in cart';
        }
    } elseif ($_POST['action'] === 'remove') {
        $equipId = $_POST['equipId'];
        $key = array_search($equipId, $_SESSION['cart']);
        if ($key !== false) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            $response['success'] = true;
            $response['message'] = 'Equipment removed from cart';
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$equipementC = new equipementController();
$cartItems = array();
foreach ($_SESSION['cart'] as $equipId) {
    $equipment = $equipementC->showEquipement($equipId);
    if ($equipment) {
        $cartItems[] = $equipment;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!--font-family-->
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <title>Shopping Cart - Hotelia Smart</title>
    <link rel="shortcut icon" type="image/icon" href="assets/HS.png"/>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/bootsnav.css">
    <link rel="stylesheet" href="assets/css/stylefront3.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <style>
        .cart-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        .cart-header {
            margin-bottom: 40px;
            text-align: center;
            position: relative;
        }
        .back-arrow {
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: #003087;
            font-size: 24px;
            text-decoration: none;
        }
        .back-arrow:hover {
            color: #004abc;
        }
        .cart-header h2 {
            color: #003087;
            font-size: 36px;
            margin-bottom: 15px;
        }
        .cart-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 25px;
        }
        .cart-table th {
            color: #003087;
            padding: 15px;
            border-bottom: 2px solid #eee;
        }
        .cart-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        .cart-total {
            margin-top: 30px;
            text-align: right;
            font-size: 18px;
            color: #003087;
        }
        .checkout-btn {
            background-color: #003087;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .checkout-btn:hover {
            background-color: #004abc;
        }
        .empty-cart {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .empty-cart i {
            color: #003087;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <section class="cart-section">
        <div class="container">
            <div class="cart-header">
                <a href="store.php" class="back-arrow"><i class="fas fa-arrow-left"></i></a>
                <h2>Shopping Cart</h2>
            </div>
            <div class="cart-table">
                <?php if (empty($cartItems)): ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart fa-3x"></i>
                        <h3>Your cart is empty</h3>
                        <p>Add some items to your cart to proceed with checkout</p>
                        <button onclick="window.location.href='store.php'" class="checkout-btn">Continue Shopping</button>
                    </div>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Equipment</th>
                                <th>Type</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $total = 0;
                            foreach ($cartItems as $item): 
                                $total += $item['prix'];
                            ?>
                            <tr>
                                <td>
                                    <?php if(!empty($item['image'])): ?>
                                        <img src="assets/images/equipements/<?php echo $item['image']; ?>" alt="<?php echo $item['nom']; ?>" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <i class="fas fa-box fa-3x"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $item['nom']; ?></td>
                                <td><?php echo $item['type']; ?></td>
                                <td><?php echo $item['prix']; ?> TND</td>
                                <td>
                                    <i class="fas fa-trash" style="color: #dc3545; cursor: pointer;" onclick="removeFromCart(<?php echo $item['reference']; ?>)"></i>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="cart-total">
                        <h4>Total: <?php echo $total; ?> TND</h4>
                        <button onclick="window.location.href='checkout.php'" class="checkout-btn">Proceed to Checkout</button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/bootsnav.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
    function removeFromCart(equipId) {
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: {
                action: 'remove',
                equipId: equipId
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    }
    </script>
</body>
</html>
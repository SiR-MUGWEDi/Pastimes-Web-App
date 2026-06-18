<?php

session_start();

include 'includes/DBConn.php';

include 'includes/ShoppingCart.php';


if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit();

}


$cart = new ShoppingCart();


if (

    $_SERVER['REQUEST_METHOD'] == 'POST'

    &&

    isset($_POST['update_cart'])

    &&

    isset($_POST['qty'])

)

{

    foreach($_POST['qty'] as $id => $qty)

    {

        $id = (int)$id;

        $qty = (int)$qty;


        // Prevent negative values

        if($qty < 0)

        {

            $qty = 0;

        }


        // Remove item if quantity is zero

        if($qty == 0)

        {

            $cart->removeItem($id);

        }

        else

        {

            $cart->updateQuantity(

                $id,

                $qty

            );

        }

    }

}


/* Return user to dashboard */

header(

"Location: user_dashboard.php"

);

exit();

?>
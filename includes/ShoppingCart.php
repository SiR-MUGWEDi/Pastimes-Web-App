<?php
class ShoppingCart {
    private $cart;

    public function __construct() {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        $this->cart = &$_SESSION['cart'];
    }

    public function addItem($productId, $quantity = 1) {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId] += $quantity;
        } else {
            $this->cart[$productId] = $quantity;
        }
    }

    public function removeItem($productId) {
        unset($this->cart[$productId]);
    }

    public function updateQuantity($productId, $quantity) {
        if ($quantity <= 0) {
            $this->removeItem($productId);
        } else {
            $this->cart[$productId] = $quantity;
        }
    }

    public function getItems() {
        global $conn;
        $items = [];
        if (empty($this->cart)) return $items;
        $ids = array_keys($this->cart);
        $idsStr = implode(',', $ids);
        $res = $conn->query("SELECT * FROM tblClothes WHERE item_id IN ($idsStr)");
        while ($row = $res->fetch_assoc()) {
            $row['quantity'] = $this->cart[$row['item_id']];
            $row['subtotal'] = $row['quantity'] * $row['price'];
            $items[] = $row;
        }
        return $items;
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->getItems() as $item) {
            $total += $item['subtotal'];
        }
        return $total;
    }

    public function emptyCart() {
        $_SESSION['cart'] = [];
        $this->cart = &$_SESSION['cart'];
    }

    public function checkout($userId) {
        global $conn;
        $conn->begin_transaction();
        try {
            $total = $this->getTotal();
            $orderNumber = 'ORD-' . time() . '-' . rand(100, 999);
            $stmt = $conn->prepare("INSERT INTO tblOrder (user_id, order_number, total_amount, status) VALUES (?, ?, ?, 'pending')");
            $stmt->bind_param("isd", $userId, $orderNumber, $total);
            $stmt->execute();
            $orderId = $conn->insert_id;

            foreach ($this->getItems() as $item) {
                $productId = $item['item_id'];
                $qty = $item['quantity'];
                $price = $item['price'];
                $subtotal = $qty * $price;

                $stmt = $conn->prepare("INSERT INTO tblOrderItem (order_id, product_id, quantity, price_at_time, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iiidd", $orderId, $productId, $qty, $price, $subtotal);
                $stmt->execute();

                // Decrement stock (if quantity column exists)
                $stmt = $conn->prepare("UPDATE tblClothes SET quantity = quantity - ? WHERE item_id = ?");
                $stmt->bind_param("ii", $qty, $productId);
                $stmt->execute();
            }

            $conn->commit();
            $this->emptyCart();
            return ['orderNumber' => $orderNumber, 'sessionId' => session_id()];
        } catch (Exception $e) {
            $conn->rollback();
            throw $e;
        }
    }
}
?>
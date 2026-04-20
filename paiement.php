<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';

$conn = getDbConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address'])) {
    $cart_data = json_decode($_POST['cart_data'], true);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);

    // Validation
    $errors = [];
    if (empty($address)) {
        $errors[] = "L'adresse est obligatoire.";
    }
    if (empty($phone)) {
        $errors[] = "Le téléphone est obligatoire.";
    }
    if (empty($cart_data) || !is_array($cart_data)) {
        $errors[] = "Panier invalide.";
    }

    if (empty($errors)) {
        // Calculate total
        $total = 0;
        foreach ($cart_data as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Check stock availability
        $stock_errors = [];
        foreach ($cart_data as $item) {
            $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->bind_param("i", $item['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $product = $result->fetch_assoc();
            if (!$product || $product['stock'] < $item['quantity']) {
                $stock_errors[] = "Stock insuffisant pour " . $item['name'];
            }
            $stmt->close();
        }

        if (empty($stock_errors)) {
            // Start transaction
            $conn->autocommit(false);

            try {
                // Insert order
                $stmt = $conn->prepare("INSERT INTO orders (user_id, total, address, phone, status) VALUES (?, ?, ?, ?, 'confirmed')");
                $stmt->bind_param("idss", $_SESSION['user_id'], $total, $address, $phone);
                $stmt->execute();
                $order_id = $conn->insert_id;
                $stmt->close();

                // Insert order items
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
                foreach ($cart_data as $item) {
                    $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
                    $stmt->execute();
                }
                $stmt->close();

                // Update stock
                $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                foreach ($cart_data as $item) {
                    $stmt->bind_param("ii", $item['quantity'], $item['id']);
                    $stmt->execute();
                }
                $stmt->close();

                $conn->commit();

                // Clear cart and redirect to confirmation
                echo "<script>localStorage.removeItem('shopkamer_cart');</script>";
                header("Location: confirmation.php?order_id=" . $order_id);
                exit;

            } catch (Exception $e) {
                $conn->rollback();
                $errors[] = "Erreur lors du traitement de la commande.";
            } finally {
                $conn->autocommit(true);
            }
        } else {
            $errors = array_merge($errors, $stock_errors);
        }
    }
}

// Get cart data
$cart_data = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_data'])) {
    $cart_data = json_decode($_POST['cart_data'], true);
} elseif (isset($_SESSION['temp_cart'])) {
    $cart_data = $_SESSION['temp_cart'];
}

// Store cart in session temporarily
$_SESSION['temp_cart'] = $cart_data;

// Calculate total
$total = 0;
foreach ($cart_data as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopKamer - Paiement</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/panier.css">
    <style>
        .payment-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
        }
        .order-summary {
            background: #1e1e1e;
            border: 1px solid #2a2a2a;
            border-radius: 18px;
            padding: 2rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.45);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .order-summary .panier-table {
            display: inline-block;
            width: auto;
            margin: 0 auto;
        }
        .order-summary .panier-table table {
            margin: 0 auto;
            width: auto;
        }
        .order-summary .panier-table th,
        .order-summary .panier-table td {
            text-align: center;
        }
        .order-summary .panier-table td:not(:first-child),
        .order-summary .panier-table th:not(:first-child) {
            text-align: center;
        }
        .form-section {
            background: #1e1e1e;
            border: 1px solid #2a2a2a;
            border-radius: 18px;
            padding: 2rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.45);
        }        
        .form-section h3,
        .order-summary h3 {
            color: #e8c97e;
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #f0ece4;
            font-weight: 500;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            background: #161616;
            color: #f0ece4;
            border: 1px solid #2a2a2a;
            padding: 0.75rem;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.2s ease;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: #e8c97e;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        .errors {
            background: rgba(220, 53, 69, 0.1);
            color: #ff6b7d;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }
        .panier-table {
            width: 100%;
            border-collapse: collapse;
            color: #f0ece4;
            margin-top: 1rem;
        }
        .panier-table th {
            text-align: left;
            padding: 0.85rem 0.5rem;
            color: #e8c97e;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-bottom: 1px solid #2a2a2a;
        }
        .panier-table th:not(:first-child) {
            text-align: right;
        }
        .panier-table td {
            padding: 1rem 0.5rem;
            vertical-align: middle;
            border-bottom: 1px solid #2a2a2a;
            font-size: 0.95rem;
        }
        .panier-table td:not(:first-child) {
            text-align: right;
        }
        .panier-table tfoot td {
            border-top: 2px solid #2a2a2a;
            font-weight: 700;
            color: #e8c97e;
        }
        @media (max-width: 768px) {
            .payment-form {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="section-title">
            <div>
                <h2>Paiement</h2>
                <p>Finalisez votre commande</p>
            </div>
        </section>

        <div class="payment-form">
            <!-- Formulaire de livraison -->
            <div class="form-section">
                <h3>Informations de livraison</h3>
                <?php if (!empty($errors)): ?>
                    <div class="errors">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="cart_data" value="<?php echo htmlspecialchars(json_encode($cart_data)); ?>">

                    <div class="form-group">
                        <label for="address">Adresse de livraison *</label>
                        <textarea id="address" name="address" required><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="phone">Téléphone *</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
                    </div>

                    <button type="submit" class="btn" style="width: 100%;">Confirmer la commande</button>
                </form>
            </div>

            <!-- Récapitulatif de commande -->
            <div class="order-summary">
                <h3>Récapitulatif de commande</h3>
                <div class="panier-table" style="margin-top: 1rem;">
                    <table>
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Qté</th>
                                <th>Prix</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_data as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo number_format($item['price'], 0, ',', ' ') . ' FCFA'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2"><strong>Total</strong></td>
                                <td><strong><?php echo number_format($total, 0, ',', ' ') . ' FCFA'; ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
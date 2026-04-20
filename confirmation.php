<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/db.php';

$conn = getDbConnection();

$order = null;
if (isset($_GET['order_id'])) {
    $order_id = (int)$_GET['order_id'];

    // Get order details
    $stmt = $conn->prepare("
        SELECT o.*, u.name, u.email
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if ($order) {
        // Get order items
        $stmt = $conn->prepare("
            SELECT oi.*, p.name, p.image
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $order_items = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
}

if (!$order) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopKamer - Confirmation de commande</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/panier.css">
    <style>
        .confirmation-container {
            max-width: 800px;
            margin: 0 auto;
            text-align: center;
        }
        .confirmation-header {
            background: linear-gradient(135deg, #2a2a2a 0%, #1e1e1e 100%);
            color: #f0ece4;
            padding: 2rem;
            border-radius: 18px;
            margin-bottom: 2rem;
            border: 1px solid #2a2a2a;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.45);
        }
        .order-number {
            font-size: 2rem;
            font-weight: bold;
            margin: 1rem 0;
            color: #e8c97e;
        }
        .order-details {
            background: #1e1e1e;
            border: 1px solid #2a2a2a;
            border-radius: 18px;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: left;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.45);
        }
        .order-details h3 {
            color: #e8c97e;
            font-size: 1.6rem;
            margin-bottom: 1.5rem;
        }
        .order-details .order-details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .order-details .detail-block {
            background: rgba(255, 255, 255, 0.02);
            padding: 1rem;
            border: 1px solid #2a2a2a;
            border-radius: 12px;
        }
        .order-details strong {
            color: #f0ece4;
            display: block;
            margin-bottom: 0.5rem;
        }
        .order-details p,
        .order-details .detail-block {
            color: #ccc;
        }
        .order-items {
            margin-top: 1.5rem;
        }
        .order-items h4 {
            color: #e8c97e;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }
        .order-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #2a2a2a;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 1rem;
            border: 1px solid #2a2a2a;
        }
        .item-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-top: 0px;
        }
        .item-name {
            font-weight: 500;
            color: #f0ece4;
            margin-bottom: 0.25rem;
        }        
        .item-details {
            color: #ccc;
            font-size: 0.9rem;
        }
        .success-icon {
            font-size: 4rem;
            color: #e8c97e;
            margin-bottom: 1rem;
        }
        .order-status {
            background: rgba(232, 201, 126, 0.1);
            color: #e8c97e;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
            display: inline-block;
            border: 1px solid rgba(232, 201, 126, 0.2);
        }
        .total-amount {
            text-align: right;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #2a2a2a;
            font-size: 1.2rem;
            font-weight: 700;
            color: #e8c97e;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <div class="confirmation-container">
            <div class="confirmation-header">
                <div class="success-icon">✓</div>
                <h2>Commande confirmée !</h2>
                <p>Merci pour votre achat. Votre commande a été enregistrée avec succès.</p>
                <div class="order-number">
                    Commande #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?>
                </div>
            </div>

            <div class="order-details">
                <h3>Détails de la commande</h3>

                <div class="order-details-grid">
                    <div class="detail-block">
                        <strong>Client :</strong>
                        <?php echo htmlspecialchars($order['name']); ?><br>
                        <?php echo htmlspecialchars($order['email']); ?>
                    </div>
                    <div class="detail-block">
                        <strong>Date :</strong>
                        <?php echo date('d/m/Y à H:i', strtotime($order['created_at'])); ?>
                    </div>
                    <div class="detail-block">
                        <strong>Adresse de livraison :</strong>
                        <?php echo nl2br(htmlspecialchars($order['address'])); ?>
                    </div>
                    <div class="detail-block">
                        <strong>Téléphone :</strong>
                        <?php echo htmlspecialchars($order['phone']); ?><br>
                        <strong>Statut :</strong>
                        <span class="order-status">
                            <?php echo $order['status'] === 'confirmed' ? 'Confirmée' : ucfirst($order['status']); ?>
                        </span>
                    </div>
                </div>

                <div class="order-items">
                    <h4>Articles commandés</h4>
                    <?php foreach ($order_items as $item): ?>
                        <div class="order-item">
                            <img src="assets/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" class="item-image">
                            <div class="item-info">
                                <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                <div class="item-details">
                                    Quantité: <?php echo $item['quantity']; ?> × <?php echo number_format($item['unit_price'], 0, ',', ' ') . ' FCFA'; ?>
                                    = <?php echo number_format($item['quantity'] * $item['unit_price'], 0, ',', ' ') . ' FCFA'; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="total-amount">
                        Total: <?php echo number_format($order['total'], 0, ',', ' ') . ' FCFA'; ?>
                    </div>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="catalogue.php" class="btn">Continuer mes achats</a>
                <a href="index.php" class="btn-secondary" style="margin-left: 1rem;">Retour à l'accueil</a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script>
        // Update cart count after clearing
        updateCartCount();
    </script>
</body>
</html>
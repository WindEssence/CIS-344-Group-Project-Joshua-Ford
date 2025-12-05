<?php
require 'connect.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { die('Invalid listing'); }

$stmt = $pdo->prepare("
  SELECT l.*, u.full_name AS landlord
  FROM listings l
  JOIN users u ON l.landlord_id = u.user_id
  WHERE l.listing_id = ?
");
$stmt->execute([$id]);
$listing = $stmt->fetch();
if (!$listing) { die('Listing not found'); }
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title><?=htmlspecialchars($listing['title'])?></title>
<link rel="stylesheet" href="style.css"></head>
<body>
  <h1><?=htmlspecialchars($listing['title'])?></h1>
  <p><?=nl2br(htmlspecialchars($listing['description']))?></p>
  <p><strong>Price:</strong> $<?=number_format($listing['price'],2)?></p>
  <p><strong>Landlord:</strong> <?=htmlspecialchars($listing['landlord'])?></p>

  <h3>Book this property</h3>
<form action="booking.php" method="post">
    <input type="hidden" name="listing_id" value="<?= (int)$listing['listing_id'] ?>">

    Tenant ID: 
    <input type="number" name="tenant_id" required><br><br>

    Your Full Name:
    <input type="text" name="contact_name" required><br><br>

    Your Phone Number:
    <input type="text" name="contact_phone" required><br><br>

    Your Email:
    <input type="email" name="contact_email" required><br><br>

    Start Date:
    <input type="date" name="start_date" required><br><br>

    End Date:
    <input type="date" name="end_date" required><br><br>

    <button type="submit">Request Booking</button>
</form>


  <p><a href="index.php">Back to listings</a></p>
</body>
</html>

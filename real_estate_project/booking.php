<?php
require 'connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}
$tenant_id  = (int)($_POST['tenant_id'] ?? 0);
$listing_id = (int)($_POST['listing_id'] ?? 0);
$start_date = $_POST['start_date'] ?? '';
$end_date   = $_POST['end_date'] ?? '';
if (!$tenant_id || !$listing_id || !$start_date || !$end_date) {
  die('Missing fields');
}
if ($start_date > $end_date) { die('Start date must be before end date'); }
try {
  $pdo->beginTransaction();
  $check = $pdo->prepare("
    SELECT COUNT(*) AS cnt
    FROM bookings
    WHERE listing_id = :lid
      AND status IN ('pending','confirmed')
      AND NOT (end_date < :start OR start_date > :end)
    FOR UPDATE
  ");
  $check->execute([':lid'=>$listing_id, ':start'=>$start_date, ':end'=>$end_date]);
  $conflict = (int)$check->fetchColumn();
  if ($conflict > 0) {
    $pdo->rollBack();
    echo "<p style='color:red;'>This listing is already booked for the chosen dates.</p>";
    echo "<p><a href='listing.php?id={$listing_id}'>Back</a></p>";
    exit;
  }
  $ins = $pdo->prepare("
    INSERT INTO bookings (tenant_id, listing_id, start_date, end_date, status)
    VALUES (:tenant, :listing, :start, :end, 'confirmed')
  ");
  $ins->execute([
    ':tenant'=>$tenant_id,
    ':listing'=>$listing_id,
    ':start'=>$start_date,
    ':end'=>$end_date
  ]);
  $bookingId = $pdo->lastInsertId();
  $pay = $pdo->prepare("
    INSERT INTO payments (booking_id, provider, provider_ref, last4, amount, status)
    VALUES (:bid, 'simulated', :ref, :last4, :amt, 'succeeded')
  ");
  $days = (new DateTime($end_date))->diff(new DateTime($start_date))->days + 1;
  $priceStmt = $pdo->prepare("SELECT price FROM listings WHERE listing_id = ?");
  $priceStmt->execute([$listing_id]);
  $price = (float)$priceStmt->fetchColumn();
  $amount = $price * max(1, $days);

  $pay->execute([
    ':bid' => $bookingId,
    ':ref' => 'SIM' . time(),
    ':last4'=> '0000',
    ':amt' => $amount
  ]);
  $pdo->commit();
  echo "<p style='color:green;'>Booking confirmed! Booking ID: {$bookingId}</p>";
  echo "<p><a href='index.php'>Back to listings</a></p>";
} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  echo "Error: " . htmlspecialchars($e->getMessage());
}

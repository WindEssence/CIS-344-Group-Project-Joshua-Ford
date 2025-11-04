<?php
require 'connect.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Listings</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Available Properties</h1>

  <?php
  $stmt = $pdo->query("
    SELECT l.listing_id, l.title, l.description, l.price, l.location, u.full_name AS landlord
    FROM listings l
    JOIN users u ON l.landlord_id = u.user_id
    ORDER BY l.created_at DESC
  ");

  while ($row = $stmt->fetch()) {
    // escape output to prevent XSS
    $title = htmlspecialchars($row['title']);
    $desc  = htmlspecialchars($row['description']);
    $loc   = htmlspecialchars($row['location']);
    $land  = htmlspecialchars($row['landlord']);
    $price = number_format($row['price'],2);
    $id    = $row['listing_id'];

    echo "<div class='card'>
            <h2>{$title}</h2>
            <p>{$desc}</p>
            <p><strong>Location:</strong> {$loc}</p>
            <p><strong>Price:</strong> \${$price}</p>
            <p><strong>Landlord:</strong> {$land}</p>
            <p><a href='listing.php?id={$id}'>View / Book</a></p>
          </div>";
  }
  ?>
</body>
</html>

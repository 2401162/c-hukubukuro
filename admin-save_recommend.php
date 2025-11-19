<?php
// save_recommend.php
require_once 'db-connect.php';


if (!empty($_POST['order'])) {
$order = json_decode($_POST['order'], true);


$pdo->query("DELETE FROM recommended");


$stmt = $pdo->prepare("INSERT INTO recommended (product_id, sort_order) VALUES (?, ?)");
foreach ($order as $i => $id) {
$stmt->execute([$id, $i + 1]);
}
}


header('Location: recommend.php');
exit;
?>
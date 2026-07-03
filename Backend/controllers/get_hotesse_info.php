<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['hotesse_nom'])) {
  echo json_encode(["status" => "error", "message" => "Non connecté"]);
  exit;
}

echo json_encode([
  "status" => "success",
  "nom" => $_SESSION['hotesse_nom']
]);

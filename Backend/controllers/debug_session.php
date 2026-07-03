<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'cookie' => $_COOKIE,
    'session' => $_SESSION,
    'session_status' => session_status(),
    'session_id' => session_id(),
]);

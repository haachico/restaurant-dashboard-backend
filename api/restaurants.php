<?php

require '../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');



   try {
    $search = $_GET['search'] ?? '';
    $location = $_GET['location'] ?? '';
    $cuisine = $_GET['cuisine'] ?? '';

    $sql = "SELECT id, name, location, cuisine FROM restaurants_data WHERE isDeleted =
    'N'";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND name LIKE ?";
        $params[] = "%$search%";
    }

    if (!empty($location)) {
        $sql .= " AND location = ?";
        $params[] = $location;
    }

    if (!empty($cuisine)) {
        $sql .= " AND cuisine = ?";
        $params[] = $cuisine;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'status_code' => 200,
        'data' => $restaurants,
        'status' => 'success'
    ];

} catch (PDOException $e) {
    $response = [
        'status_code' => 500,
        'data' => null,
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ];
}

echo json_encode($response);

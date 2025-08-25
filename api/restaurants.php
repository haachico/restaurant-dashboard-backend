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
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

    $itemsPerPage = 2;
    $offset = ($currentPage - 1) * $itemsPerPage;


    $sql = "SELECT id, name, location, cuisine FROM restaurants_data WHERE isDeleted = 'N'";
    $params = [];

    $countSql = "SELECT COUNT(*) FROM restaurants_data WHERE isDeleted = 'N'";
    $countParams = [];



    if (!empty($search)) {
        $sql .= " AND name LIKE ?";
        $countSql .= " AND name LIKE ?";
        $params[] = "%$search%";
        $countParams[] = "%$search%";
    }

    if (!empty($location)) {
        $sql .= " AND location = ?";
        $countSql .= " AND location = ?";
        $params[] = $location;
        $countParams[] = $location;
    }

    if (!empty($cuisine)) {
        $sql .= " AND cuisine = ?";
        $countSql .= " AND cuisine = ?";
        $params[] = $cuisine;
        $countParams[] = $cuisine;
    }

    $distinctSql = "SELECT DISTINCT location, cuisine FROM restaurants_data WHERE isDeleted = 'N'";
    $distinctParams = [];



    $sql .= " LIMIT $itemsPerPage OFFSET $offset";



    $countStmt = $conn->prepare($countSql);
    $countStmt->execute($countParams);
    $totalCount = $countStmt->fetchColumn();

    $distinctStmt = $conn->prepare($distinctSql);
    $distinctStmt->execute($distinctParams);
    $distinctResults = $distinctStmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [
        'status_code' => 200,
        'data' => $restaurants,
        'total_count' => (int)$totalCount,
        'distinct_locations_cuisines' => $distinctResults,
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

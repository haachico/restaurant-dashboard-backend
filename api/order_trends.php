<?php

require '../config/db.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');


try {

    $restaurant_id = $_GET['restaurant_id'] ?? '';
    $today = date('Y-m-d');
    $start_date = $_GET['start_date'] ?? $today;
    $end_date = $_GET['end_date'] ?? $today;


    $sql = "SELECT Date(order_time) as order_date,
            COUNT(id) as orders_count,
            SUM(order_amount) as daily_revenue,
            SUM(order_amount) / COUNT(id) AS average_order_value
        FROM orders 
        WHERE restaurant_id = ? AND order_time BETWEEN ? AND ?
        GROUP BY order_date
        ORDER BY order_date";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$restaurant_id, $start_date, $end_date]);

    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dailyRecords = [];

    foreach ($records as $row) {
        $dailyRecords[] = $row;
    }


    $sql = "SELECT DATE(order_time) as order_date, HOUR(order_time) as hour, COUNT(*) as order_count
        FROM orders
        WHERE restaurant_id = ? AND order_time BETWEEN ? AND ?
        GROUP BY order_date, hour
        ORDER BY order_date, order_count DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$restaurant_id, $start_date, $end_date]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $maxRevenueResSql = "SELECT r.name as restaurant_name, MAX(o.order_amount) as max_revenue
    FROM orders o
    JOIN restaurants_data r ON o.restaurant_id = r.id
    WHERE o.order_time BETWEEN ? AND ?
    GROUP BY r.name
    ORDER BY max_revenue DESC
    LIMIT 3";

    $stmt = $conn->prepare($maxRevenueResSql);
    $stmt->execute([$start_date, $end_date]);
    $maxRevenueRestaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);



    $trendy_hrs = [];
    $daily = [];

    foreach ($results as $row) {
        $date = $row['order_date'];
        $hour = $row['hour'];
        $count = $row['order_count'];

        if (!isset($daily[$date]) || $count > $daily[$date][0]['order_count']) {
            $daily[$date] = [ ['date' => $date, 'hour' => $hour, 'order_count' => $count] ];
        } elseif ($count == $daily[$date][0]['order_count']) {
            $daily[$date][] = ['date' => $date, 'hour' => $hour, 'order_count' => $count];
        }
    }

// Flatten to a single array
foreach ($daily as $peaks) {
    foreach ($peaks as $peak) {
        $trendy_hrs[] = $peak;
    }
}

    $orders['trendy_hours'] = $trendy_hrs;
    $orders['daily_records'] = $dailyRecords;
    $orders['max_revenue_restaurants'] = $maxRevenueRestaurants;

    $response = [
        'status_code' => 200,
        'data' => $orders,
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

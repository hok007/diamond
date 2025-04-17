<?php
    header('Content-Type: application/json');
    require 'db.php';

    try {
        $rows_per_page = isset($_GET['rows_per_page']) ? intval($_GET['rows_per_page']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $offset = ($page - 1) * $rows_per_page;

        $sql_paginated = "SELECT c.category_name, p.product_id, p.code, p.name, p.description, p.image, p.price
            FROM categories c INNER JOIN products p ON c.category_id = p.category_id
            ORDER BY c.category_id ASC, p.product_id ASC LIMIT ?, ?";
        $stmt = $conn->prepare($sql_paginated);
        $stmt->bind_param('ii', $offset, $rows_per_page);
        $stmt->execute();
        $result_paginated = $stmt->get_result();

        $total_rows_query = "SELECT COUNT(*) as total FROM products";
        $total_rows_result = $conn->query($total_rows_query);
        if (!$total_rows_result) {
            throw new Exception('Total rows query error: ' . $conn->error);
        }
        $total_rows = $total_rows_result->fetch_assoc()['total'];
        $total_pages = ceil($total_rows / $rows_per_page);

        $adminProducts = [];
        while ($row = $result_paginated->fetch_assoc()) {
            $adminProducts[] = $row;
        }

        $stmt->close();
        $conn->close();
        
        echo json_encode(['success' => true, 'products' => $adminProducts]);
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
?>

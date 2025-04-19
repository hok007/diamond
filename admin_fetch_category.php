<?php
    header('Content-Type: application/json');
    require 'db.php';

    try {
        $rows_per_page = isset($_GET['rows_per_page']) ? intval($_GET['rows_per_page']) : 10;
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';
        $offset = ($page - 1) * $rows_per_page;

        $sql_paginated = "SELECT c.category_id, c.category_name
            FROM categories c";
        
        $params = [];
        $types = '';
        if (!empty($search)) {
            $sql_paginated .= " WHERE c.category_name LIKE ?";
            $search_param = "%$search%";
            $params = [$search_param];
            $types = 's';
        }

        $sql_paginated .= " ORDER BY c.category_id ASC LIMIT ?, ?";
        $params[] = $offset;
        $params[] = $rows_per_page;
        $types .= 'ii';

        $stmt = $conn->prepare($sql_paginated);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result_paginated = $stmt->get_result();

        $total_rows_query = "SELECT COUNT(*) as total FROM categories c";
        if (!empty($search)) {
            $total_rows_query .= " WHERE c.category_name LIKE ?";
        }
        $stmt_total = $conn->prepare($total_rows_query);
        if (!empty($search)) {
            $stmt_total->bind_param('s', $search_param);
        }
        $stmt_total->execute();
        $total_rows_result = $stmt_total->get_result();
        $total_rows = $total_rows_result->fetch_assoc()['total'];
        $total_pages = ceil($total_rows / $rows_per_page);

        $categories = [];
        while ($row = $result_paginated->fetch_assoc()) {
            $categories[] = $row;
        }

        $stmt->close();
        $stmt_total->close();
        $conn->close();

        echo json_encode([
            'success' => true,
            'categories' => $categories,
            'total_pages' => $total_pages,
            'current_page' => $page,
            'rows_per_page' => $rows_per_page,
            'offset' => $offset,
            'total_rows' => $total_rows,
            'search' => $search
        ]);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
?>

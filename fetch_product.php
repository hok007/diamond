<?php
    require 'fetch_store.php';
    require 'db.php';

    $productLists = [];
    $sql = "SELECT c.category_name, p.code, p.name, p.description, p.image, p.price
            FROM categories c INNER JOIN products p ON c.category_id = p.category_id
            WHERE p.store_id = ?
            ORDER BY c.category_id ASC, p.product_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $storeData['store_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categoryName = $row['category_name'];

            if (!isset($productLists[$categoryName])) {
                $productLists[$categoryName] = [];
            }

            $images = json_decode($row['image'], true);
            $productLists[$categoryName][] = [
                "code" => $row['code'],
                "name" => $row['name'],
                "description" => $row['description'],
                "image" => $images,
                "price" => $row['price']
            ];
        }
    }
    
    $stmt->close();
    $conn->close();
?>

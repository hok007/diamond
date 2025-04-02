<?php
    require 'db.php';

    $productLists = [];
    $sql = "SELECT c.category_name, p.code, p.name, p.description, p.image, p.price
            FROM categories c INNER JOIN products p ON c.category_id = p.category_id 
            ORDER BY c.category_id ASC, p.product_id ASC";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categoryName = $row['category_name'];
            if (!isset($productLists[$categoryName])) {
                $productLists[$categoryName] = [];
            }
            $productLists[$categoryName][] = [
                "code" => $row['code'],
                "name" => $row['name'],
                "description" => $row['description'],
                "image" => $row['image'],
                "price" => $row['price']
            ];
        }
    }

    $conn->close();
?>

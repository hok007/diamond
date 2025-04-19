<?php
    header('Content-Type: application/json');
    require 'db.php';

    try {
        $category = $_POST['category'] ?? '';
        $code = $_POST['code'] ?? '';
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';

        if (empty($category) || empty($code) || empty($name) || empty($price)) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            exit;
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
        
            $imageName = uniqid() . '-' . date('Ymd') . '-' . basename($_FILES['image']['name']);
            $fullPath = $uploadDir . $imageName;
        
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $fullPath)) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to upload image']);
                exit;
            }
        
            $imagePath = json_encode([$fullPath]);
        } else {
            $imagePath = json_encode([]);
        }

        $stmt = $conn->prepare("
            INSERT INTO products (category_id, code, name, description, price, image, store_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $storeId = 1;
        $stmt->bind_param("isssdsi", $category, $code, $name, $description, $price, $imagePath, $storeId);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['message' => 'Product added successfully']);
        } else {
            if ($stmt->errno == 1062) {
                http_response_code(400);
                echo json_encode(['error' => 'Product code already exists']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Database error: ' . $stmt->error]);
            }
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
?>

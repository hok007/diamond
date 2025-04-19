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

        $uploadDir = 'uploads/';
        $imagePaths = [];

        if (isset($_FILES['image']) && is_array($_FILES['image']['name'])) {
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            foreach ($_FILES['image']['name'] as $index => $imgName) {
                $tmpName = $_FILES['image']['tmp_name'][$index];
                $error = $_FILES['image']['error'][$index];

                if ($error === UPLOAD_ERR_OK && is_uploaded_file($tmpName)) {
                    $uniqueName = uniqid() . '-' . basename($imgName);
                    $targetPath = $uploadDir . $uniqueName;

                    if (move_uploaded_file($tmpName, $targetPath)) {
                        $imagePaths[] = $targetPath;
                    } else {
                        error_log("Failed to move uploaded file: $tmpName");
                    }
                } else {
                    error_log("Upload error on index $index: $error");
                }
            }
        }

        $imageJson = json_encode($imagePaths);

        $stmt = $conn->prepare("
            INSERT INTO products (category_id, code, name, description, price, image, store_id)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $storeId = 1;
        $stmt->bind_param("isssdsi", $category, $code, $name, $description, $price, $imageJson, $storeId);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(['message' => 'Product added successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Database error: ' . $stmt->error]);
        }

        $stmt->close();
        $conn->close();

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
    }
?>

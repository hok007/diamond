<?php
    require 'db.php';

    $store_code = isset($_GET['store']) ? $_GET['store'] : null;

    try {
        $stmt = $conn->prepare("SELECT * FROM stores WHERE store_code = ?");
        $stmt->bind_param("s", $store_code);
        $stmt->execute();
        $result = $stmt->get_result();
        $storeData = $result->fetch_assoc();

        if (!$storeData) {
            http_response_code(404);
            exit;
        }
    } catch (Exception $e) {
        die("Database Error: " . $e->getMessage());
    }

    $conn->close();
?>

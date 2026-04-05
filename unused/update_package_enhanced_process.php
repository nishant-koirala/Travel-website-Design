<?php
include('../../database/db_connect.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: edit_package_enhanced.php?id=' . ($_GET['id'] ?? ''));
    exit();
}

$id = $_POST['id'] ?? '';

try {
    // Start transaction
    $pdo->beginTransaction();
    
    // Update basic package information
    $updateSql = "UPDATE packages SET 
        title = :title,
        price = :price,
        description = :description,
        duration_days = :duration_days,
        difficulty_level = :difficulty_level,
        accommodation_type = :accommodation_type,
        transportation = :transportation,
        includes = :includes,
        excludes = :excludes";
    
    // Add image to update if new one is uploaded
    $params = [
        'title' => $_POST['title'],
        'price' => $_POST['price'],
        'description' => $_POST['description'],
        'duration_days' => $_POST['duration_days'],
        'difficulty_level' => $_POST['difficulty_level'],
        'accommodation_type' => $_POST['accommodation_type'],
        'transportation' => $_POST['transportation'],
        'includes' => $_POST['includes'],
        'excludes' => $_POST['excludes'],
        'id' => $id
    ];
    
    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = '../../images/' . $image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $params['image'] = $image_name;
            $updateSql .= ", image = :image";
        }
    }
    
    $updateSql .= " WHERE id = :id";
    $stmt = $pdo->prepare($updateSql);
    $stmt->execute($params);
    
    // Handle itinerary updates
    if (isset($_POST['day_title'])) {
        // Delete existing itinerary for this package
        $deleteSql = "DELETE FROM itinerary_details WHERE package_id = :package_id";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute(['package_id' => $id]);
        
        // Insert new itinerary details
        $insertSql = "INSERT INTO itinerary_details (package_id, day_number, title, description, meals, activities, accommodation) VALUES (:package_id, :day_number, :title, :description, :meals, :activities, :accommodation)";
        $insertStmt = $pdo->prepare($insertSql);
        
        foreach ($_POST['day_title'] as $index => $title) {
            if (!empty(trim($title))) {
                $insertParams = [
                    'package_id' => $id,
                    'day_number' => $index + 1,
                    'title' => $title,
                    'description' => $_POST['day_description'][$index] ?? '',
                    'meals' => $_POST['day_meals'][$index] ?? '',
                    'activities' => $_POST['day_activities'][$index] ?? '',
                    'accommodation' => $_POST['day_accommodation'][$index] ?? ''
                ];
                $insertStmt->execute($insertParams);
            }
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Redirect back to edit page with success message
    header('Location: edit_package_enhanced.php?id=' . $id . '&success=1');
    exit();
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    
    $error_message = "Error updating package: " . $e->getMessage();
    
    // Redirect back with error message
    header('Location: edit_package_enhanced.php?id=' . $id . '&error=' . urlencode($error_message));
    exit();
}
?>

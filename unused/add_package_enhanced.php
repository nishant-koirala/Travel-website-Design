<?php
// Include the database connection
$filePath = '../../database/db_connect.php';
if (file_exists($filePath)) {
    include($filePath);
} else {
    die("Error: Database connection file not found at $filePath");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect all form data with proper checks
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $duration_days = $_POST['duration_days'] ?? '';
    $difficulty_level = $_POST['difficulty_level'] ?? '';
    $accommodation_type = $_POST['accommodation_type'] ?? '';
    $transportation = $_POST['transportation'] ?? '';
    $includes = $_POST['includes'] ?? '';
    $excludes = $_POST['excludes'] ?? '';
    
    // Validate required fields
    $errors = [];
    if (empty($title)) $errors[] = "Package title is required";
    if (empty($description)) $errors[] = "Package description is required";
    if (empty($price) || $price <= 0) $errors[] = "Valid price is required";
    if (empty($duration_days) || $duration_days < 1) $errors[] = "Duration must be at least 1 day";
    if (empty($difficulty_level)) $errors[] = "Difficulty level is required";
    if (empty($accommodation_type)) $errors[] = "Accommodation type is required";
    if (empty($transportation)) $errors[] = "Transportation is required";
    
    // Handle image upload
    $imageRelativePath = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $uploadDir = '../../images/';
        $imagePath = $uploadDir . $imageName;
        
        // Validate file type
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $errors[] = "Invalid image file type. Only JPG, JPEG, PNG, and GIF are allowed.";
        } else {
            // Move uploaded file to images directory
            if (move_uploaded_file($imageTmpPath, $imagePath)) {
                $imageRelativePath = $imageName;
            } else {
                $errors[] = "Failed to upload image.";
            }
        }
    } else {
        $errors[] = "Please upload a package image.";
    }
    
    // If there are no errors, insert into database
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Insert package with all new fields
            $stmt = $pdo->prepare("INSERT INTO packages (title, description, price, image, duration_days, difficulty_level, accommodation_type, transportation, includes, excludes) VALUES (:title, :description, :price, :image, :duration_days, :difficulty_level, :accommodation_type, :transportation, :includes, :excludes)");
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'price' => $price,
                'image' => $imageRelativePath,
                'duration_days' => $duration_days,
                'difficulty_level' => $difficulty_level,
                'accommodation_type' => $accommodation_type,
                'transportation' => $transportation,
                'includes' => $includes,
                'excludes' => $excludes
            ]);
            
            // Get the package ID
            $packageId = $pdo->lastInsertId();
            
            // Handle itinerary if provided
            if (isset($_POST['day_title'])) {
                // Delete any existing itinerary for this package
                $pdo->exec("DELETE FROM itinerary_details WHERE package_id = $packageId");
                
                // Insert new itinerary details
                $insertSql = "INSERT INTO itinerary_details (package_id, day_number, title, description, meals, activities, accommodation) VALUES (:package_id, :day_number, :title, :description, :meals, :activities, :accommodation)";
                $insertStmt = $pdo->prepare($insertSql);
                
                foreach ($_POST['day_title'] as $index => $dayTitle) {
                    if (!empty(trim($dayTitle))) {
                        $insertParams = [
                            'package_id' => $packageId,
                            'day_number' => $index + 1,
                            'title' => $dayTitle,
                            'description' => $_POST['day_description'][$index] ?? '',
                            'meals' => $_POST['day_meals'][$index] ?? '',
                            'activities' => $_POST['day_activities'][$index] ?? '',
                            'accommodation' => $_POST['day_accommodation'][$index] ?? ''
                        ];
                        $insertStmt->execute($insertParams);
                    }
                }
            }
            
            $pdo->commit();
            
            $success = "Package '$title' has been added successfully!";
            
        } catch (PDOException $e) {
            if (isset($pdo)) {
                $pdo->rollBack();
            }
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}

include '../component/nav_admin.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Enhanced Package - Travel Website Admin</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }
        
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            margin: 0;
            font-size: 2rem;
        }
        
        .form-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            padding: 30px;
        }
        
        .form-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .itinerary-section {
            grid-column: 1 / -1;
            background: #e8f5f4;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .itinerary-item {
            background: white;
            padding: 20px;
            border-radius: 6px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        
        .day-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .day-number {
            background: #667eea;
            color: white;
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: bold;
        }
        
        .remove-day {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .add-day-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: transform 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .form-container {
                grid-template-columns: 1fr;
            }
            
            .itinerary-section {
                grid-column: 1;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="header">
            <h1>🏔️ Add Enhanced Package</h1>
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <h3>❌ Please fix the following errors:</h3>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success">
                <h3>✅ <?php echo htmlspecialchars($success); ?></h3>
            </div>
        <?php endif; ?>
        
        <form method="post" action="add_package_enhanced.php" enctype="multipart/form-data">
            <!-- Basic Information Section -->
            <div class="form-section">
                <h3>📋 Basic Information</h3>
                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label for="title">Package Title *</label>
                            <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="price">Price per Person ($) *</label>
                            <input type="number" name="price" id="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" step="0.01" required>
                        </div>
                    </div>
                    <div>
                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea name="description" id="description" class="form-control" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="image">Package Image *</label>
                            <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Package Details Section -->
            <div class="form-section">
                <h3>🏔️ Package Details</h3>
                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label for="duration_days">Duration (Days) *</label>
                            <input type="number" name="duration_days" id="duration_days" class="form-control" value="<?php echo htmlspecialchars($duration_days); ?>" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="difficulty_level">Difficulty Level *</label>
                            <select name="difficulty_level" id="difficulty_level" class="form-control" required>
                                <option value="easy" <?php echo ($difficulty_level == 'easy') ? 'selected' : ''; ?>>Easy</option>
                                <option value="moderate" <?php echo ($difficulty_level == 'moderate') ? 'selected' : ''; ?>>Moderate</option>
                                <option value="challenging" <?php echo ($difficulty_level == 'challenging') ? 'selected' : ''; ?>>Challenging</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="form-group">
                            <label for="accommodation_type">Accommodation Type *</label>
                            <input type="text" name="accommodation_type" id="accommodation_type" class="form-control" value="<?php echo htmlspecialchars($accommodation_type); ?>" placeholder="e.g., Mountain Teahouses, 3-Star Hotels" required>
                        </div>
                        <div class="form-group">
                            <label for="transportation">Transportation *</label>
                            <input type="text" name="transportation" id="transportation" class="form-control" value="<?php echo htmlspecialchars($transportation); ?>" placeholder="e.g., Private vehicle, Domestic flight" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Inclusions/Exclusions Section -->
            <div class="form-section">
                <h3>📝 Inclusions & Exclusions</h3>
                <div class="form-grid">
                    <div>
                        <div class="form-group">
                            <label for="includes">What's Included *</label>
                            <textarea name="includes" id="includes" class="form-control" rows="3" placeholder="e.g., Experienced guide, All meals, Accommodation" required><?php echo htmlspecialchars($includes); ?></textarea>
                        </div>
                    </div>
                    <div>
                        <div class="form-group">
                            <label for="excludes">What's Excluded *</label>
                            <textarea name="excludes" id="excludes" class="form-control" rows="3" placeholder="e.g., Personal expenses, Tips, International flights" required><?php echo htmlspecialchars($excludes); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Itinerary Section -->
            <div class="itinerary-section">
                <h3>📅 Day-by-Day Itinerary</h3>
                <div id="itinerary-container">
                    <div class="itinerary-item">
                        <div class="day-header">
                            <span class="day-number">Day 1</span>
                            <button type="button" class="remove-day" onclick="removeDay(this)">✕ Remove</button>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Day Title *</label>
                                <input type="text" name="day_title[]" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Description *</label>
                                <textarea name="day_description[]" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                            <div class="form-group">
                                <label>Meals</label>
                                <input type="text" name="day_meals[]" class="form-control" placeholder="e.g., Breakfast, Lunch, Dinner">
                            </div>
                            <div class="form-group">
                                <label>Activities</label>
                                <input type="text" name="day_activities[]" class="form-control" placeholder="e.g., Forest trekking, River crossing">
                            </div>
                            <div class="form-group">
                                <label>Accommodation</label>
                                <input type="text" name="day_accommodation[]" class="form-control" placeholder="e.g., Namche Lodge">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <button type="button" class="add-day-btn" onclick="addNewDay()">➕ Add New Day</button>
                <button type="submit" class="btn-primary">🚀 Create Package</button>
            </div>
        </form>
    </div>

    <script>
        let dayCount = 1;
        
        function removeDay(button) {
            if (confirm('Are you sure you want to remove this day?')) {
                button.closest('.itinerary-item').remove();
                updateDayNumbers();
            }
        }
        
        function addNewDay() {
            dayCount++;
            const container = document.getElementById('itinerary-container');
            
            const newDay = document.createElement('div');
            newDay.className = 'itinerary-item';
            newDay.innerHTML = `
                <div class="day-header">
                    <span class="day-number">Day ${dayCount}</span>
                    <button type="button" class="remove-day" onclick="removeDay(this)">✕ Remove</button>
                </div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Day Title *</label>
                        <input type="text" name="day_title[]" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="day_description[]" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div class="form-group">
                        <label>Meals</label>
                        <input type="text" name="day_meals[]" class="form-control" placeholder="e.g., Breakfast, Lunch, Dinner">
                    </div>
                    <div class="form-group">
                        <label>Activities</label>
                        <input type="text" name="day_activities[]" class="form-control" placeholder="e.g., Forest trekking, River crossing">
                    </div>
                    <div class="form-group">
                        <label>Accommodation</label>
                        <input type="text" name="day_accommodation[]" class="form-control" placeholder="e.g., Namche Lodge">
                    </div>
                </div>
            `;
            
            container.appendChild(newDay);
        }
        
        function updateDayNumbers() {
            const days = document.querySelectorAll('.itinerary-item');
            days.forEach((day, index) => {
                const dayNumber = day.querySelector('.day-number');
                if (dayNumber) {
                    dayNumber.textContent = `Day ${index + 1}`;
                }
            });
        }
    </script>
</body>
</html>

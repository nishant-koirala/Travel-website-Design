<?php
// Direct database update for Nepali packages
include 'db_connect.php';

echo "<h2>Direct Package Update</h2>";

try {
    // Update Package 1 - Everest Base Camp
    $pdo->exec("UPDATE packages SET 
        title = 'Everest Base Camp Trek',
        description = 'Experience the world\\'s highest mountain with this iconic trek to Everest Base Camp. Journey through the Khumbu region, witness stunning Himalayan peaks, and reach the historic Everest Base Camp at 5,364m.',
        price = 1299.99,
        duration_days = 14,
        includes = 'Experienced guide, Porter service, All meals, Teahouse accommodation, Trekking permits, Insurance, First aid kit, Sleeping bag, Down jacket',
        excludes = 'Personal trekking gear, Alcohol, Tips, International flights, Travel insurance, Extra snacks',
        difficulty_level = 'challenging',
        accommodation_type = 'Mountain Teahouses',
        transportation = 'Domestic flight + Private vehicle',
        image = 'everest-base-camp.jpg'
        WHERE id = 1");
    
    echo "<div style='color: green;'>✓ Updated Package 1: Everest Base Camp Trek</div>";
    
    // Update Package 2 - Annapurna Circuit
    $pdo->exec("UPDATE packages SET 
        title = 'Annapurna Circuit Trek',
        description = 'Complete the legendary Annapurna Circuit, one of Nepal\\'s most famous treks. This 14-day journey takes you through diverse landscapes, terraced farms, and traditional Gurung villages while circling the majestic Annapurna massif.',
        price = 999.99,
        duration_days = 14,
        includes = 'Experienced guide, Porter service, All meals, Lodge accommodation, Trekking permits, Insurance, Sleeping bag, Warm clothing',
        excludes = 'Personal trekking equipment, Tips, International flights, Travel insurance, Hot shower fees',
        difficulty_level = 'moderate',
        accommodation_type = 'Mountain Lodges',
        transportation = 'Private vehicle + Walking',
        image = 'annapurna-circuit.jpg'
        WHERE id = 2");
    
    echo "<div style='color: green;'>✓ Updated Package 2: Annapurna Circuit Trek</div>";
    
    // Update Package 3 - Langtang Valley
    $pdo->exec("UPDATE packages SET 
        title = 'Langtang Valley Trek',
        description = 'Discover the hidden paradise of the Langtang Valley. This moderate 7-day trek takes you through pristine forests, traditional Tamang villages, and ancient Buddhist monasteries with stunning mountain views throughout.',
        price = 699.99,
        duration_days = 7,
        includes = 'Experienced guide, All meals, Teahouse accommodation, Trekking permits, Insurance, Porter service',
        excludes = 'Personal expenses, Tips, International flights, Travel insurance, Alcoholic beverages',
        difficulty_level = 'moderate',
        accommodation_type = 'Traditional Teahouses',
        transportation = 'Private vehicle + Walking',
        image = 'langtang-valley.jpg'
        WHERE id = 3");
    
    echo "<div style='color: green;'>✓ Updated Package 3: Langtang Valley Trek</div>";
    
    // Update Package 4 - Ghorepani Helicopter
    $pdo->exec("UPDATE packages SET 
        title = 'Ghorepani Helicopter Tour',
        description = 'Experience the ultimate Himalayan adventure with a breathtaking helicopter tour to Ghorepani viewpoint. Witness sunrise over the world\\'s highest peaks including Everest, Lhotse, and Makalu in this unforgettable journey.',
        price = 2499.99,
        duration_days = 3,
        includes = 'Helicopter transfer, Experienced pilot, Breakfast at Kathmandu hotel, Ground transportation, Insurance, Certificate of achievement',
        excludes = 'Lunch and dinner, Personal expenses, Travel insurance, Tips, International flights',
        difficulty_level = 'easy',
        accommodation_type = '4-Star Hotel',
        transportation = 'Helicopter + Private vehicle',
        image = 'ghorepani-helicopter.jpg'
        WHERE id = 4");
    
    echo "<div style='color: green;'>✓ Updated Package 4: Ghorepani Helicopter Tour</div>";
    
    // Update Package 5 - Mardi Himal
    $pdo->exec("UPDATE packages SET 
        title = 'Mardi Himal Trek',
        description = 'Trek to the sacred Mardi Himal (5,587m), considered the most beautiful peak in the world. This challenging 12-day journey offers panoramic views of Everest and pristine alpine landscapes.',
        price = 1199.99,
        duration_days = 12,
        includes = 'Experienced guide, Porter service, All meals, Camping equipment, Trekking permits, Insurance, Climbing gear, Warm clothing',
        excludes = 'Personal climbing equipment, Tips, International flights, Travel insurance, Personal snacks',
        difficulty_level = 'challenging',
        accommodation_type = 'Camping and Teahouses',
        transportation = 'Domestic flight + Private vehicle',
        image = 'mardi-himal.jpg'
        WHERE id = 5");
    
    echo "<div style='color: green;'>✓ Updated Package 5: Mardi Himal Trek</div>";
    
    // Clear existing itineraries and add new ones
    $pdo->exec("DELETE FROM itinerary_details WHERE package_id IN (1, 2, 3, 4, 5)");
    
    // Add Everest Base Camp itinerary (simplified)
    $everestItinerary = [
        [1, 1, 'Arrival in Kathmandu', 'Arrive in Kathmandu, transfer to hotel, briefing about the trek, equipment check, and welcome dinner with your guide team.', 'Dinner', 'Hotel transfer, Briefing, Equipment check', 'Kathmandu Hotel'],
        [1, 2, 'Flight to Lukla', 'Early morning flight to Lukla (2,860m), scenic mountain flight, trekking begins after landing.', 'Breakfast', 'Scenic flight, Lukla landing', 'Lukla Teahouse'],
        [1, 3, 'Lukla to Phakding', 'Trek through beautiful rhododendron forests, gradual ascent to Phakding (2,610m), acclimatization day.', 'Breakfast, Lunch, Dinner', 'Forest trekking, River crossing, Mountain views', 'Phakding Lodge'],
        [1, 4, 'Phakding to Namche', 'Continue ascent through traditional Sherpa villages, stunning views of Thamserku peak, reach Namche Bazaar (3,440m).', 'Breakfast, Lunch, Dinner', 'Village exploration, Thamserku views', 'Namche Lodge']
    ];
    
    foreach ($everestItinerary as $day) {
        $stmt = $pdo->prepare("INSERT INTO itinerary_details (package_id, day_number, title, description, meals, activities, accommodation) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute($day);
    }
    
    echo "<div style='color: blue;'>ℹ Added " . count($everestItinerary) . " days itinerary for Everest Base Camp</div>";
    
    echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✓ All packages updated successfully!";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✗ Database Error: " . $e->getMessage();
    echo "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Direct Update - Travel Website</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background-color: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1, h2, h3 { color: #333; }
        .btn { display: inline-block; padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; margin: 10px 5px; }
        .btn-success { background: #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <div style="text-align: center; margin-top: 30px;">
            <a href="../package_details.php?id=1" class="btn btn-success">Test Updated Package</a>
            <a href="../package_enhanced.php" class="btn">Package List</a>
        </div>
    </div>
</body>
</html>

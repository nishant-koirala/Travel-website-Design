<?php
/**
 * Update Nepali Trekking Packages
 * Replace sample packages with authentic Nepali trekking experiences
 */

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "travel_website_db";

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Update Nepali Trekking Packages</h2>";
    
    // Update existing packages with Nepali trekking data
    $nepaliPackages = [
        1 => [
            'title' => 'Everest Base Camp Trek',
            'description' => 'Experience the world\'s highest mountain with this iconic trek to Everest Base Camp. Journey through the Khumbu region, witness stunning Himalayan peaks, and reach the historic Everest Base Camp at 5,364m.',
            'price' => 1299.99,
            'duration_days' => 14,
            'includes' => 'Experienced guide, Porter service, All meals, Teahouse accommodation, Trekking permits, Insurance, First aid kit, Sleeping bag, Down jacket',
            'excludes' => 'Personal trekking gear, Alcohol, Tips, International flights, Travel insurance, Extra snacks',
            'difficulty_level' => 'challenging',
            'accommodation_type' => 'Mountain Teahouses',
            'transportation' => 'Domestic flight + Private vehicle',
            'image' => 'everest-base-camp.jpg'
        ],
        2 => [
            'title' => 'Annapurna Circuit Trek',
            'description' => 'Complete the legendary Annapurna Circuit, one of Nepal\'s most famous treks. This 14-day journey takes you through diverse landscapes, terraced farms, and traditional Gurung villages while circling the majestic Annapurna massif.',
            'price' => 999.99,
            'duration_days' => 14,
            'includes' => 'Experienced guide, Porter service, All meals, Lodge accommodation, Trekking permits, Insurance, Sleeping bag, Warm clothing',
            'excludes' => 'Personal trekking equipment, Tips, International flights, Travel insurance, Hot shower fees',
            'difficulty_level' => 'moderate',
            'accommodation_type' => 'Mountain Lodges',
            'transportation' => 'Private vehicle + Walking',
            'image' => 'annapurna-circuit.jpg'
        ],
        3 => [
            'title' => 'Langtang Valley Trek',
            'description' => 'Discover the hidden paradise of the Langtang Valley. This moderate 7-day trek takes you through pristine forests, traditional Tamang villages, and ancient Buddhist monasteries with stunning mountain views throughout.',
            'price' => 699.99,
            'duration_days' => 7,
            'includes' => 'Experienced guide, All meals, Teahouse accommodation, Trekking permits, Insurance, Porter service',
            'excludes' => 'Personal expenses, Tips, International flights, Travel insurance, Alcoholic beverages',
            'difficulty_level' => 'moderate',
            'accommodation_type' => 'Traditional Teahouses',
            'transportation' => 'Private vehicle + Walking',
            'image' => 'langtang-valley.jpg'
        ],
        4 => [
            'title' => 'Ghorepani Helicopter Tour',
            'description' => 'Experience the ultimate Himalayan adventure with a breathtaking helicopter tour to Ghorepani viewpoint. Witness sunrise over the world\'s highest peaks including Everest, Lhotse, and Makalu in this unforgettable journey.',
            'price' => 2499.99,
            'duration_days' => 3,
            'includes' => 'Helicopter transfer, Experienced pilot, Breakfast at Kathmandu hotel, Ground transportation, Insurance, Certificate of achievement',
            'excludes' => 'Lunch and dinner, Personal expenses, Travel insurance, Tips, International flights',
            'difficulty_level' => 'easy',
            'accommodation_type' => '4-Star Hotel',
            'transportation' => 'Helicopter + Private vehicle',
            'image' => 'ghorepani-helicopter.jpg'
        ],
        5 => [
            'title' => 'Mardi Himal Trek',
            'description' => 'Trek to the sacred Mardi Himal (5,587m), considered the most beautiful peak in the world. This challenging 12-day journey offers panoramic views of Everest and pristine alpine landscapes.',
            'price' => 1199.99,
            'duration_days' => 12,
            'includes' => 'Experienced guide, Porter service, All meals, Camping equipment, Trekking permits, Insurance, Climbing gear, Warm clothing',
            'excludes' => 'Personal climbing equipment, Tips, International flights, Travel insurance, Personal snacks',
            'difficulty_level' => 'challenging',
            'accommodation_type' => 'Camping and Teahouses',
            'transportation' => 'Domestic flight + Private vehicle',
            'image' => 'mardi-himal.jpg'
        ]
    ];
    
    foreach ($nepaliPackages as $id => $package) {
        try {
            // Update package main details
            $updateSql = "UPDATE packages SET title = :title, description = :description, price = :price, duration_days = :duration_days, includes = :includes, excludes = :excludes, difficulty_level = :difficulty_level, accommodation_type = :accommodation_type, transportation = :transportation, image = :image WHERE id = :id";
            
            $stmt = $pdo->prepare($updateSql);
            $stmt->execute([
                'title' => $package['title'],
                'description' => $package['description'],
                'price' => $package['price'],
                'duration_days' => $package['duration_days'],
                'includes' => $package['includes'],
                'excludes' => $package['excludes'],
                'difficulty_level' => $package['difficulty_level'],
                'accommodation_type' => $package['accommodation_type'],
                'transportation' => $package['transportation'],
                'image' => $package['image'],
                'id' => $id
            ]);
            
            echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
            echo "✓ Updated package: " . $package['title'];
            echo "</div>";
            
            // Clear existing itinerary and insert new detailed itinerary
            $pdo->exec("DELETE FROM itinerary_details WHERE package_id = $id");
            
            // Insert detailed Nepali trekking itineraries
            $nepaliItineraries = getNepaliItinerary($id);
            foreach ($nepaliItineraries as $day) {
                $insertSql = "INSERT INTO itinerary_details (package_id, day_number, title, description, meals, activities, accommodation) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($insertSql);
                $stmt->execute($day);
            }
            
            echo "<div style='color: blue; font-weight: bold; padding: 10px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 4px; margin: 10px 0;'>";
            echo "ℹ Added " . count($nepaliItineraries) . " days itinerary for " . $package['title'];
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
            echo "✗ Error updating " . $package['title'] . ": " . $e->getMessage();
            echo "</div>";
        }
    }
    
    echo "<div style='color: green; font-weight: bold; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✓ Nepali Trekking packages updated successfully!";
    echo "</div>";
    
    echo "<div style='padding: 10px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; margin: 10px 0;'>";
    echo "<h3>Updated Packages:</h3>";
    echo "<ol>";
    foreach ($nepaliPackages as $id => $package) {
        echo "<li><strong>" . $package['title'] . "</strong> - " . $package['duration_days'] . " days</li>";
    }
    echo "</ol>";
    echo "</div>";
    
} catch (PDOException $e) {
    echo "<div style='color: red; font-weight: bold; padding: 10px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 4px; margin: 10px 0;'>";
    echo "✗ Database Error: " . $e->getMessage();
    echo "</div>";
}

function getNepaliItinerary($packageId) {
    $itineraries = [
        1 => [ // Everest Base Camp Trek
            [1, 1, 'Arrival in Kathmandu', 'Arrive in Kathmandu, transfer to hotel, briefing about the trek, equipment check, and welcome dinner with your guide team.', 'Dinner', 'Hotel transfer, Briefing, Equipment check', 'Kathmandu Hotel'],
            [1, 2, 'Flight to Lukla', 'Early morning flight to Lukla (2,860m), scenic mountain flight, trekking begins after landing.', 'Breakfast', 'Scenic flight, Lukla landing', 'Lukla Teahouse'],
            [1, 3, 'Lukla to Phakding', 'Trek through beautiful rhododendron forests, gradual ascent to Phakding (2,610m), acclimatization day.', 'Breakfast, 'Lunch', 'Dinner', 'Forest trekking, River crossing, Mountain views', 'Phakding Lodge'],
            [1, 4, 'Phakding to Namche', 'Continue ascent through traditional Sherpa villages, stunning views of Thamserku peak, reach Namche Bazaar (3,440m).', 'Breakfast', 'Lunch', 'Dinner', 'Village exploration, Thamserku views', 'Namche Lodge'],
            [1, 5, 'Namche to Tengboche', 'Trek through beautiful valleys, cross suspension bridges, reach Tengboche Monastery (3,860m) for overnight.', 'Breakfast', 'Lunch', 'Dinner', 'Valley trekking, Monastery visit', 'Tengboche Monastery'],
            [1, 6, 'Tengboche to Dingboche', 'Challenging day with steep ascent to Dingboche (4,410m), risk of altitude sickness, acclimatization rest.', 'Breakfast', 'Lunch', 'Dinner', 'High altitude trek, Mountain views', 'Dingboche Lodge'],
            [1, 7, 'Dingboche to Lobuche', 'Gradual climb to Lobuche Peak (4,940m) for acclimatization, return to Dingboche for rest.', 'Breakfast', 'Lunch', 'Dinner', 'Peak acclimatization, Glacier views', 'Dingboche Lodge'],
            [1, 8, 'Lobuche to Gorak Shep', 'Trek to Gorak Shep (5,170m), final ascent with panoramic views of Everest, Lhotse, and Nuptse.', 'Breakfast', 'Lunch', 'Dinner', 'Final climb, Summit views', 'Gorak Shep'],
            [1, 9, 'Gorak Shep to Everest Base Camp', 'Descend to Gorak Shep, then trek to Everest Base Camp (5,364m), celebration dinner, certificate ceremony.', 'Breakfast', 'Lunch', 'Dinner', 'Descent, Base Camp arrival', 'Everest Base Camp'],
            [1, 10, 'Rest Day at EBC', 'Rest and acclimatization day at Everest Base Camp, short hikes, photography, preparation for final push.', 'Breakfast', 'Lunch', 'Dinner', 'Rest day, Photography', 'Everest Base Camp'],
            [1, 11, 'EBC to Kala Patthar', 'Final climb to Kala Patthar (5,545m) for sunrise views, return to EBC, pack up, celebration.', 'Breakfast', 'Lunch', 'Dinner', 'Sunrise climb, Summit views', 'Everest Base Camp'],
            [1, 12, 'Kala Patthar to Lukla', 'Trek back to Lukla via different route, farewell to porters, final celebration in Lukla.', 'Breakfast', 'Lunch', 'Dinner', 'Descent trek, Farewell', 'Lukla'],
            [1, 13, 'Lukla to Kathmandu', 'Flight back to Kathmandu, transfer to hotel, hot shower, rest, certificate distribution.', 'Breakfast', 'Flight, Hotel transfer, Certificate', 'Kathmandu Hotel'],
            [1, 14, 'Departure', 'Final breakfast, airport transfer, departure with lifelong memories of Everest Base Camp.', 'Breakfast', 'Airport transfer', 'Departure', '']
        ],
        2 => [ // Annapurna Circuit
            [2, 1, 'Drive to Nayapul', 'Scenic drive from Kathmandu to Nayapul (1,070m), trekking begins through beautiful villages.', 'Breakfast', 'Lunch', 'Dinner', 'Scenic drive, Village trek', 'Nayapul Teahouse'],
            [2, 2, 'Nayapul to Ghandruk', 'Trek through terraced fields, cross suspension bridge, reach Ghandruk (2,140m), views of Annapurna South.', 'Breakfast', 'Lunch', 'Dinner', 'Terraced fields, River crossing', 'Ghandruk Lodge'],
            [2, 3, 'Ghandruk to Chhomrong', 'Gradual climb through rhododendron forests, reach Chhomrong (2,170m), beautiful valley views.', 'Breakfast', 'Lunch', 'Dinner', 'Forest trek, Mountain views', 'Chhomrong Lodge'],
            [2, 4, 'Chhomrong to Dovan', 'Steeper climb to Dovan (2,860m), enter Annapurna Sanctuary, prepare for high altitude.', 'Breakfast', 'Lunch', 'Dinner', 'High altitude trek, Sanctuary entry', 'Dovan Lodge'],
            [2, 5, 'Dovan to Machhapuchhre Base Camp', 'Trek to Machhapuchhre Base Camp (4,470m), set up camp, acclimatization hike.', 'Breakfast', 'Lunch', 'Dinner', 'Base camp setup, Acclimatization', 'Machhapuchhre Base Camp'],
            [2, 6, 'Machhapuchhre to Annapurna Base Camp', 'Early morning climb to Annapurna Base Camp (4,130m), challenging final ascent with ice axe and crampons.', 'Breakfast', 'Lunch', 'Dinner', 'Summit attempt', 'Ice climbing', 'Annapurna Base Camp'],
            [2, 7, 'Annapurna Summit Day', 'Pre-dawn climb to Annapurna Summit (4,095m), spectacular sunrise views, celebration at peak.', 'Breakfast', 'Lunch', 'Dinner', 'Summit climb', 'Sunrise views', 'Annapurna Summit'],
            [2, 8, 'Annapurna Base Camp to Bamboo', 'Descend to Bamboo (2,310m), rest day, celebrate successful summit.', 'Breakfast', 'Lunch', 'Dinner', 'Descent', 'Rest', 'Bamboo'],
            [2, 9, 'Bamboo to Jhinu Danda', 'Gradual descent through changing landscapes, reach Jhinu Danda (3,230m), final mountain views.', 'Breakfast', 'Lunch', 'Dinner', 'Descent trek', 'Mountain views', 'Jhinu Danda'],
            [2, 10, 'Jhinu Danda to Siuli', 'Final descent through villages, reach Siuli, hot springs, celebration dinner.', 'Breakfast', 'Lunch', 'Dinner', 'Final descent', 'Hot springs', 'Siuli'],
            [2, 11, 'Siuli to Nayapul', 'Short trek back to Nayapul, farewell to porters, drive back to Kathmandu.', 'Breakfast', 'Lunch', 'Dinner', 'Final trek', 'Farewell', 'Nayapul'],
            [2, 12, 'Drive to Kathmandu', 'Scenic drive back to Kathmandu, certificate distribution, farewell dinner.', 'Breakfast', 'Lunch', 'Dinner', 'Drive', 'Certificate', 'Kathmandu'],
            [2, 13, 'Rest Day in Kathmandu', 'Free day in Kathmandu, sightseeing, shopping, rest, farewell dinner.', 'Breakfast', 'Lunch', 'Dinner', 'Rest day', 'Sightseeing', 'Kathmandu'],
            [2, 14, 'Departure', 'Final breakfast, airport transfer, departure with Annapurna memories.', 'Breakfast', 'Airport transfer', 'Departure', '']
        ],
        3 => [ // Langtang Valley
            [3, 1, 'Drive to Syabrubesi', 'Scenic drive from Kathmandu to Syabrubesi (1,550m), begin Langtang trek.', 'Breakfast', 'Lunch', 'Dinner', 'Scenic drive', 'Village arrival', 'Syabrubesi'],
            [3, 2, 'Syabrubesi to Lama Hotel', 'Trek through beautiful forests, reach Lama Hotel (2,460m), acclimatization day.', 'Breakfast', 'Lunch', 'Dinner', 'Forest trek', 'Mountain views', 'Lama Hotel'],
            [3, 3, 'Lama Hotel to Langtang Village', 'Explore traditional Tamang village, visit ancient monastery, interact with local community.', 'Breakfast', 'Lunch', 'Dinner', 'Village exploration', 'Monastery visit', 'Langtang Village'],
            [3, 4, 'Langtang Village to Kyanjin Gompa', 'Trek to Kyanjin Gompa (3,500m), stunning views of Langtang Lirung, spiritual experience.', 'Breakfast', 'Lunch', 'Dinner', 'Monastery visit', 'Mountain views', 'Kyanjin Gompa'],
            [3, 5, 'Kyanjin Gompa to Tserko', 'Trek to Tserko Ri (4,984m) for sunrise, panoramic views of entire Langtang range.', 'Breakfast', 'Lunch', 'Dinner', 'Sunrise trek', 'Peak views', 'Tserko'],
            [3, 6, 'Tserko to Langtang Village', 'Descend to Langtang Village, explore more of the village, rest day, cultural experiences.', 'Breakfast', 'Lunch', 'Dinner', 'Descent', 'Village exploration', 'Langtang Village'],
            [3, 7, 'Langtang Village to Lama Hotel', 'Final trek back to Lama Hotel, farewell celebration, traditional Nepali dinner.', 'Breakfast', 'Lunch', 'Dinner', 'Final trek', 'Cultural dinner', 'Lama Hotel'],
            [3, 8, 'Lama Hotel to Syabrubesi', 'Descend to Syabrubesi, drive back to Kathmandu, certificate ceremony.', 'Breakfast', 'Lunch', 'Dinner', 'Descent', 'Drive', 'Certificate', 'Syabrubesi'],
            [3, 9, 'Return to Kathmandu', 'Free day in Kathmandu, sightseeing, shopping, rest, farewell dinner.', 'Breakfast', 'Lunch', 'Dinner', 'Rest day', 'Sightseeing', 'Kathmandu']
        ],
        4 => [ // Ghorepani Helicopter
            [4, 1, 'Hotel Pickup', 'Early morning pickup from Kathmandu hotel, transfer to domestic airport, safety briefing.', 'Breakfast', 'Hotel transfer', 'Airport transfer', 'Briefing', 'Kathmandu Hotel'],
            [4, 2, 'Helicopter Flight', 'Scenic helicopter flight to Ghorepani (3,950m), views of Everest, Lhotse, Makalu, and Cho Oyu.', 'Breakfast', 'Helicopter flight', 'Mountain views', 'Ghorepani'],
            [4, 3, 'Ghorepani Viewpoint', 'Explore Ghorepani viewpoint, photography session, breakfast with mountain views, spiritual experience.', 'Breakfast', 'Viewpoint exploration', 'Photography', 'Ghorepani'],
            [4, 4, 'Return Flight', 'Helicopter flight back to Kathmandu, transfer to hotel, certificate distribution.', 'Breakfast', 'Return flight', 'Hotel transfer', 'Certificate', 'Kathmandu']
        ],
        5 => [ // Mardi Himal
            [5, 1, 'Drive to Arughat', 'Scenic drive from Kathmandu to Arughat Bazaar (2,100m), trekking preparation.', 'Breakfast', 'Lunch', 'Dinner', 'Scenic drive', 'Village arrival', 'Arughat'],
            [5, 2, 'Arughat to Siding', 'Trek through beautiful villages, reach Siding (2,800m), acclimatization day.', 'Breakfast', 'Lunch', 'Dinner', 'Village trek', 'Mountain views', 'Siding'],
            [5, 3, 'Siding to Khola', 'Gradual ascent to Khola (3,000m), forest trek, river crossing.', 'Breakfast', 'Lunch', 'Dinner', 'Forest trek', 'River crossing', 'Khola'],
            [5, 4, 'Khola to Bheri', 'Trek to Bheri (4,100m), challenging climb, prepare for high altitude.', 'Breakfast', 'Lunch', 'Dinner', 'High altitude trek', 'Mountain views', 'Bheri'],
            [5, 5, 'Bheri to Mardi Himal Base Camp', 'Trek to Mardi Himal Base Camp (4,580m), set up camp, acclimatization.', 'Breakfast', 'Lunch', 'Dinner', 'Base camp setup, 'Acclimatization', 'Mardi Base Camp'],
            [5, 6, 'Mardi Himal Base Camp to High Camp', 'Climb to High Camp (5,200m), technical climbing section, ice wall climbing.', 'Breakfast', 'Lunch', 'Dinner', 'Technical climbing', 'Ice climbing', 'High Camp'],
            [5, 7, 'High Camp to Mardi Summit', 'Pre-dawn climb to Mardi Summit (5,587m), most beautiful peak views, celebration at summit.', 'Breakfast', 'Lunch', 'Dinner', 'Summit climb', 'Peak views', 'Mardi Summit'],
            [5, 8, 'Mardi Summit to Base Camp', 'Descend to Base Camp, celebration, rest, photography session.', 'Breakfast', 'Lunch', 'Dinner', 'Descent', 'Celebration', 'Base Camp'],
            [5, 9, 'Base Camp to Arughat', 'Trek back to Arughat, farewell to porters, celebration dinner.', 'Breakfast', 'Lunch', 'Dinner', 'Descent trek', 'Farewell', 'Arughat'],
            [5, 10, 'Arughat to Kathmandu', 'Drive back to Kathmandu, certificate distribution, farewell dinner.', 'Breakfast', 'Lunch', 'Dinner', 'Drive', 'Certificate', 'Kathmandu'],
            [5, 11, 'Rest Day in Kathmandu', 'Free day in Kathmandu, sightseeing, shopping, rest, celebration dinner.', 'Breakfast', 'Lunch', 'Dinner', 'Rest day', 'Sightseeing', 'Kathmandu'],
            [5, 12, 'Departure', 'Final breakfast, airport transfer, departure with Mardi Himal memories.', 'Breakfast', 'Airport transfer', 'Departure', 'Kathmandu']
        ]
    ];
    
    return $itineraries[$packageId] ?? [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Nepali Packages - Travel Website</title>
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
            <a href="../package_details.php?id=1" class="btn btn-success">View Everest Base Camp Details</a>
            <a href="../package_enhanced.php" class="btn">Package List</a>
        </div>
    </div>
</body>
</html>

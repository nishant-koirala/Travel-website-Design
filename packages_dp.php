<?php
include 'database/db_connect.php'; // Your PDO connection file

// Array with all the package updates
$updates = [
    [
        'name' => 'Himalayan Adventure',
        'description' => "Embark on an exhilarating trek through the Himalayas, where you'll witness breathtaking views, explore ancient trails, and experience the culture of remote mountain villages.",
        'price' => 1500,
        'image' => 'images/img-1.jpg'
    ],
    [
        'name' => 'Cultural Tour of Kathmandu',
        'description' => "Discover the rich heritage of Kathmandu Valley, with visits to UNESCO World Heritage Sites, including the ancient temples of Pashupatinath, Swayambhunath, and Durbar Square.",
        'price' => 800,
        'image' => 'images/img-2.jpg'
    ],
    [
        'name' => 'Chitwan Jungle Safari',
        'description' => "Immerse yourself in the wild beauty of Chitwan National Park. Experience the thrill of spotting rhinos, tigers, and elephants on a guided jungle safari.",
        'price' => 1200,
        'image' => 'images/img-3.jpg'
    ],
    [
        'name' => 'Lumbini Pilgrimage Tour',
        'description' => "Visit the birthplace of Lord Buddha in Lumbini, a sacred site that draws pilgrims and history enthusiasts alike. Explore ancient monasteries and soak in the peaceful atmosphere.",
        'price' => 1000,
        'image' => 'images/img-4.jpg'
    ],
    [
        'name' => 'Pokhara Lakeside Retreat',
        'description' => "Relax by the serene Phewa Lake in Pokhara, surrounded by stunning mountain views. Enjoy boating, paragliding, and exploring nearby caves and waterfalls.",
        'price' => 900,
        'image' => 'images/img-5.jpg'
    ],
    [
        'name' => 'Everest Base Camp Trek',
        'description' => "Challenge yourself with a trek to Everest Base Camp, an unforgettable journey through Sherpa villages, ancient monasteries, and some of the world's highest peaks.",
        'price' => 2000,
        'image' => 'images/img-6.jpg'
    ],
    [
        'name' => 'Bhaktapur Heritage Walk',
        'description' => "Explore the medieval city of Bhaktapur, known for its preserved architecture, intricate wood carvings, and vibrant festivals that showcase Newari culture.",
        'price' => 750,
        'image' => 'images/img-7.jpg'
    ],
    [
        'name' => 'Annapurna Circuit Trek',
        'description' => "Traverse diverse landscapes on the Annapurna Circuit, from lush subtropical forests to high-altitude deserts, with panoramic views of the Annapurna and Dhaulagiri ranges.",
        'price' => 1800,
        'image' => 'images/img-8.jpg'
    ],
    [
        'name' => 'Gosaikunda Holy Lake Trek',
        'description' => "Journey to the sacred Gosaikunda Lake, a pilgrimage site for Hindus and Buddhists, set amidst the tranquil beauty of the Langtang Himalayas.",
        'price' => 1300,
        'image' => 'images/img-9.jpg'
    ],
    [
        'name' => 'Upper Mustang Expedition',
        'description' => "Explore the remote and mystical region of Upper Mustang, with its ancient Tibetan culture, unique landscapes, and the hidden kingdom of Lo Manthang.",
        'price' => 2200,
        'image' => 'images/img-10.jpg'
    ],
    [
        'name' => 'Rara Lake Serenity Tour',
        'description' => "Discover the tranquil beauty of Rara Lake, Nepal's largest lake, surrounded by lush forests and snow-capped peaks, offering a perfect escape into nature.",
        'price' => 1700,
        'image' => 'images/img-11.jpg'
    ],
    [
        'name' => 'Tilicho Lake Trek',
        'description' => "Join an adventurous trek to Tilicho Lake, one of the highest lakes in the world. Walk through breathtaking mountain landscapes, rugged trails, and immerse yourself in the majestic beauty of the Annapurna region.",
        'price' => 700,
        'image' => 'images/img-12.jpg'
    ]
];

// Prepare and execute the SQL update for each package
foreach ($updates as $package) {
    $sql = "UPDATE packages SET description = :description, price = :price, image = :image WHERE name = :name";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':description' => $package['description'],
        ':price' => $package['price'],
        ':image' => $package['image'],
        ':name' => $package['name']
    ]);
}

echo "Packages updated successfully!";
?>

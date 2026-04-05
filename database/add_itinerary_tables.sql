-- Add Itinerary Functionality to Travel Website Database

USE travel_website_db;

-- Add itinerary fields to packages table
ALTER TABLE packages 
ADD COLUMN itinerary TEXT NULL AFTER description,
ADD COLUMN duration_days INT DEFAULT 1 AFTER price,
ADD COLUMN includes TEXT NULL AFTER itinerary,
ADD COLUMN excludes TEXT NULL AFTER includes,
ADD COLUMN difficulty_level ENUM('easy', 'moderate', 'challenging') DEFAULT 'easy' AFTER excludes,
ADD COLUMN accommodation_type VARCHAR(255) NULL AFTER difficulty_level,
ADD COLUMN transportation VARCHAR(255) NULL AFTER accommodation_type;

-- Create itinerary_details table for detailed day-by-day itineraries
CREATE TABLE IF NOT EXISTS itinerary_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id INT NOT NULL,
    day_number INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    meals VARCHAR(255), -- e.g., "Breakfast, Lunch, Dinner"
    activities TEXT, -- List of activities for the day
    accommodation VARCHAR(255), -- Hotel/lodge name for the night
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    INDEX idx_package_id (package_id),
    INDEX idx_day_number (day_number),
    UNIQUE KEY unique_package_day (package_id, day_number)
);

-- Create package_inclusions table for structured inclusions/exclusions
CREATE TABLE IF NOT EXISTS package_inclusions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id INT NOT NULL,
    inclusion_type ENUM('inclusion', 'exclusion') NOT NULL,
    item VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    INDEX idx_package_id (package_id),
    INDEX idx_inclusion_type (inclusion_type)
);

-- Create package_images table for multiple images per package
CREATE TABLE IF NOT EXISTS package_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id INT NOT NULL,
    image_name VARCHAR(255) NOT NULL,
    image_caption VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES packages(id) ON DELETE CASCADE,
    INDEX idx_package_id (package_id),
    INDEX idx_is_primary (is_primary)
);

-- Update existing packages with sample itinerary data
UPDATE packages SET 
    duration_days = 7,
    includes = 'Airport transfers, Hotel accommodation, Daily breakfast, Guided tours, Entrance fees',
    excludes = 'Lunch and dinner, Personal expenses, Travel insurance, Tips',
    difficulty_level = 'moderate',
    accommodation_type = '3-Star Hotels',
    transportation = 'Private vehicle with driver'
WHERE title = 'Beach Paradise';

UPDATE packages SET 
    duration_days = 5,
    includes = 'Mountain guide, Climbing equipment, Accommodation, All meals, Insurance',
    excludes = 'Personal gear, Alcohol, Tips, Extra activities',
    difficulty_level = 'challenging',
    accommodation_type = 'Mountain Lodges',
    transportation = '4x4 Vehicle'
WHERE title = 'Mountain Adventure';

UPDATE packages SET 
    duration_days = 3,
    includes = 'City guide, Museum entries, Hotel accommodation, Daily breakfast',
    excludes = 'Lunch and dinner, Shopping, Personal expenses',
    difficulty_level = 'easy',
    accommodation_type = '4-Star Hotels',
    transportation = 'Public transport + Walking'
WHERE title = 'City Explorer';

UPDATE packages SET 
    duration_days = 6,
    includes = 'Safari drives, Park fees, Accommodation, All meals, Guide services',
    excludes = 'International flights, Travel insurance, Tips, Alcoholic beverages',
    difficulty_level = 'moderate',
    accommodation_type = 'Safari Lodges',
    transportation = 'Safari vehicles'
WHERE title = 'Safari Experience';

UPDATE packages SET 
    duration_days = 4,
    includes = 'Island transfers, Beach resort, Daily breakfast, Water sports equipment',
    excludes = 'Lunch and dinner, Personal expenses, Travel insurance',
    difficulty_level = 'easy',
    accommodation_type = 'Beach Resort',
    transportation = 'Speed boat + Private vehicle'
WHERE title = 'Island Getaway';

-- Insert sample itinerary details for Beach Paradise package
INSERT INTO itinerary_details (package_id, day_number, title, description, meals, activities, accommodation) VALUES
(1, 1, 'Arrival and Beach Welcome', 'Arrive at the airport, transfer to beach resort, welcome drink and orientation', 'Dinner', 'Airport transfer, Check-in, Beach walk', 'Ocean View Resort'),
(1, 2, 'Beach Exploration Day', 'Full day beach activities with water sports', 'Breakfast, Lunch', 'Swimming, Snorkeling, Beach volleyball', 'Ocean View Resort'),
(1, 3, 'Island Hopping Tour', 'Visit nearby islands and pristine beaches', 'Breakfast, Lunch', 'Boat tour, Island hopping, Snorkeling', 'Ocean View Resort'),
(1, 4, 'Cultural Experience', 'Visit local villages and experience local culture', 'Breakfast, Lunch', 'Village tour, Local market visit, Cultural show', 'Ocean View Resort'),
(1, 5, 'Water Sports Adventure', 'Adventure water sports activities', 'Breakfast, Lunch', 'Jet skiing, Parasailing, Banana boat', 'Ocean View Resort'),
(1, 6, 'Relaxation and Spa', 'Free day for relaxation and spa treatments', 'Breakfast', 'Spa treatment, Beach relaxation', 'Ocean View Resort'),
(1, 7, 'Departure', 'Final breakfast and airport transfer', 'Breakfast', 'Check-out, Airport transfer', '');

-- Insert sample itinerary details for Mountain Adventure package
INSERT INTO itinerary_details (package_id, day_number, title, description, meals, activities, accommodation) VALUES
(2, 1, 'Base Camp Arrival', 'Arrive at base camp, equipment check, orientation', 'Dinner', 'Base camp setup, Equipment briefing', 'Mountain Base Camp'),
(2, 2, 'Acclimatization Day', 'Short hike to acclimatize to altitude', 'Breakfast, Lunch, Dinner', 'Acclimatization hike, Training session', 'Mountain Base Camp'),
(2, 3, 'Summit Attempt Day 1', 'Begin summit climb to first camp', 'Breakfast, Lunch, Dinner', 'Mountain climbing, Camp setup', 'High Camp 1'),
(2, 4, 'Summit Attempt Day 2', 'Continue climb to summit', 'Breakfast, Lunch, Dinner', 'Summit push, Photo session', 'High Camp 2'),
(2, 5, 'Descent and Celebration', 'Descend to base camp, celebration dinner', 'Breakfast, Lunch, Dinner', 'Descent, Celebration', 'Mountain Base Camp');

-- Insert sample package images
INSERT INTO package_images (package_id, image_name, image_caption, is_primary, sort_order) VALUES
(1, 'beach1.jpg', 'Beautiful beach view', TRUE, 1),
(1, 'beach2.jpg', 'Beach resort', FALSE, 2),
(1, 'beach3.jpg', 'Water sports', FALSE, 3),
(2, 'mountain1.jpg', 'Mountain peak', TRUE, 1),
(2, 'mountain2.jpg', 'Base camp', FALSE, 2),
(2, 'mountain3.jpg', 'Climbing route', FALSE, 3),
(3, 'city1.jpg', 'City skyline', TRUE, 1),
(3, 'city2.jpg', 'Museum', FALSE, 2),
(3, 'city3.jpg', 'City tour', FALSE, 3),
(4, 'safari1.jpg', 'Wildlife safari', TRUE, 1),
(4, 'safari2.jpg', 'Safari lodge', FALSE, 2),
(4, 'safari3.jpg', 'Animals', FALSE, 3),
(5, 'island1.jpg', 'Island paradise', TRUE, 1),
(5, 'island2.jpg', 'Beach resort', FALSE, 2),
(5, 'island3.jpg', 'Island sunset', FALSE, 3);

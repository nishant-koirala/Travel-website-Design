-- Nepal trekking packages for chatbot / site (import in phpMyAdmin on travel_website_db)
-- Prefer: open database/seed_trekking_packages.php?run=1 (skips existing titles)

-- Core columns (title, description, price, image)
INSERT INTO packages (title, description, price, image) VALUES
('Everest Base Camp Trek — 14 Days', 'Classic Himalayan trek from Lukla to Everest Base Camp (5364m). Teahouse lodges, acclimatization days, stunning Khumbu views. Best Mar–May & Sep–Nov. Guided group departures.', 1899.00, 'everest-trek.jpg'),
('Annapurna Circuit Trek — 16 Days', 'Full circuit crossing Thorong La Pass (5416m). Diverse landscapes from subtropical valleys to high desert. Tea houses and cultural villages. Iconic Nepal trekking experience.', 1650.00, 'annapurna-circuit.jpg'),
('Langtang Valley Trek — 10 Days', 'Closer to Kathmandu; lush forests, Tamang culture, views of Langtang Lirung. Moderate difficulty — great first high-altitude trek in Nepal.', 980.00, 'langtang.jpg'),
('Mardi Himal Trek — 9 Days', 'Shorter trek with close-up views of Machhapuchhre and Annapurna. Less crowded trails, ridge-line camps, romantic sunsets — ideal for couples.', 890.00, 'mardi-himal.jpg'),
('Ghorepani Poon Hill Trek — 7 Days', 'Gentle trek; sunrise over Annapurna and Dhaulagiri from Poon Hill. Perfect for beginners and families; rhododendron forests in spring.', 650.00, 'poon-hill.jpg'),
('Manaslu Circuit Trek — 18 Days', 'Remote circuit around the eighth-highest peak; restricted area permit trek. Dramatic gorges, Tibetan-influenced villages, Larkya La pass.', 2199.00, 'manaslu.jpg'),
('Upper Mustang Trek — 14 Days', 'Forbidden Kingdom; arid Tibetan plateau landscapes, ancient monasteries, unique culture. Special permit required — premium adventure.', 2450.00, 'mustang.jpg'),
('Helambu Trek — 8 Days', 'Easy–moderate trek near Kathmandu; Sherpa villages, Buddhist stupas, great short itinerary for limited time.', 720.00, 'helambu.jpg');

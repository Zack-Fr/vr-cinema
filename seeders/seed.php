<?php
// backend/seeders/seed.php

require_once __DIR__ . '/../config/connection.php';
require_once __DIR__ . '/MovieSeeder.php';

try {
    $seeder = new MovieSeeder($mysqli, __DIR__ . '/data/movies.json');
    $seeder->run();
    echo "🎬 Seeding complete!\n";
} catch (Exception $e) {
    echo "Seeder error: " . $e->getMessage() . "\n";
    exit(1);
}

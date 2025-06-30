<?php
// backend/seeders/MovieSeeder.php

class MovieSeeder
{
    private mysqli $db;
    private string $dataFile;

    public function __construct(mysqli $db, string $dataFile)
    {
        $this->db       = $db;
        $this->dataFile = $dataFile;
    }

    public function run(): void
    {
        // 1. Read & decode JSON
        if (!file_exists($this->dataFile)) {
            throw new Exception("Seed file not found: {$this->dataFile}");
        }
        $json = file_get_contents($this->dataFile);
        $items = json_decode($json, true);
        if (!is_array($items)) {
            throw new Exception("Invalid JSON in seed file.");
        }

        require_once __DIR__ . '/../config/connection.php';
        require_once __DIR__ . '/../models/Movies.php';
        require_once __DIR__ . '/../models/Showtime.php';

        foreach ($items as $movieData) {
            // In MovieSeeder->run(), before inserting:
        // $this->db->query('SET FOREIGN_KEY_CHECKS=0');
        // $this->db->query('TRUNCATE TABLE showtimes');
        // $this->db->query('TRUNCATE TABLE movies');
        // $this->db->query('SET FOREIGN_KEY_CHECKS=1');

            // 2. Insert movie
            $movieId = Movies::create($this->db, [
                'title'        => $movieData['title'],
                'cast'         => $movieData['cast'],
                'genre'        => $movieData['genre'],
                'rating'       => $movieData['rating'],
                'is_upcoming'  => $movieData['is_upcoming'],
                'release_date' => $movieData['release_date'],
                'description'  => $movieData['description']
            ]);
            echo "Created movie ID {$movieId}: {$movieData['title']}\n";

            // 3. Bulk‐create showtimes
            $times = $movieData['showtimes'] ?? [];
            if ($times) {
                $created = Showtime::bulkCreate($this->db, $movieId, $times);
                echo "  → Showtimes: " . implode(', ', $created) . "\n";
            }
        }
    }
}

<?php

require_once __DIR__ . '/Model.php';

class Showtime extends Model
{
    protected static string $table = 'showtimes';

    public int    $id;
    public int    $movie_id;
    public string $start_time;
    public string $created_at;
    public string $updated_at;

    public function __construct(array $data)
    {
        $this->id         = (int) $data['id'];
        $this->movie_id   = (int) $data['movie_id'];
        $this->start_time = $data['start_time'];
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
    }

    /**
     * Create a single showtime and return its new ID.
     */
    public static function create(mysqli $mysqli, int $movieId, string $startTime): int
    {
        $sql = "
        INSERT INTO " . static::$table . " (movie_id, start_time)
        VALUES (?, ?)
        ";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $mysqli->error);
        }
        $stmt->bind_param('is', $movieId, $startTime);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        return $mysqli->insert_id;
    }

    /**
     * Bulk‐create multiple showtimes in a single transaction.
     * Returns an array of the new showtime IDs.
     *
     * @param int      $movieId    The movie to attach these showtimes to
     * @param string[] $startTimes List of DATETIME strings
     * @return int[]               Array of inserted showtime IDs
     */
    public static function bulkCreate(mysqli $mysqli, int $movieId, array $startTimes): array
    {
        $mysqli->begin_transaction();
        try {
            $ids = [];
            foreach ($startTimes as $time) {
                $ids[] = self::create($mysqli, $movieId, $time);
            }
            $mysqli->commit();
            return $ids;
        } catch (Exception $e) {
            $mysqli->rollback();
            throw $e;
        }
    }

    public function toArray(): array
    {
        return [
            'id'         => $this->id,
            'movie_id'   => $this->movie_id,
            'start_time' => $this->start_time,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

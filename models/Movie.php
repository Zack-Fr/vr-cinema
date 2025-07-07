<?php
require_once __DIR__ . '/Model.php';

class Movie extends Model
{
    // 1. Table name (used by parent::find / parent::all)
    protected static string $table = 'movies';

    // 2. Instance properties
    public int    $id;
    public string $title;
    public string $cast;
    public string $genre;
    public string $rating;
    public int    $is_upcoming;
    public string $release_date;
    public string $description;

    // 3. Hydrate from DB row
    public function __construct(array $data)
    {
        $this->id           = (int) ($data['id']          ?? 0);
        $this->title        = $data['title']              ?? '';
        $this->cast         = $data['cast']               ?? '';
        $this->genre        = $data['genre']              ?? '';
        $this->rating       = $data['rating']             ?? '';
        $this->is_upcoming  = (int) ($data['is_upcoming'] ?? 0);
        $this->release_date = $data['release_date']       ?? '';
        $this->description  = $data['description']        ?? '';
    }

    // 4. Create a new movie record and return its ID
    public static function create(mysqli $mysqli, array $data): int
    {
        $sql = '
        INSERT INTO ' . static::$table . ' 
            (title, cast, genre, rating, is_upcoming, release_date, description)
        VALUES (?, ?, ?, ?, ?, ?, ?)
        ';
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $mysqli->error);
        }

        // Bind parameters: s=string, i=integer
        $stmt->bind_param(
        'sssisss',
        $data['title'],
        $data['cast'],
        $data['genre'],
        $data['rating'],
        $data['is_upcoming'],
        $data['release_date'],
        $data['description']
        );

        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }

        return $mysqli->insert_id;
    }


    // 5. toArray for JSON responses
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'cast'         => $this->cast,
            'genre'        => $this->genre,
            'rating'       => $this->rating,
            'is_upcoming'  => $this->is_upcoming,
            'release_date' => $this->release_date,
            'description'  => $this->description,
        ];
    }
}

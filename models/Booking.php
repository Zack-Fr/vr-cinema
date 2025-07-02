<?php


require_once __DIR__ . '/Model.php';

class Booking extends Model
{
    protected static string $table = 'bookings';

    public int    $id;
    public int    $user_id;
    public int    $showtime_id;
    public array  $seats;        // decoded JSON
    public string $status;       // 'pending'|'confirmed'|'cancelled'|'failed'
    public string $created_at;
    public string $updated_at;

    public function __construct(array $data)
    {
        $this->id          = $data['id'];
        $this->user_id     = $data['user_id'];
        $this->showtime_id = $data['showtime_id'];
        // ensure seats is always an array
        $this->seats       = is_array($data['seats'])? $data['seats']: json_decode($data['seats'], true);
        $this->status      = $data['status'];
        $this->created_at  = $data['created_at'];
        $this->updated_at  = $data['updated_at'];
    }

    /**
     * Create a new booking record.
     * Returns the new booking ID.
     *
     * @param int   $userId
     * @param int   $showtimeId
     * @param array $seats      List of seat IDs or labels.
     */
    public static function create(mysqli $mysqli, int $userId, int $showtimeId, array $seats): int
    {
        $jsonSeats = $mysqli->real_escape_string(json_encode($seats));
        $status = 'pending';

        $sql  = "INSERT INTO " . static::$table . " (user_id, showtime_id, seats, status) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("issi", $userId, $showtimeId, $jsonSeats, $status);

        if (!$stmt->execute()) {
            throw new Exception("Booking creation failed: " . $stmt->error);
        }
        return $mysqli->insert_id;
    }

    /**
     * Fetch all bookings for a given user.
     *
     * @return Booking[]
     */
    public static function findByUser(mysqli $mysqli, int $userId): array
    {
        $sql  = "SELECT * FROM " . static::$table . " WHERE user_id = ? ORDER BY created_at DESC";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        $res       = $stmt->get_result();
        $instances = [];
        while ($row = $res->fetch_assoc()) {
            $instances[] = new static($row);
        }
        return $instances;
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'user_id'     => $this->user_id,
            'showtime_id' => $this->showtime_id,
            'seats'       => $this->seats,
            'status'      => $this->status,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}

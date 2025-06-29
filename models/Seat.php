<?php


require_once __DIR__ . '/Model.php';

class Seat extends Model
{
    protected static string $table = 'seats';

    public int    $id;
    public int    $showtime_id;
    public string $row;
    public int    $number;
    public string $status;       // 'available'|'booked'|'blocked'

    public function __construct(array $data)
    {
        $this->id          = $data['id'];
        $this->showtime_id = $data['showtime_id'];
        $this->row         = $data['row'];
        $this->number      = $data['number'];
        $this->status      = $data['status'];
    }

    /**
     * Fetch all seats for a showtime (for layout rendering).
     *
     * @return Seat[]
     */
    public static function allByShowtime(mysqli $mysqli, int $showtimeId): array
    {
        $sql  = "SELECT * FROM " . static::$table . " WHERE showtime_id = ? ORDER BY row, number";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("i", $showtimeId);
        $stmt->execute();

        $res       = $stmt->get_result();
        $instances = [];
        while ($row = $res->fetch_assoc()) {
            $instances[] = new static($row);
        }
        return $instances;
    }

    /**
     * Bulk‐update seat statuses (e.g. mark as 'booked').
     *
     * @param int[] $seatIds
     */
    public static function updateStatusBulk(mysqli $mysqli, array $seatIds, string $newStatus): bool
    {
        if (empty($seatIds)) {
            return true;
        }
        // build placeholders & types
        $placeholders = implode(',', array_fill(0, count($seatIds), '?'));
        $types        = str_repeat('i', count($seatIds));
        $sql          = "UPDATE " . static::$table . " SET status = ? WHERE id IN ($placeholders)";

        $stmt = $mysqli->prepare($sql);
        // bind 's' for the status, then all seat IDs
        $stmt->bind_param('s' . $types, $newStatus, ...$seatIds);
        return $stmt->execute();
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'showtime_id' => $this->showtime_id,
            'row'         => $this->row,
            'number'      => $this->number,
            'status'      => $this->status,
        ];
    }
}

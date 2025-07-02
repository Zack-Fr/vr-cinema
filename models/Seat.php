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
    *@param int $showtimeId
    *@param string $row
    *@param int $number  
    *@param int $newSeatId
    *@throws Exception
    
     */
        public static function create(mysqli $mysqli, int $showtimeId, string $row, int $number): int
    {
        $sql = "
            INSERT INTO `" . static::$table . "`
            (`showtime_id`, `row`, `number`)
            VALUES (?, ?, ?)
        ";
        $stmt = $mysqli->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare failed: ' . $mysqli->error);
        }
        $stmt->bind_param('isi', $showtimeId, $row, $number);
        if (!$stmt->execute()) {
            throw new Exception('Execute failed: ' . $stmt->error);
        }
        return $mysqli->insert_id;
    }
    /**
        * @param int   $showtimeId
        * @param int   $showtimeId
        * @param array $seats         Array of ['row'=>'A','number'=>1] entries
        * @return int[]               Array of newly inserted seat IDs
        * @throws Exception           On any failure (rolls back)
     */
    public static function bulkCreate(mysqli $mysqli, int $showtimeId, array $seats): array
    {
        $mysqli->begin_transaction();
        try {
            $ids = [];
            foreach ($seats as $s) {
                // Expect each $s to have 'row' and 'number'
                $r = $s['row']    ?? null;
                $n = $s['number'] ?? null;
                if ($r === null || $n === null) {
                    throw new Exception('Invalid seat entry: ' . json_encode($s));
                }
                $ids[] = self::create($mysqli, $showtimeId, $r, (int)$n);
            }
            $mysqli->commit();
            return $ids;
        } catch (Exception $e) {
            $mysqli->rollback();
            throw $e;
        }
    }
    /**  Fetch all seats for a showtime (for layout rendering).
    *@return Seat[]
    */
    public static function allByShowtime(mysqli $mysqli, int $showtimeId): array
    {
        $sql  = "SELECT * FROM " . static::$table . " WHERE `showtime_id` = ? ORDER BY `row`, `number`";
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
     * @param string $newStatus
     * @return bool
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

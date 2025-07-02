<?php 
require("../config/connection.php");

$query =
SET @showtimeId = 17;
INSERT INTO seats (showtime_id, `row`, `number`)
SELECT 
@showtimeId,
r.letter,
n.num
FROM (
SELECT 'A' AS letter
UNION ALL SELECT 'B'
UNION ALL SELECT 'C'
UNION ALL SELECT 'D'
UNION ALL SELECT 'E'
UNION ALL SELECT 'F'
UNION ALL SELECT 'G'
UNION ALL SELECT 'H'
) AS r
CROSS JOIN (
SELECT 1  AS num
UNION ALL SELECT 2
UNION ALL SELECT 3
UNION ALL SELECT 4
UNION ALL SELECT 5
UNION ALL SELECT 6
UNION ALL SELECT 7
UNION ALL SELECT 8
UNION ALL SELECT 9
UNION ALL SELECT 10
UNION ALL SELECT 11
UNION ALL SELECT 12
) AS n;

-- 3) Verify it worked
SELECT id, `row`, `number`, status
FROM seats
WHERE showtime_id = @showtimeId
ORDER BY `row`, `number`;
    
$execute = $mysqli->prepare($query);
$execute->execute();
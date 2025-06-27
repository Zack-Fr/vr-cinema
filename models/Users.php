<?php
require_once("Model.php");

class User extends Model
{

    private int $id;
    private int $name;

    private int $email;

    private int $password;

    private int $created_at;

    public static function create(mysqli $mysqli, array $data): int
    {

        $sql = "INSERT INTO " . static::$table . " (name, email, password) VALUES (?, ?, ?)";
        $stmt = $mysqli->prepare($sql);

        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bind_param("sss", $data['name'], $data['email'], $hash);
        if (!$stmt->execute()) {
            // handle duplicate
            throw new Exception($stmt->error);
        }


        return $mysqli->insert_id;
    }
    protected static string $table = "users";

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->name = $data["name"];
        $this->email = $data["email"];
        $this->password = $data["password"];
        $this->created_at = $data["created_at"];
    }
    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getEmail(): string
    {
        return $this->email;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function getCreated_at(): string
    {
        return $this->created_at;
    }
    public function toArray()
    {
        return [$this->id];
    }
}
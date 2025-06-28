<?php
require_once("Model.php");

class User extends Model
{

    private int $id;
    private string $name;

    private string $age;

    private string $email;

    private ?string $mobile_num;

    private string $password;

    private string $created_at;

    public static function create(mysqli $mysqli, array $data): int
    {

        $sql = "INSERT INTO " . static::$table . " (name, email, password , age) VALUES (?, ?, ?, ?)";
        $stmt = $mysqli->prepare($sql);
        

        $hash = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bind_param("sssi", $data['name'], $data['email'] ,$hash, $data['age']);
        if (!$stmt->execute()) {
            // handle duplicate
            throw new Exception($stmt->error);
        }


        return $mysqli->insert_id;
    }

    public static function verifyCredentials(mysqli $mysqli, string $email, string $password): ?self {
    $sql = "SELECT * FROM " . static::$table . " WHERE email = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    if (!$data) {
        return null; // user not found
    }
    // verify password
    if (!password_verify($password, $data['password'])) {
        return null;
    }
    // instantiate and return user
    return new static($data);
    }

    public function update(mysqli $mysqli): bool {
    $sql = "UPDATE " . static::$table . " SET name = ?, mobile_num = ? WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssi", 
    $this->name, $this->mobile_num, $this->id);
    return $stmt->execute();
    }
    protected static string $table = "users";

    public function __construct(array $data)
    {
        $this->id = $data["id"];
        $this->name = $data["name"]; 
        $this->age = $data["age"]; 
        $this->email = $data["email"];
        $this->password = $data["password"];
        $this->created_at = $data["created_at"];
        $this->mobile_num = $data["mobile_num"];
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
    public function getMobile_num(): string
    {
        return $this->mobile_num;
    }
    public function setMobileNum(?string $mobile_num): void {
        $this->mobile_num = $mobile_num;
}


public function toArray(): array {
    return [
        'id'         => $this->id,
        'name'       => $this->name,
        'email'      => $this->email,
        'created_at' =>$this->created_at,
        'age'        =>$this->age,
        'mobile_num' =>$this->mobile_num,
        
    ];
}
}
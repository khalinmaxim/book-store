<?php
class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getUserByUsername($username) {
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser($username, $password, $email, $role = 'user') {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO users (username, password, email, role)
                 VALUES (:username, :password, :email, :role)";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            ':username' => $username,
            ':password' => $hashed_password,
            ':email' => $email,
            ':role' => $role
        ]);
    }
}
?>

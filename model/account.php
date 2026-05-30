<?php

require_once __DIR__ . '/db.php';

class Account {
    private PDO    $conn;
    private string $table = 'accounts';

    public ?int    $account_id = null;
    public string  $first_name = '';
    public string  $last_name  = '';
    public string  $contact    = '';
    public string  $email      = '';
    public string  $hire_date  = '';
    public string  $password   = '';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function findByEmail(string $email): array|false {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE email = :email LIMIT 1"
        );
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function findById(int $account_id): array|false {
        $stmt = $this->conn->prepare(
            "SELECT * FROM {$this->table} WHERE account_id = :account_id LIMIT 1"
        );
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function emailExists(string $email, ?int $exclude_id = null): bool {
        $sql = "SELECT account_id FROM {$this->table} WHERE email = :email";
        if ($exclude_id !== null) {
            $sql .= " AND account_id != :exclude_id";
        }
        $sql .= " LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        if ($exclude_id !== null) {
            $stmt->bindParam(':exclude_id', $exclude_id, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create(): bool {
        $hashed = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
                (first_name, last_name, contact, email, hire_date, password)
            VALUES
                (:first_name, :last_name, :contact, :email, :hire_date, :password)
        ");
        $stmt->bindParam(':first_name', $this->first_name);
        $stmt->bindParam(':last_name',  $this->last_name);
        $stmt->bindParam(':contact',    $this->contact);
        $stmt->bindParam(':email',      $this->email);
        $stmt->bindParam(':hire_date',  $this->hire_date);
        $stmt->bindParam(':password',   $hashed);
        return $stmt->execute();
    }

    public function update(bool $change_password = false): bool {
        if ($change_password) {
            $hashed = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt = $this->conn->prepare("
                UPDATE {$this->table}
                SET first_name = :first_name,
                    last_name  = :last_name,
                    contact    = :contact,
                    email      = :email,
                    hire_date  = :hire_date,
                    password   = :password
                WHERE account_id = :account_id
            ");
            $stmt->bindParam(':password', $hashed);
        } else {
            $stmt = $this->conn->prepare("
                UPDATE {$this->table}
                SET first_name = :first_name,
                    last_name  = :last_name,
                    contact    = :contact,
                    email      = :email,
                    hire_date  = :hire_date
                WHERE account_id = :account_id
            ");
        }
        $stmt->bindParam(':first_name',  $this->first_name);
        $stmt->bindParam(':last_name',   $this->last_name);
        $stmt->bindParam(':contact',     $this->contact);
        $stmt->bindParam(':email',       $this->email);
        $stmt->bindParam(':hire_date',   $this->hire_date);
        $stmt->bindParam(':account_id',  $this->account_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $account_id): bool {
        $stmt = $this->conn->prepare(
            "DELETE FROM {$this->table} WHERE account_id = :account_id"
        );
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

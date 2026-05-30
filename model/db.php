<?php

class Database {
    private string $host;
    private int    $port;
    private string $db_name;
    private string $username;
    private string $password;
    private ?PDO   $conn = null;

    public function __construct() {
        $this->loadEnv(__DIR__ . '/../.env');

        $this->host     = $_SERVER['DB_HOST'] ?? (getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? 'localhost'));
        $this->port     = (int)($_SERVER['DB_PORT'] ?? (getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? 3306)));
        $this->db_name  = $_SERVER['DB_NAME'] ?? (getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? ''));
        $this->username = $_SERVER['DB_USER'] ?? (getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root'));
        $this->password = $_SERVER['DB_PASS'] ?? (getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? ''));
    }

    public function getConnection(): PDO {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);
            } catch (PDOException $e) {
                die(json_encode(['db_error' => $e->getMessage()]));
            }
        }
        return $this->conn;
    }

    private function loadEnv(string $path): void {
        if (!file_exists($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
    }
}

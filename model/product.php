<?php

require_once __DIR__ . '/db.php';

class Product {
    private PDO    $conn;
    private string $table = 'products';

    public ?int    $product_id   = null;
    public int     $account_id   = 0;
    public string  $sku          = '';
    public string  $product_name = '';
    public string  $category     = '';
    public string  $description  = '';
    public int     $quantity     = 0;
    public float   $unit_price   = 0.00;
    public string  $supplier     = '';
    public string  $status       = 'active';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll(int $account_id, array $filters = []): array {
        $sql = "SELECT * FROM {$this->table} WHERE account_id = :account_id";

        if (!empty($filters['category'])) $sql .= " AND category = :category";
        if (!empty($filters['status']))   $sql .= " AND status = :status";
        if (!empty($filters['search']))   $sql .= " AND (product_name LIKE :search OR sku LIKE :search)";

        $sql .= " ORDER BY date_added DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);

        if (!empty($filters['category'])) $stmt->bindParam(':category', $filters['category']);
        if (!empty($filters['status']))   $stmt->bindParam(':status',   $filters['status']);
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $stmt->bindParam(':search', $search);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById(int $product_id, int $account_id): array|false {
        $stmt = $this->conn->prepare("
            SELECT * FROM {$this->table}
            WHERE product_id = :product_id AND account_id = :account_id
            LIMIT 1
        ");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function exists(int $product_id, int $account_id): bool {
        $stmt = $this->conn->prepare("
            SELECT product_id FROM {$this->table}
            WHERE product_id = :product_id AND account_id = :account_id
            LIMIT 1
        ");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function create(): bool {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table}
                (account_id, sku, product_name, category, description,
                 quantity, unit_price, supplier, status, date_added, last_updated)
            VALUES
                (:account_id, :sku, :product_name, :category, :description,
                 :quantity, :unit_price, :supplier, :status, NOW(), NOW())
        ");
        return $this->bindCoreFields($stmt);
    }

    public function update(): bool {
        $stmt = $this->conn->prepare("
            UPDATE {$this->table}
            SET sku          = :sku,
                product_name = :product_name,
                category     = :category,
                description  = :description,
                quantity     = :quantity,
                unit_price   = :unit_price,
                supplier     = :supplier,
                status       = :status,
                last_updated = NOW()
            WHERE product_id = :product_id
              AND account_id = :account_id
        ");
        $stmt->bindParam(':product_id', $this->product_id, PDO::PARAM_INT);
        return $this->bindCoreFields($stmt);
    }

    public function delete(int $product_id, int $account_id): bool {
        $stmt = $this->conn->prepare("
            DELETE FROM {$this->table}
            WHERE product_id = :product_id AND account_id = :account_id
        ");
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getCategories(int $account_id): array {
        $stmt = $this->conn->prepare("
            SELECT DISTINCT category FROM {$this->table}
            WHERE account_id = :account_id
            ORDER BY category ASC
        ");
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getStats(int $account_id): array {
        $stmt = $this->conn->prepare("
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN status = 'active'    THEN 1 ELSE 0 END) AS active,
                SUM(CASE WHEN status = 'low_stock' THEN 1 ELSE 0 END) AS low_stock,
                SUM(CASE WHEN status = 'inactive'  THEN 1 ELSE 0 END) AS inactive,
                SUM(quantity * unit_price) AS total_value
            FROM {$this->table}
            WHERE account_id = :account_id
        ");
        $stmt->bindParam(':account_id', $account_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: [];
    }

    private function bindCoreFields(PDOStatement $stmt): bool {
        $stmt->bindParam(':account_id',   $this->account_id, PDO::PARAM_INT);
        $stmt->bindParam(':sku',          $this->sku);
        $stmt->bindParam(':product_name', $this->product_name);
        $stmt->bindParam(':category',     $this->category);
        $stmt->bindParam(':description',  $this->description);
        $stmt->bindParam(':quantity',     $this->quantity,   PDO::PARAM_INT);
        $stmt->bindParam(':unit_price',   $this->unit_price);
        $stmt->bindParam(':supplier',     $this->supplier);
        $stmt->bindParam(':status',       $this->status);
        return $stmt->execute();
    }
}

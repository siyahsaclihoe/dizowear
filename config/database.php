<?php
/**
 * Dızo Wear - Database Configuration
 * PDO tabanlı veritabanı bağlantı sınıfı
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // Veritabanı ayarları
    private $host = 'localhost';
    private $dbname = 'dizowear';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die('Veritabanı bağlantı hatası: ' . $e->getMessage());
        }
    }
    
    /**
     * Singleton pattern ile tek instance döndür
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * PDO bağlantısını döndür
     */
    public function getConnection(): PDO {
        return $this->connection;
    }
    
    /**
     * Prepared statement ile sorgu çalıştır
     */
    public function query(string $sql, array $params = []): PDOStatement {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Tek satır getir
     */
    public function fetch(string $sql, array $params = []): ?array {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }
    
    /**
     * Tüm satırları getir
     */
    public function fetchAll(string $sql, array $params = []): array {
        return $this->query($sql, $params)->fetchAll();
    }
    
    /**
     * Son eklenen ID'yi döndür
     */
    public function lastInsertId(): string {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Clone engelle (Singleton pattern)
     */
    private function __clone() {}
    
    /**
     * Unserialize engelle (Singleton pattern)
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

<?php
declare(strict_types=1);

class PerformanceController
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = require __DIR__ . '/../db.php';
    }

    public function logMetric(): void
    {
        header('Content-Type: application/json');
        
        // Only accept POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }
        
        // Get JSON input
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data || !isset($data['metric']) || !isset($data['value'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid data']);
            exit;
        }
        
        try {
            // Create performance_metrics table if not exists
            $this->createTableIfNotExists();
            
            // Insert metric
            $stmt = $this->pdo->prepare("
                INSERT INTO performance_metrics 
                (metric_name, metric_value, url, user_agent, ip_address, recorded_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $data['metric'],
                (float)$data['value'],
                $data['url'] ?? '',
                $data['user_agent'] ?? '',
                $_SERVER['REMOTE_ADDR'] ?? ''
            ]);
            
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            error_log("Performance metric logging error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
        
        exit;
    }
    
    public function getMetrics(): void
    {
        header('Content-Type: application/json');
        
        // Simple auth check - only for admin
        session_start();
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        try {
            $days = (int)($_GET['days'] ?? 7);
            $metric = $_GET['metric'] ?? '';
            
            $sql = "
                SELECT 
                    metric_name,
                    AVG(metric_value) as avg_value,
                    MIN(metric_value) as min_value,
                    MAX(metric_value) as max_value,
                    COUNT(*) as count,
                    DATE(recorded_at) as date
                FROM performance_metrics 
                WHERE recorded_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
            ";
            
            $params = [$days];
            
            if ($metric) {
                $sql .= " AND metric_name = ?";
                $params[] = $metric;
            }
            
            $sql .= " GROUP BY metric_name, DATE(recorded_at) ORDER BY date DESC, metric_name";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'data' => $results,
                'period_days' => $days
            ]);
        } catch (Exception $e) {
            error_log("Performance metrics retrieval error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Internal server error']);
        }
        
        exit;
    }
    
    private function createTableIfNotExists(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS performance_metrics (
                id INT AUTO_INCREMENT PRIMARY KEY,
                metric_name VARCHAR(50) NOT NULL,
                metric_value DECIMAL(10,2) NOT NULL,
                url TEXT,
                user_agent TEXT,
                ip_address VARCHAR(45),
                recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_metric_date (metric_name, recorded_at),
                INDEX idx_recorded_at (recorded_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        
        $this->pdo->exec($sql);
    }
}

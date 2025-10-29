<?php

class AuditTrail {
    private static $instance = null;
    private $db;
    private $logger;
    
    private function __construct() {
        $this->db = Database::getInstance();
        $this->logger = Logger::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get audit logs with filters
     */
    public function getLogs($filters = [], $page = 1, $perPage = 20) {
        try {
            $conditions = [];
            $params = [];
            $types = "";
            
            if (!empty($filters['user_id'])) {
                $conditions[] = "al.user_id = ?";
                $params[] = $filters['user_id'];
                $types .= "i";
            }
            
            if (!empty($filters['action'])) {
                $conditions[] = "al.action = ?";
                $params[] = $filters['action'];
                $types .= "s";
            }
            
            if (!empty($filters['table_name'])) {
                $conditions[] = "al.table_name = ?";
                $params[] = $filters['table_name'];
                $types .= "s";
            }
            
            if (!empty($filters['date_from'])) {
                $conditions[] = "al.created_at >= ?";
                $params[] = $filters['date_from'];
                $types .= "s";
            }
            
            if (!empty($filters['date_to'])) {
                $conditions[] = "al.created_at <= ?";
                $params[] = $filters['date_to'];
                $types .= "s";
            }
            
            $whereClause = empty($conditions) ? "" : "WHERE " . implode(" AND ", $conditions);
            
            // Get total count
            $countSql = "SELECT COUNT(*) as total 
                        FROM audit_logs al 
                        $whereClause";
            
            $stmt = $this->db->executeQuery($countSql, $types, $params);
            $total = $stmt->get_result()->fetch_assoc()['total'];
            
            // Get paginated results
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT al.*, u.first_name, u.last_name, u.email 
                   FROM audit_logs al
                   LEFT JOIN users u ON al.user_id = u.id 
                   $whereClause 
                   ORDER BY al.created_at DESC 
                   LIMIT ? OFFSET ?";
            
            $params[] = $perPage;
            $params[] = $offset;
            $types .= "ii";
            
            $stmt = $this->db->executeQuery($sql, $types, $params);
            $logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            return [
                'logs' => $logs,
                'total' => $total,
                'pages' => ceil($total / $perPage),
                'current_page' => $page
            ];
            
        } catch (Exception $e) {
            $this->logger->error('Failed to fetch audit logs', [
                'error' => $e->getMessage(),
                'filters' => $filters
            ]);
            return false;
        }
    }
    
    /**
     * Get available audit actions
     */
    public function getActions() {
        try {
            $sql = "SELECT DISTINCT action FROM audit_logs ORDER BY action";
            $stmt = $this->db->executeQuery($sql, "", []);
            return array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'action');
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Get audited tables
     */
    public function getTables() {
        try {
            $sql = "SELECT DISTINCT table_name FROM audit_logs WHERE table_name IS NOT NULL ORDER BY table_name";
            $stmt = $this->db->executeQuery($sql, "", []);
            return array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC), 'table_name');
        } catch (Exception $e) {
            return [];
        }
    }
    
    /**
     * Format change details for display
     */
    public function formatChanges($oldValues, $newValues) {
        $changes = [];
        
        if ($oldValues && $newValues) {
            $old = json_decode($oldValues, true);
            $new = json_decode($newValues, true);
            
            foreach ($new as $key => $value) {
                if (!isset($old[$key]) || $old[$key] !== $value) {
                    $changes[] = [
                        'field' => $key,
                        'old' => $old[$key] ?? null,
                        'new' => $value
                    ];
                }
            }
        } elseif ($newValues) {
            // New record creation
            $new = json_decode($newValues, true);
            foreach ($new as $key => $value) {
                $changes[] = [
                    'field' => $key,
                    'old' => null,
                    'new' => $value
                ];
            }
        }
        
        return $changes;
    }
}
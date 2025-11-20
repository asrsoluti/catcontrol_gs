<?php
require_once __DIR__ . '/../config/database.php';

class Servico {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO servicos (codigo_servico, nome, descricao, categoria, 
                preco, tempo_estimado, ativo) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $this->generateCodigo(),
            $data['nome'],
            $data['descricao'] ?? null,
            $data['categoria'] ?? null,
            $data['preco'] ?? 0,
            $data['tempo_estimado'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE servicos SET 
                nome = ?, descricao = ?, categoria = ?,
                preco = ?, tempo_estimado = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nome'],
            $data['descricao'] ?? null,
            $data['categoria'] ?? null,
            $data['preco'] ?? 0,
            $data['tempo_estimado'] ?? null,
            $id
        ]);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM servicos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT * FROM servicos WHERE 1=1";
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (nome LIKE ? OR codigo_servico LIKE ? OR descricao LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['categoria'])) {
            $sql .= " AND categoria = ?";
            $params[] = $filters['categoria'];
        }
        
        if (isset($filters['ativo'])) {
            $sql .= " AND ativo = ?";
            $params[] = $filters['ativo'];
        }
        
        $sql .= " ORDER BY nome ASC";
        
        if (isset($filters['limit'])) {
            $offset = ($filters['page'] ?? 0) * $filters['limit'];
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $filters['limit'];
            $params[] = $offset;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM servicos WHERE 1=1";
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (nome LIKE ? OR codigo_servico LIKE ? OR descricao LIKE ?)";
            $searchTerm = "%{$filters['search']}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($filters['categoria'])) {
            $sql .= " AND categoria = ?";
            $params[] = $filters['categoria'];
        }
        
        if (isset($filters['ativo'])) {
            $sql .= " AND ativo = ?";
            $params[] = $filters['ativo'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    private function generateCodigo() {
        $sql = "SELECT MAX(CAST(codigo_servico AS UNSIGNED)) as ultimo FROM servicos";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $proximo = ($result['ultimo'] ?? 0) + 1;
        return str_pad($proximo, 6, '0', STR_PAD_LEFT);
    }
    
    public function getCategorias() {
        $sql = "SELECT DISTINCT categoria FROM servicos WHERE categoria IS NOT NULL ORDER BY categoria";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

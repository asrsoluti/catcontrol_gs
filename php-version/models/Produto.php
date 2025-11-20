<?php
require_once __DIR__ . '/../config/database.php';

class Produto {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function create($data) {
        $sql = "INSERT INTO produtos (codigo_produto, nome, descricao, categoria, unidade, 
                preco_custo, preco_venda, estoque_minimo, estoque_atual, ativo) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $this->generateCodigo(),
            $data['nome'],
            $data['descricao'] ?? null,
            $data['categoria'] ?? null,
            $data['unidade'] ?? 'UN',
            $data['preco_custo'] ?? 0,
            $data['preco_venda'] ?? 0,
            $data['estoque_minimo'] ?? 0,
            $data['estoque_atual'] ?? 0
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE produtos SET 
                nome = ?, descricao = ?, categoria = ?, unidade = ?,
                preco_custo = ?, preco_venda = ?, 
                estoque_minimo = ?, estoque_atual = ?
                WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nome'],
            $data['descricao'] ?? null,
            $data['categoria'] ?? null,
            $data['unidade'] ?? 'UN',
            $data['preco_custo'] ?? 0,
            $data['preco_venda'] ?? 0,
            $data['estoque_minimo'] ?? 0,
            $data['estoque_atual'] ?? 0,
            $id
        ]);
    }
    
    public function findById($id) {
        $sql = "SELECT * FROM produtos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    public function getAll($filters = []) {
        $sql = "SELECT * FROM produtos WHERE 1=1";
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (nome LIKE ? OR codigo_produto LIKE ? OR descricao LIKE ?)";
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
        $sql = "SELECT COUNT(*) as total FROM produtos WHERE 1=1";
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (nome LIKE ? OR codigo_produto LIKE ? OR descricao LIKE ?)";
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
        $sql = "SELECT MAX(CAST(codigo_produto AS UNSIGNED)) as ultimo FROM produtos";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $proximo = ($result['ultimo'] ?? 0) + 1;
        return str_pad($proximo, 6, '0', STR_PAD_LEFT);
    }
    
    public function getCategorias() {
        $sql = "SELECT DISTINCT categoria FROM produtos WHERE categoria IS NOT NULL ORDER BY categoria";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}

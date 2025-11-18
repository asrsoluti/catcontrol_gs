<?php
/**
 * Modelo de Cliente
 */

class Cliente {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Criar novo cliente
     */
    public function create($data) {
        // Gerar código do cliente
        $codigo = $this->generateCodigo();
        
        $sql = "INSERT INTO clientes (
                    codigo_cliente, razao_social, nome_fantasia, tipo_pessoa,
                    cpf_cnpj, inscricao_estadual, endereco, numero, complemento,
                    bairro, cidade, uf, cep, telefone, celular, fax, email,
                    contato_principal, observacoes, created_by
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $codigo,
            $data['razao_social'],
            $data['nome_fantasia'] ?? null,
            $data['tipo_pessoa'] ?? 'PJ',
            $data['cpf_cnpj'],
            $data['inscricao_estadual'] ?? null,
            $data['endereco'] ?? null,
            $data['numero'] ?? null,
            $data['complemento'] ?? null,
            $data['bairro'] ?? null,
            $data['cidade'] ?? null,
            $data['uf'] ?? null,
            $data['cep'] ?? null,
            $data['telefone'] ?? null,
            $data['celular'] ?? null,
            $data['fax'] ?? null,
            $data['email'] ?? null,
            $data['contato_principal'] ?? null,
            $data['observacoes'] ?? null,
            $_SESSION['user_id'] ?? null
        ]);
    }
    
    /**
     * Buscar cliente por ID
     */
    public function findById($id) {
        $sql = "SELECT c.*, u.nome as criado_por_nome,
                (SELECT COUNT(*) FROM cat WHERE cliente_id = c.id) as total_cats
                FROM clientes c
                LEFT JOIN usuarios u ON c.created_by = u.id
                WHERE c.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Listar clientes
     */
    public function getAll($filters = []) {
        $sql = "SELECT c.*, u.nome as criado_por_nome,
                (SELECT COUNT(*) FROM cat WHERE cliente_id = c.id) as total_cats
                FROM clientes c
                LEFT JOIN usuarios u ON c.created_by = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (isset($filters['ativo'])) {
            $sql .= " AND c.ativo = ?";
            $params[] = $filters['ativo'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.razao_social LIKE ? OR c.nome_fantasia LIKE ? 
                      OR c.cpf_cnpj LIKE ? OR c.email LIKE ? OR c.cidade LIKE ?)";
            $search = "%{$filters['search']}%";
            $params = array_merge($params, array_fill(0, 5, $search));
        }
        
        // Paginação
        $limit = $filters['limit'] ?? 20;
        $offset = ($filters['page'] ?? 0) * $limit;
        
        $sql .= " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Contar total de clientes
     */
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE 1=1";
        $params = [];
        
        if (isset($filters['ativo'])) {
            $sql .= " AND ativo = ?";
            $params[] = $filters['ativo'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (razao_social LIKE ? OR nome_fantasia LIKE ? 
                      OR cpf_cnpj LIKE ? OR email LIKE ? OR cidade LIKE ?)";
            $search = "%{$filters['search']}%";
            $params = array_merge($params, array_fill(0, 5, $search));
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Atualizar cliente
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'razao_social', 'nome_fantasia', 'tipo_pessoa', 'cpf_cnpj',
            'inscricao_estadual', 'endereco', 'numero', 'complemento',
            'bairro', 'cidade', 'uf', 'cep', 'telefone', 'celular',
            'fax', 'email', 'contato_principal', 'observacoes', 'ativo'
        ];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE clientes SET " . implode(", ", $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Verificar se CPF/CNPJ já existe
     */
    public function cpfCnpjExists($cpfCnpj, $excludeId = null) {
        $sql = "SELECT id FROM clientes WHERE cpf_cnpj = ?";
        $params = [$cpfCnpj];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Gerar código do cliente
     */
    private function generateCodigo() {
        $sql = "SELECT MAX(CAST(codigo_cliente AS UNSIGNED)) as ultimo FROM clientes";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $proximo = ($result['ultimo'] ?? 0) + 1;
        return str_pad($proximo, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Alternar status ativo/inativo
     */
    public function toggleStatus($id) {
        $sql = "UPDATE clientes SET ativo = NOT ativo WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
    
    /**
     * Buscar para select (autocomplete)
     */
    public function search($term, $limit = 10) {
        $sql = "SELECT id, razao_social, cpf_cnpj, cidade 
                FROM clientes 
                WHERE ativo = 1 
                AND (razao_social LIKE ? OR nome_fantasia LIKE ? OR cpf_cnpj LIKE ?)
                ORDER BY razao_social
                LIMIT ?";
        
        $search = "%$term%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$search, $search, $search, $limit]);
        return $stmt->fetchAll();
    }
}
?>
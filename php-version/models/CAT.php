<?php
/**
 * Modelo de CAT (Chamada de Assistência Técnica)
 */

class CAT {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Criar nova CAT
     */
    public function create($data) {
        $sql = "INSERT INTO cat (
                    numero_sac, pedido_numero, cliente_id, contato_nome,
                    contato_telefone, contato_email, contato_cargo,
                    produto_id, produto_descricao, numero_serie_produto,
                    problema_reclamado, tipo_atendimento, prioridade,
                    observacoes, atendente_id, status_id, data_abertura, hora_abertura
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), TIME(NOW()))";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute([
            $data['numero_sac'] ?? null,
            $data['pedido_numero'] ?? null,
            $data['cliente_id'],
            $data['contato_nome'] ?? null,
            $data['contato_telefone'] ?? null,
            $data['contato_email'] ?? null,
            $data['contato_cargo'] ?? null,
            $data['produto_id'] ?? null,
            $data['produto_descricao'] ?? null,
            $data['numero_serie_produto'] ?? null,
            $data['problema_reclamado'],
            $data['tipo_atendimento'] ?? 'AVULSO',
            $data['prioridade'] ?? 'NORMAL',
            $data['observacoes'] ?? null,
            $_SESSION['user_id']
        ]);
        
        if ($result) {
            $catId = $this->db->lastInsertId();
            
            // Registrar no histórico
            $this->addHistorico($catId, 'ABERTURA', 'CAT aberta', null, 1);
            
            return $catId;
        }
        
        return false;
    }
    
    /**
     * Buscar CAT por ID
     */
    public function findById($id) {
        $sql = "SELECT c.*,
                cl.razao_social as cliente_nome,
                cl.cpf_cnpj as cliente_documento,
                cl.telefone as cliente_telefone,
                cl.email as cliente_email,
                cl.endereco as cliente_endereco,
                cl.cidade as cliente_cidade,
                cl.uf as cliente_uf,
                p.nome as produto_nome,
                p.codigo_produto,
                s.nome as status_nome,
                s.cor_hex as status_cor,
                u1.nome as atendente_nome,
                u2.nome as tecnico_nome
                FROM cat c
                LEFT JOIN clientes cl ON c.cliente_id = cl.id
                LEFT JOIN produtos p ON c.produto_id = p.id
                LEFT JOIN status_cat s ON c.status_id = s.id
                LEFT JOIN usuarios u1 ON c.atendente_id = u1.id
                LEFT JOIN usuarios u2 ON c.tecnico_responsavel_id = u2.id
                WHERE c.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Listar CATs
     */
    public function getAll($filters = []) {
        $sql = "SELECT c.*,
                cl.razao_social as cliente_nome,
                s.nome as status_nome,
                s.cor_hex as status_cor,
                u1.nome as atendente_nome,
                u2.nome as tecnico_nome
                FROM cat c
                LEFT JOIN clientes cl ON c.cliente_id = cl.id
                LEFT JOIN status_cat s ON c.status_id = s.id
                LEFT JOIN usuarios u1 ON c.atendente_id = u1.id
                LEFT JOIN usuarios u2 ON c.tecnico_responsavel_id = u2.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status_id'])) {
            $sql .= " AND c.status_id = ?";
            $params[] = $filters['status_id'];
        }
        
        if (!empty($filters['cliente_id'])) {
            $sql .= " AND c.cliente_id = ?";
            $params[] = $filters['cliente_id'];
        }
        
        if (!empty($filters['tecnico_id'])) {
            $sql .= " AND c.tecnico_responsavel_id = ?";
            $params[] = $filters['tecnico_id'];
        }
        
        if (!empty($filters['data_inicio'])) {
            $sql .= " AND DATE(c.data_abertura) >= ?";
            $params[] = $filters['data_inicio'];
        }
        
        if (!empty($filters['data_fim'])) {
            $sql .= " AND DATE(c.data_abertura) <= ?";
            $params[] = $filters['data_fim'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.numero_cat LIKE ? OR c.numero_sac LIKE ? 
                      OR cl.razao_social LIKE ? OR c.problema_reclamado LIKE ?)";
            $search = "%{$filters['search']}%";
            $params = array_merge($params, array_fill(0, 4, $search));
        }
        
        // Paginação
        $limit = $filters['limit'] ?? 20;
        $offset = ($filters['page'] ?? 0) * $limit;
        
        $sql .= " ORDER BY c.data_abertura DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Contar total de CATs
     */
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM cat c 
                LEFT JOIN clientes cl ON c.cliente_id = cl.id
                WHERE 1=1";
        $params = [];
        
        if (!empty($filters['status_id'])) {
            $sql .= " AND c.status_id = ?";
            $params[] = $filters['status_id'];
        }
        
        if (!empty($filters['cliente_id'])) {
            $sql .= " AND c.cliente_id = ?";
            $params[] = $filters['cliente_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.numero_cat LIKE ? OR c.numero_sac LIKE ? 
                      OR cl.razao_social LIKE ? OR c.problema_reclamado LIKE ?)";
            $search = "%{$filters['search']}%";
            $params = array_merge($params, array_fill(0, 4, $search));
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    /**
     * Atualizar CAT
     */
    public function update($id, $data) {
        // Verificar se pode editar
        $cat = $this->findById($id);
        if (!$cat) return false;
        
        // Verificar se status permite edição
        $sql = "SELECT permite_edicao FROM status_cat WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$cat['status_id']]);
        $status = $stmt->fetch();
        
        if (!$status['permite_edicao']) {
            return false;
        }
        
        $fields = [];
        $params = [];
        
        $allowedFields = [
            'contato_nome', 'contato_telefone', 'contato_email', 'contato_cargo',
            'produto_id', 'produto_descricao', 'numero_serie_produto',
            'problema_reclamado', 'diagnostico_tecnico', 'solucao_aplicada',
            'tipo_atendimento', 'tecnico_responsavel_id', 'data_atendimento',
            'hora_inicio_atendimento', 'hora_fim_atendimento', 'tempo_total_minutos',
            'previsao_entrega', 'status_id', 'prioridade', 'observacoes',
            'valor_total', 'desconto', 'forma_pagamento'
        ];
        
        $statusChanged = false;
        $oldStatusId = $cat['status_id'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
                
                if ($field === 'status_id' && $data[$field] != $oldStatusId) {
                    $statusChanged = true;
                }
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE cat SET " . implode(", ", $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($params);
        
        // Se mudou status, registrar no histórico
        if ($result && $statusChanged) {
            $this->addHistorico($id, 'MUDANCA_STATUS', 'Status alterado', 
                              $oldStatusId, $data['status_id']);
        }
        
        return $result;
    }
    
    /**
     * Adicionar ao histórico
     */
    public function addHistorico($catId, $tipo, $descricao, $statusAnteriorId = null, $statusNovoId = null) {
        $sql = "INSERT INTO cat_historico (
                    cat_id, usuario_id, tipo_movimento, descricao,
                    status_anterior_id, status_novo_id
                ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $catId,
            $_SESSION['user_id'],
            $tipo,
            $descricao,
            $statusAnteriorId,
            $statusNovoId
        ]);
    }
    
    /**
     * Buscar histórico da CAT
     */
    public function getHistorico($catId) {
        $sql = "SELECT h.*, u.nome as usuario_nome,
                s1.nome as status_anterior_nome,
                s2.nome as status_novo_nome
                FROM cat_historico h
                JOIN usuarios u ON h.usuario_id = u.id
                LEFT JOIN status_cat s1 ON h.status_anterior_id = s1.id
                LEFT JOIN status_cat s2 ON h.status_novo_id = s2.id
                WHERE h.cat_id = ?
                ORDER BY h.data_movimento DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$catId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Buscar status disponíveis
     */
    public function getStatus() {
        $sql = "SELECT * FROM status_cat ORDER BY ordem";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Estatísticas do dashboard
     */
    public function getStats() {
        $stats = [];
        
        // Total de CATs abertas
        $sql = "SELECT COUNT(*) as total FROM cat c
                JOIN status_cat s ON c.status_id = s.id
                WHERE s.finaliza_cat = 0";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $stats['abertas'] = $result['total'];
        
        // CATs finalizadas hoje
        $sql = "SELECT COUNT(*) as total FROM cat c
                JOIN status_cat s ON c.status_id = s.id
                WHERE s.finaliza_cat = 1 AND DATE(c.data_fechamento) = CURDATE()";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $stats['finalizadas_hoje'] = $result['total'];
        
        // CATs aguardando
        $sql = "SELECT COUNT(*) as total FROM cat c
                WHERE c.status_id IN (3, 4)"; // Aguardando Peças, Aguardando Cliente
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $stats['aguardando'] = $result['total'];
        
        // Total de clientes ativos
        $sql = "SELECT COUNT(*) as total FROM clientes WHERE ativo = 1";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        $stats['clientes'] = $result['total'];
        
        return $stats;
    }
}
?>
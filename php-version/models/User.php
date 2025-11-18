<?php
/**
 * Modelo de Usuário
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Autenticar usuário
     */
    public function login($email, $senha) {
        $sql = "SELECT u.*, n.nome as nivel_nome, n.permissoes 
                FROM usuarios u
                JOIN niveis_usuario n ON u.nivel_id = n.id
                WHERE u.email = ? AND u.ativo = 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($senha, $user['senha'])) {
            // Atualizar último acesso
            $this->updateLastAccess($user['id']);
            
            // Criar sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_nome'] = $user['nome'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_nivel'] = $user['nivel_nome'];
            $_SESSION['user_nivel_id'] = $user['nivel_id'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        return true;
    }
    
    /**
     * Criar novo usuário
     */
    public function create($data) {
        $sql = "INSERT INTO usuarios (nome, email, senha, nivel_id, telefone, ativo) 
                VALUES (?, ?, ?, ?, ?, 1)";
        
        $hashedPassword = password_hash($data['senha'], PASSWORD_HASH_ALGO);
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nome'],
            $data['email'],
            $hashedPassword,
            $data['nivel_id'],
            $data['telefone'] ?? null
        ]);
    }
    
    /**
     * Buscar usuário por ID
     */
    public function findById($id) {
        $sql = "SELECT u.*, n.nome as nivel_nome 
                FROM usuarios u
                JOIN niveis_usuario n ON u.nivel_id = n.id
                WHERE u.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Listar todos os usuários
     */
    public function getAll($filters = []) {
        $sql = "SELECT u.*, n.nome as nivel_nome 
                FROM usuarios u
                JOIN niveis_usuario n ON u.nivel_id = n.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['ativo'])) {
            $sql .= " AND u.ativo = ?";
            $params[] = $filters['ativo'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (u.nome LIKE ? OR u.email LIKE ?)";
            $search = "%{$filters['search']}%";
            $params[] = $search;
            $params[] = $search;
        }
        
        $sql .= " ORDER BY u.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Atualizar usuário
     */
    public function update($id, $data) {
        $fields = [];
        $params = [];
        
        if (isset($data['nome'])) {
            $fields[] = "nome = ?";
            $params[] = $data['nome'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = ?";
            $params[] = $data['email'];
        }
        
        if (isset($data['nivel_id'])) {
            $fields[] = "nivel_id = ?";
            $params[] = $data['nivel_id'];
        }
        
        if (isset($data['telefone'])) {
            $fields[] = "telefone = ?";
            $params[] = $data['telefone'];
        }
        
        if (isset($data['ativo'])) {
            $fields[] = "ativo = ?";
            $params[] = $data['ativo'];
        }
        
        if (isset($data['senha'])) {
            $fields[] = "senha = ?";
            $params[] = password_hash($data['senha'], PASSWORD_HASH_ALGO);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE usuarios SET " . implode(", ", $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Verificar se email já existe
     */
    public function emailExists($email, $excludeId = null) {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Atualizar último acesso
     */
    private function updateLastAccess($userId) {
        $sql = "UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    /**
     * Buscar níveis de usuário
     */
    public function getNiveis() {
        $sql = "SELECT * FROM niveis_usuario ORDER BY id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Alterar senha
     */
    public function changePassword($userId, $senhaAtual, $senhaNova) {
        // Verificar senha atual
        $user = $this->findById($userId);
        
        if (!$user || !password_verify($senhaAtual, $user['senha'])) {
            return false;
        }
        
        // Atualizar senha
        $sql = "UPDATE usuarios SET senha = ? WHERE id = ?";
        $hashedPassword = password_hash($senhaNova, PASSWORD_HASH_ALGO);
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$hashedPassword, $userId]);
    }
}
?>
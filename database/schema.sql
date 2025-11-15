-- Sistema de Controle de CAT (Chamada de Assistência Técnica)
-- Banco de dados MySQL/MariaDB
-- Versão 1.0

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS cat_system DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cat_system;

-- ====================================
-- TABELA: Configuração da Empresa
-- ====================================
CREATE TABLE IF NOT EXISTS empresa_config (
    id INT PRIMARY KEY AUTO_INCREMENT,
    razao_social VARCHAR(255) NOT NULL,
    nome_fantasia VARCHAR(255),
    cnpj VARCHAR(20) UNIQUE,
    inscricao_estadual VARCHAR(50),
    endereco VARCHAR(255),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    uf CHAR(2),
    cep VARCHAR(10),
    telefone VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(255),
    website VARCHAR(255),
    logo_path VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ====================================
-- TABELA: Níveis de Usuário
-- ====================================
CREATE TABLE IF NOT EXISTS niveis_usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL UNIQUE,
    descricao VARCHAR(255),
    permissoes JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Inserir níveis padrão
INSERT INTO niveis_usuario (nome, descricao, permissoes) VALUES
('Administrador', 'Acesso total ao sistema', '{"all": true}'),
('Supervisor', 'Acesso a relatórios e gerenciamento', '{"cat": true, "relatorios": true, "usuarios": false}'),
('Técnico', 'Acesso a CATs e atendimentos', '{"cat": true, "relatorios": false, "usuarios": false}'),
('Atendente', 'Acesso básico para abertura de CATs', '{"cat": ["create", "read"], "relatorios": false, "usuarios": false}');

-- ====================================
-- TABELA: Usuários
-- ====================================
CREATE TABLE IF NOT EXISTS usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    nivel_id INT NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    ultimo_acesso DATETIME,
    foto_perfil VARCHAR(500),
    telefone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nivel_id) REFERENCES niveis_usuario(id)
);

-- ====================================
-- TABELA: Clientes
-- ====================================
CREATE TABLE IF NOT EXISTS clientes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_cliente VARCHAR(50) UNIQUE,
    razao_social VARCHAR(255) NOT NULL,
    nome_fantasia VARCHAR(255),
    tipo_pessoa ENUM('PF', 'PJ') DEFAULT 'PJ',
    cpf_cnpj VARCHAR(20) UNIQUE NOT NULL,
    inscricao_estadual VARCHAR(50),
    endereco VARCHAR(255),
    numero VARCHAR(20),
    complemento VARCHAR(100),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    uf CHAR(2),
    cep VARCHAR(10),
    telefone VARCHAR(20),
    celular VARCHAR(20),
    fax VARCHAR(20),
    email VARCHAR(255),
    contato_principal VARCHAR(255),
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT,
    FOREIGN KEY (created_by) REFERENCES usuarios(id)
);

-- ====================================
-- TABELA: Fornecedores
-- ====================================
CREATE TABLE IF NOT EXISTS fornecedores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_fornecedor VARCHAR(50) UNIQUE,
    razao_social VARCHAR(255) NOT NULL,
    nome_fantasia VARCHAR(255),
    cnpj VARCHAR(20) UNIQUE NOT NULL,
    inscricao_estadual VARCHAR(50),
    inscricao_municipal VARCHAR(50),
    endereco VARCHAR(255),
    numero VARCHAR(20),
    complemento VARCHAR(100),
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    uf CHAR(2),
    cep VARCHAR(10),
    telefone VARCHAR(20),
    celular VARCHAR(20),
    email VARCHAR(255),
    website VARCHAR(255),
    contato_principal VARCHAR(255),
    cargo_contato VARCHAR(100),
    banco VARCHAR(100),
    agencia VARCHAR(20),
    conta VARCHAR(20),
    pix_key VARCHAR(255),
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ====================================
-- TABELA: Categorias de Produtos
-- ====================================
CREATE TABLE IF NOT EXISTS categorias_produto (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL UNIQUE,
    descricao VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ====================================
-- TABELA: Produtos
-- ====================================
CREATE TABLE IF NOT EXISTS produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_produto VARCHAR(50) UNIQUE NOT NULL,
    referencia_antiga VARCHAR(50),
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    categoria_id INT,
    unidade VARCHAR(20) DEFAULT 'UN',
    marca VARCHAR(100),
    modelo VARCHAR(100),
    numero_serie VARCHAR(100),
    fornecedor_id INT,
    preco_custo DECIMAL(15,2),
    preco_venda DECIMAL(15,2),
    estoque_minimo INT DEFAULT 0,
    estoque_atual INT DEFAULT 0,
    garantia_meses INT DEFAULT 12,
    peso DECIMAL(10,3),
    dimensoes VARCHAR(100),
    imagem_path VARCHAR(500),
    manual_path VARCHAR(500),
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias_produto(id),
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
);

-- ====================================
-- TABELA: Serviços
-- ====================================
CREATE TABLE IF NOT EXISTS servicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_servico VARCHAR(50) UNIQUE NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    tempo_estimado INT COMMENT 'Tempo em minutos',
    valor_hora DECIMAL(10,2),
    valor_fixo DECIMAL(10,2),
    tipo_cobranca ENUM('HORA', 'FIXO') DEFAULT 'HORA',
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ====================================
-- TABELA: Materiais
-- ====================================
CREATE TABLE IF NOT EXISTS materiais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    codigo_material VARCHAR(50) UNIQUE NOT NULL,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    unidade VARCHAR(20) DEFAULT 'UN',
    marca VARCHAR(100),
    fornecedor_id INT,
    preco_custo DECIMAL(15,2),
    preco_venda DECIMAL(15,2),
    estoque_minimo INT DEFAULT 0,
    estoque_atual INT DEFAULT 0,
    localizacao VARCHAR(100),
    observacoes TEXT,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
);

-- ====================================
-- TABELA: Status de CAT
-- ====================================
CREATE TABLE IF NOT EXISTS status_cat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL UNIQUE,
    cor_hex VARCHAR(7),
    ordem INT DEFAULT 0,
    permite_edicao BOOLEAN DEFAULT TRUE,
    finaliza_cat BOOLEAN DEFAULT FALSE
);

-- Inserir status padrão
INSERT INTO status_cat (nome, cor_hex, ordem, permite_edicao, finaliza_cat) VALUES
('Em Aberto', '#FFEB3B', 1, TRUE, FALSE),
('Em Atendimento', '#2196F3', 2, TRUE, FALSE),
('Aguardando Peças', '#FF9800', 3, TRUE, FALSE),
('Aguardando Cliente', '#9C27B0', 4, TRUE, FALSE),
('Finalizada', '#4CAF50', 5, FALSE, TRUE),
('Cancelada', '#F44336', 6, FALSE, TRUE);

-- ====================================
-- TABELA: CAT (Chamada de Assistência Técnica)
-- ====================================
CREATE TABLE IF NOT EXISTS cat (
    id INT PRIMARY KEY AUTO_INCREMENT,
    numero_cat VARCHAR(20) UNIQUE NOT NULL,
    numero_sac VARCHAR(20),
    pedido_numero VARCHAR(50),
    data_abertura DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    hora_abertura TIME,
    cliente_id INT NOT NULL,
    contato_nome VARCHAR(255),
    contato_telefone VARCHAR(20),
    contato_email VARCHAR(255),
    contato_cargo VARCHAR(100),
    produto_id INT,
    produto_descricao TEXT,
    numero_serie_produto VARCHAR(100),
    problema_reclamado TEXT NOT NULL,
    diagnostico_tecnico TEXT,
    solucao_aplicada TEXT,
    tipo_atendimento ENUM('GARANTIA', 'FORA_GARANTIA', 'CONTRATO', 'AVULSO') DEFAULT 'AVULSO',
    tecnico_responsavel_id INT,
    data_atendimento DATE,
    hora_inicio_atendimento TIME,
    hora_fim_atendimento TIME,
    tempo_total_minutos INT,
    previsao_entrega DATE,
    data_fechamento DATETIME,
    status_id INT NOT NULL DEFAULT 1,
    prioridade ENUM('BAIXA', 'NORMAL', 'ALTA', 'URGENTE') DEFAULT 'NORMAL',
    observacoes TEXT,
    valor_total DECIMAL(15,2),
    desconto DECIMAL(15,2),
    forma_pagamento VARCHAR(50),
    atendente_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (produto_id) REFERENCES produtos(id),
    FOREIGN KEY (tecnico_responsavel_id) REFERENCES usuarios(id),
    FOREIGN KEY (status_id) REFERENCES status_cat(id),
    FOREIGN KEY (atendente_id) REFERENCES usuarios(id),
    INDEX idx_numero_cat (numero_cat),
    INDEX idx_data_abertura (data_abertura),
    INDEX idx_status (status_id),
    INDEX idx_cliente (cliente_id)
);

-- ====================================
-- TABELA: Itens da CAT (Produtos)
-- ====================================
CREATE TABLE IF NOT EXISTS cat_produtos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cat_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    valor_unitario DECIMAL(15,2),
    desconto DECIMAL(5,2) DEFAULT 0,
    valor_total DECIMAL(15,2),
    garantia ENUM('SIM', 'NAO') DEFAULT 'NAO',
    observacoes TEXT,
    FOREIGN KEY (cat_id) REFERENCES cat(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

-- ====================================
-- TABELA: Serviços da CAT
-- ====================================
CREATE TABLE IF NOT EXISTS cat_servicos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cat_id INT NOT NULL,
    servico_id INT NOT NULL,
    descricao_executada TEXT,
    tempo_executado INT COMMENT 'Tempo em minutos',
    valor_hora DECIMAL(10,2),
    valor_total DECIMAL(15,2),
    tecnico_id INT,
    data_execucao DATE,
    FOREIGN KEY (cat_id) REFERENCES cat(id) ON DELETE CASCADE,
    FOREIGN KEY (servico_id) REFERENCES servicos(id),
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(id)
);

-- ====================================
-- TABELA: Materiais da CAT
-- ====================================
CREATE TABLE IF NOT EXISTS cat_materiais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cat_id INT NOT NULL,
    material_id INT NOT NULL,
    quantidade DECIMAL(10,3) NOT NULL DEFAULT 1,
    valor_unitario DECIMAL(15,2),
    valor_total DECIMAL(15,2),
    observacoes TEXT,
    FOREIGN KEY (cat_id) REFERENCES cat(id) ON DELETE CASCADE,
    FOREIGN KEY (material_id) REFERENCES materiais(id)
);

-- ====================================
-- TABELA: Histórico/Movimentação da CAT
-- ====================================
CREATE TABLE IF NOT EXISTS cat_historico (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cat_id INT NOT NULL,
    data_movimento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    tipo_movimento VARCHAR(50),
    descricao TEXT NOT NULL,
    status_anterior_id INT,
    status_novo_id INT,
    observacoes TEXT,
    FOREIGN KEY (cat_id) REFERENCES cat(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (status_anterior_id) REFERENCES status_cat(id),
    FOREIGN KEY (status_novo_id) REFERENCES status_cat(id)
);

-- ====================================
-- TABELA: Anexos da CAT
-- ====================================
CREATE TABLE IF NOT EXISTS cat_anexos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cat_id INT NOT NULL,
    tipo ENUM('IMAGEM', 'VIDEO', 'DOCUMENTO', 'AUDIO') NOT NULL,
    nome_arquivo VARCHAR(255) NOT NULL,
    caminho_arquivo VARCHAR(500) NOT NULL,
    tamanho_bytes BIGINT,
    descricao TEXT,
    usuario_id INT,
    data_upload DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cat_id) REFERENCES cat(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- ====================================
-- TABELA: Avaliação de Qualidade
-- ====================================
CREATE TABLE IF NOT EXISTS cat_avaliacao_qualidade (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cat_id INT NOT NULL UNIQUE,
    necessidade_troca_garantia ENUM('SIM', 'NAO'),
    peca_retornou_empresa ENUM('SIM', 'NAO'),
    necessario_rnc ENUM('SIM', 'NAO'),
    numero_rnc VARCHAR(50),
    observacoes TEXT,
    avaliado_por INT,
    data_avaliacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cat_id) REFERENCES cat(id) ON DELETE CASCADE,
    FOREIGN KEY (avaliado_por) REFERENCES usuarios(id)
);

-- ====================================
-- TABELA: Pesquisa de Satisfação
-- ====================================
CREATE TABLE IF NOT EXISTS cat_satisfacao (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cat_id INT NOT NULL UNIQUE,
    contato_resposta VARCHAR(255),
    cargo_funcao VARCHAR(100),
    nota_prazo_atendimento INT CHECK (nota_prazo_atendimento >= 0 AND nota_prazo_atendimento <= 10),
    nota_qualidade_servico INT CHECK (nota_qualidade_servico >= 0 AND nota_qualidade_servico <= 10),
    nota_geral INT CHECK (nota_geral >= 0 AND nota_geral <= 10),
    comentarios TEXT,
    sugestoes TEXT,
    data_pesquisa DATETIME DEFAULT CURRENT_TIMESTAMP,
    pesquisador_id INT,
    FOREIGN KEY (cat_id) REFERENCES cat(id) ON DELETE CASCADE,
    FOREIGN KEY (pesquisador_id) REFERENCES usuarios(id)
);

-- ====================================
-- TABELA: Assinaturas Digitais
-- ====================================
CREATE TABLE IF NOT EXISTS cat_assinaturas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cat_id INT NOT NULL,
    tipo_assinante ENUM('CLIENTE', 'TECNICO', 'SUPERVISOR') NOT NULL,
    nome_assinante VARCHAR(255) NOT NULL,
    documento_assinante VARCHAR(20),
    assinatura_base64 TEXT,
    ip_address VARCHAR(45),
    data_assinatura DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cat_id) REFERENCES cat(id) ON DELETE CASCADE
);

-- ====================================
-- TABELA: Fechamento Mensal
-- ====================================
CREATE TABLE IF NOT EXISTS fechamentos_mensais (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mes INT NOT NULL,
    ano INT NOT NULL,
    data_fechamento DATETIME NOT NULL,
    usuario_responsavel_id INT NOT NULL,
    total_cats INT DEFAULT 0,
    total_cats_finalizadas INT DEFAULT 0,
    total_cats_canceladas INT DEFAULT 0,
    total_faturamento DECIMAL(15,2) DEFAULT 0,
    observacoes TEXT,
    status ENUM('ABERTO', 'FECHADO') DEFAULT 'ABERTO',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_responsavel_id) REFERENCES usuarios(id),
    UNIQUE KEY uk_mes_ano (mes, ano)
);

-- ====================================
-- TABELA: Logs do Sistema
-- ====================================
CREATE TABLE IF NOT EXISTS sistema_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    acao VARCHAR(100) NOT NULL,
    tabela VARCHAR(50),
    registro_id INT,
    dados_anteriores JSON,
    dados_novos JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- ====================================
-- VIEWS para Relatórios
-- ====================================

-- View: CATs com informações completas
CREATE OR REPLACE VIEW vw_cat_completa AS
SELECT 
    c.*,
    cl.razao_social AS cliente_nome,
    cl.cpf_cnpj AS cliente_documento,
    cl.telefone AS cliente_telefone,
    cl.email AS cliente_email,
    p.nome AS produto_nome,
    p.codigo_produto,
    s.nome AS status_nome,
    s.cor_hex AS status_cor,
    u1.nome AS atendente_nome,
    u2.nome AS tecnico_nome,
    DATEDIFF(IFNULL(c.data_fechamento, NOW()), c.data_abertura) AS dias_aberto
FROM cat c
LEFT JOIN clientes cl ON c.cliente_id = cl.id
LEFT JOIN produtos p ON c.produto_id = p.id
LEFT JOIN status_cat s ON c.status_id = s.id
LEFT JOIN usuarios u1 ON c.atendente_id = u1.id
LEFT JOIN usuarios u2 ON c.tecnico_responsavel_id = u2.id;

-- View: Resumo por Cliente
CREATE OR REPLACE VIEW vw_resumo_cliente AS
SELECT 
    cl.id,
    cl.razao_social,
    cl.cpf_cnpj,
    COUNT(c.id) AS total_cats,
    SUM(CASE WHEN s.finaliza_cat = TRUE THEN 1 ELSE 0 END) AS cats_finalizadas,
    SUM(CASE WHEN s.nome = 'Em Aberto' THEN 1 ELSE 0 END) AS cats_abertas,
    SUM(IFNULL(c.valor_total, 0)) AS valor_total_servicos,
    MAX(c.data_abertura) AS ultima_cat
FROM clientes cl
LEFT JOIN cat c ON cl.id = c.cliente_id
LEFT JOIN status_cat s ON c.status_id = s.id
GROUP BY cl.id;

-- ====================================
-- TRIGGERS
-- ====================================

-- Trigger para gerar número de CAT automaticamente
DELIMITER $$
CREATE TRIGGER before_insert_cat
BEFORE INSERT ON cat
FOR EACH ROW
BEGIN
    DECLARE next_number INT;
    DECLARE current_year VARCHAR(2);
    
    SET current_year = DATE_FORMAT(NOW(), '%y');
    
    SELECT IFNULL(MAX(CAST(SUBSTRING(numero_cat, 1, LENGTH(numero_cat) - 3) AS UNSIGNED)), 0) + 1
    INTO next_number
    FROM cat
    WHERE numero_cat LIKE CONCAT('%/', current_year);
    
    SET NEW.numero_cat = CONCAT(next_number, '/', current_year);
    
    IF NEW.numero_sac IS NULL THEN
        SET NEW.numero_sac = CONCAT(LPAD(next_number, 6, '0'), '-', current_year, 'M');
    END IF;
END$$
DELIMITER ;

-- Trigger para registrar histórico de mudança de status
DELIMITER $$
CREATE TRIGGER after_update_cat_status
AFTER UPDATE ON cat
FOR EACH ROW
BEGIN
    IF OLD.status_id != NEW.status_id THEN
        INSERT INTO cat_historico (
            cat_id, 
            usuario_id, 
            tipo_movimento, 
            descricao, 
            status_anterior_id, 
            status_novo_id
        ) VALUES (
            NEW.id,
            NEW.atendente_id,
            'MUDANCA_STATUS',
            CONCAT('Status alterado de ', 
                (SELECT nome FROM status_cat WHERE id = OLD.status_id), 
                ' para ', 
                (SELECT nome FROM status_cat WHERE id = NEW.status_id)),
            OLD.status_id,
            NEW.status_id
        );
    END IF;
END$$
DELIMITER ;

-- ====================================
-- ÍNDICES ADICIONAIS
-- ====================================
CREATE INDEX idx_cat_data_mes ON cat(YEAR(data_abertura), MONTH(data_abertura));
CREATE INDEX idx_cliente_ativo ON clientes(ativo);
CREATE INDEX idx_produto_ativo ON produtos(ativo);
CREATE INDEX idx_usuario_email ON usuarios(email);

-- ====================================
-- DADOS INICIAIS
-- ====================================

-- Inserir usuário administrador padrão (senha: admin123)
INSERT INTO usuarios (nome, email, senha, nivel_id) VALUES
('Administrador', 'admin@sistema.com', '$2a$10$rBV2JDeWW3.vKyeQcM8fFOoaK2D1Mz6hX8uJLQoHqm6I5h5LwBFZe', 1);

-- Inserir configuração da empresa padrão
INSERT INTO empresa_config (razao_social, nome_fantasia) VALUES
('Sua Empresa LTDA', 'Sua Empresa');
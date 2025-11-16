# Documentação da API - Sistema CAT

## Base URL
```
http://localhost:3000/api
```

## Autenticação

Todas as rotas (exceto login) requerem token JWT no header:
```
Authorization: Bearer {token}
```

---

## 🔐 Autenticação

### Login
```http
POST /auth/login
```
**Body:**
```json
{
  "email": "admin@sistema.com",
  "senha": "admin123"
}
```
**Response:**
```json
{
  "success": true,
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "user": {
    "id": 1,
    "nome": "Administrador",
    "email": "admin@sistema.com",
    "nivel": "Administrador",
    "permissoes": {"all": true}
  }
}
```

### Registrar Usuário
```http
POST /auth/register
```
**Headers:** `Authorization: Bearer {token}`
**Body:**
```json
{
  "nome": "João Silva",
  "email": "joao@empresa.com",
  "senha": "senha123",
  "nivel_id": 3,
  "telefone": "(11) 98765-4321"
}
```

### Trocar Senha
```http
POST /auth/change-password
```
**Headers:** `Authorization: Bearer {token}`
**Body:**
```json
{
  "senhaAtual": "senha123",
  "senhaNova": "novaSenha456"
}
```

---

## 📋 CATs (Chamadas de Assistência Técnica)

### Listar CATs
```http
GET /cat
```
**Query Parameters:**
- `page` (default: 1)
- `limit` (default: 10)
- `search` - Busca por número, cliente ou problema
- `status` - ID do status
- `cliente_id` - ID do cliente
- `data_inicio` - Data inicial (YYYY-MM-DD)
- `data_fim` - Data final (YYYY-MM-DD)
- `tecnico_id` - ID do técnico

**Response:**
```json
{
  "success": true,
  "data": [{
    "id": 1,
    "numero_cat": "1/24",
    "numero_sac": "000001-24M",
    "cliente_nome": "Hospital Central",
    "problema_reclamado": "Motor do dorso com defeito",
    "status_nome": "Em Aberto",
    "status_cor": "#FFEB3B",
    "data_abertura": "2024-01-15T10:30:00Z"
  }],
  "pagination": {
    "page": 1,
    "limit": 10,
    "total": 50,
    "totalPages": 5
  }
}
```

### Buscar CAT por ID
```http
GET /cat/{id}
```
**Response:** Retorna CAT completa com produtos, serviços, histórico, anexos, etc.

### Criar Nova CAT
```http
POST /cat
```
**Body:**
```json
{
  "cliente_id": 1,
  "contato_nome": "Dr. João Silva",
  "contato_telefone": "(11) 98765-4321",
  "contato_email": "joao@hospital.com",
  "produto_id": 5,
  "produto_descricao": "Cama hospitalar motorizada",
  "numero_serie_produto": "CH2024-001",
  "problema_reclamado": "Motor do dorso não funciona",
  "tipo_atendimento": "GARANTIA",
  "prioridade": "ALTA",
  "observacoes": "Cliente preferencial",
  "produtos": [
    {
      "produto_id": 10,
      "quantidade": 1,
      "valor_unitario": 500.00,
      "garantia": "SIM"
    }
  ]
}
```

### Atualizar CAT
```http
PUT /cat/{id}
```
**Body:** Campos a serem atualizados
```json
{
  "status_id": 2,
  "tecnico_responsavel_id": 3,
  "diagnostico_tecnico": "Motor queimado, necessária substituição",
  "solucao_aplicada": "Troca do motor realizada",
  "data_atendimento": "2024-01-16",
  "tempo_total_minutos": 120
}
```

### Adicionar Anexo
```http
POST /cat/{id}/anexos
```
**Body:**
```json
{
  "tipo": "IMAGEM",
  "nome_arquivo": "motor_defeito.jpg",
  "caminho_arquivo": "/uploads/2024/01/motor_defeito.jpg",
  "tamanho_bytes": 2048576,
  "descricao": "Foto do motor com defeito"
}
```

---

## 👥 Clientes

### Listar Clientes
```http
GET /clientes
```
**Query Parameters:**
- `page` (default: 1)
- `limit` (default: 10)
- `search` - Busca por nome, CNPJ, email ou cidade
- `ativo` - true/false/all (default: true)

### Buscar Cliente
```http
GET /clientes/{id}
```

### Criar Cliente
```http
POST /clientes
```
**Body:**
```json
{
  "razao_social": "Hospital Central LTDA",
  "nome_fantasia": "Hospital Central",
  "tipo_pessoa": "PJ",
  "cpf_cnpj": "12.345.678/0001-90",
  "inscricao_estadual": "123.456.789.012",
  "endereco": "Rua das Flores, 123",
  "bairro": "Centro",
  "cidade": "São Paulo",
  "uf": "SP",
  "cep": "01234-567",
  "telefone": "(11) 3333-4444",
  "email": "contato@hospital.com",
  "contato_principal": "Dr. João Silva"
}
```

### Atualizar Cliente
```http
PUT /clientes/{id}
```
**Body:** Campos a serem atualizados

### Ativar/Desativar Cliente
```http
PATCH /clientes/{id}/toggle-status
```

---

## 📦 Produtos

### Listar Produtos
```http
GET /produtos
```
**Query Parameters:**
- `page` (default: 1)
- `limit` (default: 10)
- `search` - Busca por código, nome, marca ou modelo
- `categoria_id` - ID da categoria
- `fornecedor_id` - ID do fornecedor
- `ativo` - true/false/all (default: true)

### Buscar Produto
```http
GET /produtos/{id}
```

### Criar Produto
```http
POST /produtos
```
**Body:**
```json
{
  "codigo_produto": "PROD-001",
  "nome": "Cama Hospitalar Motorizada",
  "descricao": "Cama com 3 movimentos motorizados",
  "categoria_id": 1,
  "marca": "MedEquip",
  "modelo": "CHM-3000",
  "fornecedor_id": 1,
  "preco_custo": 5000.00,
  "preco_venda": 8000.00,
  "estoque_minimo": 2,
  "estoque_atual": 10,
  "garantia_meses": 24
}
```

### Atualizar Produto
```http
PUT /produtos/{id}
```

### Ajustar Estoque
```http
POST /produtos/{id}/ajustar-estoque
```
**Body:**
```json
{
  "quantidade": 5,
  "tipo": "ENTRADA",
  "motivo": "Recebimento de mercadoria"
}
```

### Listar Categorias
```http
GET /produtos/categorias/list
```

### Criar Categoria
```http
POST /produtos/categorias
```
**Body:**
```json
{
  "nome": "Equipamentos Hospitalares",
  "descricao": "Equipamentos para uso hospitalar"
}
```

---

## 🔧 Status de Resposta

| Código | Descrição |
|--------|-----------|
| 200 | Sucesso |
| 201 | Criado com sucesso |
| 400 | Erro de validação |
| 401 | Não autorizado |
| 404 | Não encontrado |
| 500 | Erro interno do servidor |

---

## 📝 Tipos de Enumeração

### Tipo de Pessoa
- `PF` - Pessoa Física
- `PJ` - Pessoa Jurídica

### Tipo de Atendimento
- `GARANTIA`
- `FORA_GARANTIA`
- `CONTRATO`
- `AVULSO`

### Prioridade
- `BAIXA`
- `NORMAL`
- `ALTA`
- `URGENTE`

### Tipo de Anexo
- `IMAGEM`
- `VIDEO`
- `DOCUMENTO`
- `AUDIO`

### Tipo de Movimento de Estoque
- `ENTRADA`
- `SAIDA`

---

## 🔑 Níveis de Usuário

| ID | Nível | Descrição | Permissões |
|----|-------|-----------|------------|
| 1 | Administrador | Acesso total | Todas |
| 2 | Supervisor | Gerenciamento e relatórios | CATs, Relatórios |
| 3 | Técnico | Atendimentos | CATs |
| 4 | Atendente | Abertura de CATs | Criar/Ler CATs |

---

## 📌 Observações

1. **Autenticação**: Todas as rotas (exceto `/auth/login`) requerem token JWT
2. **Paginação**: Use os parâmetros `page` e `limit` para paginar resultados
3. **Filtros**: Combine múltiplos parâmetros de query para filtrar resultados
4. **Datas**: Use formato ISO 8601 (YYYY-MM-DD ou YYYY-MM-DDTHH:mm:ss)
5. **Valores monetários**: Envie como números decimais (ex: 1234.56)
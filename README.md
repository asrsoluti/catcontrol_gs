# Sistema CAT - Controle de Assistência Técnica

## 📋 Visão Geral

Sistema completo para gerenciamento de Chamadas de Assistência Técnica (CAT), desenvolvido com TypeScript, Node.js e MySQL/MariaDB. O sistema oferece controle total sobre atendimentos técnicos, desde a abertura até o fechamento, com recursos avançados de gerenciamento, relatórios e avaliações.

## 🚀 Funcionalidades Principais

### ✅ Funcionalidades Implementadas

1. **Sistema de Autenticação**
   - Login com email e senha
   - Tokens JWT para segurança
   - Níveis de acesso (Administrador, Supervisor, Técnico, Atendente)
   - Controle de permissões por nível

2. **Gestão de CATs**
   - Criação de novas CATs com numeração automática
   - Acompanhamento do status (Em Aberto, Em Atendimento, Aguardando Peças, etc.)
   - Histórico completo de movimentações
   - Anexos de imagens, vídeos e documentos

3. **Cadastros Completos**
   - **Clientes**: CPF/CNPJ, endereços, contatos
   - **Produtos**: Código, descrição, garantia, fornecedor
   - **Serviços**: Tempo estimado, valores
   - **Materiais**: Estoque, preços
   - **Fornecedores**: Dados completos, bancários

4. **Dashboard**
   - Visualização de CATs abertas
   - Estatísticas em tempo real
   - Últimas movimentações
   - Indicadores de desempenho

5. **Banco de Dados**
   - Estrutura completa MySQL/MariaDB
   - Triggers para automação
   - Views para relatórios
   - Índices otimizados

### 📝 Funcionalidades em Desenvolvimento

1. **Módulo de Relatórios**
   - Relatórios por período
   - Filtros por cliente, técnico, status
   - Exportação para PDF/Excel
   - Gráficos estatísticos

2. **Avaliação de Qualidade**
   - Formulário de avaliação pós-atendimento
   - Necessidade de troca em garantia
   - Abertura de RNC (Registro de Não Conformidade)

3. **Pesquisa de Satisfação**
   - Avaliação do cliente
   - Notas de 0 a 10
   - Comentários e sugestões
   - Relatórios de satisfação

4. **Impressão de CATs**
   - Layout personalizado
   - QR Code para rastreamento
   - Assinatura digital do cliente
   - Envio por email

5. **Fechamento Mensal**
   - Consolidação de CATs do mês
   - Cálculo de faturamento
   - Relatórios gerenciais
   - Bloqueio de edições

6. **Configurações da Empresa**
   - Upload de logomarca
   - Dados da empresa
   - Personalização de documentos
   - Configurações de email

## 🛠️ Tecnologias Utilizadas

- **Backend**: Node.js + TypeScript
- **Framework**: Hono (Web framework rápido)
- **Banco de Dados**: MySQL/MariaDB
- **Autenticação**: JWT (JSON Web Tokens)
- **Frontend**: HTML5 + TailwindCSS + JavaScript Vanilla
- **Upload**: Multer para arquivos
- **PDF**: html-pdf-node para geração
- **Imagens**: Sharp para processamento

## 📁 Estrutura do Projeto

```
webapp/
├── src/
│   ├── config/          # Configurações (database, auth)
│   ├── middleware/      # Middlewares (autenticação)
│   ├── routes/          # Rotas da API
│   └── index.ts        # Arquivo principal
├── database/
│   └── schema.sql      # Estrutura do banco de dados
├── uploads/            # Arquivos enviados
├── .env               # Configurações de ambiente
├── package.json       # Dependências
└── README.md         # Documentação
```

## 🔧 Instalação e Configuração

### Pré-requisitos

- Node.js 18+ instalado
- MySQL 8.0+ ou MariaDB 10.5+
- Git para versionamento

### Passo a Passo

1. **Clone o repositório**
```bash
git clone [url-do-repositorio]
cd webapp
```

2. **Instale as dependências**
```bash
npm install
```

3. **Configure o banco de dados**

Primeiro, crie o banco de dados executando o script SQL:
```bash
mysql -u root -p < database/schema.sql
```

4. **Configure as variáveis de ambiente**

Edite o arquivo `.env` com suas configurações:
```env
DB_HOST=localhost
DB_PORT=3306
DB_USER=seu_usuario
DB_PASSWORD=sua_senha
DB_NAME=cat_system
```

5. **Execute o sistema**

Modo desenvolvimento:
```bash
npm run dev
```

Modo produção:
```bash
npm run build
npm start
```

6. **Acesse o sistema**

Abra o navegador em: `http://localhost:3000`

**Credenciais padrão:**
- Email: admin@sistema.com
- Senha: admin123

## 📊 Modelo de Dados

### Tabelas Principais

- **usuarios**: Usuários do sistema
- **clientes**: Cadastro de clientes
- **produtos**: Catálogo de produtos
- **servicos**: Serviços oferecidos
- **materiais**: Materiais utilizados
- **cat**: Chamadas de Assistência Técnica
- **cat_produtos**: Produtos da CAT
- **cat_servicos**: Serviços executados
- **cat_materiais**: Materiais utilizados
- **cat_historico**: Histórico de movimentações
- **cat_anexos**: Arquivos anexados
- **cat_avaliacao_qualidade**: Avaliações de qualidade
- **cat_satisfacao**: Pesquisas de satisfação

## 🔄 APIs Disponíveis

### Autenticação
- `POST /api/auth/login` - Login no sistema
- `POST /api/auth/register` - Cadastro de usuário
- `POST /api/auth/change-password` - Trocar senha

### CATs
- `GET /api/cat` - Listar CATs
- `GET /api/cat/:id` - Buscar CAT por ID
- `POST /api/cat` - Criar nova CAT
- `PUT /api/cat/:id` - Atualizar CAT
- `POST /api/cat/:id/anexos` - Adicionar anexo

### Clientes
- `GET /api/clientes` - Listar clientes
- `GET /api/clientes/:id` - Buscar cliente
- `POST /api/clientes` - Criar cliente
- `PUT /api/clientes/:id` - Atualizar cliente
- `PATCH /api/clientes/:id/toggle-status` - Ativar/Desativar

## 🚦 Status das CATs

1. **Em Aberto** - CAT recém criada
2. **Em Atendimento** - Técnico trabalhando
3. **Aguardando Peças** - Esperando material
4. **Aguardando Cliente** - Dependendo do cliente
5. **Finalizada** - Atendimento concluído
6. **Cancelada** - CAT cancelada

## 📈 Próximos Passos

1. **Completar módulos pendentes**
   - Interface completa de CATs
   - Sistema de impressão
   - Relatórios avançados
   - Upload de anexos

2. **Melhorias de UX**
   - Interface responsiva mobile
   - Notificações em tempo real
   - Dashboard interativo
   - Temas personalizáveis

3. **Integrações**
   - API REST completa
   - Webhooks para eventos
   - Integração com WhatsApp
   - Email automático

4. **Segurança**
   - Auditoria de ações
   - Backup automático
   - Criptografia de dados sensíveis
   - 2FA (autenticação de dois fatores)

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature
3. Commit suas mudanças
4. Push para a branch
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob licença proprietária. Todos os direitos reservados.

## 📞 Suporte

Para suporte e dúvidas:
- Email: suporte@sistema.com
- Documentação: /docs
- Issues: GitHub Issues

## 🎯 Notas Importantes

⚠️ **ATENÇÃO**: Este sistema requer um banco de dados MySQL/MariaDB externo. Não é possível executar com banco de dados em memória ou SQLite devido às funcionalidades específicas do MySQL utilizadas (triggers, views, JSON fields).

💡 **DICA**: Para desenvolvimento local, recomenda-se usar Docker com MySQL:
```bash
docker run -d -p 3306:3306 -e MYSQL_ROOT_PASSWORD=senha mysql:8.0
```

---
Desenvolvido com ❤️ para gestão eficiente de assistência técnica
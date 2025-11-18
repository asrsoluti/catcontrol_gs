# 🐘 Sistema CAT - Versão PHP

## Versão PHP para Hospedagem cPanel

Esta é a versão PHP do Sistema CAT, desenvolvida especialmente para rodar em **hospedagens compartilhadas com cPanel**.

---

## 📊 Sobre Esta Versão

A versão PHP foi criada para atender usuários que:
- ✅ Possuem hospedagem compartilhada (cPanel)
- ✅ Não têm acesso a VPS ou servidor dedicado
- ✅ Precisam de instalação simples via FTP
- ✅ Querem começar a usar rapidamente

---

## 🔧 Tecnologias Utilizadas

- **PHP** 7.4+ (compatível com PHP 8.x)
- **MySQL** 5.7+ ou MariaDB 10.2+
- **PDO** para acesso seguro ao banco de dados
- **TailwindCSS** para interface responsiva
- **FontAwesome** para ícones
- **Vanilla JavaScript** para interatividade

---

## ✨ Funcionalidades

### ✅ Implementadas

1. **Sistema de Login**
   - Autenticação segura com sessões PHP
   - 4 níveis de usuário (Admin, Supervisor, Técnico, Atendente)
   - Controle de permissões por nível

2. **Dashboard**
   - Estatísticas em tempo real
   - CATs abertas, finalizadas, aguardando
   - Total de clientes ativos
   - Últimas CATs registradas

3. **Gestão de CATs**
   - Criar, visualizar, editar CATs
   - Numeração automática (Trigger MySQL)
   - Histórico completo de movimentações
   - Status personalizáveis com cores
   - Filtros avançados

4. **Cadastro de Clientes**
   - PF e PJ
   - Todos os dados cadastrais
   - Busca e filtros
   - Controle de ativo/inativo

5. **Cadastro de Produtos**
   - Código, descrição, garantia
   - Controle de estoque
   - Categorias
   - Vinculação com fornecedores

6. **Interface Moderna**
   - Layout responsivo
   - Design profissional
   - Navegação intuitiva
   - Mensagens flash (sucesso/erro)

---

## 📁 Estrutura do Projeto

```
php-version/
├── config/
│   ├── config.php         # Configurações gerais
│   └── database.php       # Conexão MySQL (PDO)
├── models/
│   ├── User.php          # Modelo de usuário
│   ├── Cliente.php       # Modelo de cliente
│   ├── CAT.php           # Modelo de CAT
│   └── Produto.php       # Modelo de produto
├── includes/
│   ├── header.php        # Cabeçalho padrão
│   └── footer.php        # Rodapé padrão
├── cats/
│   ├── list.php         # Listar CATs
│   ├── view.php         # Visualizar CAT
│   ├── create.php       # Criar CAT
│   └── edit.php         # Editar CAT
├── clientes/
│   ├── list.php         # Listar clientes
│   ├── create.php       # Criar cliente
│   └── edit.php         # Editar cliente
├── produtos/
│   ├── list.php         # Listar produtos
│   └── edit.php         # Editar produto
├── uploads/             # Arquivos enviados
├── logs/                # Logs do sistema
├── .htaccess           # Configurações Apache
├── index.php           # Dashboard
├── login.php           # Login
└── logout.php          # Logout
```

---

## 🚀 Instalação Rápida

### **Passo 1: Preparar Banco de Dados**
```sql
1. Criar banco no cPanel
2. Criar usuário
3. Importar: ../database/schema.sql
```

### **Passo 2: Upload dos Arquivos**
```
1. Fazer upload via FTP/File Manager
2. Enviar para: public_html/cat-system/
```

### **Passo 3: Configurar**
```php
// Editar: config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'seu_banco');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');

// Editar: config/config.php
define('SITE_URL', 'https://seudominio.com/cat-system');
```

### **Passo 4: Acessar**
```
URL: https://seudominio.com/cat-system/login.php
Email: admin@sistema.com
Senha: admin123
```

**📖 Guia completo:** Ver arquivo `INSTALL_CPANEL.md`

---

## 🔑 Credenciais Padrão

**IMPORTANTE:** Altere a senha após primeiro login!

- **Email:** admin@sistema.com
- **Senha:** admin123

---

## 🔒 Segurança

### **Recursos de Segurança Implementados:**

1. **Senhas Criptografadas**
   - BCrypt com cost 10
   - Hash seguro no banco de dados

2. **SQL Injection Protection**
   - PDO com prepared statements
   - Parametrização de queries

3. **XSS Protection**
   - Sanitização de inputs
   - htmlspecialchars em outputs

4. **Session Security**
   - Cookie httponly
   - Regeneração de session ID
   - Timeout automático

5. **File Protection**
   - .htaccess protegendo config/
   - Validação de extensões de upload
   - Limite de tamanho de arquivo

---

## 📊 Requisitos do Servidor

### **Mínimos:**
- PHP 7.4+
- MySQL 5.7+ ou MariaDB 10.2+
- 100MB espaço em disco
- mod_rewrite habilitado

### **Recomendados:**
- PHP 8.0+
- MySQL 8.0+
- 500MB espaço em disco
- SSL/HTTPS ativo

### **Extensões PHP Necessárias:**
- PDO
- pdo_mysql
- mbstring
- json
- session
- zip (para backups)
- gd (para manipulação de imagens)

---

## 🆚 Comparação: PHP vs Node.js

| Característica | Versão PHP | Versão Node.js |
|----------------|------------|----------------|
| **Hospedagem** | ✅ Compartilhada (cPanel) | ❌ Requer VPS/Dedicado |
| **Instalação** | ✅ Simples (FTP) | ⚠️ Complexa (SSH/PM2) |
| **Custo** | 💰 Baixo (R$ 10-50/mês) | 💰💰 Médio/Alto (R$ 50-200/mês) |
| **Performance** | ⚡ Boa | ⚡⚡ Excelente |
| **Escalabilidade** | ⚠️ Limitada | ✅ Alta |
| **Manutenção** | ✅ Fácil | ⚠️ Requer conhecimento |
| **Deploy** | ✅ Upload FTP | ⚠️ Git + PM2 + Build |

**Escolha PHP se:** Você tem hospedagem compartilhada e quer simplicidade  
**Escolha Node.js se:** Você tem VPS e quer performance máxima

---

## 📈 Roadmap

### **Em Desenvolvimento:**
- [ ] Sistema de upload de anexos
- [ ] Impressão de CATs em PDF
- [ ] Relatórios avançados
- [ ] Pesquisa de satisfação
- [ ] Avaliação de qualidade
- [ ] Envio de emails automáticos
- [ ] Backup automático

### **Planejado:**
- [ ] API RESTful
- [ ] Integração WhatsApp
- [ ] App mobile
- [ ] Notificações push
- [ ] Chat interno

---

## 🔧 Configurações

### **Alterar URL do Sistema:**
```php
// config/config.php
define('SITE_URL', 'https://seudominio.com/pasta');
```

### **Configurar Upload:**
```php
// config/config.php
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);
```

### **Debug Mode:**
```php
// config/config.php
define('DEBUG_MODE', true);  // Habilitar em desenvolvimento
define('DEBUG_MODE', false); // Desabilitar em produção
```

---

## 🆘 Problemas Comuns

### **Erro: Cannot connect to database**
```
Verificar:
1. Credenciais em config/database.php
2. Usuário adicionado ao banco no cPanel
3. Banco de dados importado corretamente
```

### **Página em branco**
```
Verificar:
1. Versão do PHP (mínimo 7.4)
2. Extensões PHP instaladas
3. Logs em logs/php_errors.log
```

### **CSS não carrega**
```
Verificar:
1. SITE_URL em config/config.php
2. Permissões dos arquivos
3. .htaccess configurado
```

---

## 📞 Suporte

- **GitHub:** https://github.com/asrsoluti/catcontrol_gs
- **Issues:** https://github.com/asrsoluti/catcontrol_gs/issues
- **Guia de Instalação:** INSTALL_CPANEL.md

---

## 📝 Licença

Este projeto está sob licença proprietária. Todos os direitos reservados.

---

## 👨‍💻 Desenvolvimento

**Versão:** 1.0.0  
**Data:** 2024  
**Linguagem:** PHP 7.4+  
**Banco de Dados:** MySQL/MariaDB  

---

**✨ Sistema pronto para uso em produção!**

Desenvolvido com ❤️ para facilitar a gestão de assistência técnica em hospedagens compartilhadas.
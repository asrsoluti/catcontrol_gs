# 📦 Guia Completo de Instalação - Sistema CAT (Versão PHP)

## ✅ Sistema Totalmente Funcional

Todos os módulos estão implementados e funcionando:
- ✅ Dashboard com estatísticas
- ✅ CATs (Criar, Editar, Visualizar, Listar)
- ✅ Clientes (CRUD completo)
- ✅ Produtos (CRUD completo)
- ✅ Serviços (CRUD completo)
- ✅ Usuários (CRUD completo)
- ✅ Configurações
- ✅ Relatórios

## 🚀 Instalação no cPanel

### Passo 1: Preparar Arquivos

1. **Baixar do GitHub:**
   ```bash
   git clone https://github.com/asrsoluti/catcontrol_gs.git
   cd catcontrol_gs/php-version
   ```

2. **Ou baixar ZIP direto:**
   - Acesse: https://github.com/asrsoluti/catcontrol_gs
   - Clique em "Code" > "Download ZIP"
   - Extraia o arquivo
   - Navegue até a pasta `php-version`

### Passo 2: Upload para Hospedagem

1. **Via cPanel File Manager:**
   - Faça login no cPanel
   - Abra "Gerenciador de Arquivos"
   - Navegue até `public_html` (ou pasta desejada)
   - Clique em "Upload"
   - Envie todos os arquivos da pasta `php-version`

2. **Via FTP (alternativa):**
   - Use FileZilla ou outro cliente FTP
   - Conecte-se à sua hospedagem
   - Envie todos os arquivos para `public_html/cat`

### Passo 3: Configurar Banco de Dados

1. **Criar Banco MySQL no cPanel:**
   - Acesse "MySQL Databases"
   - Crie um novo banco: `seu_usuario_catdb`
   - Crie um usuário: `seu_usuario_cat`
   - Defina uma senha forte
   - Adicione o usuário ao banco com "ALL PRIVILEGES"

2. **Importar Estrutura:**
   - Acesse "phpMyAdmin"
   - Selecione o banco criado
   - Clique em "Importar"
   - Selecione o arquivo `database.sql`
   - Clique em "Executar"

### Passo 4: Configurar Conexão

Edite o arquivo `config/database.php`:

```php
<?php
class Database {
    private static $instance = null;
    private $conn;
    
    // CONFIGURAR AQUI - Altere com seus dados do cPanel
    private $host = "localhost";           // Geralmente é localhost
    private $db_name = "seu_usuario_catdb"; // Nome do banco criado
    private $username = "seu_usuario_cat";  // Usuário do banco
    private $password = "sua_senha_aqui";   // Senha definida
    private $charset = "utf8mb4";
    
    // Resto do código permanece igual...
}
```

### Passo 5: Configurar Permissões

No cPanel File Manager, configure as permissões:

```
- Diretórios: 755
- Arquivos PHP: 644
- uploads/ : 755 (criar se não existir)
- logs/ : 755 (criar se não existir)
```

### Passo 6: Testar Instalação

1. **Acesse o script de diagnóstico:**
   ```
   https://seu-dominio.com/cat/test_system.php
   ```

2. **Verificar:**
   - ✅ Todos arquivos devem estar presentes
   - ✅ Conexão com banco deve estar OK
   - ✅ Todas as tabelas devem aparecer

3. **Fazer Login:**
   ```
   URL: https://seu-dominio.com/cat/login.php
   Email: admin@sistema.com
   Senha: admin123
   ```

### Passo 7: Segurança Pós-Instalação

1. **Alterar senha do admin:**
   - Faça login
   - Vá em Usuários > Editar Admin
   - Troque a senha padrão

2. **Desabilitar modo debug:**
   - Edite `config/config.php`
   - Mude `define('DEBUG_MODE', true);` para `false`

3. **Remover arquivo de teste:**
   ```bash
   # Ou pelo File Manager
   rm test_system.php
   ```

4. **Configurar SSL (HTTPS):**
   - No cPanel, vá em "SSL/TLS"
   - Ative certificado Let's Encrypt
   - No `config/config.php`, linha 40:
     ```php
     ini_set('session.cookie_secure', 1); // Mude 0 para 1
     ```

## 🔧 Configurações Avançadas

### Ajustar Limites de Upload

Edite `.htaccess` (criar se não existir):

```apache
php_value upload_max_filesize 20M
php_value post_max_size 20M
php_value max_execution_time 300
php_value max_input_time 300
```

### Configurar Cronjob para Limpeza

No cPanel > Cron Jobs, adicione:

```bash
0 2 * * * /usr/bin/php /home/seu_usuario/public_html/cat/cron/cleanup.php
```

### Backup Automático

Configure backup diário do banco de dados no cPanel.

## 🐛 Resolução de Problemas

### Erro: "Não é possível conectar ao banco"
- Verifique credenciais em `config/database.php`
- Confirme que o usuário tem permissões no banco
- Teste conexão via phpMyAdmin

### Erro: "Página não encontrada" (404)
- Verifique se os arquivos foram enviados corretamente
- Confirme permissões dos arquivos (644) e pastas (755)
- Teste URL direta: `https://seu-dominio.com/cat/index.php`

### Links do menu não funcionam
- Limpe cache do navegador (Ctrl+Shift+Del)
- Verifique se `SITE_URL` está correto em `config/config.php`
- O sistema detecta automaticamente, mas pode precisar ajuste manual

### Erro: "Session não iniciada"
- Verifique permissões da pasta `/tmp` no servidor
- Em `config/config.php`, adicione no início:
  ```php
  ini_set('session.save_path', BASE_PATH . '/sessions');
  ```
- Crie pasta `sessions` com permissão 755

### Página em branco
- Ative modo debug em `config/config.php`
- Verifique logs de erro do PHP no cPanel
- Geralmente é erro de sintaxe ou falta de extensão PHP

## 📞 Suporte

- **GitHub Issues:** https://github.com/asrsoluti/catcontrol_gs/issues
- **Email:** (adicione seu email de suporte)

## 📝 Credenciais Padrão

⚠️ **IMPORTANTE: Altere imediatamente após primeiro login!**

```
Email: admin@sistema.com
Senha: admin123
Nível: Administrador
```

## 🎯 Próximos Passos

Após instalação bem-sucedida:

1. ✅ Alterar senha do administrador
2. ✅ Cadastrar usuários da equipe
3. ✅ Configurar dados da empresa
4. ✅ Cadastrar status personalizados de CAT
5. ✅ Importar base de clientes (se houver)
6. ✅ Configurar backup automático
7. ✅ Testar todos os módulos

## 📊 Estrutura do Sistema

```
php-version/
├── config/          # Configurações do sistema
├── models/          # Camada de dados (User, Cliente, CAT, etc)
├── includes/        # Header e Footer
├── cats/            # Módulo de CATs
├── clientes/        # Módulo de Clientes
├── produtos/        # Módulo de Produtos
├── servicos/        # Módulo de Serviços
├── usuarios/        # Módulo de Usuários
├── relatorios/      # Módulo de Relatórios
├── uploads/         # Anexos de CATs (criar)
├── logs/            # Logs do sistema (criar)
├── login.php        # Página de login
├── logout.php       # Logout
├── index.php        # Dashboard
└── database.sql     # Estrutura do banco
```

## ✨ Recursos do Sistema

### Dashboard
- Estatísticas em tempo real
- CATs abertas, em andamento, concluídas
- Atividades recentes
- Gráficos e métricas

### Módulo CATs
- Criação de chamados técnicos
- Controle de status
- Histórico completo de movimentações
- Prioridades (Baixa, Normal, Alta, Urgente)
- Anexos e observações

### Módulo Clientes
- Cadastro PF e PJ
- Busca de CEP automática (ViaCEP)
- Histórico de CATs por cliente
- Dados completos de contato

### Módulo Produtos
- Controle de estoque
- Preços de custo e venda
- Categorização
- Estoque mínimo

### Módulo Serviços
- Catálogo de serviços
- Precificação
- Tempo estimado
- Categorias personalizadas

### Controle de Acesso
- 4 níveis de usuário
- Permissões granulares
- Auditoria de ações
- Sessões seguras

---

**Sistema desenvolvido para controle completo de assistência técnica** 🔧

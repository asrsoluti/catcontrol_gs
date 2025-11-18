# 📦 Guia de Instalação no cPanel
## Sistema CAT - Versão PHP

Este guia detalha como instalar o Sistema CAT em hospedagem compartilhada com cPanel.

---

## 📋 Requisitos Mínimos

- **PHP**: 7.4 ou superior
- **MySQL**: 5.7 ou superior (ou MariaDB 10.2+)
- **Extensões PHP**: PDO, PDO_MySQL, mbstring, json
- **Espaço em Disco**: Mínimo 100MB
- **cPanel**: Acesso completo

---

## 🚀 Passo a Passo de Instalação

### **Passo 1: Download dos Arquivos**

1. Baixe o sistema do GitHub:
   ```
   https://github.com/asrsoluti/catcontrol_gs
   ```

2. Localize a pasta `php-version/` no repositório

3. Baixe todos os arquivos desta pasta para seu computador

---

### **Passo 2: Preparar Banco de Dados MySQL**

1. **Acesse o cPanel** da sua hospedagem

2. **Vá em "MySQL® Databases"** (Banco de Dados MySQL)

3. **Criar novo banco de dados:**
   - Nome: `seu_usuario_cat` (ou outro nome de sua preferência)
   - Clique em "Criar banco de dados"

4. **Criar novo usuário:**
   - Nome de usuário: `seu_usuario_cat_user`
   - Senha: Gere uma senha forte (anote-a!)
   - Clique em "Criar usuário"

5. **Adicionar usuário ao banco:**
   - Selecione o usuário criado
   - Selecione o banco criado
   - Marque "TODOS OS PRIVILÉGIOS"
   - Clique em "Fazer mudanças"

6. **Anotar informações:**
   ```
   Host: localhost
   Database: seu_usuario_cat
   Username: seu_usuario_cat_user
   Password: [sua senha gerada]
   ```

---

### **Passo 3: Importar Estrutura do Banco**

1. **Acesse phpMyAdmin** no cPanel

2. **Selecione o banco de dados** criado

3. **Clique na aba "Importar"**

4. **Escolher arquivo:**
   - Clique em "Escolher arquivo"
   - Selecione o arquivo `../database/schema.sql` (da pasta principal do projeto)
   
5. **Clique em "Executar"**

6. **Verificar:**
   - Você deve ver 20+ tabelas criadas
   - Verifique se a tabela `usuarios` tem pelo menos 1 registro (admin)

---

### **Passo 4: Upload dos Arquivos via FTP**

#### **Opção A: Usando FileZilla (Recomendado)**

1. **Abrir FileZilla**

2. **Conectar ao servidor:**
   - Host: ftp.seudominio.com (ou IP fornecido)
   - Usuário: seu usuário FTP
   - Senha: sua senha FTP
   - Porta: 21

3. **Navegar até public_html**
   - Ou até a pasta do seu domínio/subdomínio

4. **Criar pasta:**
   - Crie uma pasta chamada `cat-system` (ou o nome que preferir)
   - Entre nesta pasta

5. **Upload dos arquivos:**
   - Selecione TODOS os arquivos da pasta `php-version/`
   - Arraste para o servidor
   - Aguarde o upload completar (pode levar alguns minutos)

#### **Opção B: Usando File Manager do cPanel**

1. **Acesse File Manager** no cPanel

2. **Navegue até public_html**

3. **Criar pasta** `cat-system`

4. **Upload:**
   - Clique em "Upload"
   - Selecione todos os arquivos
   - Aguarde o upload

5. **Extrair (se enviou ZIP):**
   - Selecione o arquivo ZIP
   - Clique em "Extract"

---

### **Passo 5: Configurar Conexão com Banco**

1. **Editar arquivo de configuração:**
   - Localize o arquivo `config/database.php`
   - Clique com botão direito > "Edit" ou "Code Editor"

2. **Alterar as constantes:**
   ```php
   define('DB_HOST', 'localhost');           // Geralmente é localhost
   define('DB_NAME', 'seu_usuario_cat');     // Nome do seu banco
   define('DB_USER', 'seu_usuario_cat_user'); // Usuário do banco
   define('DB_PASS', 'sua_senha_aqui');       // Senha do banco
   ```

3. **Salvar o arquivo** (Ctrl+S ou botão Save)

---

### **Passo 6: Configurar URL do Sistema**

1. **Editar arquivo config.php:**
   - Localize o arquivo `config/config.php`
   - Edite a linha:
   ```php
   define('SITE_URL', 'https://seudominio.com/cat-system');
   ```
   
2. **Altere para seu domínio real:**
   - Se estiver na raiz: `https://seudominio.com`
   - Se estiver em pasta: `https://seudominio.com/cat-system`
   - Se for subdomínio: `https://cat.seudominio.com`

3. **Salvar o arquivo**

---

### **Passo 7: Ajustar Permissões**

1. **No File Manager do cPanel:**

2. **Criar pasta uploads:**
   - Dentro de cat-system, crie pasta `uploads`
   - Permissão: 755

3. **Criar pasta logs:**
   - Dentro de cat-system, crie pasta `logs`
   - Permissão: 755

4. **Ajustar permissão de pastas:**
   - Clique com botão direito na pasta `uploads`
   - "Change Permissions"
   - Marque: Owner: Read, Write, Execute (7)
   - Marque: Group: Read, Execute (5)
   - Marque: World: Read, Execute (5)
   - Resultado: 755

5. **Repetir para pasta `logs`**

---

### **Passo 8: Configurar Domínio/Subdomínio (Opcional)**

#### **Se quiser usar subdomínio (ex: cat.seudominio.com):**

1. **Ir em "Subdomains"** no cPanel

2. **Criar subdomínio:**
   - Subdomínio: `cat`
   - Document Root: `public_html/cat-system`
   - Criar

3. **Aguardar propagação DNS** (até 24h)

#### **Se usar na raiz do domínio:**

1. **Configurar em "Addon Domains"** ou usar domínio principal

---

### **Passo 9: Testar Instalação**

1. **Acessar o sistema:**
   ```
   https://seudominio.com/cat-system/login.php
   ```

2. **Credenciais padrão:**
   - **Email:** admin@sistema.com
   - **Senha:** admin123

3. **Se aparecer a tela de login:** ✅ **Instalação bem-sucedida!**

4. **Se aparecer erro:**
   - Verifique as configurações do banco em `config/database.php`
   - Verifique se o banco foi importado corretamente
   - Verifique os logs em `logs/php_errors.log`

---

### **Passo 10: Segurança Pós-Instalação**

1. **ALTERAR SENHA DO ADMIN:**
   - Login no sistema
   - Vá em Configurações > Alterar Senha
   - **OBRIGATÓRIO!**

2. **Desabilitar Debug Mode:**
   - Edite `config/config.php`
   - Altere: `define('DEBUG_MODE', false);`

3. **Proteger arquivos .htaccess:**
   - Certifique-se que o arquivo `.htaccess` está na raiz
   - Verifica as regras de proteção

4. **Backup Regular:**
   - Configure backup automático no cPanel
   - Backup semanal do banco de dados
   - Backup mensal dos arquivos

---

## 🔧 Configurações Avançadas

### **SSL/HTTPS (Recomendado)**

1. **No cPanel, vá em "SSL/TLS Status"**

2. **Ativar AutoSSL** (Let's Encrypt gratuito)

3. **Após ativar, editar .htaccess:**
   - Descomentar linhas de forçar HTTPS

### **PHP Version**

1. **No cPanel, vá em "Select PHP Version"**

2. **Selecione PHP 7.4 ou 8.0**

3. **Ativar extensões:**
   - PDO
   - pdo_mysql
   - mbstring
   - json
   - zip
   - gd (para manipulação de imagens)

### **Limite de Upload**

1. **No cPanel, "Select PHP Version" > "Options"**

2. **Ajustar:**
   - upload_max_filesize: 10M
   - post_max_size: 10M
   - max_execution_time: 300

---

## 🆘 Problemas Comuns

### **Erro: "Cannot connect to database"**

**Solução:**
- Verifique credenciais em `config/database.php`
- Confirme que usuário foi adicionado ao banco no cPanel
- Teste conexão no phpMyAdmin

### **Página em branco**

**Solução:**
- Ative `define('DEBUG_MODE', true);` temporariamente
- Verifique `logs/php_errors.log`
- Verifique versão do PHP (mínimo 7.4)

### **Erro 500**

**Solução:**
- Verifique arquivo `.htaccess`
- Verifique permissões das pastas (755)
- Verifique logs do servidor

### **CSS/JS não carregam**

**Solução:**
- Verifique URL em `config/config.php`
- Limpe cache do navegador
- Verifique permissões dos arquivos

---

## 📁 Estrutura de Arquivos

```
cat-system/
├── config/
│   ├── config.php         ⚙️ Configurações gerais
│   └── database.php       🗄️ Conexão com banco
├── includes/
│   ├── header.php         📄 Cabeçalho padrão
│   └── footer.php         📄 Rodapé padrão
├── models/
│   ├── User.php          👤 Modelo de usuário
│   ├── Cliente.php       👥 Modelo de cliente
│   └── CAT.php           📋 Modelo de CAT
├── cats/
│   ├── list.php          📝 Lista de CATs
│   ├── view.php          👁️ Visualizar CAT
│   └── edit.php          ✏️ Editar CAT
├── clientes/
│   ├── list.php          📝 Lista de clientes
│   └── edit.php          ✏️ Editar cliente
├── uploads/              📂 Arquivos enviados
├── logs/                 📊 Logs do sistema
├── .htaccess            🔒 Configurações Apache
├── index.php            🏠 Dashboard
└── login.php            🔐 Página de login
```

---

## ✅ Checklist de Instalação

- [ ] Banco de dados criado no cPanel
- [ ] Usuário do banco criado e configurado
- [ ] Estrutura SQL importada (schema.sql)
- [ ] Arquivos enviados via FTP/File Manager
- [ ] Arquivo config/database.php configurado
- [ ] Arquivo config/config.php configurado
- [ ] Pastas uploads e logs criadas com permissão 755
- [ ] Sistema acessível pelo navegador
- [ ] Login funciona com credenciais padrão
- [ ] Senha do admin alterada
- [ ] Debug mode desabilitado
- [ ] SSL/HTTPS ativado (se disponível)
- [ ] Backup configurado

---

## 📞 Suporte

Se encontrar problemas:

1. **Verificar logs:** `logs/php_errors.log`
2. **GitHub Issues:** https://github.com/asrsoluti/catcontrol_gs/issues
3. **Documentação:** Ver arquivos README.md e API.md

---

**✨ Instalação Concluída! Bom uso do Sistema CAT!**
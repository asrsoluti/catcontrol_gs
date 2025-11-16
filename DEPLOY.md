# 🚀 Guia de Deploy - Sistema CAT

Este guia fornece instruções passo a passo para fazer o deploy do Sistema CAT em diferentes ambientes.

## 📦 Repositório GitHub

**URL:** https://github.com/asrsoluti/catcontrol_gs

```bash
git clone https://github.com/asrsoluti/catcontrol_gs.git
cd catcontrol_gs
```

---

## 🖥️ Deploy em Servidor VPS/Dedicado (Linux)

### Pré-requisitos

- Ubuntu 20.04+ ou CentOS 7+
- Node.js 18+
- MySQL 8.0+ ou MariaDB 10.5+
- Apache 2.4+ ou Nginx
- PM2 (gerenciador de processos)

### Passo 1: Preparar o Servidor

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Instalar MySQL/MariaDB
sudo apt install -y mysql-server

# Instalar Apache
sudo apt install -y apache2

# Instalar PM2 globalmente
sudo npm install -g pm2

# Instalar Git
sudo apt install -y git
```

### Passo 2: Configurar MySQL

```bash
# Entrar no MySQL
sudo mysql

# Criar banco de dados e usuário
CREATE DATABASE cat_system;
CREATE USER 'cat_user'@'localhost' IDENTIFIED BY 'senha_segura_aqui';
GRANT ALL PRIVILEGES ON cat_system.* TO 'cat_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Passo 3: Clonar e Configurar Aplicação

```bash
# Criar diretório para aplicação
sudo mkdir -p /var/www/cat-system
cd /var/www/cat-system

# Clonar repositório
sudo git clone https://github.com/asrsoluti/catcontrol_gs.git .

# Instalar dependências
sudo npm install

# Criar arquivo .env
sudo cp .env.example .env
sudo nano .env
```

**Configurar .env:**
```env
DB_HOST=localhost
DB_PORT=3306
DB_USER=cat_user
DB_PASSWORD=senha_segura_aqui
DB_NAME=cat_system

PORT=3000
NODE_ENV=production

JWT_SECRET=chave_secreta_super_segura_aleatoria
JWT_EXPIRES_IN=24h

APP_URL=https://cat.seudominio.com
```

### Passo 4: Importar Banco de Dados

```bash
# Importar estrutura do banco
mysql -u cat_user -p cat_system < database/schema.sql
```

### Passo 5: Compilar Aplicação

```bash
# Compilar TypeScript
npm run build

# Criar pasta de uploads
mkdir -p uploads
mkdir -p logs

# Ajustar permissões
sudo chown -R www-data:www-data /var/www/cat-system
sudo chmod -R 755 /var/www/cat-system
sudo chmod -R 777 uploads logs
```

### Passo 6: Configurar PM2

```bash
# Iniciar aplicação com PM2
pm2 start ecosystem.config.js --env production

# Salvar configuração PM2
pm2 save

# Configurar PM2 para iniciar no boot
pm2 startup systemd
# Execute o comando que o PM2 mostrar
```

### Passo 7: Configurar Apache

```bash
# Habilitar módulos necessários
sudo a2enmod proxy
sudo a2enmod proxy_http
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Copiar configuração do Virtual Host
sudo cp apache-vhost.conf /etc/apache2/sites-available/cat-system.conf

# Editar configuração
sudo nano /etc/apache2/sites-available/cat-system.conf
# Ajuste ServerName e caminhos conforme necessário

# Habilitar site
sudo a2ensite cat-system.conf

# Desabilitar site padrão (opcional)
sudo a2dissite 000-default.conf

# Testar configuração
sudo apache2ctl configtest

# Reiniciar Apache
sudo systemctl restart apache2
```

### Passo 8: Configurar Firewall

```bash
# Permitir portas HTTP e HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 3000/tcp

# Habilitar firewall
sudo ufw enable
```

### Passo 9: Configurar SSL (Let's Encrypt)

```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-apache

# Obter certificado SSL
sudo certbot --apache -d cat.seudominio.com -d www.cat.seudominio.com

# Renovação automática já está configurada
# Testar renovação
sudo certbot renew --dry-run
```

---

## 🌐 Deploy em Hospedagem Compartilhada (cPanel)

### Passo 1: Preparar Arquivos

```bash
# No seu computador local
git clone https://github.com/asrsoluti/catcontrol_gs.git
cd catcontrol_gs

# Instalar dependências
npm install

# Compilar
npm run build

# Criar arquivo ZIP dos arquivos necessários
zip -r cat-system.zip dist/ database/ uploads/ .env.example .htaccess package.json package-lock.json ecosystem.config.js
```

### Passo 2: Upload via cPanel

1. Acesse o cPanel
2. Vá em "File Manager"
3. Navegue até `public_html` (ou subdiretório)
4. Faça upload do arquivo `cat-system.zip`
5. Extraia o arquivo ZIP

### Passo 3: Configurar Node.js no cPanel

1. Acesse "Setup Node.js App"
2. Clique em "Create Application"
3. Configure:
   - **Node.js version**: 18.x
   - **Application mode**: Production
   - **Application root**: /home/usuario/public_html/cat-system
   - **Application URL**: cat.seudominio.com
   - **Application startup file**: dist/index.js

### Passo 4: Configurar Banco de Dados

1. Acesse "MySQL Databases"
2. Crie novo banco de dados: `usuario_cat`
3. Crie novo usuário: `usuario_cat`
4. Adicione usuário ao banco com todas as permissões
5. Acesse phpMyAdmin
6. Importe o arquivo `database/schema.sql`

### Passo 5: Configurar .env

1. No File Manager, renomeie `.env.example` para `.env`
2. Edite o arquivo com os dados corretos:
   - DB_HOST (geralmente localhost)
   - DB_USER
   - DB_PASSWORD
   - DB_NAME

### Passo 6: Instalar Dependências e Iniciar

No terminal SSH (ou Terminal do cPanel):

```bash
cd ~/public_html/cat-system
npm install --production
npm start
```

---

## 🐳 Deploy com Docker

### Passo 1: Criar Dockerfile

Já incluído no projeto como `Dockerfile`.

### Passo 2: Criar docker-compose.yml

Já incluído no projeto como `docker-compose.yml`.

### Passo 3: Deploy

```bash
# Construir e iniciar containers
docker-compose up -d

# Ver logs
docker-compose logs -f

# Parar containers
docker-compose down

# Reconstruir após mudanças
docker-compose up -d --build
```

---

## 📊 Monitoramento e Manutenção

### Comandos PM2 Úteis

```bash
# Listar aplicações
pm2 list

# Ver logs em tempo real
pm2 logs cat-system

# Monitorar recursos
pm2 monit

# Reiniciar aplicação
pm2 restart cat-system

# Parar aplicação
pm2 stop cat-system

# Deletar aplicação
pm2 delete cat-system

# Salvar configuração atual
pm2 save
```

### Backup do Banco de Dados

```bash
# Backup manual
mysqldump -u cat_user -p cat_system > backup_$(date +%Y%m%d).sql

# Backup automático diário (crontab)
0 2 * * * mysqldump -u cat_user -pSENHA cat_system > /backups/cat_$(date +\%Y\%m\%d).sql
```

### Atualização da Aplicação

```bash
cd /var/www/cat-system

# Fazer backup
git stash

# Baixar atualizações
git pull origin main

# Restaurar configurações locais
git stash pop

# Instalar novas dependências
npm install

# Recompilar
npm run build

# Reiniciar aplicação
pm2 restart cat-system
```

---

## 🔒 Checklist de Segurança

- [ ] Alterar senha padrão do usuário admin
- [ ] Configurar SSL/HTTPS
- [ ] Configurar backup automático do banco de dados
- [ ] Configurar firewall
- [ ] Manter sistema operacional atualizado
- [ ] Usar senhas fortes no .env (JWT_SECRET, DB_PASSWORD)
- [ ] Configurar rate limiting no Nginx/Apache
- [ ] Habilitar logs de auditoria
- [ ] Configurar monitoramento (Uptime, New Relic, etc.)
- [ ] Restringir acesso SSH por IP
- [ ] Configurar fail2ban

---

## 🆘 Problemas Comuns

### Erro: Cannot connect to database

**Solução:**
```bash
# Verificar se MySQL está rodando
sudo systemctl status mysql

# Verificar credenciais no .env
cat .env | grep DB_

# Testar conexão
mysql -u cat_user -p cat_system
```

### Erro: Port 3000 already in use

**Solução:**
```bash
# Ver processo usando a porta
sudo lsof -i :3000

# Matar processo
sudo kill -9 PID

# Ou mudar porta no .env
```

### Erro: Permission denied

**Solução:**
```bash
# Ajustar permissões
sudo chown -R www-data:www-data /var/www/cat-system
sudo chmod -R 755 /var/www/cat-system
sudo chmod -R 777 uploads logs
```

---

## 📞 Suporte

Para suporte adicional:
- **Issues**: https://github.com/asrsoluti/catcontrol_gs/issues
- **Email**: admin@seudominio.com

---

## 📝 URLs Importantes

- **Repositório**: https://github.com/asrsoluti/catcontrol_gs
- **Aplicação**: http://cat.seudominio.com
- **API Docs**: http://cat.seudominio.com/api/health

---

**Última atualização:** 2024
# 🚀 Guia Rápido de Início

## ⚡ Deploy em 5 Minutos

### Opção 1: Docker (Recomendado - Mais Fácil)

```bash
# 1. Clonar repositório
git clone https://github.com/asrsoluti/catcontrol_gs.git
cd catcontrol_gs

# 2. Editar senhas no docker-compose.yml
nano docker-compose.yml
# Altere: MYSQL_ROOT_PASSWORD, MYSQL_PASSWORD, JWT_SECRET

# 3. Iniciar
docker-compose up -d

# 4. Acessar
# http://localhost:3000
# Email: admin@sistema.com
# Senha: admin123
```

✅ **Pronto! Sistema rodando!**

---

### Opção 2: VPS/Servidor (Ubuntu)

```bash
# 1. Instalar dependências
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs mysql-server git
sudo npm install -g pm2

# 2. Clonar e configurar
git clone https://github.com/asrsoluti/catcontrol_gs.git
cd catcontrol_gs
chmod +x setup.sh
./setup.sh

# 3. Iniciar
pm2 start ecosystem.config.js --env production
pm2 save
pm2 startup

# 4. Configurar Apache/Nginx (opcional)
sudo cp apache-vhost.conf /etc/apache2/sites-available/cat.conf
sudo a2ensite cat.conf
sudo systemctl restart apache2
```

✅ **Sistema em produção!**

---

### Opção 3: Instalação Manual

```bash
# 1. Clonar repositório
git clone https://github.com/asrsoluti/catcontrol_gs.git
cd catcontrol_gs

# 2. Instalar dependências
npm install

# 3. Configurar .env
cp .env.example .env
nano .env
# Configure: DB_HOST, DB_USER, DB_PASSWORD, DB_NAME

# 4. Importar banco de dados
mysql -u root -p < database/schema.sql

# 5. Compilar
npm run build

# 6. Iniciar
npm start
# Ou com PM2: pm2 start ecosystem.config.js
```

---

## 🔑 Acesso ao Sistema

Após iniciar o sistema, acesse:

**URL Local**: http://localhost:3000

**Credenciais:**
- Email: `admin@sistema.com`
- Senha: `admin123`

⚠️ **Importante**: Altere a senha após primeiro login!

---

## 📁 Arquivos Importantes

| Arquivo | Descrição |
|---------|-----------|
| `DEPLOY.md` | Guia completo de deploy |
| `README.md` | Documentação geral |
| `API.md` | Documentação da API |
| `database/schema.sql` | Estrutura do banco |
| `.env.example` | Exemplo de configuração |

---

## 🆘 Problemas Comuns

### Erro: Cannot connect to database
```bash
# Verificar se MySQL está rodando
sudo systemctl status mysql
sudo systemctl start mysql

# Verificar credenciais no .env
cat .env | grep DB_
```

### Erro: Port 3000 already in use
```bash
# Matar processo na porta 3000
sudo lsof -i :3000
sudo kill -9 [PID]
```

### Erro: Permission denied
```bash
# Ajustar permissões
sudo chown -R $USER:$USER .
chmod -R 755 .
```

---

## 📞 Suporte

- **GitHub**: https://github.com/asrsoluti/catcontrol_gs
- **Issues**: https://github.com/asrsoluti/catcontrol_gs/issues
- **Documentação Completa**: Ver DEPLOY.md

---

## ✨ Recursos do Sistema

- ✅ Gestão completa de CATs
- ✅ Cadastro de clientes e produtos
- ✅ Dashboard interativo
- ✅ API RESTful
- ✅ Sistema de autenticação
- ✅ Interface web moderna
- ✅ Banco de dados MySQL

---

**Desenvolvido com ❤️ para gestão eficiente de assistência técnica**
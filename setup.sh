#!/bin/bash

echo "======================================"
echo " Sistema CAT - Setup Inicial"
echo "======================================"
echo ""

# Verificar se o MySQL está instalado
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL não encontrado. Por favor, instale o MySQL ou MariaDB primeiro."
    echo "   Ubuntu/Debian: sudo apt install mysql-server"
    echo "   MacOS: brew install mysql"
    echo "   Windows: Baixe em https://dev.mysql.com/downloads/"
    exit 1
fi

# Copiar .env.example se .env não existir
if [ ! -f .env ]; then
    cp .env.example .env
    echo "✅ Arquivo .env criado. Por favor, configure suas credenciais do banco de dados."
fi

# Solicitar credenciais do banco de dados
echo ""
echo "Por favor, forneça as credenciais do MySQL:"
read -p "Host (default: localhost): " db_host
db_host=${db_host:-localhost}

read -p "Porta (default: 3306): " db_port
db_port=${db_port:-3306}

read -p "Usuário (default: root): " db_user
db_user=${db_user:-root}

read -sp "Senha: " db_password
echo ""

# Atualizar .env com as credenciais
sed -i.bak "s/DB_HOST=.*/DB_HOST=$db_host/" .env
sed -i.bak "s/DB_PORT=.*/DB_PORT=$db_port/" .env
sed -i.bak "s/DB_USER=.*/DB_USER=$db_user/" .env
sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=$db_password/" .env

# Criar banco de dados
echo ""
echo "Criando banco de dados..."
mysql -h "$db_host" -P "$db_port" -u "$db_user" -p"$db_password" < database/schema.sql

if [ $? -eq 0 ]; then
    echo "✅ Banco de dados criado com sucesso!"
else
    echo "❌ Erro ao criar banco de dados. Verifique suas credenciais."
    exit 1
fi

# Instalar dependências
echo ""
echo "Instalando dependências do Node.js..."
npm install

# Criar pasta de uploads
mkdir -p uploads

echo ""
echo "======================================"
echo " ✅ Setup concluído com sucesso!"
echo "======================================"
echo ""
echo "Credenciais de acesso padrão:"
echo "  Email: admin@sistema.com"
echo "  Senha: admin123"
echo ""
echo "Para iniciar o sistema:"
echo "  npm run dev  (modo desenvolvimento)"
echo "  npm start    (modo produção)"
echo ""
echo "Acesse: http://localhost:3000"
echo ""
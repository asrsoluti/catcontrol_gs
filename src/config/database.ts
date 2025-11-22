import mysql from 'mysql2/promise';
import dotenv from 'dotenv';

dotenv.config();

// Criar pool de conexões
const pool = mysql.createPool({
  host: process.env.DB_HOST || '147.79.104.198',
  port: parseInt(process.env.DB_PORT || '3306'),
  user: process.env.DB_USER || 'usrcatsystem',
  password: process.env.DB_PASSWORD || 'Asr@cs#1210',
  database: process.env.DB_NAME || 'catsystem',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0,
  enableKeepAlive: true,
  keepAliveInitialDelay: 0
});

// Testar conexão
export async function testConnection() {
  try {
    const connection = await pool.getConnection();
    console.log('✅ Conexão com banco de dados estabelecida com sucesso!');
    connection.release();
    return true;
  } catch (error) {
    console.error('❌ Erro ao conectar com banco de dados:', error);
    return false;
  }
}

export default pool;

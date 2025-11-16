import { Hono } from 'hono';
import { auth } from '../config/auth';
import db from '../config/database';
import { RowDataPacket } from 'mysql2';
import { AppContext } from '../types';

const authRoutes = new Hono<AppContext>();

// Login
authRoutes.post('/login', async (c) => {
  try {
    const { email, senha } = await c.req.json();

    if (!email || !senha) {
      return c.json({ error: 'Email e senha são obrigatórios' }, 400);
    }

    // Buscar usuário no banco
    const [users] = await db.execute<RowDataPacket[]>(
      `SELECT u.*, n.nome as nivel_nome, n.permissoes 
       FROM usuarios u 
       JOIN niveis_usuario n ON u.nivel_id = n.id 
       WHERE u.email = ? AND u.ativo = 1`,
      [email]
    );

    if (users.length === 0) {
      return c.json({ error: 'Credenciais inválidas' }, 401);
    }

    const user = users[0];

    // Verificar senha
    const senhaValida = await auth.verifyPassword(senha, user.senha);
    if (!senhaValida) {
      return c.json({ error: 'Credenciais inválidas' }, 401);
    }

    // Atualizar último acesso
    await db.execute(
      'UPDATE usuarios SET ultimo_acesso = NOW() WHERE id = ?',
      [user.id]
    );

    // Gerar token
    const token = auth.generateToken({
      id: user.id,
      email: user.email,
      nome: user.nome,
      nivel_id: user.nivel_id
    });

    // Remover senha da resposta
    delete user.senha;

    return c.json({
      success: true,
      token,
      user: {
        id: user.id,
        nome: user.nome,
        email: user.email,
        nivel: user.nivel_nome,
        permissoes: JSON.parse(user.permissoes),
        foto_perfil: user.foto_perfil
      }
    });

  } catch (error) {
    console.error('Erro no login:', error);
    return c.json({ error: 'Erro interno do servidor' }, 500);
  }
});

// Registro de novo usuário (apenas admin)
authRoutes.post('/register', async (c) => {
  try {
    const { nome, email, senha, nivel_id, telefone } = await c.req.json();

    if (!nome || !email || !senha || !nivel_id) {
      return c.json({ error: 'Dados obrigatórios não fornecidos' }, 400);
    }

    // Verificar se email já existe
    const [existing] = await db.execute<RowDataPacket[]>(
      'SELECT id FROM usuarios WHERE email = ?',
      [email]
    );

    if (existing.length > 0) {
      return c.json({ error: 'Email já cadastrado' }, 400);
    }

    // Hash da senha
    const hashedPassword = await auth.hashPassword(senha);

    // Inserir usuário
    const [result] = await db.execute(
      `INSERT INTO usuarios (nome, email, senha, nivel_id, telefone) 
       VALUES (?, ?, ?, ?, ?)`,
      [nome, email, hashedPassword, nivel_id, telefone]
    );

    return c.json({
      success: true,
      message: 'Usuário criado com sucesso'
    });

  } catch (error) {
    console.error('Erro ao registrar usuário:', error);
    return c.json({ error: 'Erro interno do servidor' }, 500);
  }
});

// Trocar senha
authRoutes.post('/change-password', async (c) => {
  try {
    const user = c.get('user');
    const { senhaAtual, senhaNova } = await c.req.json();

    if (!senhaAtual || !senhaNova) {
      return c.json({ error: 'Senhas são obrigatórias' }, 400);
    }

    // Buscar senha atual do usuário
    const [users] = await db.execute<RowDataPacket[]>(
      'SELECT senha FROM usuarios WHERE id = ?',
      [user.id]
    );

    if (users.length === 0) {
      return c.json({ error: 'Usuário não encontrado' }, 404);
    }

    // Verificar senha atual
    const senhaValida = await auth.verifyPassword(senhaAtual, users[0].senha);
    if (!senhaValida) {
      return c.json({ error: 'Senha atual incorreta' }, 401);
    }

    // Hash da nova senha
    const hashedPassword = await auth.hashPassword(senhaNova);

    // Atualizar senha
    await db.execute(
      'UPDATE usuarios SET senha = ? WHERE id = ?',
      [hashedPassword, user.id]
    );

    return c.json({
      success: true,
      message: 'Senha alterada com sucesso'
    });

  } catch (error) {
    console.error('Erro ao alterar senha:', error);
    return c.json({ error: 'Erro interno do servidor' }, 500);
  }
});

export default authRoutes;
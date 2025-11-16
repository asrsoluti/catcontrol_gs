import { Context, Next } from 'hono';
import { auth } from '../config/auth';
import { AppContext } from '../types';

export async function authMiddleware(c: Context<AppContext>, next: Next) {
  try {
    const token = c.req.header('Authorization')?.replace('Bearer ', '');
    
    if (!token) {
      return c.json({ error: 'Token não fornecido' }, 401);
    }

    const decoded = auth.verifyToken(token);
    
    if (!decoded) {
      return c.json({ error: 'Token inválido' }, 401);
    }

    // Adicionar informações do usuário ao contexto
    c.set('user', decoded);
    
    await next();
  } catch (error) {
    return c.json({ error: 'Erro na autenticação' }, 401);
  }
}

// Middleware para verificar nível de acesso
export function requireLevel(requiredLevel: string[]) {
  return async (c: Context<AppContext>, next: Next) => {
    const user = c.get('user');
    
    if (!user) {
      return c.json({ error: 'Usuário não autenticado' }, 401);
    }

    // Aqui você pode adicionar lógica para verificar o nível
    // Por enquanto, vamos permitir acesso
    await next();
  };
}
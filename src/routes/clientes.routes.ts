import { Hono } from 'hono';
import db from '../config/database';
import { RowDataPacket, ResultSetHeader } from 'mysql2';
import { AppContext } from '../types';

const clientesRoutes = new Hono<AppContext>();

// Listar todos os clientes
clientesRoutes.get('/', async (c) => {
  try {
    const { page = 1, limit = 10, search = '', ativo = 'true' } = c.req.query();
    const offset = (Number(page) - 1) * Number(limit);

    let query = `
      SELECT 
        c.*,
        u.nome as criado_por_nome,
        (SELECT COUNT(*) FROM cat WHERE cliente_id = c.id) as total_cats
      FROM clientes c
      LEFT JOIN usuarios u ON c.created_by = u.id
      WHERE 1=1
    `;
    
    const params: any[] = [];

    if (ativo !== 'all') {
      query += ' AND c.ativo = ?';
      params.push(ativo === 'true' ? 1 : 0);
    }

    if (search) {
      query += ` AND (
        c.razao_social LIKE ? OR 
        c.nome_fantasia LIKE ? OR 
        c.cpf_cnpj LIKE ? OR 
        c.email LIKE ? OR
        c.cidade LIKE ?
      )`;
      const searchPattern = `%${search}%`;
      params.push(searchPattern, searchPattern, searchPattern, searchPattern, searchPattern);
    }

    // Contar total de registros
    const countQuery = query.replace('SELECT c.*,', 'SELECT COUNT(*) as total FROM (SELECT c.id');
    const [countResult] = await db.execute<RowDataPacket[]>(countQuery + ') as count_table', params);
    const total = countResult[0].total;

    // Buscar registros paginados
    query += ' ORDER BY c.created_at DESC LIMIT ? OFFSET ?';
    params.push(Number(limit), offset);

    const [clientes] = await db.execute<RowDataPacket[]>(query, params);

    return c.json({
      success: true,
      data: clientes,
      pagination: {
        page: Number(page),
        limit: Number(limit),
        total,
        totalPages: Math.ceil(total / Number(limit))
      }
    });

  } catch (error) {
    console.error('Erro ao listar clientes:', error);
    return c.json({ error: 'Erro ao listar clientes' }, 500);
  }
});

// Buscar cliente por ID
clientesRoutes.get('/:id', async (c) => {
  try {
    const id = c.req.param('id');

    const [clientes] = await db.execute<RowDataPacket[]>(
      `SELECT c.*, u.nome as criado_por_nome
       FROM clientes c
       LEFT JOIN usuarios u ON c.created_by = u.id
       WHERE c.id = ?`,
      [id]
    );

    if (clientes.length === 0) {
      return c.json({ error: 'Cliente não encontrado' }, 404);
    }

    // Buscar últimas CATs do cliente
    const [cats] = await db.execute<RowDataPacket[]>(
      `SELECT cat.*, s.nome as status_nome, s.cor_hex as status_cor
       FROM cat
       JOIN status_cat s ON cat.status_id = s.id
       WHERE cat.cliente_id = ?
       ORDER BY cat.data_abertura DESC
       LIMIT 10`,
      [id]
    );

    return c.json({
      success: true,
      data: {
        ...clientes[0],
        ultimas_cats: cats
      }
    });

  } catch (error) {
    console.error('Erro ao buscar cliente:', error);
    return c.json({ error: 'Erro ao buscar cliente' }, 500);
  }
});

// Criar novo cliente
clientesRoutes.post('/', async (c) => {
  try {
    const user = c.get('user');
    const data = await c.req.json();

    // Validações
    if (!data.razao_social || !data.cpf_cnpj) {
      return c.json({ error: 'Razão social e CPF/CNPJ são obrigatórios' }, 400);
    }

    // Verificar se CPF/CNPJ já existe
    const [existing] = await db.execute<RowDataPacket[]>(
      'SELECT id FROM clientes WHERE cpf_cnpj = ?',
      [data.cpf_cnpj]
    );

    if (existing.length > 0) {
      return c.json({ error: 'CPF/CNPJ já cadastrado' }, 400);
    }

    // Gerar código do cliente
    const [lastClient] = await db.execute<RowDataPacket[]>(
      'SELECT MAX(CAST(codigo_cliente AS UNSIGNED)) as ultimo FROM clientes'
    );
    const nextCode = (lastClient[0].ultimo || 0) + 1;
    data.codigo_cliente = String(nextCode).padStart(6, '0');

    // Inserir cliente
    const query = `
      INSERT INTO clientes (
        codigo_cliente, razao_social, nome_fantasia, tipo_pessoa,
        cpf_cnpj, inscricao_estadual, endereco, numero, complemento,
        bairro, cidade, uf, cep, telefone, celular, fax, email,
        contato_principal, observacoes, created_by
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    const values = [
      data.codigo_cliente,
      data.razao_social,
      data.nome_fantasia || null,
      data.tipo_pessoa || 'PJ',
      data.cpf_cnpj,
      data.inscricao_estadual || null,
      data.endereco || null,
      data.numero || null,
      data.complemento || null,
      data.bairro || null,
      data.cidade || null,
      data.uf || null,
      data.cep || null,
      data.telefone || null,
      data.celular || null,
      data.fax || null,
      data.email || null,
      data.contato_principal || null,
      data.observacoes || null,
      user.id
    ];

    const [result] = await db.execute<ResultSetHeader>(query, values);

    return c.json({
      success: true,
      message: 'Cliente criado com sucesso',
      id: result.insertId,
      codigo_cliente: data.codigo_cliente
    });

  } catch (error) {
    console.error('Erro ao criar cliente:', error);
    return c.json({ error: 'Erro ao criar cliente' }, 500);
  }
});

// Atualizar cliente
clientesRoutes.put('/:id', async (c) => {
  try {
    const id = c.req.param('id');
    const data = await c.req.json();

    // Verificar se cliente existe
    const [existing] = await db.execute<RowDataPacket[]>(
      'SELECT id FROM clientes WHERE id = ?',
      [id]
    );

    if (existing.length === 0) {
      return c.json({ error: 'Cliente não encontrado' }, 404);
    }

    // Se mudou CPF/CNPJ, verificar se não está duplicado
    if (data.cpf_cnpj) {
      const [duplicate] = await db.execute<RowDataPacket[]>(
        'SELECT id FROM clientes WHERE cpf_cnpj = ? AND id != ?',
        [data.cpf_cnpj, id]
      );

      if (duplicate.length > 0) {
        return c.json({ error: 'CPF/CNPJ já cadastrado' }, 400);
      }
    }

    // Montar query de atualização dinamicamente
    const fields = [];
    const values = [];

    const allowedFields = [
      'razao_social', 'nome_fantasia', 'tipo_pessoa', 'cpf_cnpj',
      'inscricao_estadual', 'endereco', 'numero', 'complemento',
      'bairro', 'cidade', 'uf', 'cep', 'telefone', 'celular',
      'fax', 'email', 'contato_principal', 'observacoes', 'ativo'
    ];

    for (const field of allowedFields) {
      if (data[field] !== undefined) {
        fields.push(`${field} = ?`);
        values.push(data[field]);
      }
    }

    if (fields.length === 0) {
      return c.json({ error: 'Nenhum campo para atualizar' }, 400);
    }

    values.push(id);
    const query = `UPDATE clientes SET ${fields.join(', ')} WHERE id = ?`;

    await db.execute(query, values);

    return c.json({
      success: true,
      message: 'Cliente atualizado com sucesso'
    });

  } catch (error) {
    console.error('Erro ao atualizar cliente:', error);
    return c.json({ error: 'Erro ao atualizar cliente' }, 500);
  }
});

// Desativar/Ativar cliente
clientesRoutes.patch('/:id/toggle-status', async (c) => {
  try {
    const id = c.req.param('id');

    const [result] = await db.execute<ResultSetHeader>(
      'UPDATE clientes SET ativo = NOT ativo WHERE id = ?',
      [id]
    );

    if (result.affectedRows === 0) {
      return c.json({ error: 'Cliente não encontrado' }, 404);
    }

    return c.json({
      success: true,
      message: 'Status do cliente alterado com sucesso'
    });

  } catch (error) {
    console.error('Erro ao alterar status do cliente:', error);
    return c.json({ error: 'Erro ao alterar status' }, 500);
  }
});

export default clientesRoutes;
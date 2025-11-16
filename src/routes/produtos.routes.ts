import { Hono } from 'hono';
import db from '../config/database';
import { RowDataPacket, ResultSetHeader } from 'mysql2';
import { AppContext } from '../types';

const produtosRoutes = new Hono<AppContext>();

// Listar todos os produtos
produtosRoutes.get('/', async (c) => {
  try {
    const { 
      page = 1, 
      limit = 10, 
      search = '', 
      categoria_id = '',
      fornecedor_id = '',
      ativo = 'true' 
    } = c.req.query();
    
    const offset = (Number(page) - 1) * Number(limit);

    let query = `
      SELECT 
        p.*,
        c.nome as categoria_nome,
        f.razao_social as fornecedor_nome,
        (SELECT COUNT(*) FROM cat WHERE produto_id = p.id) as total_cats
      FROM produtos p
      LEFT JOIN categorias_produto c ON p.categoria_id = c.id
      LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
      WHERE 1=1
    `;
    
    const params: any[] = [];

    if (ativo !== 'all') {
      query += ' AND p.ativo = ?';
      params.push(ativo === 'true' ? 1 : 0);
    }

    if (categoria_id) {
      query += ' AND p.categoria_id = ?';
      params.push(categoria_id);
    }

    if (fornecedor_id) {
      query += ' AND p.fornecedor_id = ?';
      params.push(fornecedor_id);
    }

    if (search) {
      query += ` AND (
        p.codigo_produto LIKE ? OR 
        p.nome LIKE ? OR 
        p.descricao LIKE ? OR 
        p.marca LIKE ? OR
        p.modelo LIKE ?
      )`;
      const searchPattern = `%${search}%`;
      params.push(searchPattern, searchPattern, searchPattern, searchPattern, searchPattern);
    }

    // Contar total
    const countQuery = query.replace(/SELECT.*FROM/, 'SELECT COUNT(*) as total FROM');
    const [countResult] = await db.execute<RowDataPacket[]>(countQuery, params);
    const total = countResult[0].total;

    // Buscar registros
    query += ' ORDER BY p.created_at DESC LIMIT ? OFFSET ?';
    params.push(Number(limit), offset);

    const [produtos] = await db.execute<RowDataPacket[]>(query, params);

    return c.json({
      success: true,
      data: produtos,
      pagination: {
        page: Number(page),
        limit: Number(limit),
        total,
        totalPages: Math.ceil(total / Number(limit))
      }
    });

  } catch (error) {
    console.error('Erro ao listar produtos:', error);
    return c.json({ error: 'Erro ao listar produtos' }, 500);
  }
});

// Buscar produto por ID
produtosRoutes.get('/:id', async (c) => {
  try {
    const id = c.req.param('id');

    const [produtos] = await db.execute<RowDataPacket[]>(
      `SELECT p.*, c.nome as categoria_nome, f.razao_social as fornecedor_nome
       FROM produtos p
       LEFT JOIN categorias_produto c ON p.categoria_id = c.id
       LEFT JOIN fornecedores f ON p.fornecedor_id = f.id
       WHERE p.id = ?`,
      [id]
    );

    if (produtos.length === 0) {
      return c.json({ error: 'Produto não encontrado' }, 404);
    }

    // Buscar últimas CATs com este produto
    const [cats] = await db.execute<RowDataPacket[]>(
      `SELECT cat.*, cl.razao_social as cliente_nome, s.nome as status_nome
       FROM cat
       JOIN clientes cl ON cat.cliente_id = cl.id
       JOIN status_cat s ON cat.status_id = s.id
       WHERE cat.produto_id = ?
       ORDER BY cat.data_abertura DESC
       LIMIT 10`,
      [id]
    );

    return c.json({
      success: true,
      data: {
        ...produtos[0],
        ultimas_cats: cats
      }
    });

  } catch (error) {
    console.error('Erro ao buscar produto:', error);
    return c.json({ error: 'Erro ao buscar produto' }, 500);
  }
});

// Criar novo produto
produtosRoutes.post('/', async (c) => {
  try {
    const data = await c.req.json();

    // Validações
    if (!data.codigo_produto || !data.nome) {
      return c.json({ error: 'Código e nome são obrigatórios' }, 400);
    }

    // Verificar se código já existe
    const [existing] = await db.execute<RowDataPacket[]>(
      'SELECT id FROM produtos WHERE codigo_produto = ?',
      [data.codigo_produto]
    );

    if (existing.length > 0) {
      return c.json({ error: 'Código do produto já existe' }, 400);
    }

    // Inserir produto
    const query = `
      INSERT INTO produtos (
        codigo_produto, referencia_antiga, nome, descricao, categoria_id,
        unidade, marca, modelo, numero_serie, fornecedor_id,
        preco_custo, preco_venda, estoque_minimo, estoque_atual,
        garantia_meses, peso, dimensoes, observacoes
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    `;

    const values = [
      data.codigo_produto,
      data.referencia_antiga || null,
      data.nome,
      data.descricao || null,
      data.categoria_id || null,
      data.unidade || 'UN',
      data.marca || null,
      data.modelo || null,
      data.numero_serie || null,
      data.fornecedor_id || null,
      data.preco_custo || 0,
      data.preco_venda || 0,
      data.estoque_minimo || 0,
      data.estoque_atual || 0,
      data.garantia_meses || 12,
      data.peso || null,
      data.dimensoes || null,
      data.observacoes || null
    ];

    const [result] = await db.execute<ResultSetHeader>(query, values);

    return c.json({
      success: true,
      message: 'Produto criado com sucesso',
      id: result.insertId
    });

  } catch (error) {
    console.error('Erro ao criar produto:', error);
    return c.json({ error: 'Erro ao criar produto' }, 500);
  }
});

// Atualizar produto
produtosRoutes.put('/:id', async (c) => {
  try {
    const id = c.req.param('id');
    const data = await c.req.json();

    // Verificar se produto existe
    const [existing] = await db.execute<RowDataPacket[]>(
      'SELECT id FROM produtos WHERE id = ?',
      [id]
    );

    if (existing.length === 0) {
      return c.json({ error: 'Produto não encontrado' }, 404);
    }

    // Se mudou código, verificar duplicação
    if (data.codigo_produto) {
      const [duplicate] = await db.execute<RowDataPacket[]>(
        'SELECT id FROM produtos WHERE codigo_produto = ? AND id != ?',
        [data.codigo_produto, id]
      );

      if (duplicate.length > 0) {
        return c.json({ error: 'Código do produto já existe' }, 400);
      }
    }

    // Montar query de atualização
    const fields = [];
    const values = [];

    const allowedFields = [
      'codigo_produto', 'referencia_antiga', 'nome', 'descricao',
      'categoria_id', 'unidade', 'marca', 'modelo', 'numero_serie',
      'fornecedor_id', 'preco_custo', 'preco_venda', 'estoque_minimo',
      'estoque_atual', 'garantia_meses', 'peso', 'dimensoes',
      'observacoes', 'ativo'
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
    const query = `UPDATE produtos SET ${fields.join(', ')} WHERE id = ?`;

    await db.execute(query, values);

    return c.json({
      success: true,
      message: 'Produto atualizado com sucesso'
    });

  } catch (error) {
    console.error('Erro ao atualizar produto:', error);
    return c.json({ error: 'Erro ao atualizar produto' }, 500);
  }
});

// Ajustar estoque
produtosRoutes.post('/:id/ajustar-estoque', async (c) => {
  try {
    const id = c.req.param('id');
    const { quantidade, tipo, motivo } = await c.req.json();

    if (!quantidade || !tipo) {
      return c.json({ error: 'Quantidade e tipo são obrigatórios' }, 400);
    }

    // Buscar estoque atual
    const [produto] = await db.execute<RowDataPacket[]>(
      'SELECT estoque_atual FROM produtos WHERE id = ?',
      [id]
    );

    if (produto.length === 0) {
      return c.json({ error: 'Produto não encontrado' }, 404);
    }

    const estoqueAtual = produto[0].estoque_atual;
    let novoEstoque = estoqueAtual;

    if (tipo === 'ENTRADA') {
      novoEstoque = estoqueAtual + quantidade;
    } else if (tipo === 'SAIDA') {
      novoEstoque = estoqueAtual - quantidade;
      if (novoEstoque < 0) {
        return c.json({ error: 'Estoque insuficiente' }, 400);
      }
    } else {
      return c.json({ error: 'Tipo inválido' }, 400);
    }

    // Atualizar estoque
    await db.execute(
      'UPDATE produtos SET estoque_atual = ? WHERE id = ?',
      [novoEstoque, id]
    );

    return c.json({
      success: true,
      message: 'Estoque ajustado com sucesso',
      estoque_anterior: estoqueAtual,
      estoque_novo: novoEstoque
    });

  } catch (error) {
    console.error('Erro ao ajustar estoque:', error);
    return c.json({ error: 'Erro ao ajustar estoque' }, 500);
  }
});

// Listar categorias
produtosRoutes.get('/categorias/list', async (c) => {
  try {
    const [categorias] = await db.execute<RowDataPacket[]>(
      'SELECT * FROM categorias_produto ORDER BY nome'
    );

    return c.json({
      success: true,
      data: categorias
    });

  } catch (error) {
    console.error('Erro ao listar categorias:', error);
    return c.json({ error: 'Erro ao listar categorias' }, 500);
  }
});

// Criar categoria
produtosRoutes.post('/categorias', async (c) => {
  try {
    const { nome, descricao } = await c.req.json();

    if (!nome) {
      return c.json({ error: 'Nome é obrigatório' }, 400);
    }

    const [result] = await db.execute<ResultSetHeader>(
      'INSERT INTO categorias_produto (nome, descricao) VALUES (?, ?)',
      [nome, descricao || null]
    );

    return c.json({
      success: true,
      message: 'Categoria criada com sucesso',
      id: result.insertId
    });

  } catch (error) {
    console.error('Erro ao criar categoria:', error);
    return c.json({ error: 'Erro ao criar categoria' }, 500);
  }
});

export default produtosRoutes;
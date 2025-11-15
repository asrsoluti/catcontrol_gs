import { Hono } from 'hono';
import db from '../config/database';
import { RowDataPacket, ResultSetHeader } from 'mysql2';
import { format } from 'date-fns';

const catRoutes = new Hono();

// Listar todas as CATs
catRoutes.get('/', async (c) => {
  try {
    const { 
      page = 1, 
      limit = 10, 
      search = '', 
      status = '', 
      cliente_id = '',
      data_inicio = '',
      data_fim = '',
      tecnico_id = ''
    } = c.req.query();
    
    const offset = (Number(page) - 1) * Number(limit);

    let query = `
      SELECT 
        cat.*,
        cl.razao_social as cliente_nome,
        cl.cpf_cnpj as cliente_documento,
        p.nome as produto_nome,
        s.nome as status_nome,
        s.cor_hex as status_cor,
        u1.nome as atendente_nome,
        u2.nome as tecnico_nome
      FROM cat
      LEFT JOIN clientes cl ON cat.cliente_id = cl.id
      LEFT JOIN produtos p ON cat.produto_id = p.id
      LEFT JOIN status_cat s ON cat.status_id = s.id
      LEFT JOIN usuarios u1 ON cat.atendente_id = u1.id
      LEFT JOIN usuarios u2 ON cat.tecnico_responsavel_id = u2.id
      WHERE 1=1
    `;
    
    const params: any[] = [];

    if (status) {
      query += ' AND cat.status_id = ?';
      params.push(status);
    }

    if (cliente_id) {
      query += ' AND cat.cliente_id = ?';
      params.push(cliente_id);
    }

    if (tecnico_id) {
      query += ' AND cat.tecnico_responsavel_id = ?';
      params.push(tecnico_id);
    }

    if (data_inicio) {
      query += ' AND DATE(cat.data_abertura) >= ?';
      params.push(data_inicio);
    }

    if (data_fim) {
      query += ' AND DATE(cat.data_abertura) <= ?';
      params.push(data_fim);
    }

    if (search) {
      query += ` AND (
        cat.numero_cat LIKE ? OR 
        cat.numero_sac LIKE ? OR 
        cl.razao_social LIKE ? OR
        cat.problema_reclamado LIKE ?
      )`;
      const searchPattern = `%${search}%`;
      params.push(searchPattern, searchPattern, searchPattern, searchPattern);
    }

    // Contar total
    const countQuery = query.replace(/SELECT.*FROM/, 'SELECT COUNT(*) as total FROM');
    const [countResult] = await db.execute<RowDataPacket[]>(countQuery, params);
    const total = countResult[0].total;

    // Buscar registros
    query += ' ORDER BY cat.data_abertura DESC LIMIT ? OFFSET ?';
    params.push(Number(limit), offset);

    const [cats] = await db.execute<RowDataPacket[]>(query, params);

    return c.json({
      success: true,
      data: cats,
      pagination: {
        page: Number(page),
        limit: Number(limit),
        total,
        totalPages: Math.ceil(total / Number(limit))
      }
    });

  } catch (error) {
    console.error('Erro ao listar CATs:', error);
    return c.json({ error: 'Erro ao listar CATs' }, 500);
  }
});

// Buscar CAT por ID
catRoutes.get('/:id', async (c) => {
  try {
    const id = c.req.param('id');

    // Buscar dados principais da CAT
    const [cats] = await db.execute<RowDataPacket[]>(
      `SELECT 
        cat.*,
        cl.razao_social as cliente_nome,
        cl.nome_fantasia as cliente_nome_fantasia,
        cl.cpf_cnpj as cliente_documento,
        cl.endereco as cliente_endereco,
        cl.bairro as cliente_bairro,
        cl.cidade as cliente_cidade,
        cl.uf as cliente_uf,
        cl.cep as cliente_cep,
        cl.telefone as cliente_telefone,
        cl.email as cliente_email,
        p.nome as produto_nome,
        p.codigo_produto,
        s.nome as status_nome,
        s.cor_hex as status_cor,
        u1.nome as atendente_nome,
        u2.nome as tecnico_nome
      FROM cat
      LEFT JOIN clientes cl ON cat.cliente_id = cl.id
      LEFT JOIN produtos p ON cat.produto_id = p.id
      LEFT JOIN status_cat s ON cat.status_id = s.id
      LEFT JOIN usuarios u1 ON cat.atendente_id = u1.id
      LEFT JOIN usuarios u2 ON cat.tecnico_responsavel_id = u2.id
      WHERE cat.id = ?`,
      [id]
    );

    if (cats.length === 0) {
      return c.json({ error: 'CAT não encontrada' }, 404);
    }

    // Buscar produtos da CAT
    const [produtos] = await db.execute<RowDataPacket[]>(
      `SELECT cp.*, p.nome, p.codigo_produto
       FROM cat_produtos cp
       JOIN produtos p ON cp.produto_id = p.id
       WHERE cp.cat_id = ?`,
      [id]
    );

    // Buscar serviços da CAT
    const [servicos] = await db.execute<RowDataPacket[]>(
      `SELECT cs.*, s.nome as servico_nome, u.nome as tecnico_nome
       FROM cat_servicos cs
       JOIN servicos s ON cs.servico_id = s.id
       LEFT JOIN usuarios u ON cs.tecnico_id = u.id
       WHERE cs.cat_id = ?`,
      [id]
    );

    // Buscar materiais da CAT
    const [materiais] = await db.execute<RowDataPacket[]>(
      `SELECT cm.*, m.nome, m.codigo_material
       FROM cat_materiais cm
       JOIN materiais m ON cm.material_id = m.id
       WHERE cm.cat_id = ?`,
      [id]
    );

    // Buscar histórico
    const [historico] = await db.execute<RowDataPacket[]>(
      `SELECT h.*, u.nome as usuario_nome, 
              s1.nome as status_anterior_nome,
              s2.nome as status_novo_nome
       FROM cat_historico h
       JOIN usuarios u ON h.usuario_id = u.id
       LEFT JOIN status_cat s1 ON h.status_anterior_id = s1.id
       LEFT JOIN status_cat s2 ON h.status_novo_id = s2.id
       WHERE h.cat_id = ?
       ORDER BY h.data_movimento DESC`,
      [id]
    );

    // Buscar anexos
    const [anexos] = await db.execute<RowDataPacket[]>(
      `SELECT a.*, u.nome as usuario_nome
       FROM cat_anexos a
       LEFT JOIN usuarios u ON a.usuario_id = u.id
       WHERE a.cat_id = ?
       ORDER BY a.data_upload DESC`,
      [id]
    );

    // Buscar avaliação de qualidade
    const [avaliacao] = await db.execute<RowDataPacket[]>(
      `SELECT aq.*, u.nome as avaliador_nome
       FROM cat_avaliacao_qualidade aq
       LEFT JOIN usuarios u ON aq.avaliado_por = u.id
       WHERE aq.cat_id = ?`,
      [id]
    );

    // Buscar pesquisa de satisfação
    const [satisfacao] = await db.execute<RowDataPacket[]>(
      `SELECT cs.*, u.nome as pesquisador_nome
       FROM cat_satisfacao cs
       LEFT JOIN usuarios u ON cs.pesquisador_id = u.id
       WHERE cs.cat_id = ?`,
      [id]
    );

    return c.json({
      success: true,
      data: {
        ...cats[0],
        produtos,
        servicos,
        materiais,
        historico,
        anexos,
        avaliacao_qualidade: avaliacao[0] || null,
        pesquisa_satisfacao: satisfacao[0] || null
      }
    });

  } catch (error) {
    console.error('Erro ao buscar CAT:', error);
    return c.json({ error: 'Erro ao buscar CAT' }, 500);
  }
});

// Criar nova CAT
catRoutes.post('/', async (c) => {
  try {
    const user = c.get('user');
    const data = await c.req.json();

    // Validações
    if (!data.cliente_id || !data.problema_reclamado) {
      return c.json({ 
        error: 'Cliente e problema reclamado são obrigatórios' 
      }, 400);
    }

    // Iniciar transação
    const connection = await db.getConnection();
    await connection.beginTransaction();

    try {
      // Inserir CAT principal
      const insertQuery = `
        INSERT INTO cat (
          numero_sac, pedido_numero, cliente_id, contato_nome,
          contato_telefone, contato_email, contato_cargo,
          produto_id, produto_descricao, numero_serie_produto,
          problema_reclamado, tipo_atendimento, prioridade,
          observacoes, atendente_id, status_id,
          data_abertura, hora_abertura
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), TIME(NOW()))
      `;

      const values = [
        data.numero_sac || null,
        data.pedido_numero || null,
        data.cliente_id,
        data.contato_nome || null,
        data.contato_telefone || null,
        data.contato_email || null,
        data.contato_cargo || null,
        data.produto_id || null,
        data.produto_descricao || null,
        data.numero_serie_produto || null,
        data.problema_reclamado,
        data.tipo_atendimento || 'AVULSO',
        data.prioridade || 'NORMAL',
        data.observacoes || null,
        user.id,
        1 // Status inicial: Em Aberto
      ];

      const [result] = await connection.execute<ResultSetHeader>(insertQuery, values);
      const catId = result.insertId;

      // Buscar o número da CAT gerado pelo trigger
      const [catData] = await connection.execute<RowDataPacket[]>(
        'SELECT numero_cat, numero_sac FROM cat WHERE id = ?',
        [catId]
      );

      // Adicionar produtos se fornecidos
      if (data.produtos && Array.isArray(data.produtos)) {
        for (const produto of data.produtos) {
          await connection.execute(
            `INSERT INTO cat_produtos 
             (cat_id, produto_id, quantidade, valor_unitario, desconto, valor_total, garantia, observacoes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
            [
              catId,
              produto.produto_id,
              produto.quantidade || 1,
              produto.valor_unitario || 0,
              produto.desconto || 0,
              produto.valor_total || 0,
              produto.garantia || 'NAO',
              produto.observacoes || null
            ]
          );
        }
      }

      // Adicionar ao histórico
      await connection.execute(
        `INSERT INTO cat_historico 
         (cat_id, usuario_id, tipo_movimento, descricao, status_novo_id)
         VALUES (?, ?, ?, ?, ?)`,
        [
          catId,
          user.id,
          'ABERTURA',
          'CAT aberta',
          1
        ]
      );

      await connection.commit();

      return c.json({
        success: true,
        message: 'CAT criada com sucesso',
        id: catId,
        numero_cat: catData[0].numero_cat,
        numero_sac: catData[0].numero_sac
      });

    } catch (error) {
      await connection.rollback();
      throw error;
    } finally {
      connection.release();
    }

  } catch (error) {
    console.error('Erro ao criar CAT:', error);
    return c.json({ error: 'Erro ao criar CAT' }, 500);
  }
});

// Atualizar CAT
catRoutes.put('/:id', async (c) => {
  try {
    const id = c.req.param('id');
    const user = c.get('user');
    const data = await c.req.json();

    // Verificar se CAT existe e pode ser editada
    const [existing] = await db.execute<RowDataPacket[]>(
      `SELECT cat.*, s.permite_edicao 
       FROM cat
       JOIN status_cat s ON cat.status_id = s.id
       WHERE cat.id = ?`,
      [id]
    );

    if (existing.length === 0) {
      return c.json({ error: 'CAT não encontrada' }, 404);
    }

    if (!existing[0].permite_edicao) {
      return c.json({ 
        error: 'CAT não pode ser editada neste status' 
      }, 400);
    }

    // Montar query de atualização
    const fields = [];
    const values = [];

    const allowedFields = [
      'contato_nome', 'contato_telefone', 'contato_email', 'contato_cargo',
      'produto_id', 'produto_descricao', 'numero_serie_produto',
      'problema_reclamado', 'diagnostico_tecnico', 'solucao_aplicada',
      'tipo_atendimento', 'tecnico_responsavel_id', 'data_atendimento',
      'hora_inicio_atendimento', 'hora_fim_atendimento', 'tempo_total_minutos',
      'previsao_entrega', 'status_id', 'prioridade', 'observacoes',
      'valor_total', 'desconto', 'forma_pagamento'
    ];

    let statusChanged = false;
    let oldStatusId = existing[0].status_id;

    for (const field of allowedFields) {
      if (data[field] !== undefined) {
        fields.push(`${field} = ?`);
        values.push(data[field]);

        if (field === 'status_id' && data[field] !== oldStatusId) {
          statusChanged = true;
        }
      }
    }

    if (fields.length > 0) {
      values.push(id);
      const query = `UPDATE cat SET ${fields.join(', ')} WHERE id = ?`;
      await db.execute(query, values);

      // Se o status mudou, registrar no histórico
      if (statusChanged) {
        await db.execute(
          `INSERT INTO cat_historico 
           (cat_id, usuario_id, tipo_movimento, descricao, status_anterior_id, status_novo_id)
           VALUES (?, ?, ?, ?, ?, ?)`,
          [
            id,
            user.id,
            'MUDANCA_STATUS',
            `Status alterado`,
            oldStatusId,
            data.status_id
          ]
        );
      }
    }

    return c.json({
      success: true,
      message: 'CAT atualizada com sucesso'
    });

  } catch (error) {
    console.error('Erro ao atualizar CAT:', error);
    return c.json({ error: 'Erro ao atualizar CAT' }, 500);
  }
});

// Adicionar anexo à CAT
catRoutes.post('/:id/anexos', async (c) => {
  try {
    const id = c.req.param('id');
    const user = c.get('user');
    const data = await c.req.json();

    // Verificar se CAT existe
    const [existing] = await db.execute<RowDataPacket[]>(
      'SELECT id FROM cat WHERE id = ?',
      [id]
    );

    if (existing.length === 0) {
      return c.json({ error: 'CAT não encontrada' }, 404);
    }

    // Inserir anexo
    const [result] = await db.execute<ResultSetHeader>(
      `INSERT INTO cat_anexos 
       (cat_id, tipo, nome_arquivo, caminho_arquivo, tamanho_bytes, descricao, usuario_id)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
      [
        id,
        data.tipo,
        data.nome_arquivo,
        data.caminho_arquivo,
        data.tamanho_bytes || null,
        data.descricao || null,
        user.id
      ]
    );

    return c.json({
      success: true,
      message: 'Anexo adicionado com sucesso',
      id: result.insertId
    });

  } catch (error) {
    console.error('Erro ao adicionar anexo:', error);
    return c.json({ error: 'Erro ao adicionar anexo' }, 500);
  }
});

export default catRoutes;
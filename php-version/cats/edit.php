<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/CAT.php';
require_once __DIR__ . '/../models/Cliente.php';

// Verificar autenticação
if (!isLoggedIn()) {
    redirect('/login.php');
}

$catModel = new CAT();
$clienteModel = new Cliente();

// Buscar CAT
$id = $_GET['id'] ?? null;
if (!$id) {
    setFlashMessage('CAT não encontrada', 'error');
    redirect('/cats/list.php');
}

$cat = $catModel->findById($id);
if (!$cat) {
    setFlashMessage('CAT não encontrada', 'error');
    redirect('/cats/list.php');
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'cliente_id' => $_POST['cliente_id'] ?? null,
        'equipamento' => $_POST['equipamento'] ?? '',
        'modelo' => $_POST['modelo'] ?? '',
        'numero_serie' => $_POST['numero_serie'] ?? '',
        'defeito_reclamado' => $_POST['defeito_reclamado'] ?? '',
        'observacoes' => $_POST['observacoes'] ?? '',
        'acessorios' => $_POST['acessorios'] ?? '',
        'prioridade' => $_POST['prioridade'] ?? 'NORMAL',
        'status_id' => $_POST['status_id'] ?? $cat['status_id']
    ];
    
    // Validações
    $errors = [];
    if (empty($data['cliente_id'])) {
        $errors[] = "Selecione um cliente";
    }
    if (empty($data['equipamento'])) {
        $errors[] = "Equipamento é obrigatório";
    }
    if (empty($data['defeito_reclamado'])) {
        $errors[] = "Defeito reclamado é obrigatório";
    }
    
    if (empty($errors)) {
        $success = $catModel->update($id, $data);
        
        if ($success) {
            // Adicionar ao histórico se houver observações novas
            if (!empty($_POST['nova_observacao'])) {
                $catModel->addHistorico($id, [
                    'descricao' => $_POST['nova_observacao'],
                    'usuario_id' => $_SESSION['user_id']
                ]);
            }
            
            setFlashMessage('CAT atualizada com sucesso!', 'success');
            redirect('/cats/view.php?id=' . $id);
        } else {
            $errors[] = "Erro ao atualizar CAT";
        }
    }
    
    if (!empty($errors)) {
        foreach ($errors as $error) {
            setFlashMessage($error, 'error');
        }
    }
    
    // Atualizar dados da CAT com os valores do POST
    foreach ($data as $key => $value) {
        $cat[$key] = $value;
    }
}

// Buscar clientes e status para os selects
$clientes = $clienteModel->getAll(['limit' => 1000]);
$statusList = $catModel->getStatus();

$pageTitle = "Editar CAT #" . $cat['numero_cat'];
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-edit mr-2"></i>Editar CAT #<?php echo htmlspecialchars($cat['numero_cat']); ?>
                </h1>
                <div class="space-x-2">
                    <a href="<?php echo url('/cats/view.php?id=' . $cat['id']); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-eye mr-2"></i>Visualizar
                    </a>
                    <a href="<?php echo url('/cats/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                </div>
            </div>
            
            <form method="POST" class="space-y-6">
                <!-- Status -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Status da CAT
                    </label>
                    <select name="status_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <?php foreach ($statusList as $status): ?>
                            <option value="<?php echo $status['id']; ?>" 
                                    <?php echo ($cat['status_id'] == $status['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($status['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Cliente -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cliente <span class="text-red-500">*</span>
                    </label>
                    <select name="cliente_id" id="cliente_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione um cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['id']; ?>" 
                                    <?php echo ($cat['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cliente['codigo_cliente']); ?> - <?php echo htmlspecialchars($cliente['nome']); ?>
                                <?php if (!empty($cliente['razao_social'])): ?>
                                    (<?php echo htmlspecialchars($cliente['razao_social']); ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Informações do Equipamento -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-laptop mr-2"></i>Informações do Equipamento
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Equipamento <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="equipamento" required 
                                   value="<?php echo htmlspecialchars($cat['equipamento']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ex: Notebook, Impressora, Desktop...">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Modelo
                            </label>
                            <input type="text" name="modelo" 
                                   value="<?php echo htmlspecialchars($cat['modelo']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ex: Dell Inspiron 15">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Série
                            </label>
                            <input type="text" name="numero_serie" 
                                   value="<?php echo htmlspecialchars($cat['numero_serie']); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="S/N do equipamento">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Prioridade
                            </label>
                            <select name="prioridade" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="BAIXA" <?php echo ($cat['prioridade'] == 'BAIXA') ? 'selected' : ''; ?>>Baixa</option>
                                <option value="NORMAL" <?php echo ($cat['prioridade'] == 'NORMAL') ? 'selected' : ''; ?>>Normal</option>
                                <option value="ALTA" <?php echo ($cat['prioridade'] == 'ALTA') ? 'selected' : ''; ?>>Alta</option>
                                <option value="URGENTE" <?php echo ($cat['prioridade'] == 'URGENTE') ? 'selected' : ''; ?>>Urgente</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- Defeito e Observações -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Defeito e Observações
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Defeito Reclamado <span class="text-red-500">*</span>
                            </label>
                            <textarea name="defeito_reclamado" required rows="4"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Descreva o problema relatado pelo cliente..."><?php echo htmlspecialchars($cat['defeito_reclamado']); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Acessórios
                            </label>
                            <textarea name="acessorios" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Ex: Fonte, mouse, teclado, case..."><?php echo htmlspecialchars($cat['acessorios']); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Observações
                            </label>
                            <textarea name="observacoes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Observações adicionais..."><?php echo htmlspecialchars($cat['observacoes']); ?></textarea>
                        </div>
                        
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-plus-circle mr-1"></i>Nova Entrada no Histórico
                            </label>
                            <textarea name="nova_observacao" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Adicione uma nova entrada ao histórico desta CAT..."></textarea>
                            <p class="text-xs text-gray-600 mt-1">
                                Será registrado com a data/hora atual e seu nome de usuário
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Botões -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="<?php echo url('/cats/view.php?id=' . $cat['id']); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

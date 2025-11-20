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
        'tecnico_id' => $_SESSION['user_id']
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
        $catId = $catModel->create($data);
        
        if ($catId) {
            setFlashMessage('CAT criada com sucesso!', 'success');
            redirect('/cats/view.php?id=' . $catId);
        } else {
            $errors[] = "Erro ao criar CAT";
        }
    }
    
    if (!empty($errors)) {
        foreach ($errors as $error) {
            setFlashMessage($error, 'error');
        }
    }
}

// Buscar clientes para o select
$clientes = $clienteModel->getAll(['limit' => 1000]);

$pageTitle = "Nova CAT";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-plus-circle mr-2"></i>Nova CAT
                </h1>
                <a href="<?php echo url('/cats/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
            
            <form method="POST" class="space-y-6">
                <!-- Cliente -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Cliente <span class="text-red-500">*</span>
                    </label>
                    <select name="cliente_id" id="cliente_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Selecione um cliente</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente['id']; ?>" 
                                    <?php echo (isset($_POST['cliente_id']) && $_POST['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cliente['codigo_cliente']); ?> - <?php echo htmlspecialchars($cliente['nome']); ?>
                                <?php if (!empty($cliente['razao_social'])): ?>
                                    (<?php echo htmlspecialchars($cliente['razao_social']); ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <a href="<?php echo url('/clientes/create.php'); ?>" class="text-sm text-blue-600 hover:text-blue-800 mt-1 inline-block">
                        <i class="fas fa-plus-circle mr-1"></i>Cadastrar novo cliente
                    </a>
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
                                   value="<?php echo htmlspecialchars($_POST['equipamento'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ex: Notebook, Impressora, Desktop...">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Modelo
                            </label>
                            <input type="text" name="modelo" 
                                   value="<?php echo htmlspecialchars($_POST['modelo'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Ex: Dell Inspiron 15">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Número de Série
                            </label>
                            <input type="text" name="numero_serie" 
                                   value="<?php echo htmlspecialchars($_POST['numero_serie'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="S/N do equipamento">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Prioridade
                            </label>
                            <select name="prioridade" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="BAIXA" <?php echo (isset($_POST['prioridade']) && $_POST['prioridade'] == 'BAIXA') ? 'selected' : ''; ?>>Baixa</option>
                                <option value="NORMAL" <?php echo (!isset($_POST['prioridade']) || $_POST['prioridade'] == 'NORMAL') ? 'selected' : ''; ?>>Normal</option>
                                <option value="ALTA" <?php echo (isset($_POST['prioridade']) && $_POST['prioridade'] == 'ALTA') ? 'selected' : ''; ?>>Alta</option>
                                <option value="URGENTE" <?php echo (isset($_POST['prioridade']) && $_POST['prioridade'] == 'URGENTE') ? 'selected' : ''; ?>>Urgente</option>
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
                                      placeholder="Descreva o problema relatado pelo cliente..."><?php echo htmlspecialchars($_POST['defeito_reclamado'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Acessórios
                            </label>
                            <textarea name="acessorios" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Ex: Fonte, mouse, teclado, case..."><?php echo htmlspecialchars($_POST['acessorios'] ?? ''); ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Observações Iniciais
                            </label>
                            <textarea name="observacoes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Observações adicionais..."><?php echo htmlspecialchars($_POST['observacoes'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Botões -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="<?php echo url('/cats/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Criar CAT
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

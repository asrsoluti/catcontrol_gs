<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Servico.php';

if (!isLoggedIn()) redirect('/login.php');

$servicoModel = new Servico();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao'] ?? null,
        'categoria' => $_POST['categoria'] ?? null,
        'preco' => floatval($_POST['preco'] ?? 0),
        'tempo_estimado' => $_POST['tempo_estimado'] ?? null
    ];
    
    if ($servicoModel->create($data)) {
        setFlashMessage('Serviço cadastrado com sucesso!', 'success');
        redirect('/servicos/list.php');
    }
}

$pageTitle = "Novo Serviço";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold"><i class="fas fa-tools mr-2"></i>Novo Serviço</h1>
            <a href="<?php echo url('/servicos/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Voltar</a>
        </div>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Nome <span class="text-red-500">*</span></label>
                <input type="text" name="nome" required class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Descrição</label>
                <textarea name="descricao" rows="3" class="w-full px-3 py-2 border rounded-md"></textarea>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Categoria</label>
                    <input type="text" name="categoria" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Preço</label>
                    <input type="number" name="preco" step="0.01" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Tempo Estimado</label>
                    <input type="text" name="tempo_estimado" placeholder="Ex: 2 horas" class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <a href="<?php echo url('/servicos/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Cadastrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

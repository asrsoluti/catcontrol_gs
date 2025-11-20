<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Produto.php';

if (!isLoggedIn()) redirect('/login.php');

$produtoModel = new Produto();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nome' => $_POST['nome'],
        'descricao' => $_POST['descricao'] ?? null,
        'categoria' => $_POST['categoria'] ?? null,
        'unidade' => $_POST['unidade'] ?? 'UN',
        'preco_custo' => floatval($_POST['preco_custo'] ?? 0),
        'preco_venda' => floatval($_POST['preco_venda'] ?? 0),
        'estoque_minimo' => intval($_POST['estoque_minimo'] ?? 0),
        'estoque_atual' => intval($_POST['estoque_atual'] ?? 0)
    ];
    
    if ($produtoModel->create($data)) {
        setFlashMessage('Produto cadastrado com sucesso!', 'success');
        redirect('/produtos/list.php');
    }
}

$pageTitle = "Novo Produto";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold"><i class="fas fa-box-open mr-2"></i>Novo Produto</h1>
            <a href="<?php echo url('/produtos/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Voltar</a>
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
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Categoria</label>
                    <input type="text" name="categoria" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Unidade</label>
                    <select name="unidade" class="w-full px-3 py-2 border rounded-md">
                        <option value="UN">Unidade</option>
                        <option value="PC">Peça</option>
                        <option value="CX">Caixa</option>
                        <option value="KG">Quilograma</option>
                        <option value="MT">Metro</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Preço de Custo</label>
                    <input type="number" name="preco_custo" step="0.01" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Preço de Venda</label>
                    <input type="number" name="preco_venda" step="0.01" class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Estoque Mínimo</label>
                    <input type="number" name="estoque_minimo" class="w-full px-3 py-2 border rounded-md">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">Estoque Atual</label>
                    <input type="number" name="estoque_atual" class="w-full px-3 py-2 border rounded-md">
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <a href="<?php echo url('/produtos/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Cadastrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

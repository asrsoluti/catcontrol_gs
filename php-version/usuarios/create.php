<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

if (!isLoggedIn()) redirect('/login.php');

$userModel = new User();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nome' => $_POST['nome'],
        'email' => $_POST['email'],
        'senha' => $_POST['senha'],
        'nivel_id' => $_POST['nivel_id']
    ];
    
    if ($userModel->create($data)) {
        setFlashMessage('Usuário cadastrado com sucesso!', 'success');
        redirect('/usuarios/list.php');
    }
}

$pageTitle = "Novo Usuário";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold"><i class="fas fa-user-plus mr-2"></i>Novo Usuário</h1>
            <a href="<?php echo url('/usuarios/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Voltar</a>
        </div>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium mb-2">Nome <span class="text-red-500">*</span></label>
                <input type="text" name="nome" required class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" required class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Senha <span class="text-red-500">*</span></label>
                <input type="password" name="senha" required class="w-full px-3 py-2 border rounded-md">
            </div>
            
            <div>
                <label class="block text-sm font-medium mb-2">Nível <span class="text-red-500">*</span></label>
                <select name="nivel_id" required class="w-full px-3 py-2 border rounded-md">
                    <option value="1">Administrador</option>
                    <option value="2">Gerente</option>
                    <option value="3">Técnico</option>
                    <option value="4">Atendente</option>
                </select>
            </div>
            
            <div class="flex justify-end space-x-4 pt-4">
                <a href="<?php echo url('/usuarios/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    <i class="fas fa-save mr-2"></i>Cadastrar
                </button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

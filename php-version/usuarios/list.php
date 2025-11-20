<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/User.php';

if (!isLoggedIn()) redirect('/login.php');

$userModel = new User();
$usuarios = $userModel->getAll();

$pageTitle = "Usuários";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-users mr-2"></i>Usuários</h1>
            <a href="<?php echo url('/usuarios/create.php'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-user-plus mr-2"></i>Novo Usuário
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Nome</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">Nível</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><?php echo htmlspecialchars($usuario['nome']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($usuario['email']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($usuario['nivel_nome']); ?></td>
                            <td class="px-6 py-4">
                                <?php if ($usuario['ativo']): ?>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Ativo</span>
                                <?php else: ?>
                                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?php echo url('/usuarios/edit.php?id=' . $usuario['id']); ?>" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

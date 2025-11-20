<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Servico.php';

if (!isLoggedIn()) redirect('/login.php');

$servicoModel = new Servico();
$filters = ['search' => $_GET['search'] ?? '', 'page' => (int)($_GET['page'] ?? 0), 'limit' => 20];
$servicos = $servicoModel->getAll($filters);
$total = $servicoModel->count($filters);

$pageTitle = "Serviços";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-tools mr-2"></i>Serviços</h1>
            <a href="<?php echo url('/servicos/create.php'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-plus-circle mr-2"></i>Novo Serviço
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" class="flex gap-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" 
                       placeholder="Buscar serviço..." class="flex-1 px-3 py-2 border rounded-md">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </form>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (empty($servicos)): ?>
                <div class="p-12 text-center">
                    <p class="text-gray-600">Nenhum serviço encontrado.</p>
                </div>
            <?php else: ?>
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Código</th>
                            <th class="px-6 py-3 text-left">Nome</th>
                            <th class="px-6 py-3 text-left">Categoria</th>
                            <th class="px-6 py-3 text-left">Preço</th>
                            <th class="px-6 py-3 text-left">Tempo Est.</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($servicos as $servico): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-mono"><?php echo htmlspecialchars($servico['codigo_servico']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($servico['nome']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($servico['categoria'] ?? '-'); ?></td>
                                <td class="px-6 py-4">R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?></td>
                                <td class="px-6 py-4"><?php echo $servico['tempo_estimado'] ?? '-'; ?></td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?php echo url('/servicos/edit.php?id=' . $servico['id']); ?>" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

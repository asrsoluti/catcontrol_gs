<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Produto.php';

if (!isLoggedIn()) redirect('/login.php');

$produtoModel = new Produto();
$filters = ['search' => $_GET['search'] ?? '', 'page' => (int)($_GET['page'] ?? 0), 'limit' => 20];
$produtos = $produtoModel->getAll($filters);
$total = $produtoModel->count($filters);

$pageTitle = "Produtos";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800"><i class="fas fa-box mr-2"></i>Produtos</h1>
            <a href="<?php echo url('/produtos/create.php'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fas fa-plus-circle mr-2"></i>Novo Produto
            </a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" class="flex gap-4">
                <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>" 
                       placeholder="Buscar produto..." class="flex-1 px-3 py-2 border rounded-md">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </form>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (empty($produtos)): ?>
                <div class="p-12 text-center">
                    <p class="text-gray-600">Nenhum produto encontrado.</p>
                </div>
            <?php else: ?>
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Código</th>
                            <th class="px-6 py-3 text-left">Nome</th>
                            <th class="px-6 py-3 text-left">Categoria</th>
                            <th class="px-6 py-3 text-left">Preço</th>
                            <th class="px-6 py-3 text-left">Estoque</th>
                            <th class="px-6 py-3 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <?php foreach ($produtos as $produto): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-mono"><?php echo htmlspecialchars($produto['codigo_produto']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($produto['nome']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($produto['categoria'] ?? '-'); ?></td>
                                <td class="px-6 py-4">R$ <?php echo number_format($produto['preco_venda'], 2, ',', '.'); ?></td>
                                <td class="px-6 py-4"><?php echo $produto['estoque_atual']; ?> <?php echo $produto['unidade']; ?></td>
                                <td class="px-6 py-4 text-right">
                                    <a href="<?php echo url('/produtos/edit.php?id=' . $produto['id']); ?>" class="text-blue-600 hover:text-blue-900">
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

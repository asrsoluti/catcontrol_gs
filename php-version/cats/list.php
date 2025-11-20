<?php
require_once '../config/config.php';
require_once '../config/database.php';

requireLogin();

$catModel = new CAT();

// Processar filtros
$filters = [
    'search' => $_GET['search'] ?? '',
    'status_id' => $_GET['status_id'] ?? '',
    'cliente_id' => $_GET['cliente_id'] ?? '',
    'data_inicio' => $_GET['data_inicio'] ?? '',
    'data_fim' => $_GET['data_fim'] ?? '',
    'page' => (int)($_GET['page'] ?? 0),
    'limit' => 20
];

$cats = $catModel->getAll($filters);
$total = $catModel->count($filters);
$totalPages = ceil($total / $filters['limit']);

// Buscar status para filtro
$statusList = $catModel->getStatus();

$pageTitle = 'CATs';
$showNewButton = true;
$newButtonUrl = 'create.php';
$newButtonText = 'Nova CAT';
include '../includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($filters['search']); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                       placeholder="Número, cliente, problema...">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select name="status_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">Todos</option>
                    <?php foreach ($statusList as $status): ?>
                        <option value="<?php echo $status['id']; ?>" 
                                <?php echo $filters['status_id'] == $status['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($status['nome']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Início</label>
                <input type="date" name="data_inicio" value="<?php echo htmlspecialchars($filters['data_inicio']); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Data Fim</label>
                <input type="date" name="data_fim" value="<?php echo htmlspecialchars($filters['data_fim']); ?>"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            
            <div class="md:col-span-4 flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
                <a href="list.php" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600">
                    <i class="fas fa-times mr-2"></i>Limpar
                </a>
            </div>
        </form>
    </div>
    
    <!-- Lista de CATs -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Problema</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Técnico</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($cats)): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-2"></i>
                                <p>Nenhuma CAT encontrada</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($cats as $cat): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-blue-600">
                                        <?php echo htmlspecialchars($cat['numero_cat']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($cat['cliente_nome']); ?></td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs truncate">
                                        <?php echo htmlspecialchars($cat['problema_reclamado']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo formatDate($cat['data_abertura']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full"
                                          style="background-color: <?php echo $cat['status_cor']; ?>22; color: <?php echo $cat['status_cor']; ?>">
                                        <?php echo htmlspecialchars($cat['status_nome']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($cat['tecnico_nome'] ?? '-'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="view.php?id=<?php echo $cat['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900 mr-3" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="edit.php?id=<?php echo $cat['id']; ?>" 
                                       class="text-green-600 hover:text-green-900" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <?php if ($totalPages > 1): ?>
            <div class="bg-gray-50 px-6 py-4 flex items-center justify-between border-t">
                <div class="text-sm text-gray-700">
                    Mostrando <?php echo count($cats); ?> de <?php echo $total; ?> registros
                </div>
                <div class="flex gap-2">
                    <?php for ($i = 0; $i < $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&<?php echo http_build_query(array_diff_key($filters, ['page' => ''])); ?>"
                           class="px-3 py-1 rounded <?php echo $filters['page'] == $i ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                            <?php echo $i + 1; ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
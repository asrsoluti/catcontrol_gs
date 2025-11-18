<?php
require_once 'config/config.php';
require_once 'config/database.php';

requireLogin();

$catModel = new CAT();
$stats = $catModel->getStats();

// Buscar últimas CATs
$recentCATs = $catModel->getAll(['limit' => 5, 'page' => 0]);

$pageTitle = 'Dashboard';
include 'includes/header.php';
?>

<div class="container mx-auto px-4 py-6">
    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- CATs Abertas -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">CATs Abertas</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $stats['abertas']; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Finalizadas Hoje -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Finalizadas Hoje</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $stats['finalizadas_hoje']; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Aguardando -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Aguardando</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $stats['aguardando']; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Clientes Ativos -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-users text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Clientes Ativos</p>
                    <p class="text-3xl font-bold text-gray-800"><?php echo $stats['clientes']; ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Últimas CATs -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-800">
                    <i class="fas fa-list mr-2"></i>Últimas CATs
                </h2>
                <a href="cats/list.php" class="text-blue-600 hover:text-blue-800 text-sm">
                    Ver todas <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Número
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Problema
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Data
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($recentCATs)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-inbox text-3xl mb-2"></i>
                                <p>Nenhuma CAT encontrada</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentCATs as $cat): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-semibold text-gray-900">
                                        <?php echo htmlspecialchars($cat['numero_cat']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($cat['cliente_nome']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 truncate max-w-xs">
                                        <?php echo htmlspecialchars(substr($cat['problema_reclamado'], 0, 50)) . '...'; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo formatDate($cat['data_abertura']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full" 
                                          style="background-color: <?php echo $cat['status_cor']; ?>22; color: <?php echo $cat['status_cor']; ?>">
                                        <?php echo htmlspecialchars($cat['status_nome']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="cats/view.php?id=<?php echo $cat['id']; ?>" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="cats/edit.php?id=<?php echo $cat['id']; ?>" 
                                       class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
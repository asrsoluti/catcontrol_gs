<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Cliente.php';

// Verificar autenticação
if (!isLoggedIn()) {
    redirect('/login.php');
}

$clienteModel = new Cliente();

// Parâmetros de busca e paginação
$filters = [
    'search' => $_GET['search'] ?? '',
    'tipo' => $_GET['tipo'] ?? '',
    'page' => (int)($_GET['page'] ?? 0),
    'limit' => 20
];

// Buscar clientes
$clientes = $clienteModel->getAll($filters);
$total = $clienteModel->count($filters);
$totalPages = ceil($total / $filters['limit']);

$pageTitle = "Clientes";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Cabeçalho -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">
                <i class="fas fa-users mr-2"></i>Clientes
            </h1>
            <a href="<?php echo url('/clientes/create.php'); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                <i class="fas fa-plus-circle mr-2"></i>Novo Cliente
            </a>
        </div>
        
        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input type="text" name="search" 
                           value="<?php echo htmlspecialchars($filters['search']); ?>"
                           placeholder="Nome, CPF/CNPJ, telefone, email..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                    <select name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <option value="PF" <?php echo $filters['tipo'] === 'PF' ? 'selected' : ''; ?>>Pessoa Física</option>
                        <option value="PJ" <?php echo $filters['tipo'] === 'PJ' ? 'selected' : ''; ?>>Pessoa Jurídica</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Resultado -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 bg-gray-50 border-b">
                <p class="text-sm text-gray-600">
                    Mostrando <strong><?php echo count($clientes); ?></strong> de <strong><?php echo $total; ?></strong> clientes
                </p>
            </div>
            
            <?php if (empty($clientes)): ?>
            <div class="p-12 text-center">
                <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                <p class="text-gray-600">Nenhum cliente encontrado.</p>
                <a href="<?php echo url('/clientes/create.php'); ?>" class="inline-block mt-4 text-blue-600 hover:text-blue-800">
                    <i class="fas fa-plus-circle mr-2"></i>Cadastrar primeiro cliente
                </a>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Código
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nome / Razão Social
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                CPF/CNPJ
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Telefone
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipo
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($clientes as $cliente): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-mono text-sm font-semibold text-gray-900">
                                    <?php echo htmlspecialchars($cliente['codigo_cliente']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($cliente['nome']); ?>
                                    </div>
                                    <?php if (!empty($cliente['razao_social']) && $cliente['razao_social'] !== $cliente['nome']): ?>
                                    <div class="text-sm text-gray-500">
                                        <?php echo htmlspecialchars($cliente['razao_social']); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo htmlspecialchars($cliente['cpf_cnpj'] ?? '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php if (!empty($cliente['telefone'])): ?>
                                    <a href="tel:<?php echo htmlspecialchars($cliente['telefone']); ?>" class="text-blue-600 hover:text-blue-800">
                                        <?php echo htmlspecialchars($cliente['telefone']); ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?php if (!empty($cliente['email'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($cliente['email']); ?>" class="text-blue-600 hover:text-blue-800">
                                        <?php echo htmlspecialchars($cliente['email']); ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <?php if ($cliente['tipo'] === 'PF'): ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Pessoa Física
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Pessoa Jurídica
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="<?php echo url('/clientes/edit.php?id=' . $cliente['id']); ?>" 
                                   class="text-blue-600 hover:text-blue-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo url('/cats/list.php?cliente_id=' . $cliente['id']); ?>" 
                                   class="text-green-600 hover:text-green-900" 
                                   title="Ver CATs">
                                    <i class="fas fa-file-alt"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 bg-gray-50 border-t flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($filters['page'] > 0): ?>
                    <a href="?<?php echo http_build_query(array_merge($filters, ['page' => $filters['page'] - 1])); ?>" 
                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Anterior
                    </a>
                    <?php endif; ?>
                    
                    <?php if ($filters['page'] < $totalPages - 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($filters, ['page' => $filters['page'] + 1])); ?>" 
                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Próxima
                    </a>
                    <?php endif; ?>
                </div>
                
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Página <span class="font-medium"><?php echo $filters['page'] + 1; ?></span> de 
                            <span class="font-medium"><?php echo $totalPages; ?></span>
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php for ($i = 0; $i < $totalPages; $i++): ?>
                                <?php if ($i == $filters['page']): ?>
                                    <span class="relative inline-flex items-center px-4 py-2 border border-blue-500 bg-blue-50 text-sm font-medium text-blue-600">
                                        <?php echo $i + 1; ?>
                                    </span>
                                <?php else: ?>
                                    <a href="?<?php echo http_build_query(array_merge($filters, ['page' => $i])); ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        <?php echo $i + 1; ?>
                                    </a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

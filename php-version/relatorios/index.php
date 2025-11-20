<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/CAT.php';

// Verificar autenticação
if (!isLoggedIn()) {
    redirect('/login.php');
}

$catModel = new CAT();
$stats = $catModel->getStats();

$pageTitle = "Relatórios";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fas fa-chart-bar mr-2"></i>Relatórios e Estatísticas
        </h1>
        
        <!-- Estatísticas Gerais -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">CATs Abertas</h3>
                    <i class="fas fa-folder-open text-2xl text-blue-600"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800"><?php echo $stats['abertas'] ?? 0; ?></p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">CATs Concluídas</h3>
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800"><?php echo $stats['concluidas'] ?? 0; ?></p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">Este Mês</h3>
                    <i class="fas fa-calendar-alt text-2xl text-purple-600"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800"><?php echo $stats['mes_atual'] ?? 0; ?></p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-medium text-gray-600">Total</h3>
                    <i class="fas fa-file-alt text-2xl text-gray-600"></i>
                </div>
                <p class="text-3xl font-bold text-gray-800"><?php echo $stats['total'] ?? 0; ?></p>
            </div>
        </div>
        
        <!-- Filtros de Relatórios -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-filter mr-2"></i>Filtros de Relatório
            </h2>
            
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Inicial</label>
                    <input type="date" name="data_inicio" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Data Final</label>
                    <input type="date" name="data_fim" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Relatório</label>
                    <select name="tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cats">CATs</option>
                        <option value="clientes">Clientes</option>
                        <option value="produtos">Produtos</option>
                        <option value="servicos">Serviços</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition">
                        <i class="fas fa-search mr-2"></i>Gerar Relatório
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Relatórios Disponíveis -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-blue-600"></i>Relatório de CATs
                </h3>
                <p class="text-gray-600 mb-4 text-sm">Relatório completo de todas as CATs com filtros</p>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-download mr-2"></i>Gerar PDF
                </button>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-users mr-2 text-green-600"></i>Relatório de Clientes
                </h3>
                <p class="text-gray-600 mb-4 text-sm">Lista completa de clientes cadastrados</p>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-download mr-2"></i>Gerar PDF
                </button>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center">
                    <i class="fas fa-chart-line mr-2 text-purple-600"></i>Relatório Financeiro
                </h3>
                <p class="text-gray-600 mb-4 text-sm">Análise financeira de produtos e serviços</p>
                <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-download mr-2"></i>Gerar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

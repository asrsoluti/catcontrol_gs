<?php
require_once __DIR__ . '/../config/config.php';

// Verificar autenticação
if (!isLoggedIn()) {
    redirect('/login.php');
}

$pageTitle = "Configurações";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">
            <i class="fas fa-cog mr-2"></i>Configurações do Sistema
        </h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Configurações da Empresa -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-building mr-2 text-blue-600"></i>Dados da Empresa
                </h2>
                <p class="text-gray-600 mb-4">Configure as informações da sua empresa</p>
                <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-edit mr-2"></i>Configurar
                </button>
            </div>
            
            <!-- Status CAT -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-list mr-2 text-green-600"></i>Status de CAT
                </h2>
                <p class="text-gray-600 mb-4">Gerencie os status disponíveis para CATs</p>
                <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-edit mr-2"></i>Gerenciar
                </button>
            </div>
            
            <!-- Usuários -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-users-cog mr-2 text-purple-600"></i>Usuários
                </h2>
                <p class="text-gray-600 mb-4">Gerencie usuários e permissões</p>
                <a href="<?php echo url('/usuarios/list.php'); ?>" class="inline-block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-edit mr-2"></i>Gerenciar
                </a>
            </div>
            
            <!-- Backup -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-database mr-2 text-yellow-600"></i>Backup
                </h2>
                <p class="text-gray-600 mb-4">Faça backup dos seus dados</p>
                <button class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-download mr-2"></i>Fazer Backup
                </button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

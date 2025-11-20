<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/CAT.php';

// Verificar autenticação
if (!isLoggedIn()) {
    redirect('/login.php');
}

$catModel = new CAT();

// Buscar CAT
$id = $_GET['id'] ?? null;
if (!$id) {
    setFlashMessage('CAT não encontrada', 'error');
    redirect('/cats/list.php');
}

$cat = $catModel->findById($id);
if (!$cat) {
    setFlashMessage('CAT não encontrada', 'error');
    redirect('/cats/list.php');
}

// Buscar histórico
$historico = $catModel->getHistorico($id);

// Definir cor do status
$statusColors = [
    'ABERTA' => 'bg-blue-100 text-blue-800',
    'EM ANDAMENTO' => 'bg-yellow-100 text-yellow-800',
    'AGUARDANDO PEÇA' => 'bg-orange-100 text-orange-800',
    'CONCLUÍDA' => 'bg-green-100 text-green-800',
    'ENTREGUE' => 'bg-gray-100 text-gray-800',
    'CANCELADA' => 'bg-red-100 text-red-800'
];

$statusColor = $statusColors[$cat['status_nome']] ?? 'bg-gray-100 text-gray-800';

// Definir cor da prioridade
$prioridadeColors = [
    'BAIXA' => 'text-green-600',
    'NORMAL' => 'text-blue-600',
    'ALTA' => 'text-orange-600',
    'URGENTE' => 'text-red-600'
];

$prioridadeColor = $prioridadeColors[$cat['prioridade']] ?? 'text-gray-600';

$pageTitle = "CAT #" . $cat['numero_cat'];
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Cabeçalho -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-file-alt mr-2"></i>CAT #<?php echo htmlspecialchars($cat['numero_cat']); ?>
                    </h1>
                    <div class="flex items-center space-x-4">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold <?php echo $statusColor; ?>">
                            <?php echo htmlspecialchars($cat['status_nome']); ?>
                        </span>
                        <span class="<?php echo $prioridadeColor; ?> font-semibold">
                            <i class="fas fa-flag mr-1"></i><?php echo htmlspecialchars($cat['prioridade']); ?>
                        </span>
                    </div>
                </div>
                <div class="space-x-2">
                    <a href="<?php echo url('/cats/edit.php?id=' . $cat['id']); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-edit mr-2"></i>Editar
                    </a>
                    <a href="<?php echo url('/cats/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                        <i class="fas fa-arrow-left mr-2"></i>Voltar
                    </a>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
                <div>
                    <p class="text-sm text-gray-600">Data de Abertura</p>
                    <p class="font-semibold"><?php echo date('d/m/Y H:i', strtotime($cat['data_abertura'])); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Técnico Responsável</p>
                    <p class="font-semibold"><?php echo htmlspecialchars($cat['tecnico_nome']); ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Última Atualização</p>
                    <p class="font-semibold"><?php echo date('d/m/Y H:i', strtotime($cat['updated_at'])); ?></p>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Coluna Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Informações do Cliente -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-user mr-2 text-blue-600"></i>Cliente
                    </h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Código:</span>
                            <span class="font-semibold"><?php echo htmlspecialchars($cat['cliente_codigo']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nome:</span>
                            <span class="font-semibold"><?php echo htmlspecialchars($cat['cliente_nome']); ?></span>
                        </div>
                        <?php if (!empty($cat['cliente_email'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-semibold">
                                <a href="mailto:<?php echo htmlspecialchars($cat['cliente_email']); ?>" class="text-blue-600 hover:text-blue-800">
                                    <?php echo htmlspecialchars($cat['cliente_email']); ?>
                                </a>
                            </span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($cat['cliente_telefone'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Telefone:</span>
                            <span class="font-semibold">
                                <a href="tel:<?php echo htmlspecialchars($cat['cliente_telefone']); ?>" class="text-blue-600 hover:text-blue-800">
                                    <?php echo htmlspecialchars($cat['cliente_telefone']); ?>
                                </a>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Informações do Equipamento -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-laptop mr-2 text-blue-600"></i>Equipamento
                    </h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Equipamento</p>
                            <p class="font-semibold text-lg"><?php echo htmlspecialchars($cat['equipamento']); ?></p>
                        </div>
                        <?php if (!empty($cat['modelo'])): ?>
                        <div>
                            <p class="text-sm text-gray-600">Modelo</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($cat['modelo']); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($cat['numero_serie'])): ?>
                        <div>
                            <p class="text-sm text-gray-600">Número de Série</p>
                            <p class="font-semibold font-mono"><?php echo htmlspecialchars($cat['numero_serie']); ?></p>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($cat['acessorios'])): ?>
                        <div>
                            <p class="text-sm text-gray-600">Acessórios</p>
                            <p class="font-semibold"><?php echo nl2br(htmlspecialchars($cat['acessorios'])); ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Defeito Reclamado -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2 text-red-600"></i>Defeito Reclamado
                    </h2>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4">
                        <p class="text-gray-800 whitespace-pre-line"><?php echo htmlspecialchars($cat['defeito_reclamado']); ?></p>
                    </div>
                </div>
                
                <?php if (!empty($cat['observacoes'])): ?>
                <!-- Observações -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-sticky-note mr-2 text-yellow-600"></i>Observações
                    </h2>
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4">
                        <p class="text-gray-800 whitespace-pre-line"><?php echo htmlspecialchars($cat['observacoes']); ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Histórico -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-history mr-2 text-green-600"></i>Histórico de Atendimento
                    </h2>
                    
                    <?php if (empty($historico)): ?>
                    <p class="text-gray-600 text-center py-4">Nenhuma entrada no histórico ainda.</p>
                    <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($historico as $item): ?>
                        <div class="border-l-4 border-blue-500 pl-4 py-2">
                            <div class="flex justify-between items-start mb-2">
                                <span class="font-semibold text-gray-800">
                                    <i class="fas fa-user-circle mr-1"></i><?php echo htmlspecialchars($item['usuario_nome']); ?>
                                </span>
                                <span class="text-sm text-gray-600">
                                    <i class="far fa-clock mr-1"></i><?php echo date('d/m/Y H:i', strtotime($item['created_at'])); ?>
                                </span>
                            </div>
                            <p class="text-gray-700 whitespace-pre-line"><?php echo htmlspecialchars($item['descricao']); ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Coluna Lateral -->
            <div class="space-y-6">
                <!-- Ações Rápidas -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-bolt mr-2 text-yellow-600"></i>Ações Rápidas
                    </h2>
                    <div class="space-y-2">
                        <a href="<?php echo url('/cats/edit.php?id=' . $cat['id']); ?>" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-lg transition">
                            <i class="fas fa-edit mr-2"></i>Editar CAT
                        </a>
                        <button onclick="window.print()" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-lg transition">
                            <i class="fas fa-print mr-2"></i>Imprimir
                        </button>
                        <a href="#" class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-2 rounded-lg transition">
                            <i class="fas fa-envelope mr-2"></i>Enviar Email
                        </a>
                    </div>
                </div>
                
                <!-- Informações Adicionais -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-blue-600"></i>Informações
                    </h2>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID:</span>
                            <span class="font-semibold"><?php echo htmlspecialchars($cat['id']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Número CAT:</span>
                            <span class="font-semibold font-mono"><?php echo htmlspecialchars($cat['numero_cat']); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Criado em:</span>
                            <span class="font-semibold"><?php echo date('d/m/Y', strtotime($cat['created_at'])); ?></span>
                        </div>
                        <?php if (!empty($cat['data_fechamento'])): ?>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Fechado em:</span>
                            <span class="font-semibold"><?php echo date('d/m/Y', strtotime($cat['data_fechamento'])); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Status do Fluxo -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-tasks mr-2 text-purple-600"></i>Fluxo
                    </h2>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center <?php echo $cat['status_nome'] == 'ABERTA' ? 'text-blue-600 font-semibold' : 'text-gray-400'; ?>">
                            <i class="fas fa-circle mr-2 text-xs"></i>
                            <span>Aberta</span>
                        </div>
                        <div class="flex items-center <?php echo $cat['status_nome'] == 'EM ANDAMENTO' ? 'text-yellow-600 font-semibold' : 'text-gray-400'; ?>">
                            <i class="fas fa-circle mr-2 text-xs"></i>
                            <span>Em Andamento</span>
                        </div>
                        <div class="flex items-center <?php echo $cat['status_nome'] == 'CONCLUÍDA' ? 'text-green-600 font-semibold' : 'text-gray-400'; ?>">
                            <i class="fas fa-circle mr-2 text-xs"></i>
                            <span>Concluída</span>
                        </div>
                        <div class="flex items-center <?php echo $cat['status_nome'] == 'ENTREGUE' ? 'text-gray-600 font-semibold' : 'text-gray-400'; ?>">
                            <i class="fas fa-circle mr-2 text-xs"></i>
                            <span>Entregue</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, nav, header, button, .bg-gray-500, .bg-blue-600 {
        display: none !important;
    }
    .container {
        max-width: 100% !important;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Sistema CAT'; ?> - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-4 border-b border-blue-700">
                <h1 class="text-xl font-bold flex items-center">
                    <i class="fas fa-tools mr-2"></i>
                    Sistema CAT
                </h1>
            </div>
            
            <nav class="flex-1 p-4 overflow-y-auto">
                <div class="space-y-2">
                    <a href="<?php echo SITE_URL; ?>/index.php" 
                       class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                    
                    <a href="<?php echo SITE_URL; ?>/cats/list.php" 
                       class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-clipboard-list mr-2"></i> CATs
                    </a>
                    
                    <a href="<?php echo SITE_URL; ?>/clientes/list.php" 
                       class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-users mr-2"></i> Clientes
                    </a>
                    
                    <a href="<?php echo SITE_URL; ?>/produtos/list.php" 
                       class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-box mr-2"></i> Produtos
                    </a>
                    
                    <a href="<?php echo SITE_URL; ?>/servicos/list.php" 
                       class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-wrench mr-2"></i> Serviços
                    </a>
                    
                    <a href="<?php echo SITE_URL; ?>/relatorios/index.php" 
                       class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-chart-bar mr-2"></i> Relatórios
                    </a>
                    
                    <div class="mt-4 pt-4 border-t border-blue-700">
                        <?php if (hasPermission('admin')): ?>
                            <a href="<?php echo SITE_URL; ?>/usuarios/list.php" 
                               class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                                <i class="fas fa-user-shield mr-2"></i> Usuários
                            </a>
                        <?php endif; ?>
                        
                        <a href="<?php echo SITE_URL; ?>/config/index.php" 
                           class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                            <i class="fas fa-cog mr-2"></i> Configurações
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="p-4 border-t border-blue-700">
                <div class="flex items-center justify-between">
                    <div class="text-sm truncate">
                        <div class="font-semibold"><?php echo htmlspecialchars($_SESSION['user_nome']); ?></div>
                        <div class="text-blue-300 text-xs"><?php echo htmlspecialchars($_SESSION['user_nivel']); ?></div>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/logout.php" 
                       class="text-red-300 hover:text-red-200"
                       title="Sair">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Conteúdo Principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 class="text-2xl font-semibold text-gray-800">
                        <?php echo $pageTitle ?? 'Dashboard'; ?>
                    </h2>
                    <div class="flex items-center space-x-4">
                        <?php if (isset($showNewButton) && $showNewButton): ?>
                            <a href="<?php echo $newButtonUrl ?? '#'; ?>" 
                               class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                                <i class="fas fa-plus mr-2"></i><?php echo $newButtonText ?? 'Novo'; ?>
                            </a>
                        <?php endif; ?>
                        <span class="text-gray-500 text-sm">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <?php echo date('d/m/Y H:i'); ?>
                        </span>
                    </div>
                </div>
            </header>
            
            <!-- Mensagens Flash -->
            <?php if ($success = getSuccess()): ?>
                <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error = getError()): ?>
                <div class="mx-6 mt-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <!-- Área de Conteúdo -->
            <main class="flex-1 overflow-y-auto"
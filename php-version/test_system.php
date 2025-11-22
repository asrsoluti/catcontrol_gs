<?php
/**
 * Script de Teste do Sistema
 * Verifica se todos os módulos estão acessíveis
 */

require_once __DIR__ . '/config/config.php';

echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <title>Teste do Sistema</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; color: #155724; }
        .error { background: #f8d7da; border-color: #f5c6cb; color: #721c24; }
        .info { background: #d1ecf1; border-color: #bee5eb; color: #0c5460; }
        ul { list-style: none; padding: 0; }
        li { padding: 5px 0; }
        .check { color: green; font-weight: bold; }
        .cross { color: red; font-weight: bold; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
<div class='container'>";

echo "<h1>🔧 Diagnóstico do Sistema CAT</h1>";

// 1. Verificar configurações
echo "<div class='section info'>";
echo "<h2>📋 Configurações do Sistema</h2>";
echo "<ul>";
echo "<li><strong>SITE_NAME:</strong> " . SITE_NAME . "</li>";
echo "<li><strong>SITE_URL:</strong> " . SITE_URL . "</li>";
echo "<li><strong>BASE_PATH:</strong> " . BASE_PATH . "</li>";
echo "<li><strong>DEBUG_MODE:</strong> " . (DEBUG_MODE ? 'Ativado' : 'Desativado') . "</li>";
echo "</ul>";
echo "</div>";

// 2. Verificar arquivos principais
echo "<div class='section'>";
echo "<h2>📁 Verificação de Arquivos</h2>";
echo "<ul>";

$files = [
    'Config' => 'config/config.php',
    'Database' => 'config/database.php',
    'Header' => 'includes/header.php',
    'Footer' => 'includes/footer.php',
    'Login' => 'login.php',
    'Logout' => 'logout.php',
    'Dashboard' => 'index.php',
];

foreach ($files as $name => $file) {
    $exists = file_exists(BASE_PATH . '/' . $file);
    echo "<li>" . ($exists ? "✅" : "❌") . " $name: $file</li>";
}
echo "</ul>";
echo "</div>";

// 3. Verificar Models
echo "<div class='section'>";
echo "<h2>🗂️ Verificação de Models</h2>";
echo "<ul>";

$models = ['User', 'Cliente', 'CAT', 'Produto', 'Servico'];
foreach ($models as $model) {
    $file = BASE_PATH . '/models/' . $model . '.php';
    $exists = file_exists($file);
    echo "<li>" . ($exists ? "✅" : "❌") . " $model.php</li>";
}
echo "</ul>";
echo "</div>";

// 4. Verificar módulos
echo "<div class='section'>";
echo "<h2>🔌 Verificação de Módulos</h2>";

$modules = [
    'CATs' => [
        'cats/list.php',
        'cats/create.php',
        'cats/edit.php',
        'cats/view.php'
    ],
    'Clientes' => [
        'clientes/list.php',
        'clientes/create.php',
        'clientes/edit.php'
    ],
    'Produtos' => [
        'produtos/list.php',
        'produtos/create.php',
        'produtos/edit.php'
    ],
    'Serviços' => [
        'servicos/list.php',
        'servicos/create.php',
        'servicos/edit.php'
    ],
    'Usuários' => [
        'usuarios/list.php',
        'usuarios/create.php',
        'usuarios/edit.php'
    ],
    'Sistema' => [
        'config/index.php',
        'relatorios/index.php'
    ]
];

foreach ($modules as $moduleName => $files) {
    echo "<h3>$moduleName</h3><ul>";
    foreach ($files as $file) {
        $exists = file_exists(BASE_PATH . '/' . $file);
        $url = SITE_URL . '/' . $file;
        echo "<li>" . ($exists ? "✅" : "❌") . " ";
        if ($exists) {
            echo "<a href='$url' target='_blank'>$file</a>";
        } else {
            echo "$file";
        }
        echo "</li>";
    }
    echo "</ul>";
}
echo "</div>";

// 5. Verificar conexão com banco
echo "<div class='section'>";
echo "<h2>🗄️ Verificação de Banco de Dados</h2>";
try {
    require_once BASE_PATH . '/config/database.php';
    $db = Database::getInstance()->getConnection();
    echo "<p class='success'>✅ Conexão com banco de dados estabelecida com sucesso!</p>";
    
    // Testar algumas tabelas
    $tables = ['usuarios', 'niveis_usuario', 'clientes', 'cat', 'produtos', 'servicos', 'status_cat'];
    echo "<h3>Tabelas do banco:</h3><ul>";
    foreach ($tables as $table) {
        try {
            $stmt = $db->query("SELECT COUNT(*) as total FROM $table");
            $result = $stmt->fetch();
            echo "<li>✅ <strong>$table:</strong> {$result['total']} registros</li>";
        } catch (Exception $e) {
            echo "<li>❌ <strong>$table:</strong> " . $e->getMessage() . "</li>";
        }
    }
    echo "</ul>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erro ao conectar ao banco: " . $e->getMessage() . "</p>";
}
echo "</div>";

// 6. Links rápidos
echo "<div class='section info'>";
echo "<h2>🔗 Links Rápidos</h2>";
echo "<ul>";
echo "<li><a href='" . SITE_URL . "/login.php'>Login</a></li>";
echo "<li><a href='" . SITE_URL . "/index.php'>Dashboard</a></li>";
echo "<li><a href='" . SITE_URL . "/cats/list.php'>CATs</a></li>";
echo "<li><a href='" . SITE_URL . "/clientes/list.php'>Clientes</a></li>";
echo "<li><a href='" . SITE_URL . "/produtos/list.php'>Produtos</a></li>";
echo "<li><a href='" . SITE_URL . "/servicos/list.php'>Serviços</a></li>";
echo "<li><a href='" . SITE_URL . "/usuarios/list.php'>Usuários</a></li>";
echo "<li><a href='" . SITE_URL . "/config/index.php'>Configurações</a></li>";
echo "<li><a href='" . SITE_URL . "/relatorios/index.php'>Relatórios</a></li>";
echo "</ul>";
echo "</div>";

echo "</div></body></html>";
?>

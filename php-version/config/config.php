<?php
/**
 * Configurações Gerais do Sistema
 * Sistema CAT - Controle de Assistência Técnica
 */

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações do Sistema
define('SITE_NAME', 'Sistema CAT');
// Detectar URL automaticamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$scriptPath = str_replace('/config/config.php', '', $_SERVER['SCRIPT_NAME']);
$scriptPath = str_replace('/config', '', $scriptPath);
define('SITE_URL', $protocol . '://' . $host . $scriptPath);
define('BASE_PATH', __DIR__ . '/..');

// Diretórios
define('UPLOAD_DIR', BASE_PATH . '/uploads');
define('LOGS_DIR', BASE_PATH . '/logs');

// Tamanhos de upload
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Configurações de segurança
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_COST', 10);

// Configurações de sessão
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Mude para 1 se usar HTTPS

// Debug (desabilitar em produção)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Autoload de classes
spl_autoload_register(function ($class) {
    $paths = [
        BASE_PATH . '/models/' . $class . '.php',
        BASE_PATH . '/controllers/' . $class . '.php',
        BASE_PATH . '/helpers/' . $class . '.php',
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Funções helper
function redirect($url) {
    header("Location: " . $url);
    exit;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'nome' => $_SESSION['user_nome'],
            'email' => $_SESSION['user_email'],
            'nivel' => $_SESSION['user_nivel']
        ];
    }
    return null;
}

function hasPermission($permission) {
    if (!isLoggedIn()) return false;
    
    $nivel = $_SESSION['user_nivel'];
    
    // Admin tem todas as permissões
    if ($nivel === 'Administrador') return true;
    
    // Lógica de permissões por nível
    $permissions = [
        'Supervisor' => ['cat_view', 'cat_edit', 'reports', 'clientes', 'produtos'],
        'Técnico' => ['cat_view', 'cat_edit', 'clientes_view', 'produtos_view'],
        'Atendente' => ['cat_view', 'cat_create', 'clientes_view']
    ];
    
    return isset($permissions[$nivel]) && in_array($permission, $permissions[$nivel]);
}

function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

function formatDateTime($datetime, $format = 'd/m/Y H:i') {
    if (empty($datetime)) return '';
    return date($format, strtotime($datetime));
}

function formatMoney($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function flash($key, $value = null) {
    if ($value === null) {
        if (isset($_SESSION['flash'][$key])) {
            $value = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $value;
        }
        return null;
    }
    $_SESSION['flash'][$key] = $value;
}

function setSuccess($message) {
    flash('success', $message);
}

function setError($message) {
    flash('error', $message);
}

function getSuccess() {
    return flash('success');
}

function getError() {
    return flash('error');
}
?>
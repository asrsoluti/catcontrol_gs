<?php
require_once 'config/config.php';
require_once 'config/database.php';

// Se já estiver logado, redirecionar para dashboard
if (isLoggedIn()) {
    redirect('index.php');
}

// Processar login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    if (empty($email) || empty($senha)) {
        setError('Por favor, preencha todos os campos');
    } else {
        $userModel = new User();
        
        if ($userModel->login($email, $senha)) {
            setSuccess('Login realizado com sucesso!');
            redirect('index.php');
        } else {
            setError('Email ou senha inválidos');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-500 to-blue-700 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-blue-100 rounded-full mb-4">
                <i class="fas fa-tools text-blue-600 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Sistema CAT</h1>
            <p class="text-gray-600 mt-2">Controle de Assistência Técnica</p>
        </div>
        
        <?php if ($error = getError()): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-envelope mr-2"></i>Email
                </label>
                <input type="email" name="email" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                       placeholder="seu@email.com"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    <i class="fas fa-lock mr-2"></i>Senha
                </label>
                <input type="password" name="senha" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500"
                       placeholder="••••••••">
            </div>
            
            <button type="submit"
                    class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200">
                <i class="fas fa-sign-in-alt mr-2"></i>Entrar
            </button>
        </form>
        
        <div class="mt-6 text-center text-sm text-gray-600">
            <p class="mb-2">Credenciais padrão:</p>
            <p class="font-mono text-xs bg-gray-100 p-2 rounded">
                Email: admin@sistema.com<br>
                Senha: admin123
            </p>
        </div>
        
        <div class="mt-6 text-center text-xs text-gray-500">
            <p>&copy; 2024 Sistema CAT. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
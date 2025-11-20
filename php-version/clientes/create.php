<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Cliente.php';

// Verificar autenticação
if (!isLoggedIn()) {
    redirect('/login.php');
}

$clienteModel = new Cliente();

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'tipo' => $_POST['tipo'] ?? 'PF',
        'nome' => $_POST['nome'] ?? '',
        'razao_social' => $_POST['razao_social'] ?? null,
        'cpf_cnpj' => $_POST['cpf_cnpj'] ?? null,
        'rg_ie' => $_POST['rg_ie'] ?? null,
        'email' => $_POST['email'] ?? null,
        'telefone' => $_POST['telefone'] ?? null,
        'celular' => $_POST['celular'] ?? null,
        'cep' => $_POST['cep'] ?? null,
        'endereco' => $_POST['endereco'] ?? null,
        'numero' => $_POST['numero'] ?? null,
        'complemento' => $_POST['complemento'] ?? null,
        'bairro' => $_POST['bairro'] ?? null,
        'cidade' => $_POST['cidade'] ?? null,
        'estado' => $_POST['estado'] ?? null,
        'observacoes' => $_POST['observacoes'] ?? null
    ];
    
    // Validações
    $errors = [];
    if (empty($data['nome'])) {
        $errors[] = "Nome é obrigatório";
    }
    
    // Validar CPF/CNPJ se fornecido
    if (!empty($data['cpf_cnpj'])) {
        $cpfCnpj = preg_replace('/[^0-9]/', '', $data['cpf_cnpj']);
        if ($data['tipo'] === 'PF' && strlen($cpfCnpj) !== 11) {
            $errors[] = "CPF inválido";
        }
        if ($data['tipo'] === 'PJ' && strlen($cpfCnpj) !== 14) {
            $errors[] = "CNPJ inválido";
        }
    }
    
    if (empty($errors)) {
        $clienteId = $clienteModel->create($data);
        
        if ($clienteId) {
            setFlashMessage('Cliente cadastrado com sucesso!', 'success');
            redirect('/clientes/edit.php?id=' . $clienteId);
        } else {
            $errors[] = "Erro ao cadastrar cliente";
        }
    }
    
    if (!empty($errors)) {
        foreach ($errors as $error) {
            setFlashMessage($error, 'error');
        }
    }
}

$pageTitle = "Novo Cliente";
include __DIR__ . '/../includes/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-user-plus mr-2"></i>Novo Cliente
                </h1>
                <a href="<?php echo url('/clientes/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Voltar
                </a>
            </div>
            
            <form method="POST" class="space-y-6" id="clienteForm">
                <!-- Tipo de Pessoa -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Pessoa <span class="text-red-500">*</span>
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="tipo" value="PF" 
                                   <?php echo (!isset($_POST['tipo']) || $_POST['tipo'] === 'PF') ? 'checked' : ''; ?>
                                   onchange="toggleTipoPessoa()"
                                   class="mr-2">
                            <span>Pessoa Física</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="tipo" value="PJ" 
                                   <?php echo (isset($_POST['tipo']) && $_POST['tipo'] === 'PJ') ? 'checked' : ''; ?>
                                   onchange="toggleTipoPessoa()"
                                   class="mr-2">
                            <span>Pessoa Jurídica</span>
                        </label>
                    </div>
                </div>
                
                <!-- Dados Principais -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-id-card mr-2"></i>Dados Principais
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nome / Nome Fantasia <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nome" required 
                                   value="<?php echo htmlspecialchars($_POST['nome'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Nome completo ou nome fantasia">
                        </div>
                        
                        <div class="md:col-span-2" id="razao_social_group" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Razão Social
                            </label>
                            <input type="text" name="razao_social" 
                                   value="<?php echo htmlspecialchars($_POST['razao_social'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Razão social da empresa">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" id="cpf_cnpj_label">
                                CPF
                            </label>
                            <input type="text" name="cpf_cnpj" id="cpf_cnpj"
                                   value="<?php echo htmlspecialchars($_POST['cpf_cnpj'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="000.000.000-00">
                        </div>
                        
                        <div id="rg_ie_group">
                            <label class="block text-sm font-medium text-gray-700 mb-2" id="rg_ie_label">
                                RG
                            </label>
                            <input type="text" name="rg_ie" 
                                   value="<?php echo htmlspecialchars($_POST['rg_ie'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="00.000.000-0">
                        </div>
                    </div>
                </div>
                
                <!-- Contato -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-phone mr-2"></i>Contato
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Telefone
                            </label>
                            <input type="text" name="telefone" id="telefone"
                                   value="<?php echo htmlspecialchars($_POST['telefone'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="(00) 0000-0000">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Celular
                            </label>
                            <input type="text" name="celular" id="celular"
                                   value="<?php echo htmlspecialchars($_POST['celular'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="(00) 00000-0000">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <input type="email" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="email@exemplo.com">
                        </div>
                    </div>
                </div>
                
                <!-- Endereço -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-map-marker-alt mr-2"></i>Endereço
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    CEP
                                </label>
                                <input type="text" name="cep" id="cep"
                                       value="<?php echo htmlspecialchars($_POST['cep'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="00000-000"
                                       onblur="buscarCEP()">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Endereço
                                </label>
                                <input type="text" name="endereco" id="endereco"
                                       value="<?php echo htmlspecialchars($_POST['endereco'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Rua, Avenida...">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Número
                                </label>
                                <input type="text" name="numero" 
                                       value="<?php echo htmlspecialchars($_POST['numero'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Nº">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Complemento
                                </label>
                                <input type="text" name="complemento" 
                                       value="<?php echo htmlspecialchars($_POST['complemento'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Apto, Sala...">
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Bairro
                                </label>
                                <input type="text" name="bairro" id="bairro"
                                       value="<?php echo htmlspecialchars($_POST['bairro'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Bairro">
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Cidade
                                </label>
                                <input type="text" name="cidade" id="cidade"
                                       value="<?php echo htmlspecialchars($_POST['cidade'] ?? ''); ?>"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Cidade">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Estado
                                </label>
                                <select name="estado" id="estado" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Selecione...</option>
                                    <option value="AC">AC</option>
                                    <option value="AL">AL</option>
                                    <option value="AP">AP</option>
                                    <option value="AM">AM</option>
                                    <option value="BA">BA</option>
                                    <option value="CE">CE</option>
                                    <option value="DF">DF</option>
                                    <option value="ES">ES</option>
                                    <option value="GO">GO</option>
                                    <option value="MA">MA</option>
                                    <option value="MT">MT</option>
                                    <option value="MS">MS</option>
                                    <option value="MG">MG</option>
                                    <option value="PA">PA</option>
                                    <option value="PB">PB</option>
                                    <option value="PR">PR</option>
                                    <option value="PE">PE</option>
                                    <option value="PI">PI</option>
                                    <option value="RJ">RJ</option>
                                    <option value="RN">RN</option>
                                    <option value="RS">RS</option>
                                    <option value="RO">RO</option>
                                    <option value="RR">RR</option>
                                    <option value="SC">SC</option>
                                    <option value="SP">SP</option>
                                    <option value="SE">SE</option>
                                    <option value="TO">TO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Observações -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-sticky-note mr-2"></i>Observações
                    </h3>
                    <textarea name="observacoes" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Observações adicionais sobre o cliente..."><?php echo htmlspecialchars($_POST['observacoes'] ?? ''); ?></textarea>
                </div>
                
                <!-- Botões -->
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="<?php echo url('/clientes/list.php'); ?>" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition">
                        Cancelar
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                        <i class="fas fa-save mr-2"></i>Cadastrar Cliente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle entre PF e PJ
function toggleTipoPessoa() {
    const tipo = document.querySelector('input[name="tipo"]:checked').value;
    const razaoSocialGroup = document.getElementById('razao_social_group');
    const cpfCnpjLabel = document.getElementById('cpf_cnpj_label');
    const rgIeLabel = document.getElementById('rg_ie_label');
    const cpfCnpjInput = document.getElementById('cpf_cnpj');
    
    if (tipo === 'PJ') {
        razaoSocialGroup.style.display = 'block';
        cpfCnpjLabel.textContent = 'CNPJ';
        rgIeLabel.textContent = 'Inscrição Estadual';
        cpfCnpjInput.placeholder = '00.000.000/0000-00';
    } else {
        razaoSocialGroup.style.display = 'none';
        cpfCnpjLabel.textContent = 'CPF';
        rgIeLabel.textContent = 'RG';
        cpfCnpjInput.placeholder = '000.000.000-00';
    }
}

// Buscar CEP via ViaCEP
function buscarCEP() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    
    if (cep.length !== 8) {
        return;
    }
    
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            if (!data.erro) {
                document.getElementById('endereco').value = data.logradouro;
                document.getElementById('bairro').value = data.bairro;
                document.getElementById('cidade').value = data.localidade;
                document.getElementById('estado').value = data.uf;
            }
        })
        .catch(error => console.error('Erro ao buscar CEP:', error));
}

// Inicializar estado do formulário
toggleTipoPessoa();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>

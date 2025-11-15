import { Hono } from 'hono';
import { cors } from 'hono/cors';
import { logger } from 'hono/logger';
import { serve } from '@hono/node-server';
import dotenv from 'dotenv';
import path from 'path';
import { readFileSync } from 'fs';

// Importar configurações e rotas
import { testConnection } from './config/database';
import { authMiddleware } from './middleware/auth.middleware';
import authRoutes from './routes/auth.routes';
import clientesRoutes from './routes/clientes.routes';
import catRoutes from './routes/cat.routes';

// Carregar variáveis de ambiente
dotenv.config();

const app = new Hono();

// Middlewares globais
app.use('*', cors());
app.use('*', logger());

// Servir arquivos estáticos
app.get('/uploads/*', async (c) => {
  const filepath = c.req.path.replace('/uploads/', '');
  try {
    const file = readFileSync(path.join(process.cwd(), 'uploads', filepath));
    return new Response(file);
  } catch {
    return c.text('File not found', 404);
  }
});

// Rotas da API
app.route('/api/auth', authRoutes);
app.use('/api/clientes/*', authMiddleware);
app.route('/api/clientes', clientesRoutes);
app.use('/api/cat/*', authMiddleware);
app.route('/api/cat', catRoutes);

// Health check
app.get('/api/health', async (c) => {
  const dbConnected = await testConnection();
  return c.json({ 
    status: 'ok',
    database: dbConnected ? 'connected' : 'disconnected',
    timestamp: new Date().toISOString()
  });
});

// Rota principal - Interface Web
app.get('/', (c) => {
  return c.html(`
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema CAT - Controle de Assistência Técnica</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.0/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .sidebar-item:hover { background-color: rgba(255,255,255,0.1); }
        .status-badge { padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 600; }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Container Principal -->
    <div id="app" class="flex h-screen">
        
        <!-- Sidebar -->
        <div class="w-64 bg-blue-800 text-white flex flex-col">
            <div class="p-4 border-b border-blue-700">
                <h1 class="text-xl font-bold flex items-center">
                    <i class="fas fa-tools mr-2"></i>
                    Sistema CAT
                </h1>
            </div>
            
            <nav class="flex-1 p-4">
                <div class="space-y-2">
                    <a href="#" onclick="showDashboard()" class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-home mr-2"></i> Dashboard
                    </a>
                    <a href="#" onclick="showCATs()" class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-clipboard-list mr-2"></i> CATs
                    </a>
                    <a href="#" onclick="showClientes()" class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-users mr-2"></i> Clientes
                    </a>
                    <a href="#" onclick="showProdutos()" class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-box mr-2"></i> Produtos
                    </a>
                    <a href="#" onclick="showServicos()" class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-wrench mr-2"></i> Serviços
                    </a>
                    <a href="#" onclick="showRelatorios()" class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                        <i class="fas fa-chart-bar mr-2"></i> Relatórios
                    </a>
                    <div class="mt-4 pt-4 border-t border-blue-700">
                        <a href="#" onclick="showConfiguracoes()" class="sidebar-item block p-3 rounded hover:bg-blue-700 transition">
                            <i class="fas fa-cog mr-2"></i> Configurações
                        </a>
                    </div>
                </div>
            </nav>
            
            <div class="p-4 border-t border-blue-700">
                <div class="flex items-center justify-between">
                    <span class="text-sm" id="userName">Usuário</span>
                    <button onclick="logout()" class="text-red-300 hover:text-red-200">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Conteúdo Principal -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 id="pageTitle" class="text-2xl font-semibold text-gray-800">Dashboard</h2>
                    <div class="flex items-center space-x-4">
                        <button onclick="openNewCAT()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            <i class="fas fa-plus mr-2"></i>Nova CAT
                        </button>
                        <span class="text-gray-500 text-sm" id="currentDateTime"></span>
                    </div>
                </div>
            </header>
            
            <!-- Área de Conteúdo -->
            <main id="mainContent" class="flex-1 p-6 overflow-auto">
                <!-- O conteúdo será carregado dinamicamente aqui -->
            </main>
        </div>
    </div>

    <!-- Modal de Login -->
    <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-8 w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">
                <i class="fas fa-tools text-blue-600 mr-2"></i>
                Sistema CAT
            </h2>
            <form onsubmit="login(event)">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <input type="email" id="loginEmail" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500"
                           placeholder="seu@email.com">
                </div>
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Senha</label>
                    <input type="password" id="loginPassword" required
                           class="w-full px-3 py-2 border rounded focus:outline-none focus:border-blue-500"
                           placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                    <i class="fas fa-sign-in-alt mr-2"></i>Entrar
                </button>
            </form>
            <div id="loginError" class="mt-4 text-red-600 text-sm text-center hidden"></div>
            <div class="mt-4 text-center text-sm text-gray-600">
                Use: admin@sistema.com / admin123
            </div>
        </div>
    </div>

    <script>
        let authToken = localStorage.getItem('authToken');
        let currentUser = null;

        // Configurar Axios
        axios.defaults.baseURL = '/api';
        axios.interceptors.request.use(config => {
            if (authToken) {
                config.headers.Authorization = \`Bearer \${authToken}\`;
            }
            return config;
        });

        // Verificar autenticação ao carregar
        window.onload = () => {
            updateDateTime();
            setInterval(updateDateTime, 60000);
            
            if (!authToken) {
                document.getElementById('loginModal').style.display = 'flex';
                document.getElementById('app').style.display = 'none';
            } else {
                checkAuth();
            }
        };

        // Função de login
        async function login(event) {
            event.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const senha = document.getElementById('loginPassword').value;

            try {
                const response = await axios.post('/auth/login', { email, senha });
                if (response.data.success) {
                    authToken = response.data.token;
                    localStorage.setItem('authToken', authToken);
                    currentUser = response.data.user;
                    document.getElementById('userName').textContent = currentUser.nome;
                    document.getElementById('loginModal').style.display = 'none';
                    document.getElementById('app').style.display = 'flex';
                    showDashboard();
                }
            } catch (error) {
                document.getElementById('loginError').textContent = 
                    error.response?.data?.error || 'Erro ao fazer login';
                document.getElementById('loginError').classList.remove('hidden');
            }
        }

        // Verificar autenticação
        async function checkAuth() {
            try {
                const response = await axios.get('/health');
                document.getElementById('loginModal').style.display = 'none';
                document.getElementById('app').style.display = 'flex';
                showDashboard();
            } catch (error) {
                localStorage.removeItem('authToken');
                authToken = null;
                document.getElementById('loginModal').style.display = 'flex';
                document.getElementById('app').style.display = 'none';
            }
        }

        // Logout
        function logout() {
            localStorage.removeItem('authToken');
            authToken = null;
            location.reload();
        }

        // Atualizar data/hora
        function updateDateTime() {
            const now = new Date();
            const formatted = now.toLocaleDateString('pt-BR') + ' ' + 
                            now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
            document.getElementById('currentDateTime').textContent = formatted;
        }

        // Funções de navegação
        function showDashboard() {
            document.getElementById('pageTitle').textContent = 'Dashboard';
            document.getElementById('mainContent').innerHTML = \`
                <div class="fade-in">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-full">
                                    <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">CATs Abertas</p>
                                    <p class="text-2xl font-bold">12</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-full">
                                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">Finalizadas Hoje</p>
                                    <p class="text-2xl font-bold">5</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-yellow-100 rounded-full">
                                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">Aguardando</p>
                                    <p class="text-2xl font-bold">8</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-full">
                                    <i class="fas fa-users text-purple-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-600">Clientes Ativos</p>
                                    <p class="text-2xl font-bold">156</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Últimas CATs</h3>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-2">Número</th>
                                            <th class="text-left py-2">Cliente</th>
                                            <th class="text-left py-2">Abertura</th>
                                            <th class="text-left py-2">Status</th>
                                            <th class="text-left py-2">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="dashboardCATList">
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-gray-500">
                                                Carregando...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            \`;
            loadDashboardData();
        }

        async function loadDashboardData() {
            try {
                const response = await axios.get('/cat?limit=5');
                const tbody = document.getElementById('dashboardCATList');
                
                if (response.data.data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">Nenhuma CAT encontrada</td></tr>';
                    return;
                }
                
                tbody.innerHTML = response.data.data.map(cat => \`
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2">\${cat.numero_cat}</td>
                        <td class="py-2">\${cat.cliente_nome || 'N/A'}</td>
                        <td class="py-2">\${new Date(cat.data_abertura).toLocaleDateString('pt-BR')}</td>
                        <td class="py-2">
                            <span class="status-badge" style="background-color: \${cat.status_cor}22; color: \${cat.status_cor}">
                                \${cat.status_nome}
                            </span>
                        </td>
                        <td class="py-2">
                            <button class="text-blue-600 hover:text-blue-800" onclick="viewCAT(\${cat.id})">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                \`).join('');
            } catch (error) {
                console.error('Erro ao carregar dashboard:', error);
            }
        }

        function showCATs() {
            document.getElementById('pageTitle').textContent = 'CATs';
            document.getElementById('mainContent').innerHTML = '<div class="fade-in">Lista de CATs em desenvolvimento...</div>';
        }

        function showClientes() {
            document.getElementById('pageTitle').textContent = 'Clientes';
            document.getElementById('mainContent').innerHTML = '<div class="fade-in">Lista de Clientes em desenvolvimento...</div>';
        }

        function showProdutos() {
            document.getElementById('pageTitle').textContent = 'Produtos';
            document.getElementById('mainContent').innerHTML = '<div class="fade-in">Lista de Produtos em desenvolvimento...</div>';
        }

        function showServicos() {
            document.getElementById('pageTitle').textContent = 'Serviços';
            document.getElementById('mainContent').innerHTML = '<div class="fade-in">Lista de Serviços em desenvolvimento...</div>';
        }

        function showRelatorios() {
            document.getElementById('pageTitle').textContent = 'Relatórios';
            document.getElementById('mainContent').innerHTML = '<div class="fade-in">Relatórios em desenvolvimento...</div>';
        }

        function showConfiguracoes() {
            document.getElementById('pageTitle').textContent = 'Configurações';
            document.getElementById('mainContent').innerHTML = '<div class="fade-in">Configurações em desenvolvimento...</div>';
        }

        function openNewCAT() {
            alert('Função de criar nova CAT em desenvolvimento');
        }

        function viewCAT(id) {
            alert('Visualizar CAT #' + id);
        }
    </script>
</body>
</html>
  `);
});

// Iniciar servidor
const port = parseInt(process.env.PORT || '3000');

serve({
  fetch: app.fetch,
  port
}, (info) => {
  console.log(`🚀 Servidor rodando em http://localhost:${info.port}`);
  testConnection();
});
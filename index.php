<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Acesso do Paciente - Portal de Recuperação</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="text/javascript" src="https://npmcdn.com/parse/dist/parse.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Gray-100 */
        }
        .fade-in {
            animation: fadeIn 0.6s ease-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div id="loading-overlay" class="fixed inset-0 bg-f3f4f6 z-50 flex items-center justify-center">
        <svg class="animate-spin h-10 w-10 text-blue-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
    </div>

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 fade-in hidden" id="login-container">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-blue-900 mb-2">Bem-vindo(a) de volta</h1>
            <p class="text-gray-500 font-medium">Acesse seu plano de recuperação</p>
        </div>

        <form id="login-form" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-blue-900 mb-1">Usuário</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input type="text" id="username" required aria-label="Usuário" aria-required="true"
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                        placeholder="Digite seu usuário">
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-blue-900 mb-1">Senha</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input type="password" id="password" required aria-label="Senha" aria-required="true"
                        class="w-full pl-10 pr-12 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent transition-all"
                        placeholder="Digite sua senha">
                    <button type="button" id="toggle-password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-blue-900 focus:outline-none focus:text-blue-900" aria-label="Mostrar senha">
                        <svg id="eye-icon" class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="flex items-center">
                <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                <label for="remember-me" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                    Lembrar de mim
                </label>
            </div>

            <div id="error-message" class="hidden bg-red-50 border-l-4 border-red-500 text-red-700 p-4 text-sm text-center rounded-r-lg" role="alert"></div>

            <button type="submit" id="login-btn" aria-label="Entrar no sistema"
                class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-3.5 px-4 rounded-xl transition-colors flex justify-center items-center shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-blue-500/50">
                <span>Entrar no Sistema</span>
            </button>
        </form>

        <div class="mt-8 text-center border-t border-gray-100 pt-6">
            <p class="text-sm text-gray-500">Em caso de dúvidas ou perda de senha,<br>entre em contato com a clínica clínica.</p>
        </div>
    </div>

    <script>
        // Inicializar o Parse
        Parse.initialize("DbAWEvALulsxiEbPcWKAljj22OBTqi00Z4pvMimk", "3DUSnVQ6K65V1gPbnXpb8iSTDjSDgDwRYcLKifZr");
        Parse.serverURL = 'https://parseapi.back4app.com/';

        document.addEventListener("DOMContentLoaded", () => {
            const overlay = document.getElementById('loading-overlay');
            const container = document.getElementById('login-container');

            // Verificar se já está logado
            const currentUser = Parse.User.current();
            if (currentUser) {
                window.location.href = 'dashboard.php';
            } else {
                // Esconder overlay e mostrar o login
                setTimeout(() => {
                    overlay.classList.add('hidden');
                    container.classList.remove('hidden');
                }, 300);
            }

            // Restore username if remembered
            const rememberedUsername = localStorage.getItem('remembered_username');
            if(rememberedUsername) {
                document.getElementById('username').value = rememberedUsername;
                document.getElementById('remember-me').checked = true;
            }
        });

        // Toggle Password Visibility
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if(type === 'text') {
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;
            } else {
                eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
            }
        });

        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const rememberMe = document.getElementById('remember-me').checked;

            const usernameValue = usernameInput.value.trim();
            const passwordValue = passwordInput.value;

            const errorMessage = document.getElementById('error-message');
            const loginBtn = document.getElementById('login-btn');

            // UI Feedback
            usernameInput.classList.remove('border-red-500');
            passwordInput.classList.remove('border-red-500');

            if(!usernameValue || !passwordValue) {
                if(!usernameValue) usernameInput.classList.add('border-red-500');
                if(!passwordValue) passwordInput.classList.add('border-red-500');
                errorMessage.textContent = "Por favor, preencha todos os campos.";
                errorMessage.classList.remove('hidden');
                return;
            }

            // Loading state
            loginBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Autenticando...';
            loginBtn.disabled = true;
            loginBtn.classList.add('opacity-75', 'cursor-not-allowed');
            errorMessage.classList.add('hidden');

            try {
                const user = await Parse.User.logIn(usernameValue, passwordValue);

                if (rememberMe) {
                    localStorage.setItem('remembered_username', usernameValue);
                } else {
                    localStorage.removeItem('remembered_username');
                }

                window.location.href = 'dashboard.php';
            } catch (error) {
                errorMessage.textContent = "Usuário ou senha incorretos. Tente novamente.";
                errorMessage.classList.remove('hidden');
                usernameInput.classList.add('border-red-500');
                passwordInput.classList.add('border-red-500');

                // Reset button
                loginBtn.innerHTML = '<span>Entrar no Sistema</span>';
                loginBtn.disabled = false;
                loginBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            }
        });
    </script>
</body>
</html>
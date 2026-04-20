<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso do Paciente - Portal de Recuperação</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="text/javascript" src="https://npmcdn.com/parse/dist/parse.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Gray-100 */
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-blue-900 mb-2">Bem-vindo(a) de volta</h1>
            <p class="text-gray-500">Acesse seu plano de recuperação</p>
        </div>

        <form id="login-form" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-medium text-blue-900 mb-1">Usuário</label>
                <input type="text" id="username" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-transparent transition-all"
                    placeholder="Digite seu usuário">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-blue-900 mb-1">Senha</label>
                <input type="password" id="password" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-900 focus:border-transparent transition-all"
                    placeholder="Digite sua senha">
            </div>

            <div id="error-message" class="hidden text-red-500 text-sm text-center"></div>

            <button type="submit" id="login-btn"
                class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex justify-center items-center">
                <span>Entrar</span>
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">Em caso de dúvidas, entre em contato com a clínica.</p>
        </div>
    </div>

    <script>
        // Inicializar o Parse
        Parse.initialize("DbAWEvALulsxiEbPcWKAljj22OBTqi00Z4pvMimk", "3DUSnVQ6K65V1gPbnXpb8iSTDjSDgDwRYcLKifZr");
        Parse.serverURL = 'https://parseapi.back4app.com/';

        // Verificar se já está logado
        const currentUser = Parse.User.current();
        if (currentUser) {
            window.location.href = 'dashboard.php';
        }

        document.getElementById('login-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const usernameValue = document.getElementById('username').value;
            const passwordValue = document.getElementById('password').value;
            const errorMessage = document.getElementById('error-message');
            const loginBtn = document.getElementById('login-btn');

            // Loading state
            loginBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Entrando...';
            loginBtn.disabled = true;
            errorMessage.classList.add('hidden');

            try {
                const user = await Parse.User.logIn(usernameValue, passwordValue);
                window.location.href = 'dashboard.php';
            } catch (error) {
                errorMessage.textContent = "Usuário ou senha incorretos.";
                errorMessage.classList.remove('hidden');

                // Reset button
                loginBtn.innerHTML = '<span>Entrar</span>';
                loginBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
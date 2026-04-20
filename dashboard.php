<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Paciente - Pós-operatório</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="text/javascript" src="https://npmcdn.com/parse/dist/parse.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Gray-100 */
        }

        .toggle-checkbox:checked {
            right: 0;
            border-color: #22c55e;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #22c55e;
        }
    </style>
</head>
<body class="pb-20"> <!-- Padding bottom for potential mobile navigation if added later -->

    <!-- Header -->
    <header class="bg-blue-900 text-white p-6 shadow-md rounded-b-3xl">
        <div class="flex justify-between items-center mb-4">
            <div>
                <h1 class="text-xl font-bold">Olá, <span id="user-name">Paciente</span>!</h1>
                <p class="text-blue-200 text-sm">Sua recuperação está indo muito bem.</p>
            </div>
            <button id="logout-btn" class="text-blue-200 hover:text-white transition-colors" aria-label="Sair">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
            </button>
        </div>

        <!-- Progress Bar -->
        <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm">
            <div class="flex justify-between text-sm mb-2">
                <span class="font-medium">Progresso da Recuperação</span>
                <span class="font-bold text-green-400">Dia 4 de 15</span>
            </div>
            <div class="w-full bg-blue-950 rounded-full h-2.5">
                <div class="bg-green-500 h-2.5 rounded-full" style="width: 26.6%"></div>
            </div>
        </div>
    </header>

    <main class="p-4 max-w-lg mx-auto space-y-6 mt-4">

        <!-- Daily Checklist -->
        <section>
            <h2 class="text-lg font-bold text-blue-900 mb-3 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
                Tarefas de Hoje
            </h2>

            <div class="space-y-3">
                <!-- Task 1 -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center transition-all duration-300 hover:shadow-md" id="task-1-card">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600" id="task-1-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800" id="task-1-title">Trocar Curativo</p>
                            <p class="text-xs text-gray-500">Após o banho</p>
                        </div>
                    </div>
                    <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" name="toggle" id="task-1-toggle" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300" onclick="toggleTask('task-1')"/>
                        <label for="task-1-toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></label>
                    </div>
                </div>

                <!-- Task 2 -->
                <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex justify-between items-center transition-all duration-300 hover:shadow-md" id="task-2-card">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600" id="task-2-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800" id="task-2-title">Caminhada Leve</p>
                            <p class="text-xs text-gray-500">10 minutos na sala</p>
                        </div>
                    </div>
                    <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in">
                        <input type="checkbox" name="toggle" id="task-2-toggle" class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300" onclick="toggleTask('task-2')"/>
                        <label for="task-2-toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></label>
                    </div>
                </div>
            </div>
        </section>

        <!-- Medication Card -->
        <section>
            <h2 class="text-lg font-bold text-blue-900 mb-3 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
                Medicação
            </h2>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-gray-800">Paracetamol 500mg</h3>
                        <p class="text-sm text-gray-500">Tomar com água</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-semibold">08:00</span>
                        <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-semibold mt-1">20:00</span>
                    </div>
                </div>
                <div class="bg-gray-50 p-3 flex justify-between gap-3">
                    <button class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg text-sm font-semibold transition-colors flex justify-center items-center" onclick="confirmMeds(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Tomei as 08:00
                    </button>
                    <button class="flex-1 bg-white border border-blue-900 text-blue-900 hover:bg-blue-50 py-2 rounded-lg text-sm font-semibold transition-colors">
                        Próximo: 20:00
                    </button>
                </div>
            </div>
        </section>

        <!-- Warning Signs Section -->
        <section>
            <div class="bg-red-50 rounded-xl p-5 border border-red-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-4 opacity-10 text-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-red-800 mb-2 flex items-center relative z-10">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Sinais de Alerta
                </h2>
                <p class="text-sm text-red-700 mb-3 relative z-10">Ligue para seu médico imediatamente se apresentar:</p>
                <ul class="text-sm text-red-900 space-y-2 list-disc list-inside relative z-10 font-medium">
                    <li>Febre acima de 38°C</li>
                    <li>Dor intensa que não melhora com remédios</li>
                    <li>Sangramento excessivo no curativo</li>
                    <li>Inchaço anormal na região operada</li>
                </ul>
                <a href="tel:0800123456" class="mt-4 block w-full bg-red-600 hover:bg-red-700 text-white text-center font-bold py-3 rounded-lg transition-colors shadow-sm relative z-10 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    Ligar para o Médico
                </a>
            </div>
        </section>

    </main>

    <script>
        // Inicializar o Parse
        Parse.initialize("DbAWEvALulsxiEbPcWKAljj22OBTqi00Z4pvMimk", "3DUSnVQ6K65V1gPbnXpb8iSTDjSDgDwRYcLKifZr");
        Parse.serverURL = 'https://parseapi.back4app.com/';

        // Verificar sessão
        const currentUser = Parse.User.current();
        if (!currentUser) {
            window.location.href = 'index.php';
        } else {
            // Pode preencher o nome do usuário se houver no banco (ex: currentUser.get('name') ou 'username')
            const username = currentUser.get('username');
            document.getElementById('user-name').textContent = username.charAt(0).toUpperCase() + username.slice(1);
        }

        // Função de Logout
        document.getElementById('logout-btn').addEventListener('click', async () => {
            await Parse.User.logOut();
            window.location.href = 'index.php';
        });

        // Função para alternar visual das tarefas completadas
        function toggleTask(taskId) {
            const checkbox = document.getElementById(taskId + '-toggle');
            const card = document.getElementById(taskId + '-card');
            const icon = document.getElementById(taskId + '-icon');
            const title = document.getElementById(taskId + '-title');

            if (checkbox.checked) {
                card.classList.add('border-green-200', 'bg-green-50');
                icon.classList.remove('bg-blue-50', 'text-blue-600');
                icon.classList.add('bg-green-100', 'text-green-600');
                title.classList.add('line-through', 'text-gray-400');
            } else {
                card.classList.remove('border-green-200', 'bg-green-50');
                icon.classList.remove('bg-green-100', 'text-green-600');
                icon.classList.add('bg-blue-50', 'text-blue-600');
                title.classList.remove('line-through', 'text-gray-400');
            }
        }

        // Função para simular confirmação de remédio
        function confirmMeds(btn) {
            btn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Confirmado!
            `;
            btn.classList.remove('bg-green-500', 'hover:bg-green-600');
            btn.classList.add('bg-green-700', 'cursor-default');
            btn.disabled = true;
        }
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1e3a8a">
    <title>Dashboard do Paciente - Pós-operatório</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <script type="text/javascript" src="https://npmcdn.com/parse/dist/parse.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }

        .toggle-checkbox:checked { right: 0; border-color: #22c55e; }
        .toggle-checkbox:checked + .toggle-label { background-color: #22c55e; }

        .fade-in { animation: fadeIn 0.5s ease-out forwards; opacity: 0; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; }

        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
        .toast-enter { animation: slideInRight 0.3s ease-out forwards; }
        .toast-exit { animation: fadeOut 0.3s ease-out forwards; }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900 transition-colors duration-300 pb-24 md:pb-8 hidden" id="app-body">

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <!-- Modais -->
    <!-- Modal Logout -->
    <div id="logout-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-sm w-full shadow-2xl">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Sair do sistema</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-6">Tem certeza que deseja sair da sua conta?</p>
            <div class="flex gap-3">
                <button id="cancel-logout-btn" class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-semibold py-3 rounded-xl transition-colors">Cancelar</button>
                <button id="confirm-logout-btn" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition-colors">Sair</button>
            </div>
        </div>
    </div>

    <!-- Modal Editar Perfil -->
    <div id="profile-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center px-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 max-w-md w-full shadow-2xl relative">
            <button id="close-profile-btn" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Editar Perfil</h3>
            <form id="profile-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Completo</label>
                    <input type="text" id="profile-name" class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone de Contato</label>
                    <input type="tel" id="profile-phone" class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alergias Conhecidas</label>
                    <input type="text" id="profile-allergies" placeholder="Ex: Dipirona, Iodo" class="w-full px-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600">
                </div>
                <button type="submit" id="save-profile-btn" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors mt-2">Salvar Alterações</button>
            </form>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-blue-900 dark:bg-gray-800 text-white shadow-md md:rounded-b-none rounded-b-3xl relative z-10 transition-colors">
        <div class="max-w-6xl mx-auto p-6">

            <!-- Top Nav -->
            <div class="flex justify-end items-center gap-3 mb-2">
                <!-- Theme Toggle -->
                <button id="theme-toggle" class="p-2 rounded-full bg-blue-800/50 hover:bg-blue-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-blue-100 transition-colors" aria-label="Alternar Tema">
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                </button>
                <button id="edit-profile-btn" class="p-2 rounded-full bg-blue-800/50 hover:bg-blue-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-blue-100 transition-colors" title="Editar Perfil">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                </button>
                <button id="logout-btn" class="p-2 rounded-full bg-red-600/80 hover:bg-red-600 text-white transition-colors" title="Sair">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                </button>
            </div>

            <!-- Profile Info -->
            <div class="flex items-center gap-4 mb-6">
                <div id="user-avatar" class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-3xl font-bold border-2 border-white/30 shrink-0"></div>
                <div class="flex-1">
                    <p class="text-blue-200 text-sm font-medium" id="greeting-time">Carregando...</p>
                    <h1 class="text-2xl font-bold"><span id="user-name">Paciente</span></h1>

                    <div class="flex flex-wrap gap-2 mt-2">
                        <span class="bg-blue-800 dark:bg-gray-700 text-blue-100 text-xs px-2 py-1 rounded-md flex items-center" id="doctor-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Dr(a). Responsável
                        </span>
                        <span class="bg-green-600/80 text-white text-xs px-2 py-1 rounded-md flex items-center" id="surgery-info">
                            Cirurgia
                        </span>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white/10 dark:bg-gray-700/50 rounded-2xl p-5 backdrop-blur-md shadow-sm border border-white/10 dark:border-gray-600">
                <div class="flex justify-between text-sm md:text-base mb-3 items-end">
                    <span class="font-medium flex items-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        Progresso da Recuperação
                    </span>
                    <span id="progress-text" class="font-bold text-green-400 bg-green-400/10 px-2 py-0.5 rounded-md">Calculando...</span>
                </div>
                <div class="w-full bg-blue-950/50 dark:bg-gray-800 rounded-full h-3 overflow-hidden">
                    <div id="progress-bar-fill" class="bg-gradient-to-r from-green-400 to-green-500 h-3 rounded-full transition-all duration-1000 ease-out" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </header>

    <main class="p-4 max-w-6xl mx-auto mt-2 md:mt-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

            <!-- Left Column: Tasks & Messages -->
            <div class="md:col-span-7 lg:col-span-8 space-y-6">

                <!-- Recados da Equipe Médica -->
                <section class="fade-in">
                    <h2 class="text-xl font-bold text-blue-900 dark:text-blue-400 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                        Recados da Equipe
                    </h2>
                    <div id="messages-container" class="space-y-3">
                        <!-- Renderizado via JS -->
                    </div>
                </section>

                <!-- Daily Checklist -->
                <section class="fade-in delay-100">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-blue-900 dark:text-blue-400 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600 dark:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                            Tarefas de Hoje
                        </h2>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400" id="task-counter">0/0</span>
                    </div>

                    <div id="all-tasks-done" class="hidden mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 flex items-start gap-3">
                        <div class="bg-green-100 dark:bg-green-800/50 p-2 rounded-full text-green-600 dark:text-green-400 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-green-800 dark:text-green-400">Excelente!</h4>
                            <p class="text-sm text-green-700 dark:text-green-300">Você concluiu todas as tarefas para a sua recuperação hoje.</p>
                        </div>
                    </div>

                    <div id="tasks-container" class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                        <!-- Renderizado via JS -->
                    </div>
                </section>
            </div>

            <!-- Right Column: Medication & Warnings -->
            <div class="md:col-span-5 lg:col-span-4 space-y-6">
                <!-- Medication Card -->
                <section class="fade-in delay-200">
                    <h2 class="text-xl font-bold text-blue-900 dark:text-blue-400 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600 dark:text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
                        Medicação
                    </h2>

                    <div id="meds-container" class="space-y-4">
                        <!-- Renderizado via JS -->
                    </div>
                </section>

                <!-- Warning Signs Section -->
                <section class="fade-in delay-300">
                    <div class="bg-gradient-to-br from-red-50 to-white dark:from-red-900/20 dark:to-gray-800 rounded-2xl p-6 border border-red-100 dark:border-red-800/50 shadow-sm relative overflow-hidden h-full group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 dark:opacity-10 text-red-600 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform duration-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                        <h2 class="text-xl font-bold text-red-800 dark:text-red-400 mb-2 flex items-center relative z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            Sinais de Alerta
                        </h2>
                        <p class="text-sm md:text-base text-red-700 dark:text-red-300 mb-4 relative z-10 font-medium">Contate seu médico se apresentar:</p>
                        <ul class="text-sm md:text-base text-red-900 dark:text-red-200 space-y-2 list-disc list-inside relative z-10" id="warning-list">
                            <li>Febre acima de 38°C</li>
                            <li>Dor intensa contínua</li>
                            <li>Sangramento excessivo no curativo</li>
                        </ul>

                        <div class="mt-6 space-y-3 relative z-10">
                            <a href="tel:0800123456" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center font-bold py-3.5 rounded-xl transition-colors shadow-md flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                Ligar para o Médico
                            </a>
                            <a href="https://wa.me/5511999999999" target="_blank" class="block w-full bg-white dark:bg-gray-800 border border-green-500 text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-gray-700 text-center font-bold py-3 rounded-xl transition-colors shadow-sm flex items-center justify-center">
                                WhatsApp Clínica
                            </a>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- JS Logic -->
    <script>
        // Configuração Inicial do Parse
        Parse.initialize("DbAWEvALulsxiEbPcWKAljj22OBTqi00Z4pvMimk", "3DUSnVQ6K65V1gPbnXpb8iSTDjSDgDwRYcLKifZr");
        Parse.serverURL = 'https://parseapi.back4app.com/';

        // Mock Data de Contexto Médico
        const medicalContextData = {
            doctorName: "Dr. Roberto Silva",
            surgeryType: "Artroscopia de Joelho",
            progress: { day: 4, total: 15 },
            messages: [
                { date: "Hoje, 09:00", text: "Sua evolução está ótima. Lembre-se de não forçar a pisada." },
                { date: "Ontem, 18:30", text: "Mantenha o gelo 3x ao dia conforme orientado." }
            ],
            tasks: [
                { id: "task-1", title: "Sessão de Fisioterapia (Casa)", desc: "15 minutos de flexão leve", done: false },
                { id: "task-2", title: "Compressa de Gelo", desc: "20 minutos no joelho operado", done: false },
                { id: "task-3", title: "Caminhada de Apoio", desc: "5 minutos com muletas", done: false }
            ],
            medications: [
                { id: "med-1", name: "Paracetamol 500mg", instruction: "Para dor", times: ["08:00", "20:00"], taken: false },
                { id: "med-2", name: "Cefalexina 500mg", instruction: "Antibiótico", times: ["12:00"], taken: false }
            ]
        };

        let currentUserContext = null;

        document.addEventListener("DOMContentLoaded", async () => {
            // Theme Toggle Logic
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            function updateIcons() {
                if (document.documentElement.classList.contains('dark')) {
                    themeToggleLightIcon.classList.remove('hidden');
                    themeToggleDarkIcon.classList.add('hidden');
                } else {
                    themeToggleLightIcon.classList.add('hidden');
                    themeToggleDarkIcon.classList.remove('hidden');
                }
            }
            updateIcons();

            themeToggleBtn.addEventListener('click', () => {
                document.documentElement.classList.toggle('dark');
                localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                updateIcons();
            });

            // Autenticação
            const currentUser = Parse.User.current();
            if (!currentUser) {
                window.location.href = 'index.php';
                return;
            }
            document.getElementById('app-body').classList.remove('hidden');

            // Header Info
            const username = currentUser.get('username') || 'Paciente';
            const firstName = username.split(' ')[0];
            document.getElementById('user-name').textContent = firstName;
            document.getElementById('user-avatar').textContent = firstName.charAt(0).toUpperCase();

            // Populate Modal
            document.getElementById('profile-name').value = username;
            document.getElementById('profile-phone').value = currentUser.get('phone') || '';
            document.getElementById('profile-allergies').value = currentUser.get('allergies') || '';

            // Greeting
            const hour = new Date().getHours();
            let greeting = "Boa noite";
            if(hour >= 5 && hour < 12) greeting = "Bom dia";
            else if(hour >= 12 && hour < 18) greeting = "Boa tarde";
            document.getElementById('greeting-time').textContent = greeting;

            // Load Context Data
            await loadContextData();
        });

        // Modais Logic
        const logoutBtn = document.getElementById('logout-btn');
        const logoutModal = document.getElementById('logout-modal');
        document.getElementById('cancel-logout-btn').addEventListener('click', () => logoutModal.classList.add('hidden'));
        logoutBtn.addEventListener('click', () => logoutModal.classList.remove('hidden'));
        document.getElementById('confirm-logout-btn').addEventListener('click', async () => {
            await Parse.User.logOut();
            window.location.href = 'index.php';
        });

        const profileBtn = document.getElementById('edit-profile-btn');
        const profileModal = document.getElementById('profile-modal');
        document.getElementById('close-profile-btn').addEventListener('click', () => profileModal.classList.add('hidden'));
        profileBtn.addEventListener('click', () => profileModal.classList.remove('hidden'));

        document.getElementById('profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('save-profile-btn');
            btn.innerHTML = 'Salvando...';

            const user = Parse.User.current();
            user.set('username', document.getElementById('profile-name').value);
            user.set('phone', document.getElementById('profile-phone').value);
            user.set('allergies', document.getElementById('profile-allergies').value);

            try {
                await user.save();
                showToast('Perfil atualizado com sucesso!', 'success');
                document.getElementById('user-name').textContent = user.get('username').split(' ')[0];
                profileModal.classList.add('hidden');
            } catch (error) {
                showToast('Erro ao salvar perfil.', 'error');
            } finally {
                btn.innerHTML = 'Salvar Alterações';
            }
        });

        async function loadContextData() {
            // Em um app real, buscaríamos do Parse.
            // Para o escopo solicitado, usamos o Mock Context contextualizado.
            const data = medicalContextData;

            // Médico e Cirurgia
            document.getElementById('doctor-info').innerHTML = `<svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg> ${data.doctorName}`;
            document.getElementById('surgery-info').textContent = data.surgeryType;

            // Progresso
            document.getElementById('progress-text').textContent = `Dia ${data.progress.day} de ${data.progress.total}`;
            setTimeout(() => {
                document.getElementById('progress-bar-fill').style.width = `${(data.progress.day / data.progress.total) * 100}%`;
            }, 300);

            // Recados
            const messagesHtml = data.messages.map(m => `
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-xl">
                    <p class="text-xs text-yellow-600 dark:text-yellow-500 font-bold mb-1">${m.date}</p>
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">${m.text}</p>
                </div>
            `).join('');
            document.getElementById('messages-container').innerHTML = messagesHtml;

            // Tarefas
            const tasksHtml = data.tasks.map(t => `
                <label class="bg-white dark:bg-gray-800 p-5 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 flex justify-between items-center transition-all duration-300 cursor-pointer" id="${t.id}-card">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-blue-50 dark:bg-gray-700 flex items-center justify-center text-blue-600 dark:text-blue-400 transition-colors" id="${t.id}-icon">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800 dark:text-white text-lg transition-all" id="${t.id}-title">${t.title}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">${t.desc}</p>
                        </div>
                    </div>
                    <div class="relative inline-block w-12 mr-2 align-middle select-none">
                        <input type="checkbox" id="${t.id}-toggle" class="task-checkbox toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer" />
                        <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 dark:bg-gray-600 cursor-pointer transition-colors duration-300"></div>
                    </div>
                </label>
            `).join('');
            document.getElementById('tasks-container').innerHTML = tasksHtml;
            setupTasksLogic();

            // Medicações
            const medsHtml = data.medications.map(m => `
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="p-4 border-b border-gray-50 dark:border-gray-700 flex justify-between items-center bg-gradient-to-r from-white dark:from-gray-800 to-blue-50/30 dark:to-gray-700/30">
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">${m.name}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">${m.instruction}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            ${m.times.map((time, idx) => `<span class="inline-block ${idx === 0 ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400'} text-xs px-2 py-1 rounded-full font-bold">${time}</span>`).join('')}
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/50 p-3">
                        <button class="med-btn w-full bg-green-500 hover:bg-green-600 text-white py-2.5 rounded-xl text-sm font-bold transition-colors flex justify-center items-center">
                            <svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            Confirmar (${m.times[0]})
                        </button>
                    </div>
                </div>
            `).join('');
            document.getElementById('meds-container').innerHTML = medsHtml;
            setupMedsLogic();
        }

        function setupTasksLogic() {
            const checkboxes = document.querySelectorAll('.task-checkbox');
            checkboxes.forEach(box => {
                box.addEventListener('change', function() {
                    const taskId = this.id.replace('-toggle', '');
                    const card = document.getElementById(taskId + '-card');
                    const icon = document.getElementById(taskId + '-icon');
                    const title = document.getElementById(taskId + '-title');

                    if (this.checked) {
                        card.classList.add('border-green-200', 'bg-green-50', 'dark:bg-green-900/20', 'dark:border-green-800');
                        icon.classList.remove('bg-blue-50', 'text-blue-600', 'dark:bg-gray-700');
                        icon.classList.add('bg-green-100', 'text-green-600', 'dark:bg-green-800', 'dark:text-green-300');
                        title.classList.add('line-through', 'text-gray-400', 'dark:text-gray-500');
                        showToast("Tarefa concluída!", 'success');
                    } else {
                        card.classList.remove('border-green-200', 'bg-green-50', 'dark:bg-green-900/20', 'dark:border-green-800');
                        icon.classList.remove('bg-green-100', 'text-green-600', 'dark:bg-green-800', 'dark:text-green-300');
                        icon.classList.add('bg-blue-50', 'text-blue-600', 'dark:bg-gray-700');
                        title.classList.remove('line-through', 'text-gray-400', 'dark:text-gray-500');
                    }

                    const total = checkboxes.length;
                    const checked = document.querySelectorAll('.task-checkbox:checked').length;
                    document.getElementById('task-counter').textContent = `${checked}/${total}`;

                    if(checked === total && total > 0) {
                        document.getElementById('all-tasks-done').classList.remove('hidden');
                        confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 }});
                    } else {
                        document.getElementById('all-tasks-done').classList.add('hidden');
                    }
                });
            });
            document.getElementById('task-counter').textContent = `0/${checkboxes.length}`;
        }

        function setupMedsLogic() {
            document.querySelectorAll('.med-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    if(this.disabled) return;
                    this.innerHTML = `<svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Confirmado!`;
                    this.classList.remove('bg-green-500', 'hover:bg-green-600');
                    this.classList.add('bg-green-700', 'opacity-80', 'cursor-default');
                    this.disabled = true;
                    showToast("Medicamento registrado!", 'success');
                });
            });
        }

        function showToast(message, type) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            const bgClass = type === 'success' ? 'bg-gray-800 dark:bg-gray-700' : 'bg-red-600';
            toast.className = `toast-enter flex items-center p-4 rounded-xl shadow-lg text-white text-sm font-medium ${bgClass}`;
            toast.innerHTML = message;
            container.appendChild(toast);
            setTimeout(() => {
                toast.classList.replace('toast-enter', 'toast-exit');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
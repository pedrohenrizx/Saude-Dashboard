<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1e3a8a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>Dashboard do Paciente - Pós-operatório</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="text/javascript" src="https://npmcdn.com/parse/dist/parse.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f3f4f6; /* Gray-100 */
            scroll-behavior: smooth;
        }

        .toggle-checkbox:checked {
            right: 0;
            border-color: #22c55e;
        }
        .toggle-checkbox:checked + .toggle-label {
            background-color: #22c55e;
        }

        .fade-in { animation: fadeIn 0.5s ease-out forwards; opacity: 0; }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #9ca3af; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #6b7280; }

        /* Toast Animation */
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        .toast-enter { animation: slideInRight 0.3s ease-out forwards; }
        .toast-exit { animation: fadeOut 0.3s ease-out forwards; }
    </style>
</head>
<body class="pb-24 md:pb-8 hidden" id="app-body"> <!-- Padding bottom for mobile FAB -->

    <!-- Toast Notifications Container -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <!-- Modal de Logout -->
    <div id="logout-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center px-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="bg-white rounded-2xl p-6 max-w-sm w-full shadow-2xl transform transition-all">
            <h3 class="text-xl font-bold text-gray-900 mb-2" id="modal-title">Sair do sistema</h3>
            <p class="text-gray-500 mb-6">Tem certeza que deseja sair da sua conta?</p>
            <div class="flex gap-3">
                <button id="cancel-logout-btn" class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-3 rounded-xl transition-colors">Cancelar</button>
                <button id="confirm-logout-btn" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 rounded-xl transition-colors">Sair</button>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="bg-blue-900 text-white shadow-md md:rounded-b-none rounded-b-3xl relative z-10">
        <div class="max-w-6xl mx-auto p-6">
            <div class="flex justify-between items-start md:items-center mb-6 flex-col md:flex-row gap-4">
                <div class="flex items-center gap-4">
                    <div id="user-avatar" class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold border-2 border-white/30" aria-hidden="true">
                        P
                    </div>
                    <div>
                        <p class="text-blue-200 text-sm font-medium" id="greeting-time">Carregando...</p>
                        <h1 class="text-2xl font-bold"><span id="user-name">Paciente</span></h1>
                        <p class="text-blue-200 text-xs md:text-sm mt-0.5 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span id="current-date">Data</span>
                        </p>
                    </div>
                </div>
                <button id="logout-btn" class="flex items-center text-blue-100 hover:text-white transition-colors bg-blue-800/40 hover:bg-blue-800 px-4 py-2.5 rounded-xl focus:outline-none focus:ring-2 focus:ring-white/50" aria-label="Sair da conta">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span class="font-medium">Sair</span>
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="bg-white/10 rounded-2xl p-5 backdrop-blur-md shadow-sm border border-white/10">
                <div class="flex justify-between text-sm md:text-base mb-3 items-end">
                    <span class="font-medium flex items-center text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                        Progresso da Recuperação
                    </span>
                    <span class="font-bold text-green-400 bg-green-400/10 px-2 py-0.5 rounded-md">Dia 4 de 15</span>
                </div>
                <div class="w-full bg-blue-950/50 rounded-full h-3 overflow-hidden">
                    <div id="progress-bar-fill" class="bg-gradient-to-r from-green-400 to-green-500 h-3 rounded-full transition-all duration-1000 ease-out" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </header>

    <main class="p-4 max-w-6xl mx-auto mt-2 md:mt-6">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">

            <!-- Left Column: Daily Checklist -->
            <div class="md:col-span-7 lg:col-span-8 space-y-6 fade-in delay-100">
                <section aria-labelledby="tasks-heading">
                    <div class="flex justify-between items-center mb-4">
                        <h2 id="tasks-heading" class="text-xl font-bold text-blue-900 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            Tarefas de Hoje
                        </h2>
                        <span class="text-sm font-medium text-gray-500" id="task-counter">0/2 concluídas</span>
                    </div>

                    <!-- Mensagem de Sucesso (escondida por padrão) -->
                    <div id="all-tasks-done" class="hidden mb-4 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                        <div class="bg-green-100 p-2 rounded-full text-green-600 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-green-800">Parabéns!</h4>
                            <p class="text-sm text-green-700">Você concluiu todas as tarefas para a sua recuperação hoje.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                        <!-- Task 1 -->
                        <label class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center transition-all duration-300 hover:shadow-md cursor-pointer group focus-within:ring-2 focus-within:ring-blue-500" id="task-1-card">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0 transition-colors" id="task-1-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-lg transition-all" id="task-1-title">Trocar Curativo</p>
                                    <p class="text-sm text-gray-500">Após o banho</p>
                                </div>
                            </div>
                            <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in flex-shrink-0">
                                <input type="checkbox" name="toggle" id="task-1-toggle" class="task-checkbox toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300 focus:outline-none" aria-label="Marcar tarefa Trocar Curativo como concluída" />
                                <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></div>
                            </div>
                        </label>

                        <!-- Task 2 -->
                        <label class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex justify-between items-center transition-all duration-300 hover:shadow-md cursor-pointer group focus-within:ring-2 focus-within:ring-blue-500" id="task-2-card">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 flex-shrink-0 transition-colors" id="task-2-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-lg transition-all" id="task-2-title">Caminhada Leve</p>
                                    <p class="text-sm text-gray-500">10 minutos na sala</p>
                                </div>
                            </div>
                            <div class="relative inline-block w-12 mr-2 align-middle select-none transition duration-200 ease-in flex-shrink-0">
                                <input type="checkbox" name="toggle" id="task-2-toggle" class="task-checkbox toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer transition-all duration-300 focus:outline-none" aria-label="Marcar tarefa Caminhada Leve como concluída"/>
                                <div class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer transition-colors duration-300"></div>
                            </div>
                        </label>
                    </div>
                </section>
            </div>

            <!-- Right Column: Medication & Warnings -->
            <div class="md:col-span-5 lg:col-span-4 space-y-6">
                <!-- Medication Card -->
                <section aria-labelledby="meds-heading" class="fade-in delay-200">
                    <h2 id="meds-heading" class="text-xl font-bold text-blue-900 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                        </svg>
                        Medicação
                    </h2>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="p-5 border-b border-gray-50 flex justify-between items-center bg-gradient-to-r from-white to-blue-50/30">
                            <div>
                                <h3 class="font-bold text-gray-800 text-lg">Paracetamol 500mg</h3>
                                <p class="text-sm text-gray-500">Tomar com água</p>
                            </div>
                            <div class="flex flex-col items-end gap-1">
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2.5 py-1 rounded-full font-bold shadow-sm" aria-label="Horário programado: 08:00">08:00</span>
                                <span class="inline-block bg-gray-100 text-gray-500 text-xs px-2.5 py-1 rounded-full font-semibold" aria-label="Próximo horário: 20:00">20:00</span>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 flex flex-col sm:flex-row gap-3">
                            <button id="med-btn" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-3 sm:py-2.5 rounded-xl text-sm font-bold transition-colors flex justify-center items-center shadow-sm focus:outline-none focus:ring-4 focus:ring-green-500/30" aria-label="Confirmar que tomou Paracetamol às 08:00">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Tomei as 08:00
                            </button>
                        </div>
                    </div>
                </section>

                <!-- Warning Signs Section -->
                <section aria-labelledby="warning-heading" class="fade-in delay-300">
                    <div class="bg-gradient-to-br from-red-50 to-white rounded-2xl p-6 border border-red-100 shadow-sm relative overflow-hidden h-full group">
                        <div class="absolute top-0 right-0 p-4 opacity-5 text-red-600 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform duration-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h2 id="warning-heading" class="text-xl font-bold text-red-800 mb-2 flex items-center relative z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Sinais de Alerta
                        </h2>
                        <p class="text-sm md:text-base text-red-700 mb-4 relative z-10 font-medium">Ligue para seu médico imediatamente se apresentar:</p>
                        <ul class="text-sm md:text-base text-red-900 space-y-2 list-disc list-inside relative z-10">
                            <li>Febre acima de 38°C</li>
                            <li>Dor intensa contínua</li>
                            <li>Sangramento excessivo no curativo</li>
                            <li>Inchaço anormal na região operada</li>
                        </ul>

                        <div class="mt-6 space-y-3 relative z-10">
                            <a href="tel:0800123456" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center font-bold py-3.5 rounded-xl transition-colors shadow-md hover:shadow-lg flex items-center justify-center text-lg focus:outline-none focus:ring-4 focus:ring-red-500/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                Ligar para o Médico
                            </a>
                            <a href="https://wa.me/5511999999999" target="_blank" rel="noopener noreferrer" class="block w-full bg-white border border-green-500 text-green-600 hover:bg-green-50 text-center font-bold py-3 rounded-xl transition-colors shadow-sm flex items-center justify-center focus:outline-none focus:ring-4 focus:ring-green-500/30">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                                </svg>
                                Falar no WhatsApp
                            </a>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </main>

    <!-- Floating Action Button for Help -->
    <button class="fixed bottom-6 right-6 md:bottom-8 md:right-8 bg-blue-900 hover:bg-blue-800 text-white rounded-full p-4 shadow-xl hover:shadow-2xl transition-all transform hover:-translate-y-1 focus:outline-none focus:ring-4 focus:ring-blue-500/50 z-40 group" aria-label="Central de Ajuda" onclick="showToast('Abrindo chat de suporte...', 'info')">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="absolute right-full mr-4 bg-gray-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
            Precisa de ajuda?
        </span>
    </button>

    <script>
        // Inicializar o Parse
        Parse.initialize("DbAWEvALulsxiEbPcWKAljj22OBTqi00Z4pvMimk", "3DUSnVQ6K65V1gPbnXpb8iSTDjSDgDwRYcLKifZr");
        Parse.serverURL = 'https://parseapi.back4app.com/';

        document.addEventListener("DOMContentLoaded", () => {
            // Verificar sessão
            const currentUser = Parse.User.current();
            if (!currentUser) {
                window.location.href = 'index.php';
                return;
            }

            // Mostrar App
            document.getElementById('app-body').classList.remove('hidden');

            // Set User Info
            const username = currentUser.get('username') || 'Paciente';
            const firstName = username.charAt(0).toUpperCase() + username.slice(1).split(' ')[0];
            document.getElementById('user-name').textContent = firstName;
            document.getElementById('user-avatar').textContent = firstName.charAt(0);

            // Set Greeting based on time
            const hour = new Date().getHours();
            let greeting = "Boa noite";
            if(hour >= 5 && hour < 12) greeting = "Bom dia";
            else if(hour >= 12 && hour < 18) greeting = "Boa tarde";
            document.getElementById('greeting-time').textContent = greeting;

            // Set current Date
            const options = { weekday: 'long', day: 'numeric', month: 'long' };
            const todayStr = new Date().toLocaleDateString('pt-BR', options);
            document.getElementById('current-date').textContent = todayStr.charAt(0).toUpperCase() + todayStr.slice(1);

            // Animate Progress Bar
            setTimeout(() => {
                document.getElementById('progress-bar-fill').style.width = '26.6%';
            }, 300);

            // Setup Tasks Toggle Listeners
            setupTasks();
        });

        // Setup Logout Modal
        const logoutBtn = document.getElementById('logout-btn');
        const modal = document.getElementById('logout-modal');
        const cancelBtn = document.getElementById('cancel-logout-btn');
        const confirmBtn = document.getElementById('confirm-logout-btn');

        logoutBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        cancelBtn.addEventListener('click', () => modal.classList.add('hidden'));
        confirmBtn.addEventListener('click', async () => {
            confirmBtn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            await Parse.User.logOut();
            window.location.href = 'index.php';
        });

        // Tasks Logic
        function setupTasks() {
            const checkboxes = document.querySelectorAll('.task-checkbox');

            checkboxes.forEach(box => {
                box.addEventListener('change', function() {
                    const taskId = this.id.replace('-toggle', '');
                    const card = document.getElementById(taskId + '-card');
                    const icon = document.getElementById(taskId + '-icon');
                    const title = document.getElementById(taskId + '-title');

                    if (this.checked) {
                        card.classList.add('border-green-200', 'bg-green-50');
                        icon.classList.remove('bg-blue-50', 'text-blue-600');
                        icon.classList.add('bg-green-100', 'text-green-600');
                        title.classList.add('line-through', 'text-gray-400');
                        showToast(`Tarefa "${title.textContent}" concluída!`, 'success');
                    } else {
                        card.classList.remove('border-green-200', 'bg-green-50');
                        icon.classList.remove('bg-green-100', 'text-green-600');
                        icon.classList.add('bg-blue-50', 'text-blue-600');
                        title.classList.remove('line-through', 'text-gray-400');
                    }

                    updateTasksCounter();
                });
            });
        }

        function updateTasksCounter() {
            const total = document.querySelectorAll('.task-checkbox').length;
            const checked = document.querySelectorAll('.task-checkbox:checked').length;

            document.getElementById('task-counter').textContent = `${checked}/${total} concluídas`;

            const allDoneBanner = document.getElementById('all-tasks-done');
            if(checked === total && total > 0) {
                allDoneBanner.classList.remove('hidden');
                allDoneBanner.classList.add('fade-in');
                confetti({
                    particleCount: 100,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: ['#22c55e', '#3b82f6', '#ffffff']
                });
            } else {
                allDoneBanner.classList.add('hidden');
                allDoneBanner.classList.remove('fade-in');
            }
        }

        // Medication Logic
        const medBtn = document.getElementById('med-btn');
        medBtn.addEventListener('click', function() {
            if(this.disabled) return;

            this.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Confirmado!
            `;
            this.classList.remove('bg-green-500', 'hover:bg-green-600');
            this.classList.add('bg-green-700', 'cursor-default', 'opacity-90');
            this.disabled = true;
            this.setAttribute('aria-label', 'Medicamento já tomado');

            showToast("Remédio registrado com sucesso!", 'success');
        });

        // Toast Notification System
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');

            let bgClass = type === 'success' ? 'bg-gray-800' : 'bg-blue-600';
            let icon = type === 'success'
                ? `<svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>`
                : `<svg class="w-5 h-5 text-white mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;

            toast.className = `toast-enter flex items-center p-4 mb-2 rounded-xl shadow-lg text-white text-sm font-medium ${bgClass} pointer-events-auto`;
            toast.innerHTML = `${icon} ${message}`;

            container.appendChild(toast);

            // Remove after 3 seconds
            setTimeout(() => {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
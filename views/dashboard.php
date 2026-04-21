<!DOCTYPE html>
<html lang="pt-BR" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1e3a8a">
    <title>Painel do Médico - Gestão de Pacientes</title>
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
    <!-- Chart.js para os gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jsPDF e html2canvas para relatórios PDF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; scroll-behavior: smooth; }

        .fade-in { animation: fadeIn 0.5s ease-out forwards; opacity: 0; }
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
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300 pb-20 md:pb-8 hidden" id="app-body">

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 flex flex-col gap-2 pointer-events-none"></div>

    <!-- Modal Editar Perfil do Médico -->
    <div id="doctor-profile-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md shadow-2xl relative">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Editar Meu Perfil
                </h3>
                <button id="close-doctor-profile-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form id="doctor-profile-form" class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome</label>
                    <input type="text" id="doc-name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                    <input type="email" id="doc-email" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 outline-none">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" id="cancel-doctor-profile-btn" class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold py-3 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" id="save-doctor-profile-btn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors flex justify-center items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Adicionar/Editar Enfermeiro(a) -->
    <div id="nurse-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md shadow-2xl relative">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Cadastrar Equipe
                </h3>
                <button id="close-nurse-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            <form id="nurse-form" class="p-6 space-y-5">
                <input type="hidden" id="n-id" value="">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Completo</label>
                    <input type="text" id="n-name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail</label>
                    <input type="email" id="n-email" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-600 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Telefone (WhatsApp)</label>
                    <input type="tel" id="n-phone" placeholder="Ex: 11999999999" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-green-600 outline-none">
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" id="cancel-nurse-btn" class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold py-3 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" id="save-nurse-btn" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition-colors flex justify-center items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Adicionar/Editar Paciente -->
    <div id="patient-modal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-2xl shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white dark:bg-gray-800 p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center z-10">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    Cadastrar Novo Paciente
                </h3>
                <button id="close-patient-modal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <form id="patient-form" class="p-6 space-y-5">
                <input type="hidden" id="p-id" value="">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nome Completo</label>
                        <input type="text" id="p-name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Idade</label>
                        <input type="number" id="p-age" required min="0" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">E-mail de Contato</label>
                        <input type="email" id="p-email" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data da Próxima Consulta</label>
                        <input type="date" id="p-date" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Estado de Saúde Atual</label>
                    <select id="p-status" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 outline-none">
                        <option value="Estável">Estável - Recuperação Adequada</option>
                        <option value="Atenção">Atenção - Observação Necessária</option>
                        <option value="Crítico">Crítico - Cuidados Intensivos</option>
                        <option value="Alta Médica">Alta Médica</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Medicamentos Prescritos</label>
                    <textarea id="p-meds" rows="3" placeholder="Ex: Paracetamol 500mg 8/8h, Omeprazol 20mg..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-600 outline-none"></textarea>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" id="cancel-patient-btn" class="flex-1 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold py-3 rounded-xl transition-colors">Cancelar</button>
                    <button type="submit" id="save-patient-btn" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-xl transition-colors flex justify-center items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Salvar Paciente
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Header do Médico -->
    <header class="bg-blue-900 dark:bg-gray-800 text-white shadow-md relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center border border-white/30 shrink-0">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">Painel Clínico</h1>
                        <p class="text-blue-200 text-sm">Bem-vindo, <span id="doctor-name">Dr(a)</span></p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button id="edit-doctor-profile-btn" class="p-2.5 rounded-xl bg-blue-800/50 hover:bg-blue-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-blue-100 transition-colors" title="Editar Meu Perfil">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </button>
                    <button id="theme-toggle" class="p-2.5 rounded-xl bg-blue-800/50 hover:bg-blue-700 dark:bg-gray-700 dark:hover:bg-gray-600 text-blue-100 transition-colors" aria-label="Alternar Tema">
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    </button>
                    <button id="logout-btn" class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-xl font-medium transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        <span class="hidden sm:inline">Sair</span>
                    </button>
                </div>
            </div>

            <!-- Quick Stats & Chart Area -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-6">
                <!-- Data Blocks -->
                <div class="lg:col-span-2 grid grid-cols-2 md:grid-cols-2 gap-4">
                    <div class="bg-white/10 dark:bg-gray-700/50 rounded-xl p-4 backdrop-blur-md border border-white/10 flex flex-col justify-center">
                        <p class="text-blue-200 text-sm font-medium">Total de Pacientes</p>
                        <p class="text-4xl font-bold mt-1" id="stat-total">0</p>
                    </div>
                    <div class="bg-white/10 dark:bg-gray-700/50 rounded-xl p-4 backdrop-blur-md border border-white/10 flex flex-col justify-center">
                        <p class="text-blue-200 text-sm font-medium">Em Estado Crítico</p>
                        <p class="text-4xl font-bold mt-1 text-red-400" id="stat-critical">0</p>
                    </div>
                    <div class="bg-white/10 dark:bg-gray-700/50 rounded-xl p-4 backdrop-blur-md border border-white/10 flex flex-col justify-center">
                        <p class="text-blue-200 text-sm font-medium">Consultas Hoje</p>
                        <p class="text-4xl font-bold mt-1 text-yellow-400" id="stat-today">0</p>
                    </div>
                    <button id="open-new-patient-btn" class="bg-blue-600 hover:bg-blue-500 rounded-xl p-4 border border-blue-400/30 transition-colors flex flex-col justify-center items-center group h-full">
                        <div class="bg-white/20 p-3 rounded-full mb-2 group-hover:scale-110 transition-transform">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <span class="font-bold text-lg">Novo Paciente</span>
                    </button>
                </div>

                <!-- Chart Block -->
                <div class="bg-white/10 dark:bg-gray-700/50 rounded-xl p-4 backdrop-blur-md border border-white/10 flex flex-col h-48 md:h-full justify-center items-center">
                    <p class="text-blue-200 text-sm font-medium w-full text-center mb-2">Visão Geral de Status</p>
                    <div class="relative w-full flex-1 max-h-32 flex justify-center">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Lista de Equipe de Enfermagem -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden fade-in mb-8">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Equipe de Enfermagem
                </h2>

                <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
                    <button id="email-group-btn" class="flex items-center justify-center px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white rounded-xl transition-colors text-sm font-medium whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        E-mail p/ Equipe
                    </button>
                    <button id="open-new-nurse-btn" class="flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors text-sm font-medium whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Novo Membro
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Nome</th>
                            <th class="p-4 font-semibold">E-mail</th>
                            <th class="p-4 font-semibold">Telefone</th>
                            <th class="p-4 font-semibold text-right">Contato & Ações</th>
                        </tr>
                    </thead>
                    <tbody id="nurses-table-body" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <tr>
                            <td colspan="4" class="p-8 text-center text-gray-500">Carregando equipe...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lista de Pacientes -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden fade-in delay-100">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Gestão de Pacientes
                </h2>

                <div class="flex flex-col sm:flex-row w-full sm:w-auto gap-3">
                    <button id="download-pdf-btn" class="flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl transition-colors text-sm font-medium whitespace-nowrap">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Exportar Relatório
                    </button>
                    <div class="relative w-full sm:w-64">
                    <input type="text" id="search-input" placeholder="Buscar paciente..." class="w-full pl-10 pr-4 py-2 rounded-xl border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider">
                            <th class="p-4 font-semibold">Paciente</th>
                            <th class="p-4 font-semibold">Contato</th>
                            <th class="p-4 font-semibold">Status</th>
                            <th class="p-4 font-semibold">Próx. Consulta</th>
                            <th class="p-4 font-semibold text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="patients-table-body" class="divide-y divide-gray-100 dark:divide-gray-700">
                        <!-- Linhas renderizadas via JS -->
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500">
                                <svg class="animate-spin h-8 w-8 mx-auto text-blue-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Carregando pacientes...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <script>
        Parse.initialize("DbAWEvALulsxiEbPcWKAljj22OBTqi00Z4pvMimk", "3DUSnVQ6K65V1gPbnXpb8iSTDjSDgDwRYcLKifZr");
        Parse.serverURL = 'https://parseapi.back4app.com/';

        // Global state
        let patientsList = [];
        let nursesList = [];
        let statusChartInstance = null;
        const Patient = Parse.Object.extend("Patient");
        const Nurse = Parse.Object.extend("Nurse");

        document.addEventListener("DOMContentLoaded", async () => {
            // Theme Toggle
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
                // Redraw chart to update legend text color
                if(patientsList.length > 0) {
                    updateStats(patientsList);
                }
            });

            // Auth Check
            const currentUser = Parse.User.current();
            if (!currentUser) {
                window.location.href = '/login';
                return;
            }
            document.getElementById('app-body').classList.remove('hidden');

            const doctorName = currentUser.get('username') || 'Médico';
            document.getElementById('doctor-name').textContent = doctorName;

            // Logout
            document.getElementById('logout-btn').addEventListener('click', async () => {
                await Parse.User.logOut();
                window.location.href = '/login';
            });

            // Load Data
            await loadPatients();
            await loadNurses();
        });

        // Doctor Profile Modal Logic
        const docProfileModal = document.getElementById('doctor-profile-modal');
        document.getElementById('edit-doctor-profile-btn').addEventListener('click', () => {
            const currentUser = Parse.User.current();
            document.getElementById('doc-name').value = currentUser.get('username') || '';
            document.getElementById('doc-email').value = currentUser.get('email') || '';
            docProfileModal.classList.remove('hidden');
        });
        document.getElementById('close-doctor-profile-modal').addEventListener('click', () => docProfileModal.classList.add('hidden'));
        document.getElementById('cancel-doctor-profile-btn').addEventListener('click', () => docProfileModal.classList.add('hidden'));

        document.getElementById('doctor-profile-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('save-doctor-profile-btn');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';
            btn.disabled = true;

            const user = Parse.User.current();
            user.set('username', document.getElementById('doc-name').value);
            user.set('email', document.getElementById('doc-email').value);

            try {
                await user.save();
                showToast("Perfil atualizado!", "success");
                document.getElementById('doctor-name').textContent = user.get('username');
                docProfileModal.classList.add('hidden');
            } catch(error) {
                showToast("Erro ao atualizar perfil: " + error.message, "error");
            } finally {
                btn.innerHTML = originalHTML;
                btn.disabled = false;
            }
        });

        // Modal Patient Logic
        const modal = document.getElementById('patient-modal');
        const openModalBtn = document.getElementById('open-new-patient-btn');
        const closeModalBtn = document.getElementById('close-patient-modal');
        const cancelModalBtn = document.getElementById('cancel-patient-btn');
        const form = document.getElementById('patient-form');

        function openModal(patientId = null) {
            form.reset();
            document.getElementById('p-id').value = '';
            document.querySelector('#patient-modal h3').innerHTML = `
                <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Cadastrar Novo Paciente
            `;

            if (patientId) {
                const patient = patientsList.find(p => p.id === patientId);
                if (patient) {
                    document.getElementById('p-id').value = patient.id;
                    document.getElementById('p-name').value = patient.get('name') || '';
                    document.getElementById('p-age').value = patient.get('age') || '';
                    document.getElementById('p-email').value = patient.get('email') || '';
                    document.getElementById('p-date').value = patient.get('nextAppointment') || '';
                    document.getElementById('p-status').value = patient.get('status') || 'Estável';
                    document.getElementById('p-meds').value = patient.get('medications') || '';

                    document.querySelector('#patient-modal h3').innerHTML = `
                        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Editar Paciente
                    `;
                }
            }
            modal.classList.remove('hidden');
        }

        function closeModal() {
            modal.classList.add('hidden');
        }

        openModalBtn.addEventListener('click', openModal);
        closeModalBtn.addEventListener('click', closeModal);
        cancelModalBtn.addEventListener('click', closeModal);

        // Search Logic
        document.getElementById('search-input').addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            const filtered = patientsList.filter(p => p.get('name').toLowerCase().includes(query) || p.get('email').toLowerCase().includes(query));
            renderTable(filtered);
        });

        // Form Submit
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('save-patient-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';
            submitBtn.disabled = true;

            const patientId = document.getElementById('p-id').value;
            let patient;

            if (patientId) {
                // Modo Edição
                patient = patientsList.find(p => p.id === patientId);
                if (!patient) {
                    showToast("Erro: Paciente não encontrado.", "error");
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    return;
                }
            } else {
                // Modo Criação
                patient = new Patient();
                patient.set("doctor", Parse.User.current());
            }

            patient.set("name", document.getElementById('p-name').value);
            patient.set("age", parseInt(document.getElementById('p-age').value));
            patient.set("email", document.getElementById('p-email').value);
            patient.set("nextAppointment", document.getElementById('p-date').value);
            patient.set("status", document.getElementById('p-status').value);
            patient.set("medications", document.getElementById('p-meds').value);

            try {
                await patient.save();
                showToast(patientId ? "Paciente atualizado com sucesso!" : "Paciente criado com sucesso!", "success");
                closeModal();
                await loadPatients();
            } catch (error) {
                showToast("Erro ao salvar paciente: " + error.message, "error");
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        // Nurse Modal Logic
        const nurseModal = document.getElementById('nurse-modal');
        const nurseForm = document.getElementById('nurse-form');

        document.getElementById('open-new-nurse-btn').addEventListener('click', () => openNurseModal());
        document.getElementById('close-nurse-modal').addEventListener('click', () => nurseModal.classList.add('hidden'));
        document.getElementById('cancel-nurse-btn').addEventListener('click', () => nurseModal.classList.add('hidden'));

        function openNurseModal(nurseId = null) {
            nurseForm.reset();
            document.getElementById('n-id').value = '';
            document.querySelector('#nurse-modal h3').innerHTML = `
                <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                Cadastrar Equipe
            `;

            if (nurseId) {
                const nurse = nursesList.find(n => n.id === nurseId);
                if (nurse) {
                    document.getElementById('n-id').value = nurse.id;
                    document.getElementById('n-name').value = nurse.get('name') || '';
                    document.getElementById('n-email').value = nurse.get('email') || '';
                    document.getElementById('n-phone').value = nurse.get('phone') || '';

                    document.querySelector('#nurse-modal h3').innerHTML = `
                        <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Editar Equipe
                    `;
                }
            }
            nurseModal.classList.remove('hidden');
        }

        nurseForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = document.getElementById('save-nurse-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Salvando...';
            submitBtn.disabled = true;

            const nurseId = document.getElementById('n-id').value;
            let nurse;

            if (nurseId) {
                nurse = nursesList.find(n => n.id === nurseId);
            } else {
                nurse = new Nurse();
                nurse.set("doctor", Parse.User.current());
            }

            nurse.set("name", document.getElementById('n-name').value);
            nurse.set("email", document.getElementById('n-email').value);
            nurse.set("phone", document.getElementById('n-phone').value);

            try {
                await nurse.save();
                showToast(nurseId ? "Enfermeiro(a) atualizado!" : "Membro da equipe adicionado!", "success");
                nurseModal.classList.add('hidden');
                await loadNurses();
            } catch (error) {
                showToast("Erro ao salvar enfermeiro: " + error.message, "error");
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });

        async function loadNurses() {
            const query = new Parse.Query(Nurse);
            query.equalTo("doctor", Parse.User.current());
            query.descending("createdAt");

            try {
                nursesList = await query.find();
                renderNursesTable(nursesList);
            } catch (error) {
                console.error("Erro ao buscar equipe", error);
                document.getElementById('nurses-table-body').innerHTML = `
                    <tr><td colspan="4" class="p-8 text-center text-red-500">Erro ao carregar equipe.</td></tr>
                `;
            }
        }

        function renderNursesTable(nurses) {
            const tbody = document.getElementById('nurses-table-body');
            if (nurses.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="p-6 text-center text-gray-500">Nenhum membro da equipe cadastrado.</td></tr>`;
                return;
            }

            const html = nurses.map(n => {
                const name = n.get('name') || 'Sem Nome';
                const initial = name.charAt(0).toUpperCase();
                const phone = n.get('phone') || '';
                const email = n.get('email') || '';

                return `
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 flex items-center justify-center font-bold text-sm shrink-0">${initial}</div>
                            <p class="font-bold text-gray-900 dark:text-white">${name}</p>
                        </div>
                    </td>
                    <td class="p-4 text-sm text-gray-600 dark:text-gray-300">${email}</td>
                    <td class="p-4 text-sm text-gray-600 dark:text-gray-300">${phone}</td>
                    <td class="p-4 text-right">
                        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="tel:${phone}" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg transition-colors" title="Ligar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </a>
                            <a href="https://wa.me/55${phone.replace(/\D/g, '')}" target="_blank" class="text-gray-500 hover:text-green-600 dark:text-gray-400 dark:hover:text-green-400 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg transition-colors" title="WhatsApp">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                            </a>
                            <button onclick="openNurseModal('${n.id}')" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 p-2 bg-gray-100 dark:bg-gray-700 rounded-lg transition-colors" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                `;
            }).join('');

            tbody.innerHTML = html;
        }

        async function loadPatients() {
            const query = new Parse.Query(Patient);
            query.equalTo("doctor", Parse.User.current());
            query.descending("createdAt");

            try {
                patientsList = await query.find();
                renderTable(patientsList);
                updateStats(patientsList);
            } catch (error) {
                console.error("Erro ao buscar pacientes", error);
                document.getElementById('patients-table-body').innerHTML = `
                    <tr><td colspan="5" class="p-8 text-center text-red-500">Erro ao carregar dados. Tente atualizar a página.</td></tr>
                `;
            }
        }

        function getStatusBadge(status) {
            switch(status) {
                case 'Estável': return '<span class="px-2.5 py-1 bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300 rounded-full text-xs font-semibold">Estável</span>';
                case 'Atenção': return '<span class="px-2.5 py-1 bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 rounded-full text-xs font-semibold">Atenção</span>';
                case 'Crítico': return '<span class="px-2.5 py-1 bg-red-100 text-red-800 dark:bg-red-900/40 dark:text-red-300 rounded-full text-xs font-semibold flex items-center w-max"><span class="w-2 h-2 rounded-full bg-red-600 mr-1.5 animate-pulse"></span> Crítico</span>';
                case 'Alta Médica': return '<span class="px-2.5 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 rounded-full text-xs font-semibold">Alta Médica</span>';
                default: return `<span class="px-2.5 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">${status}</span>`;
            }
        }

        function renderTable(patients) {
            const tbody = document.getElementById('patients-table-body');

            if (patients.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400 font-medium">Nenhum paciente encontrado</p>
                            <p class="text-xs text-gray-400 mt-1">Clique em "Novo Paciente" para começar</p>
                        </td>
                    </tr>
                `;
                return;
            }

            const html = patients.map(p => {
                const name = p.get('name') || 'Sem Nome';
                const initial = name.charAt(0).toUpperCase();

                // Formata Data DD/MM/YYYY
                let dateFormatted = p.get('nextAppointment') || '-';
                if(dateFormatted !== '-') {
                    const parts = dateFormatted.split('-');
                    if(parts.length === 3) dateFormatted = `${parts[2]}/${parts[1]}/${parts[0]}`;
                }

                return `
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/50 transition-colors group">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300 flex items-center justify-center font-bold text-lg shrink-0">${initial}</div>
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">${name}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">${p.get('age')} anos</p>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-sm text-gray-600 dark:text-gray-300">${p.get('email')}</td>
                    <td class="p-4">${getStatusBadge(p.get('status'))}</td>
                    <td class="p-4 text-sm font-medium text-gray-700 dark:text-gray-300 flex items-center">
                        <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                        ${dateFormatted}
                    </td>
                    <td class="p-4 text-right">
                        <button onclick="openModal('${p.id}')" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium text-sm p-2 bg-blue-50 dark:bg-gray-700 rounded-lg transition-opacity flex items-center inline-flex">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Editar
                        </button>
                    </td>
                </tr>
                `;
            }).join('');

            tbody.innerHTML = html;
        }

        function updateStats(patients) {
            const total = patients.length;
            const critical = patients.filter(p => p.get('status') === 'Crítico').length;
            const stable = patients.filter(p => p.get('status') === 'Estável').length;
            const attention = patients.filter(p => p.get('status') === 'Atenção').length;
            const discharged = patients.filter(p => p.get('status') === 'Alta Médica').length;

            // Check today's appointments
            const todayStr = new Date().toISOString().split('T')[0];
            const todayAppt = patients.filter(p => p.get('nextAppointment') === todayStr).length;

            document.getElementById('stat-total').textContent = total;
            document.getElementById('stat-critical').textContent = critical;
            document.getElementById('stat-today').textContent = todayAppt;

            updateChart(stable, attention, critical, discharged);
        }

        function updateChart(stable, attention, critical, discharged) {
            const ctx = document.getElementById('statusChart').getContext('2d');

            if (statusChartInstance) {
                statusChartInstance.destroy();
            }

            const data = {
                labels: ['Estável', 'Atenção', 'Crítico', 'Alta Médica'],
                datasets: [{
                    data: [stable, attention, critical, discharged],
                    backgroundColor: [
                        '#22c55e', // green
                        '#eab308', // yellow
                        '#ef4444', // red
                        '#3b82f6'  // blue
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            };

            const config = {
                type: 'pie',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                color: document.documentElement.classList.contains('dark') ? '#e5e7eb' : '#374151',
                                font: {
                                    family: "'Inter', sans-serif",
                                    size: 11
                                },
                                boxWidth: 12
                            }
                        }
                    }
                }
            };

            statusChartInstance = new Chart(ctx, config);
        }

        // Download PDF Logic
        document.getElementById('download-pdf-btn').addEventListener('click', () => {
            if(patientsList.length === 0) {
                showToast('Nenhum paciente para exportar.', 'error');
                return;
            }

            const btn = document.getElementById('download-pdf-btn');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Gerando...';
            btn.disabled = true;

            setTimeout(() => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF();

                const doctorName = document.getElementById('doctor-name').textContent;
                const today = new Date().toLocaleDateString('pt-BR');

                // Cabeçalho PDF
                doc.setFontSize(20);
                doc.setTextColor(30, 58, 138); // blue-900
                doc.text('Relatório Clínico - Gestão de Pacientes', 14, 22);

                doc.setFontSize(11);
                doc.setTextColor(100, 100, 100);
                doc.text(`Médico Responsável: ${doctorName}`, 14, 30);
                doc.text(`Data de Emissão: ${today}`, 14, 36);

                // Tabela
                const tableColumn = ["Nome do Paciente", "Idade", "Contato", "Status", "Próx. Consulta"];
                const tableRows = [];

                patientsList.forEach(p => {
                    let dateFormatted = p.get('nextAppointment') || '-';
                    if(dateFormatted !== '-') {
                        const parts = dateFormatted.split('-');
                        if(parts.length === 3) dateFormatted = `${parts[2]}/${parts[1]}/${parts[0]}`;
                    }

                    const rowData = [
                        p.get('name') || 'Sem Nome',
                        p.get('age') ? `${p.get('age')} anos` : '-',
                        p.get('email') || '-',
                        p.get('status') || '-',
                        dateFormatted
                    ];
                    tableRows.push(rowData);
                });

                doc.autoTable({
                    head: [tableColumn],
                    body: tableRows,
                    startY: 45,
                    styles: { font: 'helvetica', fontSize: 10 },
                    headStyles: { fillColor: [30, 58, 138] },
                    alternateRowStyles: { fillColor: [243, 244, 246] }
                });

                doc.save(`Relatorio_Pacientes_${today.replace(/\//g, '-')}.pdf`);

                btn.innerHTML = originalHTML;
                btn.disabled = false;
                showToast('Relatório baixado com sucesso!', 'success');
            }, 500);
        });

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
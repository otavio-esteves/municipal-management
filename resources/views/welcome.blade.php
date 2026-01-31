<!DOCTYPE html>
<html lang="pt-br" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Painel Urbanismo - Responsivo</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    animation: {
                        'spin-slow': 'spin 3s linear infinite',
                    }
                }
            }
        }
    </script>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* Utilitários Customizados */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Transições */
        .modal-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        /* Ajuste para inputs date no iOS */
        input[type="date"] { -webkit-appearance: none; min-height: 2.5rem; }
    </style>
</head>
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 h-screen flex flex-col overflow-hidden selection:bg-slate-300 dark:selection:bg-slate-700">

    <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-3 md:px-6 md:py-4 flex flex-col md:flex-row items-center justify-between z-20 shrink-0 gap-3 md:gap-4 transition-colors duration-200">
        
        <div class="flex items-center gap-3 w-full md:w-auto">
            <div class="bg-slate-900 dark:bg-slate-800 text-white p-2 rounded-lg transition-colors duration-200 shrink-0">
                <i class="ph ph-buildings text-xl"></i>
            </div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white transition-colors duration-200 truncate">Urbanismo</h1>
            
            <button type="button" onclick="toggleTheme()" class="ml-auto md:hidden p-2 text-slate-500 dark:text-slate-400 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                <i class="ph ph-moon block dark:hidden"></i>
                <i class="ph ph-sun hidden dark:block text-amber-400"></i>
            </button>
        </div>

        <div class="w-full md:w-auto overflow-hidden">
            <div class="flex items-center bg-slate-50 dark:bg-slate-800 rounded-lg p-1 border border-slate-200 dark:border-slate-700 shadow-inner overflow-x-auto no-scrollbar touch-pan-x transition-colors duration-200 w-full md:max-w-2xl">
                <div class="flex items-center px-3 py-1 gap-2 border-r border-slate-200 dark:border-slate-700 shrink-0">
                    <i class="ph ph-files text-slate-400"></i>
                    <span class="text-xs font-bold text-slate-600 dark:text-slate-300 whitespace-nowrap">Total 6</span>
                </div>
                <div class="flex items-center px-3 py-1 gap-2 border-r border-slate-200 dark:border-slate-700 shrink-0">
                    <div class="w-2 h-2 rounded-full bg-red-500"></div>
                    <span class="text-xs font-bold text-red-600 dark:text-red-400 whitespace-nowrap">Urgentes 3</span>
                </div>
                <div class="flex items-center px-3 py-1 gap-2 border-r border-slate-200 dark:border-slate-700 shrink-0">
                    <div class="w-2 h-2 rounded-full bg-amber-500"></div>
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400 whitespace-nowrap">Vencidas 1</span>
                </div>
                <div class="flex items-center px-3 py-1 gap-2 border-r border-slate-200 dark:border-slate-700 shrink-0">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">Em And. 15</span>
                </div>
                <div class="flex items-center px-3 py-1 gap-2 shrink-0">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 whitespace-nowrap">Concluídas 54</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <button type="button" onclick="toggleTheme()" class="hidden md:block p-2 text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 transition-colors duration-200">
                <i class="ph ph-moon block dark:hidden"></i>
                <i class="ph ph-sun hidden dark:block text-amber-400"></i>
            </button>

            <div class="relative flex-1 md:w-64">
                <i class="ph ph-magnifying-glass absolute left-3 top-2.5 text-slate-400 pointer-events-none"></i>
                <input type="text" placeholder="Buscar..." class="w-full pl-9 pr-4 py-2 text-sm border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-slate-400 dark:focus:ring-slate-600 focus:outline-none shadow-sm placeholder-slate-400 dark:placeholder-slate-500 transition-shadow duration-200">
            </div>

            <button type="button" onclick="openModal()" class="flex items-center justify-center gap-2 bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-4 py-2 rounded-lg font-bold text-sm hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors duration-200 shadow-sm whitespace-nowrap active:scale-95 transform">
                <span>Nova ODS</span>
                <i class="ph ph-plus font-bold"></i>
            </button>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto p-4 md:p-6 pb-32 bg-slate-100 dark:bg-slate-950 transition-colors duration-200"> 
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all duration-200 cursor-pointer active:scale-[0.98] transform">
                <div class="h-1.5 w-full bg-red-500 absolute top-0"></div> 
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-lg font-bold text-slate-800 dark:text-white transition-colors">ODS -001</span>
                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2 py-1 rounded border border-slate-200 dark:border-slate-700 uppercase transition-colors">
                            Ilumin.
                        </span>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1 transition-colors truncate">Troca de Lâmpada</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors truncate">Av. Bernardo Sayão</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 transition-colors">25/11/2025</p>
                    </div>
                    <div class="flex justify-end mt-4">
                        <span class="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/50 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1 transition-colors">
                            <i class="ph ph-warning-circle text-red-500 dark:text-red-400"></i> Urgente
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all duration-200 cursor-pointer active:scale-[0.98] transform">
                <div class="h-1.5 w-full bg-red-500 absolute top-0"></div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-lg font-bold text-slate-800 dark:text-white transition-colors">ODS -002</span>
                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2 py-1 rounded border border-slate-200 dark:border-slate-700 uppercase transition-colors">Obra</span>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1 transition-colors truncate">Buraco na Via</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors truncate">Rua das Magnólias</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 transition-colors">26/11/2025</p>
                    </div>
                    <div class="flex justify-end mt-4">
                        <span class="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/50 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1 transition-colors">
                            <i class="ph ph-warning-circle text-red-500 dark:text-red-400"></i> Urgente
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all duration-200 cursor-pointer active:scale-[0.98] transform">
                <div class="h-1.5 w-full bg-blue-500 absolute top-0"></div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-lg font-bold text-slate-800 dark:text-white transition-colors">ODS -045</span>
                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2 py-1 rounded border border-slate-200 dark:border-slate-700 uppercase transition-colors">Limpeza</span>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1 transition-colors truncate">Roçagem Praça</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors truncate">Praça da Bíblia</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 transition-colors">20/11/2025</p>
                    </div>
                    <div class="flex justify-end mt-4">
                        <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1 transition-colors">
                            <i class="ph ph-spinner text-blue-500 dark:text-blue-400 animate-spin-slow"></i> Em And.
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all duration-200 cursor-pointer active:scale-[0.98] transform">
                <div class="h-1.5 w-full bg-emerald-500 absolute top-0"></div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-lg font-bold text-slate-800 dark:text-white transition-colors">ODS -010</span>
                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2 py-1 rounded border border-slate-200 dark:border-slate-700 uppercase transition-colors">Ilumin.</span>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1 transition-colors truncate">Troca Relé</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors truncate">Av. Brasil, Centro</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 transition-colors">10/11/2025</p>
                    </div>
                    <div class="flex justify-end mt-4">
                        <span class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1 transition-colors">
                            <i class="ph ph-check-circle text-emerald-500 dark:text-emerald-400"></i> Concluída
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all duration-200 cursor-pointer active:scale-[0.98] transform">
                <div class="h-1.5 w-full bg-blue-500 absolute top-0"></div>
                <div class="p-5">
                    <div class="flex justify-between items-start mb-3">
                        <span class="text-lg font-bold text-slate-800 dark:text-white transition-colors">ODS -046</span>
                        <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2 py-1 rounded border border-slate-200 dark:border-slate-700 uppercase transition-colors">Limpeza</span>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1 transition-colors truncate">Retirada Entulho</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 transition-colors truncate">Setor Santa Efigênia</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 transition-colors">21/11/2025</p>
                    </div>
                    <div class="flex justify-end mt-4">
                        <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1 transition-colors">
                            <i class="ph ph-spinner text-blue-500 dark:text-blue-400 animate-spin-slow"></i> Em And.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="modalODS" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 modal-transition" aria-hidden="true">
        <div class="bg-white dark:bg-slate-900 w-full max-w-lg m-4 rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden transform scale-95 modal-transition max-h-[90vh] flex flex-col">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white transition-colors">Nova Ordem de Serviço</h2>
                </div>
                <button type="button" onclick="closeModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors duration-200 p-2" aria-label="Fechar Modal">
                    <i class="ph ph-x text-2xl"></i>
                </button>
            </div>

            <form class="p-6 space-y-4 overflow-y-auto custom-scrollbar" onsubmit="event.preventDefault(); closeModal();">
                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1 transition-colors">Título do Serviço</label>
                    <input type="text" placeholder="Ex: Reparo de calçada" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none transition-all duration-200 text-slate-900 dark:text-slate-100">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1 transition-colors">Localização</label>
                    <input type="text" placeholder="Nome da rua, bairro ou praça" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none transition-all duration-200 text-slate-900 dark:text-slate-100">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1 transition-colors">Categoria</label>
                        <select class="w-full h-10 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none transition-all duration-200 text-slate-900 dark:text-slate-100">
                            <option>Iluminação</option>
                            <option>Limpeza</option>
                            <option>Obras</option>
                            <option>Urbanismo</option>
                        </select>
                    </div>

                    <div class="col-span-2 sm:col-span-1">
                        <label class="block w-full text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1 transition-colors">Data Início</label>
                        <input type="date" class="w-full h-10 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none transition-all duration-200 text-slate-900 dark:text-slate-100">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-3 transition-colors">Nível de Prioridade</label>
                    
                    <label class="relative block cursor-pointer group select-none">
                        <input type="checkbox" name="is_urgent" class="sr-only">
                        
                        <div class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-200 
                                    bg-white dark:bg-slate-950/40 border-slate-200 dark:border-slate-800
                                    group-hover:border-slate-300 dark:group-hover:border-slate-700
                                    group-has-[:checked]:border-red-500 group-has-[:checked]:bg-red-50/50 dark:group-has-[:checked]:bg-red-950/20
                                    active:scale-[0.99]">
                            
                            <div class="flex items-center gap-4">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl transition-colors
                                            bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-500
                                            group-has-[:checked]:bg-red-500 group-has-[:checked]:text-white">
                                    <i class="ph-fill ph-warning text-xl"></i>
                                </div>
                                
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white leading-none mb-1">Prioridade Urgente</span>
                                    <span class="text-xs text-slate-500">Destaque visual imediato no painel</span>
                                </div>
                            </div>

                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all
                                        border-slate-200 dark:border-slate-700
                                        group-has-[:checked]:bg-red-500 group-has-[:checked]:border-red-500">
                                <i class="ph ph-check text-white text-[10px] font-bold scale-0 group-has-[:checked]:scale-100 transition-transform"></i>
                            </div>
                        </div>
                    </label>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1 transition-colors">Observação</label>
                    <textarea rows="3" placeholder="Informações extras..." class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none transition-all duration-200 resize-none text-slate-900 dark:text-slate-100"></textarea>
                </div>
                    
                <div class="pt-2 flex gap-3">
                    <button type="button" onclick="closeModal()" class="flex-1 px-4 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors duration-200 active:scale-95 transform">
                        Cancelar
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-all duration-200 shadow-lg shadow-slate-900/10 hover:shadow-xl active:scale-95 transform">
                        Criar ODS
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="fixed bottom-6 left-0 right-0 z-40 flex justify-center px-4 pointer-events-none">
        <nav class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-md border border-slate-200 dark:border-slate-700 shadow-xl rounded-2xl p-2 pointer-events-auto flex gap-1 overflow-x-auto max-w-full no-scrollbar transition-colors duration-200 touch-pan-x">
            <a href="#" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors duration-200 whitespace-nowrap active:scale-95">Agricultura</a>
            <a href="#" class="px-4 py-2 rounded-xl text-sm font-bold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-900/50 border border-emerald-100 dark:border-emerald-800 shadow-sm whitespace-nowrap flex items-center gap-2 transition-colors duration-200 active:scale-95">
                <i class="ph ph-check-circle"></i> Urbanismo
            </a>
            <a href="#" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors duration-200 whitespace-nowrap active:scale-95">Transportes</a>
            <a href="#" class="px-4 py-2 rounded-xl text-sm font-medium text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors duration-200 whitespace-nowrap active:scale-95">Saúde</a>
        </nav>
    </div>

    <script>
        // Lógica de Tema
        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.toggle('dark');
            localStorage.theme = isDark ? 'dark' : 'light';
        }

        // Lógica do Modal com Scroll Lock
        const modal = document.getElementById('modalODS');
        const modalContent = modal.querySelector('div');
        const body = document.body;

        function openModal() {
            modal.classList.remove('hidden');
            requestAnimationFrame(() => {
                modal.classList.remove('opacity-0');
                modalContent.classList.remove('scale-95');
            });
            modal.setAttribute('aria-hidden', 'false');
            // Impede o scroll do fundo no mobile
            // body.style.overflow = 'hidden'; 
        }

        function closeModal() {
            modal.classList.add('opacity-0');
            modalContent.classList.add('scale-95');
            modal.setAttribute('aria-hidden', 'true');
            // body.style.overflow = '';
            
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }

        // Fechar ao clicar fora
        modal.addEventListener('click', (e) => {
            if (e.target === modal) closeModal();
        });
        
        // Fechar com ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });
    </script>
</body>
</html>
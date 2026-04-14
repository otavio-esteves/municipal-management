<div class="flex-1 flex flex-col h-full overflow-hidden" 
     x-data="{ 
        showOdsModal: false, 
        modalView: 'details',
        openModal(mode) {
            this.showOdsModal = true;
            this.modalView = mode === 'checklist' ? 'checklist' : 'details';
            if (mode === 'create') {
                $wire.resetForm();
            }
        }
     }"
     x-on:ods-saved.window="showOdsModal = false"
>
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex flex-col md:flex-row items-center justify-between shrink-0 gap-3 transition-colors duration-200">
        <div class="flex items-center gap-3">
            <h1 class="font-bold text-xl text-slate-900 dark:text-white">{{ $secretariat->name ?? 'Secretaria' }}</h1>
            <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2 py-1 rounded uppercase border border-slate-200 dark:border-slate-700">Painel ODS</span>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <button type="button" class="h-8 w-8 flex items-center justify-center text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-md border border-slate-200 dark:border-slate-700 transition-colors shrink-0">
                <i class="ph ph-funnel text-base"></i>
            </button>

            <div class="relative flex-1 md:w-56 h-8">
                <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" placeholder="Buscar ODS..." class="w-full h-full pl-8 pr-3 text-xs border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-slate-400 focus:outline-none transition-shadow">
            </div>

            <button type="button" x-on:click="openModal('create')" class="h-8 flex items-center justify-center gap-1.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-3 rounded-md font-bold text-xs hover:bg-slate-800 dark:hover:bg-slate-200 transition-colors shadow-sm whitespace-nowrap shrink-0">
                <span>Nova ODS +</span>
            </button>
        </div>
    </div>

    <main class="flex-1 overflow-y-auto p-6 pb-32 bg-slate-100 dark:bg-slate-950 transition-colors duration-200 custom-scrollbar">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            
            @foreach($serviceOrders as $ods)
                <div x-on:click="openModal('edit')" class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden group hover:shadow-md transition-all duration-200 cursor-pointer">
                    <div class="h-1.5 w-full bg-{{ $ods->color }}-500 absolute top-0"></div>
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-lg font-bold text-slate-800 dark:text-white transition-colors">{{ $ods->code }}</span>
                            <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2 py-1 rounded border border-slate-200 dark:border-slate-700 uppercase">{{ mb_substr($ods->category, 0, 6) }}.</span>
                        </div>
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1 truncate">{{ $ods->title }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $ods->location }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $ods->date }}</p>
                        </div>
                        <div class="flex justify-between items-center mt-4">
                            <button type="button" x-on:click.stop="openModal('checklist')" class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors" title="Abrir Checklist">
                                <i class="ph ph-list-checks text-lg"></i>
                            </button>

                            @if($ods->urgent)
                                <span class="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/50 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1">
                                    <i class="ph ph-warning-circle text-red-500 dark:text-red-400"></i> Urgente
                                </span>
                            @elseif($ods->status === 'completed')
                                <span class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1">
                                    <i class="ph ph-check-circle text-emerald-500 dark:text-emerald-400"></i> Concluída
                                </span>
                            @else
                                <span class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50 text-xs font-bold px-3 py-1.5 rounded-full flex items-center gap-1">
                                    <i class="ph ph-spinner text-blue-500 dark:text-blue-400 animate-spin-slow"></i> Em And.
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

        </div>
    </main>

    <div x-show="showOdsModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm"
         style="display: none;"
    >
        <div class="absolute inset-0" x-on:click="showOdsModal = false"></div>

        <div x-show="showOdsModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden relative flex flex-col max-h-[90vh] h-[90vh] md:h-[621px]"
        >
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                <h2 class="text-lg font-bold text-slate-900 dark:text-white" x-text="$wire.odsId ? 'Editar Ordem de Serviço' : 'Nova Ordem de Serviço'"></h2>
                
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="modalView = modalView === 'details' ? 'checklist' : 'details'" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i class="text-2xl" :class="modalView === 'details' ? 'ph ph-list-checks' : 'ph ph-info'"></i>
                    </button>
                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>
                    <button type="button" x-on:click="showOdsModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>
            </div>

            <form wire:submit.prevent="save" class="flex flex-col flex-1 overflow-hidden">
                
                <div x-show="modalView === 'details'" class="space-y-4 p-6 overflow-y-auto custom-scrollbar flex-1">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Título do Serviço</label>
                        <input type="text" wire:model="title" placeholder="Ex: Reparo de calçada" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none text-slate-900 dark:text-slate-100" />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Localização</label>
                        <input type="text" wire:model="location" placeholder="Nome da rua, bairro ou praça" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none text-slate-900 dark:text-slate-100" />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div class="col-span-2 sm:col-span-1 relative">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Categoria</label>
                            <select wire:model="categoryId" class="w-full h-10 pl-3 pr-10 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none text-slate-900 dark:text-slate-100 appearance-none">
                                <option value="">Selecione...</option>
                                <option value="1">Iluminação</option>
                                <option value="2">Limpeza</option>
                                <option value="3">Obras</option>
                            </select>
                        </div>
                        <div class="col-span-2 sm:col-span-1">
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Data Início</label>
                            <input type="date" wire:model="dueDate" class="w-full h-10 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none text-slate-900 dark:text-slate-100 dark:[color-scheme:dark]" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-3">Nível de Prioridade</label>
                        <label class="relative block cursor-pointer group select-none">
                            <input type="checkbox" wire:model="isUrgent" class="sr-only" />
                            <div class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-75 bg-white dark:bg-slate-950/40 border-slate-200 dark:border-slate-800 group-has-[:checked]:bg-red-50/50 dark:group-has-[:checked]:bg-red-950/20">
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-xl transition-colors bg-red-100 dark:bg-red-900/30 text-red-600 group-has-[:checked]:bg-red-500 group-has-[:checked]:text-white">
                                        <i class="ph-fill ph-warning text-xl"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-slate-900 dark:text-white leading-none mb-1">Prioridade Urgente</span>
                                        <span class="text-xs text-slate-500">Destaque visual imediato no painel</span>
                                    </div>
                                </div>
                                <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all border-slate-200 dark:border-slate-700 group-has-[:checked]:bg-red-500 group-has-[:checked]:border-red-500">
                                    <i class="ph ph-check text-white text-[10px] font-bold scale-0 group-has-[:checked]:scale-100 transition-transform"></i>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Observação</label>
                        <textarea wire:model="observation" rows="3" placeholder="Informações extras..." class="block w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none resize-none text-slate-900 dark:text-slate-100 custom-scrollbar"></textarea>
                    </div>
                </div>

                <div x-show="modalView === 'checklist'" style="display: none;" class="flex flex-col h-full p-6">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2 shrink-0">Checklist de Execução</label>
                    
                    <ul class="space-y-2 flex-1 overflow-y-auto custom-scrollbar mb-4 min-h-0">
                        <li class="text-xs text-slate-400 dark:text-slate-500 italic py-2 text-center">Implementação Livewire do Checklist In-Place na Fase 4</li>
                    </ul>
                </div>

                <div class="px-6 pb-6 pt-0 shrink-0 border-t-0">
                    <div class="flex gap-3">
                        <button type="button" x-on:click="showOdsModal = false" class="flex-1 px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">Cancelar</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-all shadow-lg hover:shadow-xl" x-text="$wire.odsId ? 'Salvar' : 'Criar ODS'"></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
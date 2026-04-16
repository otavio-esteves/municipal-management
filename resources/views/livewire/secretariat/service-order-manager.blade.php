<div class="h-screen flex flex-col overflow-hidden bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 selection:bg-slate-300 dark:selection:bg-slate-700"
     x-data="{ 
        modalOpen: false, 
        modalView: 'details', 
        mode: 'create' 
     }"
     x-on:open-ods-modal.window="modalOpen = true; modalView = 'details'; mode = $event.detail.mode"
     x-on:ods-saved.window="modalOpen = false">

    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <header class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-6 py-4 flex flex-col md:flex-row items-center justify-between z-20 shrink-0 gap-3 transition-colors duration-200">
        <div class="flex items-center gap-2 w-full md:w-auto">
            <button class="h-8 w-8 flex items-center justify-center rounded-md border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400">
                <i class="ph ph-list text-lg"></i>
            </button>
            <div class="flex items-center gap-3">
                <span class="font-bold text-slate-900 dark:text-white tracking-tight">
                    {{ $secretariat->name }} <span class="text-slate-400 font-normal">/ {{ auth()->user()->name }}</span>
                </span>
            </div>
        </div>

        <div class="h-8 flex items-center bg-slate-50 dark:bg-slate-800 rounded-md px-1 border border-slate-200 dark:border-slate-700 shadow-inner overflow-x-auto max-w-full">
            <div class="flex items-center px-2.5 h-full gap-1.5 border-r border-slate-200 dark:border-slate-700 whitespace-nowrap">
                <i class="ph ph-files text-slate-400 text-sm"></i>
                <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Total <span class="font-bold">{{ $serviceOrders->count() }}</span></span>
            </div>
            <div class="flex items-center px-2.5 h-full gap-1.5 border-r border-slate-200 dark:border-slate-700 whitespace-nowrap">
                <div class="w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_5px_rgba(239,68,68,0.5)]"></div>
                <span class="text-xs font-medium text-red-600 dark:text-red-400">Urgentes <span class="font-bold">{{ $serviceOrders->where('is_urgent', true)->count() }}</span></span>
            </div>
            <div class="flex items-center px-2.5 h-full gap-1.5 whitespace-nowrap">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.5)]"></div>
                <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400">Concluídas <span class="font-bold">{{ $serviceOrders->where('status', 'completed')->count() }}</span></span>
            </div>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <button class="h-8 w-8 flex items-center justify-center text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700 rounded-md bg-white dark:bg-slate-900 hover:bg-slate-100 transition-colors">
                <i class="ph ph-funnel text-base"></i>
            </button>
            <div class="relative flex-1 md:w-56 h-8">
                <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar..." 
                    class="w-full h-full pl-8 pr-3 text-xs border border-slate-300 dark:border-slate-700 rounded-md bg-white dark:bg-slate-800 focus:ring-2 focus:ring-slate-400 outline-none transition-shadow shadow-sm placeholder-slate-400">
            </div>
            <button x-on:click="$wire.resetForm(); modalOpen = true; mode = 'create'; modalView = 'details'" 
                class="h-8 flex items-center justify-center gap-1.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 px-3 rounded-md font-bold text-xs hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-sm whitespace-nowrap">
                <span>Nova ODS +</span>
            </button>
        </div>
    </header>

    @if (session()->has('error'))
        <div class="m-6 p-4 bg-red-500 text-white rounded-xl shadow-lg flex items-center gap-3">
            <i class="ph-fill ph-warning-circle text-2xl"></i>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <main class="flex-1 overflow-y-auto p-6 pb-32 custom-scrollbar">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($serviceOrders as $ods)
                <div x-on:click="$wire.edit({{ $ods->id }}); modalView = 'details'; mode = 'edit'; modalOpen = true"
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 relative overflow-hidden group hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                    
                    <div class="h-1.5 w-full absolute top-0 {{ $ods->is_urgent ? 'bg-red-500' : ($ods->status === 'completed' ? 'bg-emerald-500' : 'bg-blue-500') }}"></div>
                    
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-lg font-bold text-slate-800 dark:text-white">{{ $ods->code }}</span>
                            <span class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-[10px] font-bold px-2 py-1 rounded border border-slate-200 dark:border-slate-700 uppercase">
                                {{ substr($ods->category->name ?? 'S/CAT', 0, 7) }}.
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-1 line-clamp-1">{{ $ods->title }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-1">{{ $ods->location ?: 'Local não informado' }}</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                {{ $ods->due_date ? \Carbon\Carbon::parse($ods->due_date)->format('d/m/Y') : '--/--/----' }}
                            </p>
                        </div>

                        <div class="flex justify-between items-center mt-4">
                            <button type="button" 
                                x-on:click.stop="$wire.edit({{ $ods->id }}); modalView = 'checklist'; modalOpen = true"
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors">
                                <i class="ph ph-list-checks text-lg"></i>
                            </button>

                            <span class="text-[10px] font-bold px-2.5 py-1.5 rounded-full flex items-center gap-1.5 transition-colors
                                {{ $ods->status === 'completed' ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50' : 
                                   ($ods->status === 'in_progress' ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50' : 
                                   'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/50') }}">
                                <i class="ph {{ $ods->status === 'completed' ? 'ph-check-circle' : ($ods->status === 'in_progress' ? 'ph-spinner animate-spin-slow' : 'ph-clock') }}"></i>
                                {{ $ods->status === 'completed' ? 'Concluída' : ($ods->status === 'in_progress' ? 'Em And.' : 'Pendente') }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 flex flex-col items-center justify-center opacity-50">
                    <i class="ph ph-files text-4xl mb-2"></i>
                    <p class="text-sm font-medium">Nenhuma ordem de serviço encontrada.</p>
                </div>
            @endforelse
        </div>
    </main>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-all duration-300"
         x-show="modalOpen" 
         x-transition:enter="ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         x-cloak>
        
        <div class="bg-white dark:bg-slate-900 w-full max-w-lg rounded-2xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden transform transition-all flex flex-col max-h-[90vh] h-[621px]"
             x-show="modalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-on:click.away="modalOpen = false">
            
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white" 
                        x-text="mode === 'create' ? 'Nova Ordem de Serviço' : 'Editar ODS - ' + '{{ $odsId }}'"></h2>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" x-on:click="modalView = (modalView === 'details' ? 'checklist' : 'details')"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                        title="Alternar Detalhes/Checklist">
                        <i class="ph text-2xl" :class="modalView === 'details' ? 'ph-list-checks' : 'ph-info'"></i>
                    </button>
                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>
                    <button type="button" x-on:click="modalOpen = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="ph ph-x text-2xl"></i>
                    </button>
                </div>
            </div>

            <form wire:submit="save" class="flex flex-col flex-1 overflow-hidden">
                <div class="space-y-4 p-6 overflow-y-auto custom-scrollbar flex-1" x-show="modalView === 'details'">
                    
                    @if ($errors->any())
                        <div class="p-3 bg-red-100 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 rounded-lg">
                            <ul class="list-disc list-inside text-xs font-bold uppercase">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Título do Serviço</label>
                        <input wire:model="title" type="text" placeholder="Ex: Reparo de calçada"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none transition-all text-slate-900 dark:text-slate-100">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Localização (Endereço/Ponto)</label>
                        <input wire:model="location" type="text" placeholder="Nome da rua, bairro ou praça"
                            class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none text-slate-900 dark:text-slate-100">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Categoria</label>
                            <select wire:model="categoryId" class="w-full h-10 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm outline-none cursor-pointer appearance-none text-slate-900 dark:text-slate-100">
                                <option value="">Selecione...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Prazo Conclusão</label>
                            <input wire:model="dueDate" type="date" class="w-full h-10 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm text-slate-900 dark:text-slate-100 [color-scheme:dark]">
                        </div>
                    </div>

                    <label class="relative block cursor-pointer group touch-manipulation select-none">
                        <input wire:model="isUrgent" type="checkbox" class="sr-only">
                        <div class="flex items-center justify-between p-4 rounded-2xl border transition-all duration-75 bg-white dark:bg-slate-950/40 border-slate-200 dark:border-slate-800"
                             :class="$wire.isUrgent ? 'bg-red-50/50 dark:bg-red-950/20 border-red-200 dark:border-red-900' : ''">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center justify-center w-10 h-10 rounded-xl transition-colors"
                                     :class="$wire.isUrgent ? 'bg-red-500 text-white' : 'bg-red-100 dark:bg-red-900/30 text-red-600'">
                                    <i class="ph-fill ph-warning text-xl"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-900 dark:text-white leading-none mb-1">Prioridade Urgente</span>
                                    <span class="text-xs text-slate-500">Destaque visual imediato no painel</span>
                                </div>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition-all"
                                 :class="$wire.isUrgent ? 'bg-red-500 border-red-500' : 'border-slate-200 dark:border-slate-700'">
                                <i class="ph ph-check text-white text-[10px] font-bold" x-show="$wire.isUrgent"></i>
                            </div>
                        </div>
                    </label>

                    <div>
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Observação</label>
                        <textarea wire:model="observation" rows="3" placeholder="Informações extras..."
                            class="block w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none resize-none text-slate-900 dark:text-slate-100"></textarea>
                    </div>
                </div>

                <div class="flex flex-col h-full overflow-hidden p-6" x-show="modalView === 'checklist'">
                    <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase mb-2">Checklist de Execução</label>
                    <div class="flex-1 flex flex-col items-center justify-center border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-slate-50/50 dark:bg-slate-950/20">
                        <i class="ph ph-list-plus text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs text-slate-400 font-medium text-center">Os itens do checklist serão <br> implementados na próxima etapa.</p>
                    </div>
                </div>

                <div class="px-6 pb-6 pt-0 shrink-0 border-t-0">
                    <div class="flex gap-3">
                        <button type="button" x-on:click="modalOpen = false"
                            class="flex-1 px-4 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-bold text-sm rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-slate-900 dark:bg-white text-white dark:text-slate-900 font-bold text-sm rounded-xl hover:bg-slate-800 dark:hover:bg-slate-100 transition-all shadow-lg">
                            <span x-text="mode === 'create' ? 'Criar ODS' : 'Salvar Alterações'"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; height: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.2); border-radius: 10px; }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb { background: rgba(148, 163, 184, 0.5); }
        [x-cloak] { display: none !important; }
    </style>
</div>
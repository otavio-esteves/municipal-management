<div class="ods-reference-font h-screen flex flex-col overflow-hidden bg-slate-950 text-slate-100 selection:bg-slate-700"
     x-data="{ 
        sidebarOpen: false,
        adminOpen: {{ request()->routeIs('admin.*') ? 'true' : 'false' }},
        filterOpen: false,
        modalOpen: false, 
        modalView: 'details', 
        mode: 'create' 
     }"
     x-init="document.documentElement.classList.add('dark')"
     x-on:open-ods-modal.window="modalOpen = true; modalView = $event.detail.view || 'details'; mode = $event.detail.mode"
     x-on:ods-saved.window="modalOpen = false"
     x-on:ods-modal-closed.window="modalOpen = false"
     x-on:keydown.escape.window="
        if (modalOpen) {
            $wire.closeModal();
        } else if (sidebarOpen) {
            sidebarOpen = false;
        } else if (filterOpen) {
            filterOpen = false;
        }
     ">

    {{-- TODO técnico: mover Phosphor Icons para asset local via Vite quando o pacote for incorporado ao build. --}}
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <div x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        x-on:click="sidebarOpen = false"
        class="fixed inset-0 bg-slate-900/20 dark:bg-black/40 backdrop-blur-sm z-40"></div>

    <aside class="fixed top-0 left-0 h-full w-80 bg-slate-50 dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800 z-50 transform transition-transform duration-300 ease-out flex flex-col shadow-2xl"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="p-2.5 flex items-center justify-between border-b border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0">
            <div class="flex items-center gap-1">
                <div class="text-black p-1.5 rounded-md shadow-slate-900/20 dark:text-white">
                    <i class="ph-fill ph-buildings text-2xl"></i>
                </div>
                <span class="font-bold text-slate-900 dark:text-white tracking-tight">
                    Prefeitura<span class="text-slate-400 font-normal">Connect</span>
                </span>
            </div>
            <button x-on:click="sidebarOpen = false" class="text-slate-400 transition-colors p-2">
                <i class="ph ph-x text-lg"></i>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-3 space-y-6 custom-scrollbar">
            <div>
                <h3 class="px-3 text-[10px] font-bold uppercase text-slate-500 mb-2 tracking-wider">Geral</h3>
                <nav class="space-y-0.5">
                    <a href="{{ route('secretariats.ods', $secretariat) }}"
                        wire:navigate
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('secretariats.ods') ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="ph-duotone ph-clipboard-text text-lg"></i>
                        Ordens de Serviço
                    </a>

                    <a href="{{ route('dashboard') }}"
                        wire:navigate
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="ph-duotone ph-chart-pie-slice text-lg"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('profile') }}"
                        wire:navigate
                        class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('profile') ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                        <i class="ph-duotone ph-user-circle text-lg"></i>
                        Perfil
                    </a>
                </nav>
            </div>

            @if (auth()->user()->isAdmin())
                <div>
                    <h3 class="px-3 text-[10px] font-bold uppercase text-slate-500 mb-2 tracking-wider">Administração</h3>
                    <nav class="space-y-0.5">
                        <button type="button"
                            x-on:click="adminOpen = !adminOpen"
                            class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-md text-slate-600 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white transition-colors group">
                            <span class="flex items-center gap-3">
                                <i class="ph-duotone ph-bank text-lg"></i>
                                Secretarias
                            </span>
                            <i class="ph ph-caret-down text-slate-400 transition-transform duration-200" :class="adminOpen ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="adminOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 -translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 -translate-y-1"
                            class="space-y-0.5 pt-1">
                            <a href="{{ route('admin.secretariats') }}"
                                wire:navigate
                                class="flex items-center gap-3 px-3 pl-10 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.secretariats') ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                <i class="ph-duotone ph-buildings text-lg opacity-70"></i>
                                Secretarias
                            </a>

                            <a href="{{ route('admin.categories') }}"
                                wire:navigate
                                class="flex items-center gap-3 px-3 pl-10 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('admin.categories') ? 'bg-slate-900 text-white dark:bg-white dark:text-slate-900' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-200/50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white' }}">
                                <i class="ph-duotone ph-tag text-lg opacity-70"></i>
                                Categorias
                            </a>
                        </div>
                    </nav>
                </div>
            @endif
        </div>

        <div class="p-3 border-t border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shrink-0">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/70 p-3">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-sm font-black tracking-widest text-white dark:bg-white dark:text-slate-900">
                        {{ \Illuminate\Support\Str::of(auth()->user()->name)->explode(' ')->take(2)->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))->implode('') }}
                    </div>
                    <div class="min-w-0">
                        <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}"
                            x-text="name"
                            x-on:profile-updated.window="name = $event.detail.name"
                            class="text-sm font-semibold text-slate-900 dark:text-white truncate"></div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <button type="button" wire:click="logout"
                    class="mt-3 w-full rounded-xl bg-slate-900 px-3 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100">
                    Sair
                </button>
            </div>
        </div>
    </aside>

    <header class="bg-slate-900 border-b border-slate-800 px-6 py-4 flex flex-col md:flex-row items-center justify-between z-20 shrink-0 gap-3 transition-colors duration-200">
        <div class="flex items-center gap-2 w-full md:w-auto">
            <button x-on:click="sidebarOpen = true" class="h-8 w-8 flex items-center justify-center rounded-md border border-slate-700 hover:bg-slate-800 text-slate-400 hover:text-slate-100 transition-all duration-200 group shrink-0">
                <i class="ph ph-list text-lg"></i>
            </button>
            <div class="flex items-center gap-3">
                <span class="font-bold text-white tracking-tight">
                    {{ $secretariat->name }} <span class="text-slate-400 font-normal">/ {{ auth()->user()->name }}</span>
                </span>
            </div>
        </div>

        <div class="h-8 flex items-center bg-slate-800 rounded-md px-1 border border-slate-700 shadow-inner overflow-x-auto max-w-full transition-colors duration-200">
            <button type="button" wire:click="applyQuickFilter('total')"
                class="flex items-center px-2.5 h-full gap-1.5 border-r border-slate-700 whitespace-nowrap transition-colors rounded-md {{ $quickFilter === '' ? 'bg-slate-700/70 text-white' : 'text-slate-300 hover:bg-slate-700/50' }}">
                <i class="ph ph-files text-slate-400 text-sm"></i>
                <span class="text-xs font-medium">Total <span class="font-bold">{{ $summary['total'] }}</span></span>
            </button>
            <button type="button" wire:click="applyQuickFilter('urgent')"
                class="flex items-center px-2.5 h-full gap-1.5 border-r border-slate-700 whitespace-nowrap transition-colors rounded-md {{ $quickFilter === 'urgent' ? 'bg-red-950/40 text-red-300' : 'text-red-400 hover:bg-red-950/20' }}">
                <div class="w-1.5 h-1.5 rounded-full bg-red-500 shadow-[0_0_5px_rgba(239,68,68,0.5)]"></div>
                <span class="text-xs font-medium">Urgentes <span class="font-bold">{{ $summary['urgent'] }}</span></span>
            </button>
            <button type="button" wire:click="applyQuickFilter('overdue')"
                class="flex items-center px-2.5 h-full gap-1.5 border-r border-slate-700 whitespace-nowrap transition-colors rounded-md {{ $quickFilter === 'overdue' ? 'bg-amber-950/40 text-amber-300' : 'text-amber-400 hover:bg-amber-950/20' }}">
                <div class="w-1.5 h-1.5 rounded-full bg-amber-500 shadow-[0_0_5px_rgba(245,158,11,0.5)]"></div>
                <span class="text-xs font-medium">Vencidas <span class="font-bold">{{ $summary['overdue'] }}</span></span>
            </button>
            <button type="button" wire:click="applyQuickFilter('in_progress')"
                class="flex items-center px-2.5 h-full gap-1.5 border-r border-slate-700 whitespace-nowrap transition-colors rounded-md {{ $quickFilter === 'in_progress' ? 'bg-blue-950/40 text-blue-300' : 'text-blue-400 hover:bg-blue-950/20' }}">
                <div class="w-1.5 h-1.5 rounded-full bg-blue-500 shadow-[0_0_5px_rgba(59,130,246,0.5)]"></div>
                <span class="text-xs font-medium">Em And. <span class="font-bold">{{ $summary['in_progress'] }}</span></span>
            </button>
            <button type="button" wire:click="applyQuickFilter('completed')"
                class="flex items-center px-2.5 h-full gap-1.5 whitespace-nowrap transition-colors rounded-md {{ $quickFilter === 'completed' ? 'bg-emerald-950/40 text-emerald-300' : 'text-emerald-400 hover:bg-emerald-950/20' }}">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_5px_rgba(16,185,129,0.5)]"></div>
                <span class="text-xs font-medium">Concluídas <span class="font-bold">{{ $summary['completed'] }}</span></span>
            </button>
        </div>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <div class="relative">
                <button type="button" x-on:click="filterOpen = !filterOpen"
                    class="h-8 w-8 flex items-center justify-center text-slate-400 border border-slate-700 rounded-md bg-slate-900 hover:bg-slate-800 transition-colors"
                    :class="({{ $filterCategoryId !== '' ? 'true' : 'false' }} || {{ $filterStatus !== '' ? 'true' : 'false' }} || {{ $filterUrgent !== '' ? 'true' : 'false' }} || {{ $quickFilter !== '' ? 'true' : 'false' }}) ? 'ring-2 ring-slate-600 text-white' : ''">
                    <i class="ph ph-funnel text-base"></i>
                </button>

                <div x-show="filterOpen"
                    x-transition:enter="ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100"
                    x-transition:leave-end="opacity-0 scale-95"
                    x-on:click.away="filterOpen = false"
                    x-cloak
                    class="absolute right-0 top-10 w-72 rounded-2xl border border-slate-800 bg-slate-900 shadow-2xl p-4 z-30">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <p class="text-sm font-bold text-white">Filtros</p>
                            <p class="text-xs text-slate-400">Refine as ordens exibidas no painel.</p>
                        </div>
                        <button type="button" wire:click="clearFilters" x-on:click="filterOpen = false"
                            class="text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
                            Limpar
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Categoria</label>
                            <select wire:model.live="filterCategoryId"
                                class="w-full h-10 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm outline-none cursor-pointer appearance-none text-slate-900 dark:text-slate-100">
                                <option value="">Todas</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Status</label>
                            <select wire:model.live="filterStatus"
                                class="w-full h-10 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm outline-none cursor-pointer appearance-none text-slate-900 dark:text-slate-100">
                                <option value="">Todos</option>
                                @foreach($statusOptions as $statusOption)
                                    <option value="{{ $statusOption->value }}">{{ $statusOption->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-wide text-slate-500 dark:text-slate-400 mb-1">Urgência</label>
                            <select wire:model.live="filterUrgent"
                                class="w-full h-10 px-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm outline-none cursor-pointer appearance-none text-slate-900 dark:text-slate-100">
                                <option value="">Todas</option>
                                <option value="1">Somente urgentes</option>
                                <option value="0">Somente não urgentes</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="relative flex-1 md:w-56 h-8">
                <i class="ph ph-magnifying-glass absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none text-sm"></i>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar..." 
                    class="w-full h-full pl-8 pr-3 text-xs border border-slate-700 rounded-md bg-slate-800 text-slate-100 focus:ring-2 focus:ring-slate-600 focus:outline-none transition-shadow shadow-sm placeholder-slate-500">
            </div>
            <button x-on:click="$wire.resetForm(); modalOpen = true; mode = 'create'; modalView = 'details'" 
                class="h-8 flex items-center justify-center gap-1.5 bg-white text-slate-900 px-3 rounded-md font-bold text-xs hover:bg-slate-200 transition-colors shadow-sm whitespace-nowrap">
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

    @if (session()->has('success'))
        <div class="m-6 p-4 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-3">
            <i class="ph-fill ph-check-circle text-2xl"></i>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if ($filterCategoryId !== '' || $filterStatus !== '' || $filterUrgent !== '' || $quickFilter !== '')
        <div class="px-6 pt-4 flex flex-wrap items-center gap-2">
            @if ($quickFilter !== '')
                <span class="inline-flex items-center gap-1 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <i class="ph ph-faders text-sm"></i>
                    {{ match($quickFilter) {
                        'urgent' => 'Urgentes',
                        'overdue' => 'Vencidas',
                        'in_progress' => 'Em andamento',
                        'completed' => 'Concluidas',
                        default => 'Filtro rapido',
                    } }}
                </span>
            @endif

            @if ($filterCategoryId !== '')
                <span class="inline-flex items-center gap-1 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <i class="ph ph-tag text-sm"></i>
                    {{ optional($categories->firstWhere('id', (int) $filterCategoryId))->name ?? 'Categoria' }}
                </span>
            @endif

            @if ($filterStatus !== '')
                <span class="inline-flex items-center gap-1 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <i class="ph ph-clock text-sm"></i>
                    {{ collect($statusOptions)->firstWhere('value', $filterStatus)?->label() ?? 'Status' }}
                </span>
            @endif

            @if ($filterUrgent !== '')
                <span class="inline-flex items-center gap-1 rounded-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                    <i class="ph ph-warning-circle text-sm"></i>
                    {{ $filterUrgent === '1' ? 'Urgentes' : 'Nao urgentes' }}
                </span>
            @endif

            <button type="button" wire:click="clearFilters"
                class="inline-flex items-center gap-1 rounded-full bg-slate-900 dark:bg-white px-3 py-1 text-xs font-semibold text-white dark:text-slate-900">
                Limpar filtros
            </button>
        </div>
    @endif

    <main class="flex-1 overflow-y-auto p-6 pb-32 bg-slate-950 transition-colors duration-200 custom-scrollbar">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            @forelse($serviceOrders as $ods)
                @php
                    $status = $ods->status;
                    $topBorderClass = match ($status) {
                        \App\Domain\ServiceOrders\ServiceOrderStatus::Completed => 'bg-emerald-500',
                        \App\Domain\ServiceOrders\ServiceOrderStatus::InProgress => 'bg-blue-500',
                        default => 'bg-blue-500',
                    };
                    $badgeClass = $ods->is_urgent
                        ? 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 border border-red-100 dark:border-red-900/50'
                        : match ($status) {
                            \App\Domain\ServiceOrders\ServiceOrderStatus::Completed => 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-900/50',
                            \App\Domain\ServiceOrders\ServiceOrderStatus::InProgress => 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/50',
                            default => 'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 border border-amber-100 dark:border-amber-900/50',
                        };
                    $statusIcon = $ods->is_urgent
                        ? 'ph-warning-circle'
                        : match ($status) {
                            \App\Domain\ServiceOrders\ServiceOrderStatus::Completed => 'ph-check-circle',
                            \App\Domain\ServiceOrders\ServiceOrderStatus::InProgress => 'ph-spinner animate-spin-slow',
                            default => 'ph-clock',
                        };
                    $statusLabel = $ods->is_urgent ? 'Urgente' : $status->label();
                @endphp
                <div x-on:click="$wire.edit({{ $ods->id }}, 'details'); modalView = 'details'; mode = 'edit'; modalOpen = true"
                    class="bg-slate-900 rounded-xl shadow-sm border border-slate-800 relative overflow-hidden group hover:shadow-md transition-all duration-200 cursor-pointer">
                    
                    <div class="h-1.5 w-full absolute top-0 {{ $ods->is_urgent ? 'bg-red-500' : $topBorderClass }}"></div>
                    
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <span class="text-lg font-bold text-white">{{ $ods->code }}</span>
                            <span class="bg-slate-800 text-slate-300 text-[10px] font-bold px-2 py-1 rounded border border-slate-700 uppercase">
                                {{ substr($ods->category->name ?? 'S/CAT', 0, 7) }}.
                            </span>
                        </div>
                        
                        <div class="mb-4">
                            <h3 class="text-sm font-semibold text-slate-200 mb-1 line-clamp-1">{{ $ods->title }}</h3>
                            <p class="text-xs text-slate-400 line-clamp-1">{{ $ods->location ?: 'Local não informado' }}</p>
                            <p class="text-xs text-slate-500 mt-1">
                                {{ $ods->due_date ? \Carbon\Carbon::parse($ods->due_date)->format('d/m/Y') : '--/--/----' }}
                            </p>
                        </div>

                        <div class="flex justify-between items-center mt-4">
                            <button type="button" 
                                x-on:click.stop="$wire.edit({{ $ods->id }}, 'checklist'); modalView = 'checklist'; mode = 'edit'; modalOpen = true"
                                class="w-7 h-7 flex items-center justify-center rounded-lg bg-slate-800 text-slate-300 hover:bg-slate-700 transition-colors">
                                <i class="ph ph-list-checks text-lg"></i>
                            </button>

                            <span class="text-[10px] font-bold px-2.5 py-1.5 rounded-full flex items-center gap-1.5 transition-colors {{ $badgeClass }}">
                                <i class="ph {{ $statusIcon }}"></i>
                                {{ $statusLabel }}
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

        @if ($serviceOrders->hasPages())
            <div class="mt-6">
                {{ $serviceOrders->links() }}
            </div>
        @endif
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
        
        <div class="bg-slate-900 w-full max-w-lg rounded-2xl shadow-2xl border border-slate-800 overflow-hidden transform transition-all flex flex-col max-h-[90vh] h-[90vh] md:h-[621px]"
             x-show="modalOpen" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-on:click.away="$wire.closeModal()">
            
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
                    <button type="button" x-on:click="$wire.closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
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

                    @if ($odsId)
                        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950/40 p-4">
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <div>
                                    <p class="text-xs font-bold uppercase text-slate-500 dark:text-slate-400">Status da Execução</p>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">Atualize o andamento da ordem sem depender do salvamento do formulário.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <button type="button"
                                    wire:click="updateStatus({{ (int) $odsId }}, '{{ \App\Domain\ServiceOrders\ServiceOrderStatus::InProgress->value }}')"
                                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition-colors {{ $currentStatus === \App\Domain\ServiceOrders\ServiceOrderStatus::InProgress->value ? 'bg-blue-500 text-white' : 'bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50' }}">
                                    <i class="ph ph-play"></i>
                                    Em Andamento
                                </button>

                                <button type="button"
                                    wire:click="updateStatus({{ (int) $odsId }}, '{{ \App\Domain\ServiceOrders\ServiceOrderStatus::Completed->value }}')"
                                    class="flex items-center justify-center gap-2 rounded-xl px-4 py-3 text-sm font-bold transition-colors {{ $currentStatus === \App\Domain\ServiceOrders\ServiceOrderStatus::Completed->value ? 'bg-emerald-500 text-white' : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50' }}">
                                    <i class="ph ph-check-circle"></i>
                                    Concluída
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex flex-col h-full overflow-hidden p-6" x-show="modalView === 'checklist'">
                    <div class="flex items-center justify-between gap-3 mb-2 shrink-0">
                        <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase">Checklist de Execução</label>
                        <span class="text-[11px] font-semibold text-slate-400 dark:text-slate-500">
                            {{ count($checklistItems) }} {{ count($checklistItems) === 1 ? 'item' : 'itens' }}
                        </span>
                    </div>

                    <ul class="space-y-2 flex-1 overflow-y-auto custom-scrollbar mb-4 min-h-0">
                        @forelse ($checklistItems as $index => $item)
                            <li wire:key="checklist-item-{{ $index }}"
                                class="flex items-center gap-3 p-2 bg-slate-50 dark:bg-slate-800/50 rounded-lg group border border-transparent hover:border-slate-200 dark:hover:border-slate-700 transition-all">
                                <button type="button"
                                    wire:click="$toggle('checklistItems.{{ $index }}.is_completed')"
                                    class="flex-shrink-0 w-5 h-5 rounded border flex items-center justify-center transition-colors {{ !empty($item['is_completed']) ? 'bg-slate-900 border-slate-900 dark:bg-slate-100 dark:border-slate-100' : 'bg-white border-slate-300 dark:bg-slate-800 dark:border-slate-600' }}">
                                    <i class="ph ph-check text-xs {{ !empty($item['is_completed']) ? 'text-white dark:text-slate-900' : 'hidden' }}"></i>
                                </button>

                                <span class="text-sm flex-1 truncate {{ !empty($item['is_completed']) ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-700 dark:text-slate-200' }}">
                                    {{ $item['label'] }}
                                </span>

                                <button type="button"
                                    wire:click="removeChecklistItem({{ $index }})"
                                    class="text-slate-400 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100 focus:opacity-100">
                                    <i class="ph ph-trash text-lg"></i>
                                </button>
                            </li>
                        @empty
                            <li class="flex flex-col items-center justify-center text-center px-6 py-10 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-2xl bg-slate-50/50 dark:bg-slate-950/20">
                                <i class="ph ph-list-plus text-3xl text-slate-300 mb-2"></i>
                                <p class="text-xs text-slate-400 font-medium">Nenhum item adicionado.</p>
                                <p class="text-xs text-slate-400">Cadastre as etapas da execução para acompanhar o andamento.</p>
                            </li>
                        @endforelse
                    </ul>

                    <div class="flex gap-2 shrink-0 pt-2 border-t border-transparent">
                        <input wire:model.live="newChecklistItem" type="text" placeholder="Adicionar etapa..."
                            wire:keydown.enter.prevent="addChecklistItem"
                            class="flex-1 px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-sm focus:ring-2 focus:ring-slate-400 outline-none transition-all text-slate-900 dark:text-slate-100">
                        <button type="button" wire:click="addChecklistItem"
                            class="px-3 py-2 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 rounded-lg transition-colors">
                            <i class="ph ph-plus font-bold"></i>
                        </button>
                    </div>
                </div>

                <div class="px-6 pb-6 pt-0 shrink-0 border-t-0">
                    <div class="flex gap-3">
                        <button type="button" x-on:click="$wire.closeModal()"
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

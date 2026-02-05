<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFO Tracking - Production System</title>
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <!-- Handsontable -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css">
    <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            /* slate-300 */
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
            /* slate-400 */
        }
    </style>
</head>

<body class="bg-gray-100 font-sans text-gray-900 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex-shrink-0 flex flex-col">
        <div class="p-4 border-b border-slate-700">
            <h1 class="text-xl font-bold">FIFO Tracking</h1>
            <p class="text-xs text-slate-400 mb-4">Production System</p>

            <!-- User Profile in Sidebar -->
            <div class="flex items-center gap-3 p-2 bg-slate-800/50 rounded-lg border border-slate-700/50 group">
                <div
                    class="h-9 w-9 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-bold shadow-inner shrink-0">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-sm font-bold text-slate-100 truncate">
                        {{ explode(' ', Auth::user()->name)[0] }}
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider font-semibold">Admin</span>
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="text-slate-500 hover:text-red-400 transition-colors text-xs p-1"
                                title="Sign Out">
                                <i class="fas fa-sign-out-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center px-6 py-2 hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                        <i class="fas fa-chart-line w-6"></i> Dashboard
                    </a>
                </li>
                <li class="px-6 pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase">Kanban</li>

                <li>
                    <a href="{{ route('kanban.index', 'cor') }}"
                        class="flex items-center px-6 py-2 hover:bg-slate-800 {{ request()->is('kanban*') ? 'bg-blue-600 text-white border-l-4 border-blue-300' : 'text-slate-300' }}">
                        <i class="fas fa-columns w-6"></i> Kanban Board
                    </a>
                </li>

                <li>
                    <a href="{{ route('plan.index') }}"
                        class="flex items-center px-6 py-2 hover:bg-slate-800 {{ request()->routeIs('plan.index') ? 'bg-blue-600 text-white border-l-4 border-blue-300' : 'text-slate-300' }}">
                        <i class="fas fa-clipboard-list w-6"></i> Daftar Rencana
                    </a>
                </li>

                <li class="px-6 pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase">Input Departments</li>

                @foreach(['cor', 'netto', 'bubut_od', 'bubut_cnc', 'bor', 'finish'] as $dept)
                    <li>
                        <a href="{{ route('input.index', $dept) }}"
                            class="flex items-center px-6 py-2 hover:bg-slate-800 {{ request()->is('input/' . $dept) ? 'bg-slate-800 text-white border-l-4 border-blue-500' : 'text-slate-300' }}">
                            <i class="fas fa-file-import w-6"></i> Input {{ ucfirst(str_replace('_', ' ', $dept)) }}
                        </a>
                    </li>
                @endforeach

                <li class="px-6 pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase">System</li>

                <li>
                    <a href="{{ route('report.index') }}"
                        class="flex items-center px-6 py-2 hover:bg-slate-800 {{ request()->routeIs('report.*') ? 'bg-blue-600 text-white border-l-4 border-blue-300' : 'text-slate-300' }}">
                        <i class="fas fa-print w-6"></i> Report SPK
                    </a>
                </li>
            </ul>
        </nav>

        <div class="p-4 border-t border-slate-700 text-xs text-center text-slate-500">
            v1.0.0
        </div>
    </aside>

    <!-- Main Content -->
    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden bg-slate-50 relative">

        <!-- Top Navigation Bar -->
        <header
            class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 shadow-sm z-20 shrink-0">
            <!-- Dynamic Top Bar Content (Process Flow) -->
            <div class="flex-1 flex items-center overflow-x-auto no-scrollbar gap-2">
                @yield('top_bar')
            </div>

            <!-- Right Side: Actions -->
            <div class="flex items-center gap-3 shrink-0 ml-2">
                @if(request()->routeIs('kanban.index'))
                    <button onclick="openReorderModal()"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold py-1.5 px-3 rounded shadow-sm flex items-center gap-2 transition-all">
                        <i class="fas fa-sort"></i>
                        <span class="hidden sm:inline">Edit Antrian</span>
                    </button>
                @endif
            </div>
        </header>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto overflow-x-auto p-6 md:p-8 custom-scrollbar">
            @if(session('success'))
                <div
                    class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center shadow-sm">
                    <i class="fas fa-check-circle mr-3 text-emerald-500"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

</body>

</html>
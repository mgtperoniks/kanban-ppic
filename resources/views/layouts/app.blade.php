<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FIFO Tracking - Production System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom scrollbar for Kanban */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1; 
        }
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1; 
            border-radius: 4px;
        }
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8; 
        }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-900 flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white flex-shrink-0 flex flex-col">
        <div class="p-4 border-b border-slate-700">
            <h1 class="text-xl font-bold">FIFO Tracking</h1>
            <p class="text-xs text-slate-400">Production System</p>
        </div>
        
        <nav class="flex-1 overflow-y-auto py-4">
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-2 hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                        <i class="fas fa-chart-line w-6"></i> Dashboard
                    </a>
                </li>
                <li class="px-6 pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase">Kanban Boards</li>
                
                @foreach(['cor', 'netto', 'bubut_od', 'bubut_cnc', 'bor', 'finish'] as $dept)
                <li>
                    <a href="{{ route('kanban.index', $dept) }}" class="flex items-center px-6 py-2 hover:bg-slate-800 {{ request()->is('kanban/'.$dept) ? 'bg-blue-600 text-white border-l-4 border-blue-300' : 'text-slate-300' }}">
                        <i class="fas fa-columns w-6"></i> {{ ucfirst(str_replace('_', ' ', $dept)) }}
                    </a>
                </li>
                @endforeach
                
                <li class="px-6 pt-4 pb-2 text-xs font-semibold text-slate-500 uppercase">Input Departments</li>
                
                @foreach(['cor', 'netto', 'bubut_od', 'bubut_cnc', 'bor', 'finish'] as $dept)
                <li>
                    <a href="{{ route('input.index', $dept) }}" class="flex items-center px-6 py-2 hover:bg-slate-800 {{ request()->is('input/'.$dept) ? 'bg-slate-800 text-white border-l-4 border-blue-500' : 'text-slate-300' }}">
                        <i class="fas fa-file-import w-6"></i> Input {{ ucfirst(str_replace('_', ' ', $dept)) }}
                    </a>
                </li>
                @endforeach
            </ul>
        </nav>
        
        <div class="p-4 border-t border-slate-700 text-xs text-center text-slate-500">
            v1.0.0
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col overflow-hidden">
        @if(session('success'))
            <div class="bg-green-500 text-white px-6 py-2 text-sm font-semibold text-center">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>

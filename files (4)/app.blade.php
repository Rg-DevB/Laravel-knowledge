{{-- ============================================================
     LARAVELKNOW — resources/views/layouts/app.blade.php
     Dark, premium developer-focused layout
     ============================================================ --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'LaravelKnow' }} — Laravel Problem Database</title>
    <meta name="description" content="The collaborative knowledge base for Laravel developers. Find solutions, share fixes, learn from the community.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- Shiki / Prism for syntax highlighting --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,700|dm-sans:400,500,600,700" rel="stylesheet" />
</head>
<body class="bg-[#0a0a0f] text-zinc-100 antialiased font-sans min-h-screen">

    {{-- ── Sidebar layout ──────────────────────────────── --}}
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside class="hidden lg:flex flex-col w-60 border-r border-zinc-800/60 bg-[#0d0d14] fixed h-full z-30">

            {{-- Logo --}}
            <div class="px-5 py-4 border-b border-zinc-800/60">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-rose-500 to-orange-400 flex items-center justify-center shadow-lg shadow-rose-500/20">
                        <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
                        </svg>
                    </div>
                    <span class="font-semibold text-sm text-zinc-100">LaravelKnow</span>
                </a>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">

                @php
                $navItems = [
                    ['route' => 'home',            'label' => 'Home',       'icon' => 'home'],
                    ['route' => 'problems.index',  'label' => 'Problems',   'icon' => 'bug'],
                    ['route' => 'problems.create', 'label' => 'New Issue',  'icon' => 'plus-circle', 'auth' => true],
                    ['route' => 'dashboard',       'label' => 'Dashboard',  'icon' => 'chart', 'auth' => true],
                ];
                @endphp

                @foreach($navItems as $item)
                    @if(empty($item['auth']) || auth()->check())
                    <a href="{{ route($item['route']) }}"
                       wire:navigate
                       @class([
                           'flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-all duration-150',
                           'bg-zinc-800/70 text-zinc-100 font-medium' => request()->routeIs($item['route']),
                           'text-zinc-400 hover:text-zinc-200 hover:bg-zinc-800/40' => !request()->routeIs($item['route']),
                       ])>
                        <x-icon :name="$item['icon']" class="w-4 h-4 flex-shrink-0" />
                        {{ $item['label'] }}
                    </a>
                    @endif
                @endforeach

                {{-- Categories --}}
                <div class="pt-4 pb-1">
                    <p class="px-3 text-xs font-semibold text-zinc-600 uppercase tracking-wider">Ecosystem</p>
                </div>
                @foreach(\App\Models\Category::orderBy('sort_order')->get() as $cat)
                <a href="{{ route('problems.index', ['category' => $cat->slug]) }}"
                   wire:navigate
                   class="flex items-center gap-3 px-3 py-1.5 rounded-lg text-xs text-zinc-500 hover:text-zinc-300 hover:bg-zinc-800/40 transition-all">
                    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: {{ $cat->color }}"></span>
                    {{ $cat->name }}
                </a>
                @endforeach
            </nav>

            {{-- User footer --}}
            <div class="border-t border-zinc-800/60 p-3">
                @auth
                <div class="flex items-center gap-3 px-2 py-2">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name).'&background=1e1e2e&color=a78bfa' }}"
                         class="w-7 h-7 rounded-full ring-1 ring-zinc-700" alt="avatar">
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium text-zinc-200 truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-zinc-500">{{ auth()->user()->reputation }} rep</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-zinc-600 hover:text-zinc-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                            </svg>
                        </button>
                    </form>
                </div>
                @else
                <div class="flex gap-2">
                    <a href="{{ route('login') }}" class="flex-1 text-center text-xs py-1.5 rounded-md border border-zinc-700 text-zinc-400 hover:text-zinc-200 hover:border-zinc-600 transition-all">Sign in</a>
                    <a href="{{ route('register') }}" class="flex-1 text-center text-xs py-1.5 rounded-md bg-rose-500/10 border border-rose-500/30 text-rose-400 hover:bg-rose-500/20 transition-all">Register</a>
                </div>
                @endauth
            </div>
        </aside>

        {{-- Main content area --}}
        <div class="flex-1 lg:ml-60 flex flex-col min-h-screen">

            {{-- Top bar --}}
            <header class="sticky top-0 z-20 border-b border-zinc-800/60 bg-[#0a0a0f]/80 backdrop-blur-xl">
                <div class="flex items-center gap-4 px-4 lg:px-6 h-14">

                    {{-- Mobile menu button --}}
                    <button class="lg:hidden text-zinc-400" x-data @click="$dispatch('toggle-sidebar')">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    {{-- Search bar --}}
                    <div class="flex-1 max-w-xl">
                        <livewire:search.search-bar />
                    </div>

                    {{-- Right actions --}}
                    <div class="flex items-center gap-3 ml-auto">
                        @auth
                        {{-- Notifications --}}
                        <livewire:notifications.notification-bell />

                        {{-- New issue CTA --}}
                        <a href="{{ route('problems.create') }}" wire:navigate
                           class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg bg-rose-500 hover:bg-rose-400 text-white text-xs font-medium transition-all shadow-lg shadow-rose-500/20">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            New Issue
                        </a>
                        @endauth
                    </div>
                </div>
            </header>

            {{-- Flash messages --}}
            @if(session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="mx-4 lg:mx-6 mt-4 flex items-center gap-3 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ session('success') }}
            </div>
            @endif

            {{-- Page content --}}
            <main class="flex-1 px-4 lg:px-6 py-6">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="border-t border-zinc-800/60 px-4 lg:px-6 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-zinc-600">
                    <span>LaravelKnow — Built with ❤️ and Laravel</span>
                    <div class="flex items-center gap-4">
                        <a href="#" class="hover:text-zinc-400 transition-colors">Privacy</a>
                        <a href="#" class="hover:text-zinc-400 transition-colors">Terms</a>
                        <a href="#" class="hover:text-zinc-400 transition-colors">GitHub</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @livewireScripts
</body>
</html>

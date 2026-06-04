<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        <style>
            .auth-image-bg {
                background-image: url('/brain/abb5ea9a-99f2-4ab3-9552-85bec5b71098/auth_illustration_1777270551408.png');
                background-size: cover;
                background-position: center;
            }
        </style>
    </head>
    <body class="min-h-screen bg-[#0a0a0f] antialiased text-zinc-100">
        <div class="relative grid h-screen grid-cols-1 lg:grid-cols-2 overflow-hidden">
            
            {{-- ── Left Side: Form ──────────────────────────────────────── --}}
            <div class="flex items-center justify-center p-8 lg:p-12 bg-[#0a0a0f] relative z-10">
                <div class="w-full max-w-sm space-y-8">
                    <div class="flex flex-col items-center lg:items-start gap-4">
                        <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-xl text-rose-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <x-app-logo-icon class="w-10 h-10 fill-current" />
                            </div>
                            <span class="text-xl font-bold tracking-tight">Knowravel</span>
                        </a>
                    </div>

                    <div class="mt-8">
                        {{ $slot }}
                    </div>

                    <p class="text-xs text-zinc-600">
                        &copy; {{ date('Y') }} Knowravel. Documentation collaborative pour Laravel.
                    </p>
                </div>
            </div>

            {{-- ── Right Side: Image ─────────────────────────────────────── --}}
            <div class="hidden lg:flex relative items-center justify-center auth-image-bg border-l border-zinc-800/60">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0a0a0f] via-transparent to-transparent opacity-60"></div>
                <div class="absolute inset-0 bg-zinc-950/20 backdrop-blur-[2px]"></div>
                
                <div class="relative z-20 p-12 max-w-lg text-center">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-[10px] font-bold uppercase tracking-widest mb-6">
                        Community Driven
                    </div>
                    <h2 class="text-4xl font-bold text-white leading-tight mb-4 tracking-tight">
                        Master Laravel with the <span class="bg-gradient-to-r from-rose-400 to-orange-400 bg-clip-text text-transparent">best community</span>.
                    </h2>
                    <p class="text-zinc-400 text-lg leading-relaxed">
                        Find solutions to complex problems, share your knowledge, and level up your skills every day.
                    </p>
                </div>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>

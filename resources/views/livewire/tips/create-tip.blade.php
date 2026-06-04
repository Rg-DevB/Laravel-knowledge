<div class="max-w-4xl mx-auto py-10 px-4">
    {{-- ── Header ────────────────────────────────────────────────────── --}}
    <div class="mb-12">
        <a href="{{ route('tips.index') }}" wire:navigate class="inline-flex items-center gap-2 text-xs font-black text-zinc-500 hover:text-rose-400 uppercase tracking-widest transition-colors mb-6 group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Retour aux tips
        </a>
        
        <h1 class="text-4xl font-black text-white tracking-tight mb-3">
            Proposer un <span class="bg-gradient-to-r from-rose-400 to-orange-400 bg-clip-text text-transparent">Tip</span>
        </h1>
        <p class="text-zinc-400 font-medium max-w-xl">
            Partage ton expertise et aide d'autres développeurs à franchir le cap du niveau Senior. Un bon tip est concis, illustré et transformateur.
        </p>
    </div>

    {{-- ── Form ──────────────────────────────────────────────────────── --}}
    <form wire:submit="save" class="space-y-8">
        
        {{-- Section: General Info --}}
        <div class="p-8 rounded-3xl border border-zinc-800/60 bg-zinc-900/20 backdrop-blur-xl relative overflow-hidden group">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-rose-500/5 rounded-full blur-[80px]"></div>
            
            <h2 class="text-sm font-black text-zinc-500 uppercase tracking-widest mb-8 flex items-center gap-3">
                <span class="w-6 h-6 rounded-lg bg-zinc-800 flex items-center justify-center text-[10px] text-zinc-400">01</span>
                Informations Générales
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Titre du Tip</label>
                    <input wire:model="title" type="text" placeholder="Ex: N+1 Query — Eager Loading avec with()"
                           class="w-full px-5 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl text-zinc-100 placeholder-zinc-700 outline-none focus:border-rose-500/50 focus:ring-4 focus:ring-rose-500/5 transition-all font-medium">
                    @error('title') <span class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Catégorie</label>
                    <select wire:model="category" 
                            class="w-full px-5 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl text-zinc-100 outline-none focus:border-rose-500/50 transition-all font-medium appearance-none cursor-pointer">
                        <option value="eloquent">Eloquent</option>
                        <option value="performance">Performance</option>
                        <option value="security">Security</option>
                        <option value="livewire">Livewire</option>
                        <option value="architecture">Architecture</option>
                        <option value="blade">Blade</option>
                        <option value="testing">Testing</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Difficulté</label>
                    <select wire:model="difficulty" 
                            class="w-full px-5 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl text-zinc-100 outline-none focus:border-rose-500/50 transition-all font-medium appearance-none cursor-pointer">
                        <option value="easy">Easy (Junior)</option>
                        <option value="medium">Medium (Mid)</option>
                        <option value="hard">Hard (Senior)</option>
                    </select>
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label class="text-xs font-bold text-zinc-400 uppercase tracking-widest ml-1">Philosophie (Pourquoi ça compte ?)</label>
                    <textarea wire:model="why" rows="4" placeholder="Explique l'impact de ce changement sur la performance, la sécurité ou la maintenance..."
                              class="w-full px-5 py-4 bg-zinc-950/50 border border-zinc-800 rounded-2xl text-zinc-100 placeholder-zinc-700 outline-none focus:border-rose-500/50 focus:ring-4 focus:ring-rose-500/5 transition-all font-medium resize-none"></textarea>
                    @error('why') <span class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        {{-- Section: Comparison --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            {{-- Junior Approach --}}
            <div class="p-8 rounded-3xl border border-red-500/10 bg-red-500/[0.02] backdrop-blur-xl relative overflow-hidden">
                <h2 class="text-sm font-black text-red-400/60 uppercase tracking-widest mb-8 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-lg bg-red-500/10 flex items-center justify-center text-[10px] text-red-400">02</span>
                    Approche Junior
                </h2>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest ml-1">Libellé du Problème</label>
                        <input wire:model="junior_label" type="text" placeholder="Ex: Requêtes répétées en boucle"
                               class="w-full px-5 py-3 bg-zinc-950/50 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-700 outline-none focus:border-red-500/30 transition-all text-sm font-medium">
                        @error('junior_label') <span class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest ml-1">Code (Exemple à éviter)</label>
                        <textarea wire:model="junior_code" rows="8" placeholder="Paste code here..."
                                  class="w-full px-5 py-4 bg-[#0d0d14] border border-zinc-800 rounded-2xl text-zinc-400 placeholder-zinc-700 outline-none focus:border-red-500/30 transition-all font-mono text-xs leading-relaxed"></textarea>
                        @error('junior_code') <span class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Senior Approach --}}
            <div class="p-8 rounded-3xl border border-emerald-500/10 bg-emerald-500/[0.02] backdrop-blur-xl relative overflow-hidden">
                <h2 class="text-sm font-black text-emerald-400/60 uppercase tracking-widest mb-8 flex items-center gap-3">
                    <span class="w-6 h-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-[10px] text-emerald-400">03</span>
                    Approche Senior
                </h2>
                
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest ml-1">Libellé de la Solution</label>
                        <input wire:model="senior_label" type="text" placeholder="Ex: Utilisation de with()"
                               class="w-full px-5 py-3 bg-zinc-950/50 border border-zinc-800 rounded-xl text-zinc-100 placeholder-zinc-700 outline-none focus:border-emerald-500/30 transition-all text-sm font-medium">
                        @error('senior_label') <span class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-zinc-500 uppercase tracking-widest ml-1">Code (Bonne pratique)</label>
                        <textarea wire:model="senior_code" rows="8" placeholder="Paste code here..."
                                  class="w-full px-5 py-4 bg-[#0d0d14] border border-zinc-800 rounded-2xl text-zinc-100 placeholder-zinc-700 outline-none focus:border-emerald-500/30 transition-all font-mono text-xs leading-relaxed shadow-[0_0_20px_rgba(16,185,129,0.03)]"></textarea>
                        @error('senior_code') <span class="text-[10px] font-bold text-rose-500 ml-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-between p-8 rounded-3xl border border-zinc-800/60 bg-zinc-900/40">
            <div class="hidden sm:block">
                <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest">Prêt à partager ?</p>
                <p class="text-[10px] text-zinc-600 font-medium">Ton tip aidera des milliers de développeurs.</p>
            </div>
            
            <button type="submit" 
                    class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-rose-500 hover:bg-rose-400 text-white font-black uppercase tracking-widest transition-all shadow-2xl shadow-rose-500/20 flex items-center justify-center gap-3 group">
                Publier le Tip
                <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
            </button>
        </div>
    </form>
</div>

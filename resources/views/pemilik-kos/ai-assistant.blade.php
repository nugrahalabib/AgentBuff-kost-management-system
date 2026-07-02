@extends('layouts.pemilik-kos')

@section('header')
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h2 class="font-bold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                {{ __('AI Business Analyst') }}
            </h2>
            <p class="text-sm text-gray-500 mt-1">Asisten cerdas untuk analisa data keuangan, okupansi, dan operasional kos.</p>
        </div>
        <div id="connectionStatus" class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-500 rounded-full text-xs font-bold ring-1 ring-gray-200">
            <span class="relative flex h-2 w-2">
              <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-400"></span>
            </span>
            Checking...
        </div>
    </div>
@endsection

@section('content')
    <!-- Marked.js for Markdown Rendering -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <div class="flex h-[calc(100vh-180px)] -mt-4 -mx-4 sm:-mx-8 bg-white border-t border-gray-200 shadow-sm overflow-hidden"
        x-data="aiAssistant()">

        <!-- Mobile Sidebar Toggle -->
        <button @click="sidebarOpen = !sidebarOpen" 
            class="lg:hidden fixed top-24 left-4 z-50 p-2 bg-white rounded-lg shadow-lg border border-gray-200">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Sidebar -->
        <div class="w-80 bg-gray-50/50 border-r border-gray-200 flex flex-col flex-shrink-0 backdrop-blur-xl transition-transform duration-300"
            :class="{'hidden lg:flex': !sidebarOpen, 'flex': sidebarOpen}">
            
            <div class="p-4">
                <button @click="resetChat"
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-emerald-200 transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2 group">
                    <svg class="w-5 h-5 transition-transform group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Percakapan Baru</span>
                </button>
            </div>

            <!-- History List -->
            <div class="flex-1 overflow-y-auto px-3 space-y-1 custom-scrollbar">
                <div class="px-2 pb-2">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Riwayat Chat</h3>
                </div>

                <template x-if="sessions.length === 0">
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-3 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-xs text-gray-400 font-medium">Belum ada riwayat.</p>
                    </div>
                </template>

                <template x-for="session in sessions" :key="session.id">
                    <div class="relative group">
                        <!-- Session Item -->
                        <div x-show="renamingId !== session.id"
                            class="w-full text-left p-3 rounded-xl transition-all duration-200 cursor-pointer border group"
                            :class="currentSessionId === session.id 
                                ? 'bg-white border-emerald-100 shadow-sm ring-1 ring-emerald-50' 
                                : 'bg-transparent border-transparent hover:bg-white hover:border-gray-100 hover:shadow-sm'"
                            @click="loadSession(session.id)">

                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 mt-0.5">
                                    <template x-if="currentSessionId === session.id">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-100"></div>
                                    </template>
                                    <template x-if="currentSessionId !== session.id">
                                         <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    </template>
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold truncate transition-colors"
                                        :class="currentSessionId === session.id ? 'text-gray-800' : 'text-gray-600 group-hover:text-gray-900'" 
                                        x-text="session.title"></h4>
                                    <p class="text-[10px] text-gray-400 mt-0.5" x-text="session.time_ago"></p>
                                </div>

                                <!-- Hover Actions -->
                                <div class="opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                                    <button @click.stop="startRename(session)" 
                                        class="p-1 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition" title="Ganti Nama">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                    </button>
                                    <button @click.stop="deleteSession(session.id)" 
                                        class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Rename Input -->
                        <div x-show="renamingId === session.id" 
                            class="p-2 bg-white ring-2 ring-emerald-500 rounded-xl shadow-lg z-10 relative my-1">
                            <input type="text" x-model="renameText" x-ref="renameInput"
                                @keydown.enter="saveRename(session.id)" @keydown.escape="cancelRename()"
                                class="w-full text-xs font-medium bg-gray-50 border-0 rounded-lg focus:ring-0 text-gray-800 placeholder-gray-400 mb-2"
                                placeholder="Nama sesi...">
                            <div class="flex justify-end gap-2">
                                <button @click="cancelRename()" class="px-2 py-1 text-[10px] font-bold text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded">BATAL</button>
                                <button @click="saveRename(session.id)" class="px-2 py-1 text-[10px] font-bold text-emerald-600 hover:bg-emerald-50 rounded">SIMPAN</button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Chat Main Area -->
        <div class="flex-1 flex flex-col bg-slate-50 relative">

            <!-- Chat Scroll Area -->
            <div class="flex-1 overflow-y-auto px-4 sm:px-8 pt-6 pb-40 space-y-6 chat-scrollbar" id="chat-container">

                <!-- Welcome State -->
                <div x-show="messages.length === 0" 
                     x-transition:enter="transition ease-out duration-500"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="max-w-3xl mx-auto mt-8 space-y-8">
                    
                    <div class="text-center space-y-3">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 shadow-lg shadow-emerald-200 mb-2">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Halo! 👋</h2>
                        <p class="text-gray-500 max-w-md mx-auto">Saya siap membantu menganalisa performa bisnis kos Anda. Tanyakan tentang keuangan, okupansi, penyewa, atau operasional.</p>
                    </div>
                
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <button @click="quickAction('Berapa total pemasukan dan pengeluaran bulan ini?')" class="group text-left bg-white hover:bg-emerald-50 border border-gray-200 hover:border-emerald-200 p-4 rounded-2xl transition-all duration-200 shadow-sm hover:shadow-md">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <span class="font-bold text-gray-800 text-sm">Ringkasan Keuangan</span>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">"Berapa pemasukan, pengeluaran, dan laba bersih bulan ini?"</p>
                        </button>
                
                        <button @click="quickAction('Siapa saja penyewa yang nunggak atau mau habis kontraknya?')" class="group text-left bg-white hover:bg-red-50 border border-gray-200 hover:border-red-200 p-4 rounded-2xl transition-all duration-200 shadow-sm hover:shadow-md">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <span class="font-bold text-gray-800 text-sm">Cek Pembayaran</span>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">"Tampilkan penyewa yang nunggak atau kontraknya segera habis."</p>
                        </button>
                        
                        <button @click="quickAction('Berapa okupansi kamar saat ini? Kamar mana saja yang kosong?')" class="group text-left bg-white hover:bg-blue-50 border border-gray-200 hover:border-blue-200 p-4 rounded-2xl transition-all duration-200 shadow-sm hover:shadow-md">
                             <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                                </div>
                                <span class="font-bold text-gray-800 text-sm">Status Kamar</span>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">"Analisa tingkat okupansi dan kamar yang tersedia."</p>
                        </button>

                        <button @click="quickAction('Ada isu maintenance apa yang sedang aktif?')" class="group text-left bg-white hover:bg-amber-50 border border-gray-200 hover:border-amber-200 p-4 rounded-2xl transition-all duration-200 shadow-sm hover:shadow-md">
                             <div class="flex items-center gap-3 mb-2">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                </div>
                                <span class="font-bold text-gray-800 text-sm">Maintenance</span>
                            </div>
                            <p class="text-xs text-gray-500 leading-relaxed">"Cek laporan maintenance yang masih aktif."</p>
                        </button>
                    </div>
                </div>

                <!-- Messages Stream -->
                <template x-for="(msg, index) in messages" :key="index">
                    <div class="flex gap-4 max-w-3xl mx-auto w-full group" 
                         :class="msg.role === 'user' ? 'flex-row-reverse' : ''"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0">

                        <!-- Avatar -->
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm text-[10px] font-bold ring-2 ring-white"
                             :class="msg.role === 'user' ? 'bg-emerald-600 text-white' : 'bg-white text-emerald-600'">
                            <span x-show="msg.role === 'user'">YOU</span>
                            <svg x-show="msg.role === 'assistant'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>

                        <!-- Bubble -->
                        <div class="max-w-[85%]">
                            <div class="rounded-2xl px-5 py-3.5 shadow-sm text-sm leading-relaxed"
                                :class="msg.role === 'user' 
                                    ? 'bg-emerald-600 text-white rounded-tr-none shadow-emerald-200' 
                                    : 'bg-white border border-gray-100 text-gray-700 rounded-tl-none shadow-gray-100'">
                                <div class="prose prose-sm max-w-none" 
                                    :class="msg.role === 'user' ? 'prose-invert' : 'prose-emerald'"
                                    x-html="renderMarkdown(msg.content)"></div>
                            </div>
                            
                            <!-- Timestamp -->
                            <div class="text-[10px] text-gray-400 mt-1 opacity-0 group-hover:opacity-100 transition-opacity"
                                 :class="msg.role === 'user' ? 'text-right' : 'text-left'"
                                 x-text="formatTime(msg.timestamp || msg.created_at)">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Thinking State -->
                <div x-show="isLoading" class="flex gap-4 max-w-3xl mx-auto w-full" 
                     x-transition:enter="transition ease-in duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100">
                    <div class="w-8 h-8 rounded-full bg-white text-emerald-600 flex items-center justify-center flex-shrink-0 shadow-sm ring-2 ring-white">
                        <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div class="bg-white border border-gray-100 px-4 py-3 rounded-2xl rounded-tl-none shadow-sm flex items-center gap-2">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                        <span class="text-xs text-gray-500 font-medium ml-2">Menganalisa data...</span>
                    </div>
                </div>

            </div>

            <!-- Fixed Input Area at Bottom -->
            <div class="sticky bottom-0 left-0 right-0 bg-gradient-to-t from-slate-50 via-slate-50 to-transparent pt-4 pb-4 px-4 sm:px-8 z-20">
                <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl border border-gray-200 p-2"
                     :class="isLoading ? 'opacity-75' : ''">
                    <form @submit.prevent="sendMessage" class="flex items-end gap-2">
                        <div class="flex-1 bg-transparent">
                            <textarea x-model="userInput" 
                                x-ref="messageInput"
                                @keydown="handleKeydown"
                                @input="autoResize"
                                rows="1"
                                class="w-full bg-transparent border-0 focus:ring-0 p-3.5 text-gray-700 placeholder-gray-400 resize-none max-h-32 text-sm"
                                placeholder="Tanyakan seputar bisnis kos... (Shift+Enter untuk baris baru)"
                                style="min-height: 48px;"></textarea>
                        </div>

                        <button type="submit" :disabled="isLoading || !userInput.trim()"
                            class="mb-1 p-2.5 rounded-xl bg-emerald-600 text-white shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:scale-110 active:scale-95 transition-all disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-show="!isLoading">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path>
                            </svg>
                             <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24" x-show="isLoading" x-cloak>
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                <p class="text-center text-[10px] text-gray-400 mt-2 font-medium">AI dapat melakukan kesalahan. Selalu verifikasi data penting.</p>
            </div>

            <!-- Gradient Overlay for Scroll -->
            <div class="absolute top-0 left-0 right-0 h-8 bg-gradient-to-b from-slate-50 to-transparent pointer-events-none z-10"></div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Sidebar scrollbar - thin and subtle */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        
        /* Chat area scrollbar - more visible */
        .chat-scrollbar {
            scrollbar-width: thin;
            scrollbar-color: #10b981 #e5e7eb;
        }
        .chat-scrollbar::-webkit-scrollbar { width: 8px; }
        .chat-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
        .chat-scrollbar::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #10b981, #059669); 
            border-radius: 4px; 
            border: 2px solid #f1f5f9;
        }
        .chat-scrollbar::-webkit-scrollbar-thumb:hover { background: #047857; }
        
        .prose ul { list-style-type: disc !important; padding-left: 1.25em !important; margin-top: 0.5em !important; margin-bottom: 0.5em !important; }
        .prose ol { list-style-type: decimal !important; padding-left: 1.25em !important; margin-top: 0.5em !important; margin-bottom: 0.5em !important; }
        .prose strong { color: inherit !important; font-weight: 700 !important; }
        .prose p { margin-top: 0.5em !important; margin-bottom: 0.5em !important; }
        .prose table { width: 100%; border-collapse: collapse; margin: 0.5em 0; font-size: 0.85em; }
        .prose th, .prose td { border: 1px solid #e5e7eb; padding: 0.5em; text-align: left; }
        .prose th { background: #f9fafb; font-weight: 600; }
        .prose code { background: #f3f4f6; padding: 0.125em 0.25em; border-radius: 0.25em; font-size: 0.9em; }
        .prose pre { background: #1f2937; color: #f9fafb; padding: 1em; border-radius: 0.5em; overflow-x: auto; }
        .prose pre code { background: transparent; padding: 0; }
    </style>

    <script>
        // Check connection on load
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                const res = await fetch("{{ route('owner.ai.health') }}");
                const data = await res.json();
                const statusEl = document.getElementById('connectionStatus');
                if (data.status === 'connected') {
                    statusEl.className = 'flex items-center gap-2 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-full text-xs font-bold ring-1 ring-emerald-100';
                    statusEl.innerHTML = `
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        Online & Ready
                    `;
                } else {
                    statusEl.className = 'flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-700 rounded-full text-xs font-bold ring-1 ring-red-100';
                    statusEl.innerHTML = `<span class="w-2 h-2 rounded-full bg-red-500"></span> API Key Missing`;
                }
            } catch (e) {
                console.error('Health check failed', e);
            }
        });

        function aiAssistant() {
            return {
                userInput: '',
                messages: [],
                sessions: [],
                currentSessionId: null,
                isLoading: false,
                renamingId: null,
                renameText: '',
                sidebarOpen: false,

                async init() {
                    await this.fetchSessions();
                },
                
                async fetchSessions() {
                    try {
                        const res = await fetch("{{ route('owner.ai.sessions') }}");
                        const data = await res.json();
                        if (data.success) {
                            this.sessions = data.sessions;
                        }
                    } catch (e) { console.error("Session fetch error", e); }
                },

                async loadSession(id) {
                    if (this.currentSessionId === id && this.renamingId !== id) return;
                    if (this.renamingId === id) return;

                    this.isLoading = true;
                    this.sidebarOpen = false; // Close sidebar on mobile
                    try {
                        const res = await fetch(`/owner/ai-assistant/sessions/${id}`);
                        const data = await res.json();
                        if (data.success) {
                            this.messages = data.messages;
                            this.currentSessionId = id;
                            this.scrollToBottom();
                        }
                    } catch (e) {
                        console.error("Load session error", e);
                        this.showToast('Gagal memuat sesi', 'error');
                    } finally {
                        this.isLoading = false;
                    }
                },

                startNewChat() {
                    this.currentSessionId = null;
                    this.messages = [];
                    this.userInput = '';
                    this.sidebarOpen = false;
                },

                resetChat() {
                    this.startNewChat();
                },

                quickAction(text) {
                    this.userInput = text;
                    this.sendMessage();
                },

                handleKeydown(e) {
                    // Enter sends, Shift+Enter adds newline
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        this.sendMessage();
                    }
                },

                autoResize(e) {
                    const textarea = e.target;
                    textarea.style.height = 'auto';
                    textarea.style.height = Math.min(textarea.scrollHeight, 128) + 'px';
                },

                async sendMessage() {
                    if (!this.userInput.trim() || this.isLoading) return;
                    
                    const text = this.userInput;
                    this.userInput = '';
                    
                    // Reset textarea height
                    if (this.$refs.messageInput) {
                        this.$refs.messageInput.style.height = '48px';
                    }
                    
                    this.messages.push({ role: 'user', content: text, timestamp: new Date().toISOString() });
                    this.scrollToBottom();
                    this.isLoading = true;

                    try {
                        const response = await fetch("{{ route('owner.ai.chat') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                message: text,
                                session_id: this.currentSessionId
                            })
                        });

                        const data = await response.json();
                        if (data.success) {
                            this.messages.push({ 
                                role: 'assistant', 
                                content: data.response,
                                timestamp: data.timestamp || new Date().toISOString()
                            });
                            if (!this.currentSessionId && data.session_id) {
                                this.currentSessionId = data.session_id;
                                this.fetchSessions();
                            }
                        } else {
                            this.messages.push({ role: 'assistant', content: "⚠️ " + data.message });
                            this.showToast(data.message, 'error');
                        }
                    } catch (error) {
                        this.messages.push({ role: 'assistant', content: "⚠️ Maaf, koneksi terputus. Silakan coba lagi." });
                        this.showToast('Koneksi terputus', 'error');
                    } finally {
                        this.isLoading = false;
                        this.scrollToBottom();
                    }
                },

                // Session Management
                startRename(session) {
                    this.renamingId = session.id;
                    this.renameText = session.title;
                    this.$nextTick(() => { 
                        if (this.$refs.renameInput) this.$refs.renameInput.focus(); 
                    });
                },

                cancelRename() {
                    this.renamingId = null; 
                    this.renameText = '';
                },

                async saveRename(id) {
                    if (!this.renameText.trim()) return;
                    try {
                        await fetch(`/owner/ai-assistant/sessions/${id}`, {
                            method: 'PATCH',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ title: this.renameText })
                        });
                        this.fetchSessions();
                        this.renamingId = null;
                    } catch (e) {
                        this.showToast('Gagal mengganti nama', 'error');
                    }
                },

                async deleteSession(id) {
                    const result = await Swal.fire({
                        title: 'Hapus Percakapan?',
                        text: 'Riwayat chat ini akan dihapus permanen.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#059669', // emerald-600
                        cancelButtonColor: '#dc2626', // red-600
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    });
                    if (!result.isConfirmed) return;
                    
                    try {
                        await fetch(`/owner/ai-assistant/sessions/${id}`, {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                        });
                        if (this.currentSessionId === id) this.startNewChat();
                        this.fetchSessions();
                        sessionStorage.setItem('toastMessage', JSON.stringify({ icon: 'success', title: 'Percakapan dihapus' }));
                        window.location.reload();
                    } catch (e) {
                        sessionStorage.setItem('toastMessage', JSON.stringify({ icon: 'error', title: 'Gagal menghapus' }));
                        window.location.reload();
                    }
                },

                renderMarkdown(text) {
                    if (typeof marked !== 'undefined') {
                        marked.setOptions({
                            breaks: true,
                            gfm: true
                        });
                        return marked.parse(text || '');
                    }
                    return text || '';
                },

                formatTime(timestamp) {
                    if (!timestamp) return '';
                    try {
                        const date = new Date(timestamp);
                        return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    } catch (e) {
                        return '';
                    }
                },

                scrollToBottom() {
                    this.$nextTick(() => {
                        const container = document.getElementById('chat-container');
                        if(container) container.scrollTop = container.scrollHeight;
                    });
                },

                showToast(message, type = 'info') {
                    if (typeof Swal !== 'undefined') {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: type,
                            title: message
                        });
                    }
                }
            }
        }
    </script>
    <script>
    // Show toast after reload if exists
    document.addEventListener('DOMContentLoaded', function() {
        const toast = sessionStorage.getItem('toastMessage');
        if (toast && typeof Swal !== 'undefined') {
            const { icon, title } = JSON.parse(toast);
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({ icon, title });
            sessionStorage.removeItem('toastMessage');
        }
    });
    </script>
@endsection
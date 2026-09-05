<div id="ai_chat_widget" class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[9999] flex flex-col items-end pointer-events-none">
    
    <div id="ai_modal_window" class="hidden pointer-events-auto w-[92vw] xs:w-[420px] sm:w-[480px] h-[75vh] max-h-[580px] bg-white rounded-3xl border border-slate-200/80 shadow-2xl flex flex-col overflow-hidden transition-all duration-300 transform scale-95 opacity-0 origin-bottom-right mb-3">
        
        <div class="p-3.5 sm:p-4 bg-indigo-600 text-white flex items-center justify-between shrink-0 rounded-t-3xl shadow-md transition-all">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-2xl bg-white/20 border border-white/30 backdrop-blur-md flex items-center justify-center text-white font-bold text-base sm:text-lg shadow-inner">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div>
                    <h3 class="font-extrabold text-white text-sm sm:text-base leading-tight flex items-center gap-2">
                        <span>Asisten AI WANNCELL</span>
                        <span class="flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                    </h3>
                    <p class="text-[11px] sm:text-xs text-indigo-100/90 font-medium">Asisten pintar operasional & analitik toko</p>
                </div>
            </div>
            <div class="flex items-center space-x-1">
                <button type="button" onclick="clearFloatingChatHistory()" class="text-indigo-100 hover:text-rose-200 hover:bg-white/10 p-2 rounded-2xl transition cursor-pointer" title="Bersihkan Chat">
                    <i class="fa-solid fa-trash-can text-xs sm:text-sm"></i>
                </button>
                <button type="button" onclick="toggleAiModal()" class="text-indigo-100 hover:text-white hover:bg-white/10 p-2 rounded-2xl transition cursor-pointer" title="Tutup Chat">
                    <i class="fa-solid fa-xmark text-base sm:text-lg"></i>
                </button>
            </div>
        </div>

        <div class="p-2.5 bg-slate-50/80 border-b border-slate-100 flex items-center space-x-2 overflow-x-auto no-scrollbar shrink-0">
            <span class="text-[10px] sm:text-[11px] text-slate-400 font-bold uppercase tracking-wider shrink-0 ml-1">Pintas:</span>
            
            <button type="button" onclick="sendFloatingQuickPrompt('Berapa penjualan hari ini?')" class="bg-white hover:bg-indigo-50 text-slate-700 hover:text-indigo-600 border border-slate-200/80 px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition shadow-sm flex items-center space-x-1.5 cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-chart-line text-indigo-500"></i>
                <span>Penjualan Hari Ini</span>
            </button>
            
            <button type="button" onclick="sendFloatingQuickPrompt('Stok apa yang hampir habis?')" class="bg-white hover:bg-amber-50 text-slate-700 hover:text-amber-600 border border-slate-200/80 px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition shadow-sm flex items-center space-x-1.5 cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-box-open text-amber-500"></i>
                <span>Cek Stok Menipis</span>
            </button>

            <button type="button" onclick="sendFloatingQuickPrompt('Berapa total pengeluaran kasir hari ini?')" class="bg-white hover:bg-rose-50 text-slate-700 hover:text-rose-600 border border-slate-200/80 px-3 py-1.5 rounded-xl text-xs font-bold shrink-0 transition shadow-sm flex items-center space-x-1.5 cursor-pointer whitespace-nowrap">
                <i class="fa-solid fa-wallet text-rose-500"></i>
                <span>Total Pengeluaran</span>
            </button>
        </div>

        <div id="floating_chat_container" class="flex-1 p-3.5 sm:p-4 overflow-y-auto space-y-3.5 bg-slate-50/40 text-xs sm:text-sm">
            
            <div class="flex items-start space-x-2.5">
                <div class="w-8 h-8 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0 shadow-sm mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="max-w-[85%] bg-white p-3 sm:p-3.5 rounded-2xl rounded-tl-none border border-slate-100 text-slate-800 shadow-sm space-y-1">
                    <p class="font-extrabold text-indigo-600 text-xs sm:text-sm">Halo {{ auth()->user()->name ?? 'Kasir' }}! 👋</p>
                    <p class="text-slate-600 leading-relaxed font-medium text-xs sm:text-sm">Saya Asisten Virtual WANNCELL. Ada data penjualan, stok, atau laporan kas toko yang bisa saya bantu cekkan?</p>
                </div>
            </div>

        </div>

        <div id="floating_typing_indicator" class="hidden px-4 py-2 bg-slate-50/50 flex items-center space-x-2.5 shrink-0">
            <div class="w-6 h-6 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-[10px]">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="bg-white border border-slate-200/80 px-3 py-1.5 rounded-2xl text-[11px] text-slate-500 font-medium flex items-center space-x-2 shadow-sm">
                <span>AI sedang membaca database POS</span>
                <span class="flex space-x-1 ml-1">
                    <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce [animation-delay:0.2s]"></span>
                    <span class="w-1.5 h-1.5 bg-indigo-600 rounded-full animate-bounce [animation-delay:0.4s]"></span>
                </span>
            </div>
        </div>

        <div class="p-3 sm:p-4 bg-white border-t border-slate-100 shrink-0">
            <form id="floating_chat_form" onsubmit="submitFloatingChatMessage(event)" class="flex items-center space-x-2">
                <input type="text" id="floating_user_input" onfocus="handleInputFocusTablet()" placeholder="Tanyakan data stok, transaksi..." class="flex-1 bg-slate-50 border border-slate-200/80 rounded-2xl px-3.5 py-2.5 sm:py-3 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white font-medium transition" autocomplete="off">
                
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white px-4 py-2.5 sm:py-3 rounded-2xl font-bold text-xs sm:text-sm transition flex items-center space-x-1.5 shadow-md shadow-indigo-200 shrink-0 cursor-pointer whitespace-nowrap">
                    <span>Kirim</span>
                    <i class="fa-solid fa-paper-plane text-[10px] sm:text-xs"></i>
                </button>
            </form>
        </div>

    </div>

    <button type="button" onclick="toggleAiModal()" class="pointer-events-auto bg-indigo-600 hover:bg-indigo-700 active:scale-90 text-white w-12 h-12 sm:w-14 sm:h-14 rounded-full shadow-2xl shadow-indigo-600/40 flex items-center justify-center text-xl sm:text-2xl transition-all duration-300 border-2 border-white cursor-pointer group">
        <i id="ai_trigger_icon" class="fa-solid fa-robot group-hover:rotate-12 transition-transform"></i>
    </button>

</div>

<script>
    function toggleAiModal() {
        const modal = document.getElementById('ai_modal_window');
        const icon = document.getElementById('ai_trigger_icon');
        const input = document.getElementById('floating_user_input');

        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('scale-95', 'opacity-0');
                modal.classList.add('scale-100', 'opacity-100');
            }, 10);

            icon.classList.remove('fa-robot');
            icon.classList.add('fa-xmark');
        } else {
            modal.classList.remove('scale-100', 'opacity-100');
            modal.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);

            icon.classList.remove('fa-xmark');
            icon.classList.add('fa-robot');
        }
    }

    // FUNGSI KHUSUS TABLET: Auto Scroll saat input diklik/fokus agar tidak tertutup Keyboard Virtual
    function handleInputFocusTablet() {
        setTimeout(() => {
            scrollFloatingBottom();
            const input = document.getElementById('floating_user_input');
            if (input) {
                input.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }, 300);
    }

    const fContainer = document.getElementById('floating_chat_container');
    const fInput = document.getElementById('floating_user_input');
    const fTyping = document.getElementById('floating_typing_indicator');

    function scrollFloatingBottom() {
        if (fContainer) {
            fContainer.scrollTop = fContainer.scrollHeight;
        }
    }

    function sendFloatingQuickPrompt(text) {
        fInput.value = text;
        document.getElementById('floating_chat_form').dispatchEvent(new Event('submit'));
    }

    function clearFloatingChatHistory() {
        fContainer.innerHTML = `
            <div class="flex items-start space-x-2.5">
                <div class="w-8 h-8 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0 shadow-sm mt-0.5">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="max-w-[85%] bg-white p-3.5 rounded-2xl rounded-tl-none border border-slate-100 text-slate-800 shadow-sm space-y-1">
                    <p class="font-extrabold text-indigo-600 text-xs sm:text-sm">Riwayat dibersihkan! 🧹</p>
                    <p class="text-slate-600 leading-relaxed font-medium text-xs sm:text-sm">Ada pertanyaan lain seputar data toko hari ini?</p>
                </div>
            </div>
        `;
    }

    async function submitFloatingChatMessage(e) {
        e.preventDefault();
        const text = fInput.value.trim();
        if (!text) return;

        const timeNow = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        
        const userHtml = `
            <div class="flex items-start justify-end space-x-2">
                <div class="max-w-[85%] bg-indigo-600 text-white p-3 sm:p-3.5 rounded-2xl rounded-tr-none text-xs sm:text-sm shadow-md shadow-indigo-100 space-y-1">
                    <p class="leading-relaxed font-medium">${escapeHtml(text)}</p>
                    <span class="block text-[9px] sm:text-[10px] text-indigo-200 text-right font-mono">${timeNow}</span>
                </div>
            </div>
        `;
        fContainer.insertAdjacentHTML('beforeend', userHtml);
        fInput.value = '';
        scrollFloatingBottom();

        fTyping.classList.remove('hidden');
        scrollFloatingBottom();

        try {
            const response = await fetch("{{ route('ai.chat') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ message: text })
            });

            const data = await response.json();
            fTyping.classList.add('hidden');

            if (data.status === 'success') {
                const aiHtml = `
                    <div class="flex items-start space-x-2.5">
                        <div class="w-8 h-8 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold shrink-0 shadow-sm mt-0.5">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                        <div class="max-w-[85%] bg-white p-3 sm:p-3.5 rounded-2xl rounded-tl-none border border-slate-100 text-slate-800 shadow-sm space-y-1">
                            <div class="text-slate-700 leading-relaxed font-medium text-xs sm:text-sm">
                                ${formatFloatingMarkdown(data.reply)}
                            </div>
                            <span class="block text-[9px] sm:text-[10px] text-gray-400 text-right font-mono">${data.time}</span>
                        </div>
                    </div>
                `;
                fContainer.insertAdjacentHTML('beforeend', aiHtml);
            } else {
                showFloatingError(data.reply || "Gagal menghubungkan ke AI.");
            }
        } catch (error) {
            fTyping.classList.add('hidden');
            showFloatingError("Terjadi kesalahan jaringan.");
        }

        scrollFloatingBottom();
    }

    function showFloatingError(msg) {
        const errorHtml = `
            <div class="flex items-start space-x-2.5">
                <div class="w-8 h-8 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 flex items-center justify-center text-xs font-bold shrink-0 shadow-sm mt-0.5">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="bg-rose-50/60 border border-rose-200/80 text-rose-700 p-3 rounded-2xl rounded-tl-none text-xs font-medium">
                    ${escapeHtml(msg)}
                </div>
            </div>
        `;
        fContainer.insertAdjacentHTML('beforeend', errorHtml);
    }

    function escapeHtml(text) {
        return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }

    function formatFloatingMarkdown(text) {
        let escaped = escapeHtml(text);
        let formatted = escaped.replace(/\*\*(.*?)\*\*/g, '<strong class="text-slate-900 font-extrabold">$1</strong>');
        formatted = formatted.replace(/\n/g, '<br>');
        return formatted;
    }
</script>
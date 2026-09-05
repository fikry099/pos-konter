<div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 flex flex-col md:flex-row items-center justify-between">
    <div class="flex items-center space-x-4 mb-4 md:mb-0">
        @if($activeShift->photo)
            <img src="{{ $activeShift->photo }}" alt="Absensi" class="w-16 h-16 rounded-full object-cover border-2 border-green-500">
        @else
            <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xl">
                {{ strtoupper(substr($activeShift->user->name, 0, 2)) }}
            </div>
        @endif
        <div>
            <div class="flex items-center space-x-2">
                <h3 class="text-lg font-bold text-gray-800">{{ $activeShift->user->name }}</h3>
                <span class="bg-green-100 text-green-700 text-xs px-2.5 py-0.5 rounded-full font-medium">Shift Aktif</span>
            </div>
            <p class="text-xs text-gray-500 mt-1">
                <i class="fa-regular fa-clock mr-1"></i> Dimulai sejak: {{ $activeShift->start_time->format('d M Y, H:i') }} WIB
            </p>
        </div>
    </div>

    <div class="bg-slate-50 px-4 py-3 rounded-lg border border-slate-200 text-right">
        <span class="text-xs text-gray-500 block">Modal Awal Laci</span>
        <span class="text-lg font-bold text-indigo-700">Rp {{ number_format($activeShift->cash_initial, 0, ',', '.') }}</span>
    </div>
</div>
@extends('layouts.app')

@section('content')
<div class="w-full space-y-6">

    <!-- KATALOG PRODUK BERTINGKAT -->
    <div class="w-full">
        @include('pos.partials.catalog')
    </div>

</div>
@endsection

{{-- DENGAN MEMINDAHKAN MODAL KE @push('scripts'), ELEMEN OTOMATIS RENDER DI LUAR MAIN CONTAINER --}}
@push('scripts')

<!-- KERANJANG BELANJA & PAYMENT -->
@include('pos.partials.cart')

<!-- MODAL ADD TO CART -->
@include('pos.partials.modal-cart')

<!-- PUSTAKA WEBCAMJS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/webcamjs/1.0.26/webcam.min.js"></script>

<!-- SCRIPT POS MODULAR -->
<script src="{{ asset('js/pos/catalog-navigation.js') }}"></script>
<script src="{{ asset('js/pos/cart-payment.js') }}"></script>

<script>
    // FUNGSI OPEN/CLOSE FLOATING CART DRAWER
    function toggleCartDrawer() {
        const drawer = document.getElementById('cart_drawer');
        const backdrop = document.getElementById('cart_drawer_backdrop');
        
        const aiWidget = document.getElementById('ai_chat_widget_wrapper') 
                      || document.getElementById('ai_chat_widget') 
                      || document.querySelector('.ai-chat-floating');

        if (!drawer || !backdrop) return;

        if (drawer.classList.contains('translate-x-full')) {
            // BUKA KERANJANG
            drawer.classList.remove('translate-x-full');
            backdrop.classList.remove('hidden');

            // SEMBUNYIKAN ROBOT AI
            if (aiWidget) {
                aiWidget.classList.add('hidden');
            }
        } else {
            // TUTUP KERANJANG
            drawer.classList.add('translate-x-full');
            backdrop.classList.add('hidden');

            // TAMPILKAN KEMBALIAN ROBOT AI
            if (aiWidget) {
                aiWidget.classList.remove('hidden');
            }
        }
    }
</script>
@endpush
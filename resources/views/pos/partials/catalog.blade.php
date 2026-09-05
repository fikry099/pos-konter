<div class="lg:col-span-7 xl:col-span-8 flex flex-col h-[calc(100vh-120px)] space-y-4">
    
    <!-- HEADER & SEARCH BAR -->
    @include('pos.partials.catalog.header-search')

    <!-- AREA NAVIGATION GRID & PRODUCTS -->
    <div class="flex-1 overflow-y-auto pr-1">
        <!-- LEVEL 1: GRID KATEGORI UTAMA -->
        @include('pos.partials.catalog.main-categories')

        <!-- LEVEL 2: GRID BRAND / OPERATOR / BANK / SUB-AKSESORIS -->
        @include('pos.partials.catalog.sub-providers')

        <!-- LEVEL 3: GRID KATALOG PRODUK -->
        @include('pos.partials.catalog.products-grid')
    </div>

</div>
<!-- SWEETALERT2 GLOBAL NOTIFICATION HANDLER (TOAST TOP-RIGHT) -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        
        // Configuration Reusable Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'rounded-2xl shadow-xl border border-slate-100 font-sans p-3',
                title: 'text-xs font-bold text-slate-800'
            },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // 1. Toast Success / Status Login
        @if(session('success') || session('status'))
            Toast.fire({
                icon: 'success',
                title: @json(session('success') ?? session('status'))
            });
        @endif

        // 2. Toast Error Access / General Error
        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: @json(session('error'))
            });
        @endif

        // 3. Toast Warning
        @if(session('warning'))
            Toast.fire({
                icon: 'warning',
                title: @json(session('warning'))
            });
        @endif

        // 4. Toast Validation Errors Form Input
        @if($errors->any())
            Toast.fire({
                icon: 'warning',
                title: @json($errors->first())
            });
        @endif

    });
</script>
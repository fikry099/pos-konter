// Function Popover Profil Toggle
function toggleProfilePopover() {
    const popover = document.getElementById('profile_popover');
    if (!popover) return;

    if (popover.classList.contains('hidden')) {
        popover.classList.remove('hidden');
        setTimeout(() => {
            popover.classList.remove('scale-95', 'opacity-0');
            popover.classList.add('scale-100', 'opacity-100');
        }, 10);
    } else {
        closeProfilePopover();
    }
}

function closeProfilePopover() {
    const popover = document.getElementById('profile_popover');
    if (popover) {
        popover.classList.remove('scale-100', 'opacity-100');
        popover.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            popover.classList.add('hidden');
        }, 200);
    }
}

// Close Popover saat klik di luar area
document.addEventListener('click', function(e) {
    const popover = document.getElementById('profile_popover');
    const profileBtn = e.target.closest('button[onclick="toggleProfilePopover()"]');
    if (popover && !popover.contains(e.target) && !profileBtn) {
        closeProfilePopover();
    }
});

// Modal Controller
function openProfileModal() {
    closeProfilePopover();
    const modal = document.getElementById('profile_modal');
    const card = document.getElementById('profile_modal_card');
    if (!modal || !card) return;

    modal.classList.remove('hidden');
    setTimeout(() => {
        card.classList.remove('scale-95', 'opacity-0');
        card.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeProfileModal() {
    const modal = document.getElementById('profile_modal');
    const card = document.getElementById('profile_modal_card');
    if (!modal || !card) return;

    card.classList.remove('scale-100', 'opacity-100');
    card.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
        const form = document.getElementById('change_password_form');
        if (form) form.reset();
    }, 200);
}

// Submit Form Change Password
async function submitChangePassword(e) {
    e.preventDefault();
    const btn = document.getElementById('btn_save_password');
    const form = e.target;
    const formData = new FormData(form);

    btn.disabled = true;
    btn.innerHTML = `<i class="fa-solid fa-spinner animate-spin"></i> <span>Memproses...</span>`;

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                          document.querySelector('input[name="_token"]')?.value;

        const response = await fetch("/profile/password", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: formData
        });

        const result = await response.json();

        if (response.ok && result.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                confirmButtonColor: '#4f46e5',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs' }
            });
            closeProfileModal();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Update Password',
                text: result.message || 'Periksa kembali data yang dimasukkan.',
                confirmButtonColor: '#e11d48',
                customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs' }
            });
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Kesalahan Sistem',
            text: 'Terjadi masalah pada koneksi jaringan.',
            confirmButtonColor: '#e11d48',
            customClass: { popup: 'rounded-2xl', confirmButton: 'rounded-xl font-bold px-4 py-2 text-xs' }
        });
    } finally {
        btn.disabled = false;
        btn.innerHTML = `<i class="fa-solid fa-shield-halved"></i> <span>Simpan Password Baru</span>`;
    }
}

// Logout Confirmation
function confirmLogout() {
    Swal.fire({
        title: 'Konfirmasi Keluar',
        text: 'Apakah Anda yakin ingin keluar dari aplikasi POS?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Keluar',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-2xl',
            confirmButton: 'rounded-xl font-bold px-4 py-2.5 text-xs',
            cancelButton: 'rounded-xl font-bold px-4 py-2.5 text-xs'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('logout-form');
            if (form) form.submit();
        }
    });
}
// Variable Global Modal Pembayaran
let currentCartTotal = 0;
let selectedPaymentMethod = 'cash';
let rawPayAmount = 0;
let qrisMediaStream = null;
let videoDevices = [];
let currentDeviceIndex = 0;

// ==========================================
// 0. HELPER MODAL CUSTOM SELECT & PEMFORMATAN RUPIAH
// ==========================================
function openSelectModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.remove('hidden');
    }
}

function closeSelectModal(id) {
    const el = document.getElementById(id);
    if (el) {
        el.classList.add('hidden');
    }
}

// ==========================================
// FUNGSI UPDATE SERVER INSTAN DARI MODAL
// ==========================================
function selectServerAndSubmit(url, providerValue, qty, modalId, key, providerLabel) {
    closeSelectModal(modalId);

    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    document.querySelector('input[name="_token"]')?.value || '';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            qty: qty,
            digital_provider: providerValue
        })
    })
    .then(res => res.json())
    .then(data => {
        // Update Teks Tombol Utama di Keranjang
        let displayElem = document.getElementById('provider_display_' + key);
        if (displayElem) {
            displayElem.innerText = providerLabel || providerValue;
        }

        // Update State Warna & Centang pada Opsi Modal
        document.querySelectorAll('.server-option-btn-' + key).forEach(btn => {
            let isSelected = btn.getAttribute('data-provider-val') === providerValue;
            let checkIcon = btn.querySelector('.fa-check');

            if (isSelected) {
                btn.className = 'server-option-btn-' + key + ' w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer bg-indigo-600 text-white shadow-md';
                if (checkIcon) checkIcon.classList.remove('hidden');
            } else {
                btn.className = 'server-option-btn-' + key + ' w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer bg-slate-50 text-slate-700 hover:bg-slate-100';
                if (checkIcon) checkIcon.classList.add('hidden');
            }
        });
    })
    .catch(err => console.error('Gagal update server:', err));
}

// ==========================================
// FUNGSI UPDATE PENJUAL INSTAN DARI MODAL
// ==========================================
function selectSellerAndSubmit(url, staffId, staffName, modalId, key) {
    closeSelectModal(modalId);

    let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                    document.querySelector('input[name="_token"]')?.value || '';

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            served_by_user_id: staffId
        })
    })
    .then(res => {
        if (!res.ok) throw new Error('Gagal update penjual via AJAX');
        return res.json();
    })
    .then(data => {
        // 1. Update Teks Label Penjual di Keranjang
        let displayElem = document.getElementById('seller_display_' + key);
        if (displayElem) {
            displayElem.innerText = staffName;
        }

        // 2. Update Tampilan Tombol Terpilih & Centang di Modal
        document.querySelectorAll('.seller-option-btn-' + key).forEach(btn => {
            let isSelected = btn.getAttribute('data-staff-id') === staffId.toString();
            let checkIcon = btn.querySelector('.fa-check');

            if (isSelected) {
                btn.className = 'seller-option-btn-' + key + ' w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer bg-amber-500 text-white shadow-md';
                if (checkIcon) checkIcon.classList.remove('hidden');
            } else {
                btn.className = 'seller-option-btn-' + key + ' w-full text-left px-4 py-3.5 rounded-2xl text-xs font-black transition-all flex items-center justify-between cursor-pointer bg-slate-50 text-slate-700 hover:bg-slate-100';
                if (checkIcon) checkIcon.classList.add('hidden');
            }
        });
    })
    .catch(err => {
        console.warn('AJAX Penjual gagal, beralih ke form submit fallback...', err);
        
        // FALLBACK FORM SUBMIT
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        let tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = csrfToken;
        form.appendChild(tokenInput);

        let staffInput = document.createElement('input');
        staffInput.type = 'hidden';
        staffInput.name = 'served_by_user_id';
        staffInput.value = staffId;
        form.appendChild(staffInput);

        document.body.appendChild(form);
        form.submit();
    });
}

// Format Teks Input ke Rupiah (Titik Ribuan)
function formatRupiahInput(element) {
    let value = element.value.replace(/[^,\d]/g, '').toString();
    let split = value.split(',');
    let sisa = split[0].length % 3;
    let rupiah = split[0].substr(0, sisa);
    let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }

    rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;
    element.value = rupiah;
}

// Bersihkan Titik Ribuan Sebelum Submit ke Laravel
function prepareCleanNumbers(e) {
    let customPriceInput = document.getElementById('modal_custom_price');
    let adminFeeInput = document.getElementById('modal_admin_fee');

    if (customPriceInput && customPriceInput.value) {
        customPriceInput.value = customPriceInput.value.replace(/[^0-9]/g, '');
    }

    if (adminFeeInput && adminFeeInput.value) {
        adminFeeInput.value = adminFeeInput.value.replace(/[^0-9]/g, '');
    }
}

// ==========================================
// 1. MODAL TAMBAH KE KERANJANG (PRODUK & CUSTOM)
// ==========================================
function openAddToCartModal(product) {
    document.getElementById('modal_product_name').innerText = product.name;
    document.getElementById('modal_product_id').value = product.id;

    let phoneContainer = document.getElementById('phone_field_container');
    let phoneInput = document.getElementById('modal_target_phone');

    let bankContainer = document.getElementById('bank_field_container');
    let accNumberInput = document.getElementById('modal_account_number');
    let accNameInput = document.getElementById('modal_account_name');

    let qtyContainer = document.getElementById('qty_field_container');
    let qtyInput = document.getElementById('modal_quantity');

    let customContainer = document.getElementById('custom_amount_container');
    let isCustomInput = document.getElementById('modal_is_custom_amount');
    let customPriceInput = document.getElementById('modal_custom_price');

    let prodName = product.name.toLowerCase();

    // Reset nilai awal
    phoneInput.value = '';
    accNumberInput.value = '';
    accNameInput.value = '';
    qtyInput.value = 1;

    if (customContainer) customContainer.classList.add('hidden');
    if (isCustomInput) isCustomInput.value = '0';
    if (customPriceInput) customPriceInput.required = false;

    // Logika tampilan field berdasarkan tipe produk
    if (prodName.includes('transfer') || prodName.includes('bank')) {
        bankContainer.classList.remove('hidden');
        phoneContainer.style.display = 'none';
        qtyContainer.classList.add('hidden');

        accNumberInput.required = true;
        accNameInput.required = true;
        phoneInput.required = false;
    } else if (product.type === 'digital') {
        phoneContainer.style.display = 'block';
        bankContainer.classList.add('hidden');
        qtyContainer.classList.add('hidden');

        phoneInput.required = true;
        accNumberInput.required = false;
        accNameInput.required = false;
    } else {
        phoneContainer.style.display = 'none';
        bankContainer.classList.add('hidden');
        qtyContainer.classList.remove('hidden');

        phoneInput.required = false;
        accNumberInput.required = false;
        accNameInput.required = false;
    }

    document.getElementById('cartModal').classList.remove('hidden');
}

function closeAddToCartModal() {
    document.getElementById('cartModal').classList.add('hidden');
}

function openCustomAmountModal(providerParam) {
    let providerName = providerParam || window.selectedProviderTitle || selectedProvider || 'TRANSFER / TOP-UP';
    providerName = providerName.toUpperCase();

    let modalTitle = document.getElementById('modal_product_name');
    if (modalTitle) modalTitle.innerText = providerName + ' (Nominal Bebas)';

    let cartModal = document.getElementById('cartModal');
    let formModal = cartModal ? cartModal.querySelector('form') : null;

    if (formModal) {
        // 1. Set nama bank/wallet ke field service_type
        let serviceTypeInput = formModal.querySelector('input[name="service_type"]');
        if (!serviceTypeInput) {
            serviceTypeInput = document.createElement('input');
            serviceTypeInput.type = 'hidden';
            serviceTypeInput.name = 'service_type';
            formModal.appendChild(serviceTypeInput);
        }
        serviceTypeInput.value = providerName;

        // 2. Set default server PPOB ke field digital_provider
        let hiddenInput = formModal.querySelector('input[name="digital_provider"]');
        if (!hiddenInput) {
            hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'digital_provider';
            formModal.appendChild(hiddenInput);
        }
        hiddenInput.value = 'Propana';
    }

    // Sisanya tetap sama...
    let customProductIdInput = document.getElementById('modal_product_id');
    if (customProductIdInput) customProductIdInput.value = 1;

    let phoneContainer = document.getElementById('phone_field_container');
    let phoneInput = document.getElementById('modal_target_phone');
    let bankContainer = document.getElementById('bank_field_container');
    let accNumberInput = document.getElementById('modal_account_number');
    let accNameInput = document.getElementById('modal_account_name');
    let customContainer = document.getElementById('custom_amount_container');
    let isCustomInput = document.getElementById('modal_is_custom_amount');
    let customPriceInput = document.getElementById('modal_custom_price');

    if (customPriceInput) customPriceInput.value = '';
    if (isCustomInput) isCustomInput.value = '1';
    if (customContainer) customContainer.classList.remove('hidden');

    let isBankType = providerName.includes('BANK') || providerName.includes('BRI') || providerName.includes('BCA') || providerName.includes('MANDIRI') || providerName.includes('BNI');

    if (isBankType) {
        if (bankContainer) bankContainer.classList.remove('hidden');
        if (phoneContainer) phoneContainer.style.display = 'none';
        if (accNumberInput) accNumberInput.required = true;
        if (accNameInput) accNameInput.required = true;
        if (phoneInput) phoneInput.required = false;
    } else {
        if (bankContainer) bankContainer.classList.add('hidden');
        if (phoneContainer) phoneContainer.style.display = 'block';
        if (phoneInput) phoneInput.required = true;
        if (accNumberInput) accNumberInput.required = false;
        if (accNameInput) accNameInput.required = false;
    }

    if (cartModal) cartModal.classList.remove('hidden');
}

// ==========================================
// 2. MODAL POPUP PEMBAYARAN & KAMERA
// ==========================================
function openPaymentModal(totalPrice) {
    currentCartTotal = totalPrice;
    document.getElementById('modal_total_display').innerText = 'Rp ' + totalPrice.toLocaleString('id-ID');
    
    switchModalPayment('cash');
    setModalQuickPay(totalPrice);

    const modal = document.getElementById('payment_modal');
    const modalCard = document.getElementById('payment_modal_card');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modalCard.classList.remove('scale-95', 'opacity-0');
        modalCard.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closePaymentModal() {
    const modal = document.getElementById('payment_modal');
    const modalCard = document.getElementById('payment_modal_card');
    modalCard.classList.remove('scale-100', 'opacity-100');
    modalCard.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);

    stopQrisCamera();
}

/**
 * STRATEGI PENCEGAHAN LOCKOUT KAMERA HP (NATIVE WEBRTC)
 */
async function startQrisCamera() {
    stopQrisCamera();

    await new Promise(resolve => setTimeout(resolve, 200));

    const videoElem = document.getElementById('qris_video');
    const placeholder = document.getElementById('qris_placeholder');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert("Browser Anda tidak mendukung akses kamera.");
        return;
    }

    try {
        if (videoDevices.length === 0) {
            try {
                const initStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                initStream.getTracks().forEach(track => track.stop());
            } catch (e) {}

            const devices = await navigator.mediaDevices.enumerateDevices();
            videoDevices = devices.filter(d => d.kind === 'videoinput');

            let backIndex = videoDevices.findIndex(d => {
                let lbl = d.label.toLowerCase();
                return lbl.includes('back') || lbl.includes('rear') || lbl.includes('environment') || lbl.includes('belakang');
            });

            if (backIndex !== -1) {
                currentDeviceIndex = backIndex;
            }
        }

        let constraints = {};

        if (videoDevices.length > 0 && videoDevices[currentDeviceIndex]) {
            constraints = {
                video: { deviceId: { exact: videoDevices[currentDeviceIndex].deviceId } },
                audio: false
            };
        } else {
            constraints = {
                video: { facingMode: 'environment' },
                audio: false
            };
        }

        let stream;
        try {
            stream = await navigator.mediaDevices.getUserMedia(constraints);
        } catch (errFallback) {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' },
                audio: false
            });
        }

        qrisMediaStream = stream;

        if (videoElem) {
            videoElem.srcObject = stream;
            videoElem.play();
            videoElem.classList.remove('hidden');
        }
        if (placeholder) placeholder.classList.add('hidden');

    } catch (err) {
        console.error("Gagal membuka kamera:", err);
        alert("Gagal mengakses kamera. Silakan periksa izin kamera pada browser Anda.");
    }
}

function toggleQrisCameraFacing() {
    if (videoDevices.length > 1) {
        currentDeviceIndex = (currentDeviceIndex + 1) % videoDevices.length;
    }
    startQrisCamera();
}

function stopQrisCamera() {
    if (qrisMediaStream) {
        qrisMediaStream.getTracks().forEach(track => {
            track.stop();
            if (qrisMediaStream) qrisMediaStream.removeTrack(track);
        });
        qrisMediaStream = null;
    }
    const videoElem = document.getElementById('qris_video');
    if (videoElem) {
        videoElem.srcObject = null;
        videoElem.classList.add('hidden');
    }
    const placeholder = document.getElementById('qris_placeholder');
    if (placeholder) placeholder.classList.remove('hidden');
}

function switchModalPayment(method) {
    selectedPaymentMethod = method;
    const btnCash = document.getElementById('btn_select_cash');
    const btnQris = document.getElementById('btn_select_qris');
    const panelCash = document.getElementById('modal_cash_panel');
    const panelQris = document.getElementById('modal_qris_panel');

    if (method === 'cash') {
        btnCash.className = 'py-2.5 px-3 rounded-xl font-extrabold text-xs flex items-center justify-center space-x-2 transition-all duration-200 cursor-pointer bg-white text-indigo-700 shadow-sm border border-indigo-100';
        btnQris.className = 'py-2.5 px-3 rounded-xl font-extrabold text-xs flex items-center justify-center space-x-2 transition-all duration-200 cursor-pointer text-slate-500 hover:text-slate-700';
        panelCash.classList.remove('hidden');
        panelQris.classList.add('hidden');

        stopQrisCamera();
    } else {
        btnQris.className = 'py-2.5 px-3 rounded-xl font-extrabold text-xs flex items-center justify-center space-x-2 transition-all duration-200 cursor-pointer bg-white text-indigo-700 shadow-sm border border-indigo-100';
        btnCash.className = 'py-2.5 px-3 rounded-xl font-extrabold text-xs flex items-center justify-center space-x-2 transition-all duration-200 cursor-pointer text-slate-500 hover:text-slate-700';
        panelQris.classList.remove('hidden');
        panelCash.classList.add('hidden');

        rawPayAmount = currentCartTotal;

        startQrisCamera();
    }
}

function setModalQuickPay(amount) {
    rawPayAmount = amount;
    document.getElementById('modal_pay_input').value = amount.toLocaleString('id-ID');
    calculateModalChange(amount);
}

function formatModalCurrency(input) {
    let value = input.value.replace(/\D/g, '');
    rawPayAmount = parseInt(value, 10) || 0;
    input.value = rawPayAmount.toLocaleString('id-ID');
    calculateModalChange(rawPayAmount);
}

function calculateModalChange(payAmount) {
    let change = payAmount - currentCartTotal;
    let changeDisplay = document.getElementById('modal_change_display');

    if (!changeDisplay) return;

    if (change >= 0) {
        changeDisplay.innerText = 'Rp ' + change.toLocaleString('id-ID');
        changeDisplay.className = 'font-bold text-emerald-600 text-sm';
    } else {
        changeDisplay.innerText = '- Rp ' + Math.abs(change).toLocaleString('id-ID');
        changeDisplay.className = 'font-bold text-rose-600 text-sm';
    }
}

// ==========================================
// 3. SNAPSHOT BUKTI QRIS & SUBMIT CHECKOUT
// ==========================================
function take_qris_snapshot() {
    const videoElem = document.getElementById('qris_video');
    if (!videoElem || !qrisMediaStream) return;

    const canvas = document.createElement('canvas');
    canvas.width = videoElem.videoWidth || 640;
    canvas.height = videoElem.videoHeight || 480;

    const ctx = canvas.getContext('2d');
    ctx.drawImage(videoElem, 0, 0, canvas.width, canvas.height);

    const data_uri = canvas.toDataURL('image/jpeg', 0.85);

    document.getElementById('qris_result').innerHTML = '<img src="'+data_uri+'" class="w-full h-full object-cover rounded-2xl"/>';
    document.getElementById('form_payment_proof').value = data_uri;

    document.getElementById('qris_camera').classList.add('hidden');
    document.getElementById('qris_result').classList.remove('hidden');
    document.getElementById('btn_snap_qris').classList.add('hidden');
    document.getElementById('btn_reset_qris').classList.remove('hidden');

    stopQrisCamera();
}

function reset_qris_camera() {
    document.getElementById('form_payment_proof').value = '';
    document.getElementById('qris_camera').classList.remove('hidden');
    document.getElementById('qris_result').classList.add('hidden');
    document.getElementById('btn_snap_qris').classList.remove('hidden');
    document.getElementById('btn_reset_qris').classList.add('hidden');

    startQrisCamera();
}

function processFinalCheckout() {
    if (selectedPaymentMethod === 'cash' && rawPayAmount < currentCartTotal) {
        Swal.fire({
            icon: 'warning',
            title: 'Uang Kurang!',
            text: 'Nominal uang tunai kurang dari total tagihan.',
            confirmButtonColor: '#4f46e5',
            customClass: { popup: 'rounded-2xl' }
        });
        return;
    }

    if (selectedPaymentMethod === 'qris' && !document.getElementById('form_payment_proof').value) {
        Swal.fire({
            icon: 'warning',
            title: 'Bukti Belum Diambil!',
            text: 'Silakan ambil foto bukti transfer QRIS terlebih dahulu.',
            confirmButtonColor: '#4f46e5',
            customClass: { popup: 'rounded-2xl' }
        });
        return;
    }

    document.getElementById('form_payment_method').value = selectedPaymentMethod;
    document.getElementById('form_pay_amount').value = rawPayAmount;

    document.getElementById('pos_checkout_form').submit();
}

// ==========================================
// 4. CONFIRMATION SWEETALERT2
// ==========================================
function confirmClearCart() {
    Swal.fire({
        title: 'Kosongkan Keranjang?',
        text: 'Seluruh item transaksi yang ada di keranjang akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Kosongkan!',
        cancelButtonText: 'Batal',
        customClass: {
            container: 'z-[99999]'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = window.clearCartUrl;
        }
    });
}

function confirmRemoveCart(removeUrl, itemName) {
    Swal.fire({
        title: 'Hapus Item?',
        text: 'Hapus "' + itemName + '" dari keranjang?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        customClass: {
            popup: 'rounded-3xl',
            confirmButton: 'rounded-xl text-xs font-bold px-4 py-2.5',
            cancelButton: 'rounded-xl text-xs font-bold px-4 py-2.5'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = removeUrl;
        }
    });
}

// ==========================================
// 5. FUNGSI UPDATE SERVER DIGITAL VIA FORM SUBMIT AUTOMATIC
// ==========================================
function updateDigitalProvider(url, providerValue, qty, key) {
    let csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
    let csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

    if (!csrfToken) {
        let inputToken = document.querySelector('input[name="_token"]');
        if (inputToken) csrfToken = inputToken.value;
    }

    // Kirim Request AJAX Fetch Tanpa Reload Halaman
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            qty: qty,
            digital_provider: providerValue
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Gagal memperbarui server');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            // Update Teks Nama Server pada Tombol Pemicu di Cart secara Instan
            let displayLabel = document.getElementById('provider_display_' + key);
            if (displayLabel) {
                displayLabel.innerText = providerValue;
            }

            // Tampilkan Notifikasi Toast
            if (typeof Swal !== 'undefined') {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'success',
                    title: 'Server diganti ke ' + providerValue
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Gagal memperbarui server provider',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000
            });
        }
    });
}

// Helper Fallback Form Submit
function submitViaFormFallback(url, providerValue, qty, csrfToken) {
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = url;

    let tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = csrfToken || '';
    form.appendChild(tokenInput);

    let qtyInput = document.createElement('input');
    qtyInput.type = 'hidden';
    qtyInput.name = 'qty';
    qtyInput.value = qty || 1;
    form.appendChild(qtyInput);

    let providerInput = document.createElement('input');
    providerInput.type = 'hidden';
    providerInput.name = 'digital_provider';
    providerInput.value = providerValue;
    form.appendChild(providerInput);

    document.body.appendChild(form);
    form.submit();
}
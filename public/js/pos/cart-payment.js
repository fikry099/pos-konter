// Variable Global Modal Pembayaran
let currentCartTotal = 0;
let selectedPaymentMethod = 'cash';
let rawPayAmount = 0;
let qrisMediaStream = null;
let videoDevices = [];
let currentDeviceIndex = 0;

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

function openCustomAmountModal() {
    let providerName = (typeof selectedProvider !== 'undefined' && selectedProvider) ? selectedProvider.toUpperCase() : 'TRANSFER / TOP-UP';
    
    let modalTitle = document.getElementById('modal_product_name');
    if (modalTitle) modalTitle.innerText = providerName + ' (Nominal Bebas)';
    
    let customProductIdInput = document.getElementById('modal_product_id');
    let firstProductItem = document.querySelector('.product-item');

    if (customProductIdInput) {
        let assignedId = 1;
        if (firstProductItem) {
            let matchData = firstProductItem.getAttribute('onclick');
            if (matchData) {
                let idMatch = matchData.match(/"id":(\d+)/);
                if (idMatch && idMatch[1]) {
                    assignedId = idMatch[1];
                }
            }
        }
        customProductIdInput.value = assignedId;
    }

    let phoneContainer = document.getElementById('phone_field_container');
    let phoneInput = document.getElementById('modal_target_phone');
    let bankContainer = document.getElementById('bank_field_container');
    let accNumberInput = document.getElementById('modal_account_number');
    let accNameInput = document.getElementById('modal_account_name');
    let customContainer = document.getElementById('custom_amount_container');
    let qtyContainer = document.getElementById('qty_field_container');
    let isCustomInput = document.getElementById('modal_is_custom_amount');
    let customPriceInput = document.getElementById('modal_custom_price');
    let adminFeeInput = document.getElementById('modal_admin_fee');

    if (customPriceInput) {
        customPriceInput.value = '';
        customPriceInput.required = true;
    }
    if (adminFeeInput) adminFeeInput.value = 2500;
    if (isCustomInput) isCustomInput.value = '1';

    if (customContainer) customContainer.classList.remove('hidden');

    let currentCategory = typeof selectedCategory !== 'undefined' ? selectedCategory : '';
    if (currentCategory === 'bank' || currentCategory === 'transfer-bank' || currentCategory === 'transfer') {
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

    if (qtyContainer) qtyContainer.classList.add('hidden');

    let cartModal = document.getElementById('cartModal');
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
 * STRATEGI PENCEGANHAN LOCKOUT KAMERA HP (NATIVE WEBRTC)
 */
async function startQrisCamera() {
    stopQrisCamera();

    // Jeda 200ms agar hardware kamera HP bebas dari kunci kueri sebelumnya
    await new Promise(resolve => setTimeout(resolve, 200));

    const videoElem = document.getElementById('qris_video');
    const placeholder = document.getElementById('qris_placeholder');

    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        alert("Browser Anda tidak mendukung akses kamera.");
        return;
    }

    try {
        // Ambil pendaftaran seluruh sensor kamera jika belum terbaca
        if (videoDevices.length === 0) {
            try {
                // Trigger awal untuk meminta izin pembacaan sensor
                const initStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                initStream.getTracks().forEach(track => track.stop());
            } catch (e) {}

            const devices = await navigator.mediaDevices.enumerateDevices();
            videoDevices = devices.filter(d => d.kind === 'videoinput');

            // Cari indeks kamera belakang secara otomatis
            let backIndex = videoDevices.findIndex(d => {
                let lbl = d.label.toLowerCase();
                return lbl.includes('back') || lbl.includes('rear') || lbl.includes('environment') || lbl.includes('belakang');
            });

            if (backIndex !== -1) {
                currentDeviceIndex = backIndex;
            }
        }

        let constraints = {};

        // 1. Prioritaskan penggunaan Device ID Spesifik
        if (videoDevices.length > 0 && videoDevices[currentDeviceIndex]) {
            constraints = {
                video: { deviceId: { exact: videoDevices[currentDeviceIndex].deviceId } },
                audio: false
            };
        } else {
            // 2. Fallback constraint facingMode 'environment'
            constraints = {
                video: { facingMode: 'environment' },
                audio: false
            };
        }

        let stream;
        try {
            stream = await navigator.mediaDevices.getUserMedia(constraints);
        } catch (errFallback) {
            // 3. Fallback standar tanpa constraint ketat
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

/**
 * TOGGLE SIRKULASI BALIK KAMERA
 */
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

        // Jalankan Kamera Belakang Secara Otomatis
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
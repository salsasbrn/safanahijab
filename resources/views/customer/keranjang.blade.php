<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Keranjang Belanja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div id="cart-items" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Cart items will be dynamically populated by JavaScript -->
                </div>

                <div id="cart-summary" class="mt-8 text-center" style="display: none;">
                    <h3 class="font-semibold text-lg">Total Keseluruhan:</h3>
                    <p id="total-price" class="text-xl font-bold"></p>
                    <button id="buy-button"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600 transition mt-4">Beli</button>
                </div>

                <p id="empty-cart" class="text-gray-500 text-center" style="display: none;">Keranjang Anda kosong.</p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const cartItems = document.getElementById('cart-items');
                const cartSummary = document.getElementById('cart-summary');
                const totalPriceEl = document.getElementById('total-price');
                const emptyCartEl = document.getElementById('empty-cart');
                const buyButton = document.getElementById('buy-button');

                // Fungsi untuk mendapatkan keranjang dari localStorage
                function getCart() {
                    return JSON.parse(localStorage.getItem('cart') || '[]');
                }

                // Fungsi untuk menyimpan keranjang ke localStorage
                function saveCart(cart) {
                    localStorage.setItem('cart', JSON.stringify(cart));
                }

                // Fungsi untuk menampilkan keranjang
                function renderCart() {
                    const cart = getCart();

                    // Kosongkan konten sebelumnya
                    cartItems.innerHTML = '';

                    if (cart.length === 0) {
                        emptyCartEl.style.display = 'block';
                        cartSummary.style.display = 'none';
                        return;
                    }

                    emptyCartEl.style.display = 'none';
                    cartSummary.style.display = 'block';

                    let totalPrice = 0;
                    cart.forEach((item, index) => {
                        const itemTotal = item.price * item.quantity;
                        totalPrice += itemTotal;

                        const cartItemEl = document.createElement('div');
                        cartItemEl.classList.add('relative', 'bg-white', 'shadow-lg', 'rounded-lg',
                            'overflow-hidden', 'border-t-4', 'border-[#8B4513]', 'hover:shadow-xl',
                            'transition');
                        cartItemEl.innerHTML = `
                        <div class="p-4">
                            <h3 class="text-lg font-bold text-[#8B4513]">${item.name}</h3>
                            <p class="text-gray-500 mt-2">Harga: <span class="font-semibold">Rp ${formatNumber(item.price)}</span></p>
                            <p class="text-gray-500">Jumlah: 
                                <input type="number" value="${item.quantity}" min="1" 
                                    class="quantity-input w-16 text-center border rounded" 
                                    data-index="${index}" />
                            </p>
                            <p class="text-gray-500">Total: <span class="font-semibold">Rp ${formatNumber(itemTotal)}</span></p>
                            <div class="mt-4 flex justify-between">
                                <button class="remove-item bg-red-500 text-white px-3 py-2 rounded hover:bg-red-600 transition" 
                                    data-index="${index}">Hapus</button>
                            </div>
                        </div>
                    `;
                        cartItems.appendChild(cartItemEl);
                    });

                    // Update total price
                    totalPriceEl.textContent = `Rp ${formatNumber(totalPrice)}`;

                    // Tambahkan event listeners
                    document.querySelectorAll('.quantity-input').forEach(input => {
                        input.addEventListener('change', updateQuantity);
                    });

                    document.querySelectorAll('.remove-item').forEach(button => {
                        button.addEventListener('click', removeItem);
                    });
                }

                // Fungsi untuk memformat angka
                function formatNumber(number) {
                    return number.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }

                // Fungsi untuk mengupdate kuantitas
                function updateQuantity(event) {
                    const index = event.target.dataset.index;
                    const newQuantity = parseInt(event.target.value);

                    const cart = getCart();
                    cart[index].quantity = newQuantity;

                    saveCart(cart);
                    renderCart();
                }

                // Fungsi untuk menghapus item
                function removeItem(event) {
                    const index = event.target.dataset.index;

                    const cart = getCart();
                    cart.splice(index, 1);

                    saveCart(cart);
                    renderCart();
                }

                // Event listener untuk tombol beli
                buyButton.addEventListener('click', function() {
                    const cart = getCart();

                    // Kirim data keranjang ke server
                    fetch('{{ route('customer.buy') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                keranjang: cart
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Hapus keranjang setelah berhasil membeli
                                localStorage.removeItem('cart');
                                renderCart();
                                window.location.href = '{{ route('customer.transaksi') }}';
                            } else {
                                // Tampilkan pesan error
                                alert(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Terjadi kesalahan saat melakukan pembelian');
                        });
                });

                // Tambahkan fungsi untuk menambah produk ke keranjang
                window.addToCart = function(productId, quantity = 1) {
                    fetch(`/product/${productId}`, { // Adjust route as needed
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                            }
                        })
                        .then(response => response.json())
                        .then(product => {
                            const cart = getCart();
                            const existingProductIndex = cart.findIndex(item => item.id === product.id);

                            if (existingProductIndex !== -1) {
                                cart[existingProductIndex].quantity += quantity;
                            } else {
                                cart.push({
                                    id: product.id,
                                    name: product.name,
                                    price: product.price,
                                    quantity: quantity
                                });
                            }

                            saveCart(cart);
                            renderCart();
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal menambahkan produk ke keranjang');
                        });
                };

                // Render cart saat halaman dimuat
                renderCart();
            });
        </script>
    @endpush

    <style>
        .bg-brown-200 {
            background-color: #D7B49A;
        }

        .bg-brown-600 {
            background-color: #8B4513;
        }
    </style>
</x-app-layout>

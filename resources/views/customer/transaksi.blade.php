<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Bagian filter dan search tetap sama -->
            <div class="flex justify-between items-center mb-6">
                <form action="{{ route('customer.transaksi') }}" method="GET" class="flex items-center">
                    <select name="status"
                        class="form-select border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-opacity-50"
                        onchange="this.form.submit()">
                        <option value="" disabled selected>Pilih Status</option>
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed
                        </option>
                        <option value="Canceled" {{ request('status') == 'Canceled' ? 'selected' : '' }}>Canceled
                        </option>
                    </select>
                </form>

                <!-- Search by ID -->
                <form action="{{ route('customer.transaksi') }}" method="GET" class="flex items-center">
                    <input type="text" name="search_id" placeholder="Cari oleh ID..."
                        value="{{ request('search_id') ?? '' }}"
                        class="border border-gray-300 rounded-lg shadow-sm focus:ring focus:ring-opacity-50 w-full max-w-xs mr-4">
                    <button type="submit"
                        class="bg-gradient-to-r from-[#A0522D] to-[#8B4513] text-white font-semibold py-2 px-6 rounded-lg shadow-md hover:shadow-xl hover:from-[#C08040] hover:to-[#A0522D] transition duration-300">
                        Cari
                    </button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($orders as $order)
                    <div
                        class="relative bg-white shadow-lg rounded-lg overflow-hidden border-t-4 border-[#8B4513] hover:shadow-xl transition">
                        <div class="p-6">
                            <!-- Status di pojok kanan atas -->
                            <span
                                class="absolute top-4 right-4 
                                {{ $order->status == 'pending' ? 'bg-yellow-400 text-gray-800' : '' }}
                                {{ $order->status == 'completed' ? 'bg-green-400 text-gray-800' : '' }}
                                {{ $order->status == 'canceled' ? 'bg-red-400 text-gray-800' : '' }}
                                rounded-full px-3 py-1 text-sm font-semibold">
                                {{ $order->status }}
                            </span>

                            <!-- Konten Card -->
                            <h3 class="text-lg font-bold text-[#8B4513]">Order ID: {{ $order->id }}</h3>
                            <p class="text-gray-500 mt-2">Total: <span class="font-semibold">Rp
                                    {{ number_format($order->total, 2) }}</span></p>
                            <p class="text-gray-500">Tanggal: <span>{{ $order->created_at->format('d M Y') }}</span>
                            </p>

                            <!-- Tombol Detail -->
                            <button onclick="openModal({{ $order->id }})"
                                class="mt-4 w-full bg-[#8B4513] text-white py-2 rounded-lg hover:bg-[#A0522D] transition duration-300">
                                Lihat Detail
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Detail Order -->
    <div id="orderDetailModal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 w-full">
        <div class="bg-white rounded-lg shadow-xl w-1/2 p-6 relative">
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <h2 class="text-xl font-bold mb-4 text-[#8B4513]">Detail Pesanan</h2>

            <div id="orderDetailContent" class="space-y-2">
                <!-- Konten detail pesanan akan diisi secara dinamis -->
            </div>

            <div class="mt-4 border-t pt-4">
                <p class="font-bold text-right">Total: <span id="modalTotalOrder"></span></p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                function openModal(orderId) {
                    fetch(`/customer/transaksi/detail/${orderId}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            const content = document.getElementById('orderDetailContent');
                            const totalSpan = document.getElementById('modalTotalOrder');

                            // Bersihkan konten sebelumnya
                            content.innerHTML = '';

                            // Tambahkan detail produk
                            data.details.forEach(detail => {
                                const detailItem = document.createElement('div');
                                detailItem.classList.add('flex', 'justify-between', 'border-b', 'pb-2');
                                detailItem.innerHTML = `
                    <div>
                        <p class="font-semibold">${detail.product.name}</p>
                        <p class="text-sm text-gray-500">
                            ${detail.quantity} x Rp ${detail.price.toLocaleString()}
                        </p>
                    </div>
                    <p class="font-semibold">
                        Rp ${(detail.quantity * detail.price).toLocaleString()}
                    </p>
                `;
                                content.appendChild(detailItem);
                            });

                            // Set total order
                            totalSpan.textContent = `Rp ${data.total.toLocaleString()}`;

                            // Tampilkan modal
                            document.getElementById('orderDetailModal').classList.remove('hidden');
                            document.getElementById('orderDetailModal').classList.add('flex');
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Gagal memuat detail pesanan');
                        });
                }

                function closeModal() {
                    document.getElementById('orderDetailModal').classList.remove('flex');
                    document.getElementById('orderDetailModal').classList.add('hidden');
                }

                // Expose functions to global scope
                window.openModal = openModal;
                window.closeModal = closeModal;
            });
        </script>
    @endpush
</x-app-layout>

<x-main-layout>
    <x-main-layout>
        <div class="p-6">
            <!-- Title -->
            <h1 class="text-center font-bold text-3xl">LapTopia PURCHASE ORDER REQUEST</h1>

            <!-- Company Details -->
            <div class="flex justify-start flex-col space-y-1 mt-6">
                <h2 class="font-bold text-2xl">LapTopia</h2>
                <p class="font-semibold">Barangay Jlectronics Street</p>
                <p class="font-semibold">Bacoor, Cavite 4102</p>
                <p class="font-semibold">Date: {{ $purchase->order_date }}</p>
            </div>

            <!-- Supplier Details -->
            @if ($purchase->supplier)
                <div class="flex justify-start flex-col space-y-1 mt-6">
                    <h2 class="font-bold text-2xl">{{ $purchase->supplier->name }}</h2>
                    <p class="font-semibold">{{ $purchase->supplier->address }}</p>
                </div>
            @endif

            <!-- Letter Body -->
            <div class="flex justify-start flex-col space-y-4 mt-10 leading-relaxed">
                <p class="italic">Dear {{ $purchase->supplier->name ?? 'Supplier' }},</p>

                <p>
                    We at <span class="font-bold">LapTopia</span> are pleased to formally request the supply of
                    computer units and related accessories
                    as part of our purchase order. Kindly review the details of our order below:
                </p>

                <!-- Order Details Table -->
                <table class="w-full border border-gray-300 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2 text-left">Product Name</th>
                            <th class="border px-3 py-2 text-left">Quantity</th>
                            <th class="border px-3 py-2 text-left">Unit Price</th>
                            <th class="border px-3 py-2 text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchase->items as $item)
                            <tr>
                                <td class="border px-3 py-2">{{ $item->product->product_name }}</td>
                                <td class="border px-3 py-2">{{ $item->quantity }}</td>
                                <td class="border px-3 py-2">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="border px-3 py-2">
                                    ₱{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <p>
                    We kindly request delivery of the above-mentioned items on or before
                    <span class="font-semibold">{{ $purchase->expected_date }}</span>.
                    Please ensure all products are in good condition and meet our specifications.
                </p>

                <p>
                    Should you have any clarifications, feel free to reach us at
                    <span class="font-semibold">LapTopia@gmail.com</span> or call us at
                    <span class="font-semibold">09123456789</span>.
                </p>

                <p class="mt-6">
                    Thank you for your continued support and partnership. We look forward to your confirmation
                    and a successful transaction.
                </p>

                <p class="mt-10">
                    Sincerely,<br />
                    <span class="font-bold">LapTopia Procurement Team</span>
                </p>
            </div>
        </div>
    </x-main-layout>

</x-main-layout>

<x-main-layout>
    <div style="padding: 24px;">
        <!-- Title -->
        <h1 style="text-align: center; font-weight: bold; font-size: 28px;">
            LapTopia PURCHASE ORDER REQUEST
        </h1>

        <!-- Company Details -->
        <div style="display: flex; flex-direction: column; margin-top: 24px; line-height: 1.6;">
            <h2 style="font-weight: bold; font-size: 24px;">LapTopia</h2>
            <p style="font-weight: 600;">Barangay Jlectronics Street</p>
            <p style="font-weight: 600;">Bacoor, Cavite 4102</p>
            <p style="font-weight: 600;">Date: {{ $purchase->order_date }}</p>
        </div>

        <!-- Supplier Details -->
        @if ($purchase->supplier)
            <div style="display: flex; flex-direction: column; margin-top: 24px; line-height: 1.6;">
                <h2 style="font-weight: bold; font-size: 24px;">{{ $purchase->supplier->name }}</h2>
                <p style="font-weight: 600;">{{ $purchase->supplier->address }}</p>
            </div>
        @endif

        <!-- Letter Body -->
        <div style="display: flex; flex-direction: column; margin-top: 40px; line-height: 1.7;">
            <p style="font-style: italic;">
                Dear {{ $purchase->supplier->name ?? 'Supplier' }},
            </p>

            <p>
                We at <span style="font-weight: bold;">LapTopia</span> are pleased to formally request the supply of
                computer units and related accessories as part of our purchase order. Kindly review the details of
                our order below:
            </p>

            <!-- Order Details Table -->
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #d1d5db; font-size: 14px;">
                <thead style="background-color: #f3f4f6;">
                    <tr>
                        <th style="border: 1px solid #d1d5db; padding: 8px 12px; text-align: left;">Product Name</th>
                        <th style="border: 1px solid #d1d5db; padding: 8px 12px; text-align: left;">Quantity</th>
                        <th style="border: 1px solid #d1d5db; padding: 8px 12px; text-align: left;">Unit Price</th>
                        <th style="border: 1px solid #d1d5db; padding: 8px 12px; text-align: left;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        <tr>
                            <td style="border: 1px solid #d1d5db; padding: 8px 12px;">
                                {{ $item->product->product_name }}</td>
                            <td style="border: 1px solid #d1d5db; padding: 8px 12px;">{{ $item->quantity }}</td>
                            <td style="border: 1px solid #d1d5db; padding: 8px 12px;">
                                ₱{{ number_format($item->unit_price, 2) }}</td>
                            <td style="border: 1px solid #d1d5db; padding: 8px 12px;">
                                ₱{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>
                We kindly request delivery of the above-mentioned items on or before
                <span style="font-weight: 600;">{{ $purchase->expected_date }}</span>.
                Please ensure all products are in good condition and meet our specifications.
            </p>

            <p>
                Should you have any clarifications, feel free to reach us at
                <span style="font-weight: 600;">LapTopia@gmail.com</span> or call us at
                <span style="font-weight: 600;">09123456789</span>.
            </p>

            <p style="margin-top: 24px;">
                Thank you for your continued support and partnership. We look forward to your confirmation
                and a successful transaction.
            </p>

            <p style="margin-top: 40px;">
                Sincerely,<br />
                <span style="font-weight: bold;">LapTopia Procurement Team</span>
            </p>

            <!-- New instruction text -->
            <p style="text-align: center; margin-top: 30px; font-weight: 600; color: #374151;">
                Please notify us if you accept our purchase order by clicking the button below.
            </p>

            <!-- Accept Order Button -->
            <div style="display: flex; justify-content: center; margin-top: 12px;">
                <a href="{{ url('/purchase/accept/' . $purchase->id) }}"
                    style="display: inline-block; padding: 10px 20px; background-color: #16a34a; color: white; text-decoration: none; border-radius: 6px;">
                    Accept Order
                </a>
            </div>
        </div>
    </div>
</x-main-layout>

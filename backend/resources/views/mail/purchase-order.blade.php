<x-main-layout>
    <div
        style="max-width: 800px; margin: 0 auto; padding: 40px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1f2937; background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">

        <!-- Header -->
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="font-size: 28px; font-weight: 700; color: #16a34a; letter-spacing: 1px;">
                PURCHASE ORDER REQUEST
            </h1>
            <p style="font-size: 18px; font-weight: 600; color: #111827;">LapTopia</p>
            <p style="color: #4b5563; font-size: 15px;">Barangay Jlectronics Street, Bacoor, Cavite 4102</p>
            <p style="color: #4b5563; font-size: 15px;">Date: <span
                    style="font-weight: 600;">{{ $purchase->order_date }}</span></p>
        </div>

        <!-- Supplier Details -->
        @if ($purchase->supplier)
            <div style="margin-bottom: 30px; line-height: 1.6;">
                <h2 style="font-size: 20px; font-weight: 700;">To:</h2>
                <p style="font-weight: 600; font-size: 16px;">{{ $purchase->supplier->name }}</p>
                <p style="font-size: 15px; color: #4b5563;">{{ $purchase->supplier->address }}</p>
            </div>
        @endif

        <!-- Letter Body -->
        <div style="line-height: 1.8; font-size: 15px;">

            <p style="font-style: italic;">Dear {{ $purchase->supplier->name ?? 'Valued Supplier' }},</p>

            <p>
                We at <strong>LapTopia</strong> would like to formally place a purchase order for computer units and
                related accessories.
                Kindly review the details of our requested items listed below:
            </p>

            <!-- Order Details Table -->
            <table
                style="width: 100%; border-collapse: collapse; margin-top: 20px; margin-bottom: 20px; border: 1px solid #d1d5db; font-size: 14px;">
                <thead style="background-color: #f3f4f6;">
                    <tr>
                        <th style="border: 1px solid #d1d5db; padding: 10px; text-align: left;">Product Name</th>
                        <th style="border: 1px solid #d1d5db; padding: 10px; text-align: left;">Quantity</th>
                        <th style="border: 1px solid #d1d5db; padding: 10px; text-align: left;">Unit Price</th>
                        <th style="border: 1px solid #d1d5db; padding: 10px; text-align: left;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($purchase->items as $item)
                        <tr>
                            <td style="border: 1px solid #d1d5db; padding: 10px;">{{ $item->product->product_name }}
                            </td>
                            <td style="border: 1px solid #d1d5db; padding: 10px;">{{ $item->quantity }}</td>
                            <td style="border: 1px solid #d1d5db; padding: 10px;">
                                ₱{{ number_format($item->unit_price, 2) }}</td>
                            <td style="border: 1px solid #d1d5db; padding: 10px;">
                                ₱{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p>
                We kindly request that the delivery be made on or before
                <strong>{{ $purchase->expected_date }}</strong>. All products must meet the agreed specifications and
                standards of quality.
            </p>

            <p>
                Should you have any questions or require clarification regarding this order, please don’t hesitate to
                contact us at
                <strong>LapTopia@gmail.com</strong> or call <strong>0912-345-6789</strong>.
            </p>

            <p style="margin-top: 24px;">
                We sincerely appreciate your continued partnership and look forward to your prompt confirmation of this
                purchase order.
            </p>

            <p style="margin-top: 40px;">
                Sincerely,<br>
                <span style="font-weight: bold;">LapTopia Procurement Team</span>
            </p>
        </div>

        <!-- Confirmation Buttons -->
        <div style="text-align: center; margin-top: 50px;">
            <p style="font-weight: 600; color: #374151; margin-bottom: 16px;">
                Please confirm your response by selecting one of the options below:
            </p>
            <div style="display: flex; justify-content: center; gap: 16px;">
                <a href="{{ url('/purchase/accept/' . $purchase->id) }}"
                    style="display: inline-block; padding: 10px 20px; background-color: #16a34a; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    Accept Order
                </a>
                <a href="{{ url('/purchase/reject/' . $purchase->id) }}"
                    style="display: inline-block; padding: 10px 20px; background-color: #dc2626; color: #ffffff; text-decoration: none; border-radius: 6px; font-weight: 600;">
                    Reject Order
                </a>
            </div>
        </div>
    </div>
</x-main-layout>

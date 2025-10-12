<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Stock Movements Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
            font-size: 24px;
        }

        p {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th,
        td {
            border: 1px solid #ccc;
        }

        thead {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        th {
            text-align: left;
        }

        td.text-center {
            text-align: center;
        }

        td.text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <h2>Stock Movements Report</h2>
    <p>Date: {{ now()->format('F d, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Reference No</th>
                <th>Supplier</th>
                <th class="text-center">Number of Items</th>
                <th class="text-center">Total Items Quantity</th>
                <th>Order Date</th>
                <th>Expected Arrival</th>
                <th class="text-right">Total Amount</th>
                <th>Status</th>
                <th>Payment Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $purchase)
                @php
                    $totalQuantity = $purchase->items->sum('quantity');
                    $totalAmount = $purchase->items->sum(function ($item) {
                        return $item->quantity * $item->unit_price;
                    });
                @endphp
                <tr>
                    <td>{{ $purchase->reference_no }}</td>
                    <td>{{ $purchase->supplier->supplier_name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $purchase->items->count() }}</td>
                    <td class="text-center">{{ $totalQuantity }}</td>
                    <td>{{ \Carbon\Carbon::parse($purchase->order_date)->format('F d, Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($purchase->expected_date)->format('F d, Y') }}</td>
                    <td class="text-right">{{ number_format($totalAmount, 2) }}</td>
                    <td>{{ ucfirst($purchase->status) }}</td>
                    <td>{{ ucfirst($purchase->payment_status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Low Stock Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 30px;
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

        .summary {
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            justify-content: space-between;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th,
        td {
            padding: 10px;
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

        .low-stock {
            background-color: #ffe5e5;
            /* light red for critical stock */
        }

        .no-data {
            text-align: center;
            margin-top: 50px;
            font-size: 18px;
            color: #888;
        }
    </style>
</head>

<body>
    <h2>Low Stock Report</h2>
    <h2>{{ $filter }} Report</h2>
    <p>Date: {{ now()->format('F d, Y') }}</p>

    @if (count($datas) > 0)
        @php
            $totalItems = count($datas);
            $totalQuantity = $datas->sum('product_quantity');
        @endphp

        <div class="summary">
            <div><strong>Total Low Stock Items:</strong> {{ $totalItems }}</div>
            <div><strong>Total Stock Quantity:</strong> {{ $totalQuantity }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Supplier</th>
                    <th class="text-center">Stock Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($datas as $product)
                    <tr @if ($product->product_quantity <= 5) class="low-stock" @endif>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                        <td>{{ $product->supplier->supplier_name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $product->product_quantity }}</td>
                        <td class="text-right">{{ number_format($product->unit_price, 2) }}</td>
                        <td>
                            @if ($product->product_quantity <= 5)
                                Reorder Soon
                            @else
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">No Out of Stock Items</div>
    @endif

</body>

</html>

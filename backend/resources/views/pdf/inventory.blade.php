<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Inventory Valuation Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 40px;
            color: #333;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        p {
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead {
            background-color: #f4f4f4;
        }

        th {
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #ccc;
        }

        td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        tfoot {
            margin-top: 20px;
        }

        .total {
            text-align: right;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
            color: #222;
        }

        .currency {
            text-align: right;
        }
    </style>
</head>

<body>

    <h2>Inventory Valuation Report</h2>
    <p>Date: {{ now()->format('F d, Y') }}</p>

    @php
        $totalValue = 0;
        foreach ($datas as $product) {
            $totalValue += floatval($product->unit_price * $product->product_quantity);
        }
    @endphp

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Brand</th>
                <th>Category</th>
                <th>Supplier</th>
                <th>Stock</th>
                <th class="currency">Unit Price</th>
                <th class="currency">Total Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $product)
                <tr>
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->brand->brand_name ?? 'N/A' }}</td>
                    <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                    <td>{{ $product->supplier->supplier_name ?? 'N/A' }}</td>
                    <td>{{ $product->product_quantity }}</td>
                    <td class="currency">{{ number_format($product->unit_price, 2) }}</td>
                    <td class="currency">{{ number_format($product->unit_price * $product->product_quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p class="total">
        Total Inventory Value: {{ number_format($totalValue, 2) }}
    </p>

</body>

</html>

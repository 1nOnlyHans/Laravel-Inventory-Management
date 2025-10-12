<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Stock Movements Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            margin: 30px;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
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
            padding: 8px 10px;
            border: 1px solid #ccc;
        }

        thead {
            background-color: #f4f4f4;
        }

        tr:nth-child(even) {
            background-color: #fafafa;
        }

        tfoot {
            margin-top: 20px;
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>Stock Movements Report</h2>
    <p>Date: {{ now()->format('F d, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Action</th>
                <th>Quantity</th>
                <th>Performed By</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $item)
                <tr>
                    <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                    <td>{{ $item->movement_type }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->user->username ?? 'N/A' }}</td>
                    <td>{{ $item->created_at->format('F d, Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>

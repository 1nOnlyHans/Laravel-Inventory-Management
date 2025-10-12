<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Sales Report</title>
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
    </style>
</head>

<body>
    <h2>Sales Report</h2>
    <p>Date: {{ now()->format('F d, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th>Transaction Ref</th>
                <th>Customer</th>
                <th>Payment Method</th>
                <th class="text-right">Total Amount</th>
                <th>Date & Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($datas as $sale)
                <tr>
                    <td>{{ $sale->reference_no }}</td>
                    <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                    <td>{{ $sale->payment_method }}</td>
                    <td class="text-right">{{ number_format($sale->total_amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('F d, Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>

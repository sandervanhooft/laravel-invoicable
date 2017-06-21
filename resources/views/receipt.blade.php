<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <title>Receipt</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #fff;
            background-image: none;
            font-size: 12px;
        }
        address{
            margin-top:15px;
        }
        h2 {
            font-size:28px;
            color:#cccccc;
        }
        .container {
            padding-top:30px;
        }
        .invoice-head td {
            padding: 0 8px;
        }
        .invoice-body{
            background-color:transparent;
        }
        .logo {
            padding-bottom: 10px;
        }
        .table th {
            vertical-align: bottom;
            font-weight: bold;
            padding: 8px;
            line-height: 20px;
            text-align: left;
        }
        .table td {
            padding: 8px;
            line-height: 20px;
            text-align: left;
            vertical-align: top;
            border-top: 1px solid #dddddd;
        }
        .well {
            margin-top: 15px;
        }
    </style>
</head>

<body>
<div class="container">
    <table style="margin-left: auto; margin-right: auto" width="550">
        <tr>
            <td width="160">
                &nbsp;
            </td>

            <!-- Sender information -->
            <td align="right">
                <strong>{{ $invoice->sender_info }}</strong>
            </td>
        </tr>
        <tr valign="top">
            <td style="font-size:28px;color:#cccccc;">
                    Receipt
            </td>

            <!-- Receiver information -->
            <td>
                <br><br>
                <strong>To:</strong> {{ $invoice->receiver_info }}
                <br>
                <strong>Date:</strong> {{ $invoice->created_at }}
            </td>
        </tr>
        <tr valign="top">
            <td>
                <!-- Invoice Info -->
                <p>
                    <strong>Invoice Reference:</strong> {{ $invoice->reference }}<br>
                </p>

                <br><br>

                <!-- Invoice Table -->
                <table width="100%" class="table" border="0">
                    <tr>
                        <th align="left">Description</th>
                        <th align="right">Date</th>
                        <th align="right">Amount</th>
                        <th align="right">Tax %</th>
                    </tr>

                    <!-- Display The Invoice Items -->
                    @foreach ($invoice->lines as $line)
                        <tr>
                            <td colspan="2">{{ $line->description }}</td>
                            <td>{{ $moneyFormatter->format($line->amount) }}</td>
                            <td>{{ $line->tax_percentage * 100 }}%</td>
                        </tr>
                    @endforeach

                    <!-- Display The Final Total -->
                    <tr style="border-top:2px solid #000;">
                        <td>&nbsp;</td>
                        <td style="text-align: right;"><strong>Total</strong></td>
                        <td><strong>{{ $moneyFormatter->format($invoice->total) }}</strong></td>
                    </tr>

                    <!-- Display The Tax specification -->
                    <tr>
                        <td colspan="2">Included tax</td>
                        <td>&nbsp;</td>
                        <td>{{ $moneyFormatter->format($invoice->tax) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="160">
                &nbsp;
            </td>

            <!-- Note -->
            <td align="right">
                <strong>{{ $invoice->note }}</strong>
            </td>
        </tr>
    </table>
</div>
</body>
</html>
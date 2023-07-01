<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            padding: 20px;
        }

        h1 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f5f5f5;
        }

        table tr:nth-child(even) {
            background-color: #fafafa;
        }
    </style>
</head>
<body>

<h1>{{$symbol}} Historical Quotes From {{$startDate}} to {{$endDate}}</h1>

<table>
    <thead>
    <tr>
        <th>Date</th>
        <th>Open</th>
        <th>High</th>
        <th>Low</th>
        <th>Close</th>
        <th>Volume</th>
    </tr>
    </thead>
    <tbody>
    @foreach($quotes as $quote)
        <tr>
            <td>{{$quote['date']}}</td>
            <td>{{$quote['open']}}</td>
            <td>{{$quote['high']}}</td>
            <td>{{$quote['low']}}</td>
            <td>{{$quote['close']}}</td>
            <td>{{$quote['volume']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>

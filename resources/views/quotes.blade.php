@extends('layout')

@section('content')
    <div class="container mx-auto px-4 py-8">

        <div class="grid grid-cols-2 gap-4">
            <h1 class="text-2xl font-bold mb-4">"{{$company->symbol}}" Historical Quotes</h1>

            <div class="flex justify-end">
                <a href="{{url('/')}}" class="bg-blue-500 text-white px-4 py-2 rounded flex items-center">Back to Search</a>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <table class="w-full bg-white border border-gray-300">
                <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Date</th>
                    <th class="py-2 px-4 border-b">Open</th>
                    <th class="py-2 px-4 border-b">High</th>
                    <th class="py-2 px-4 border-b">Low</th>
                    <th class="py-2 px-4 border-b">Close</th>
                    <th class="py-2 px-4 border-b">Volume</th>
                </tr>
                </thead>
                <tbody>
                @foreach($historicalData as $item)
                    <tr>
                        <td class="py-2 px-4 border-b">{{$item['date']}}</td>
                        <td class="py-2 px-4 border-b">{{$item['open']}}</td>
                        <td class="py-2 px-4 border-b">{{$item['high']}}</td>
                        <td class="py-2 px-4 border-b">{{$item['low']}}</td>
                        <td class="py-2 px-4 border-b">{{$item['close']}}</td>
                        <td class="py-2 px-4 border-b">{{$item['volume']}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <div class="w-full">
                <canvas id="chartContainer"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const data = @json($historicalData);
        const labels = data.map(d => d.date);
        const openPrices = data.map(d => d.open);
        const closePrices = data.map(d => d.close);

        const dummyData = {
            labels: ['2023-06-01', '2023-06-02', '2023-06-03', '2023-06-04', '2023-06-05'],
            openPrices: [100, 110, 120, 115, 105],
            closePrices: [105, 115, 125, 120, 110]
        };

        generateChart(labels, openPrices, closePrices);

        function generateChart(labels, openPrices, closePrices) {
            const ctx = document.getElementById('chartContainer').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Open Price',
                        data: openPrices,
                        borderColor: 'blue',
                        backgroundColor: 'rgba(0, 0, 255, 0.1)',
                        fill: true
                    }, {
                        label: 'Close Price',
                        data: closePrices,
                        borderColor: 'green',
                        backgroundColor: 'rgba(0, 255, 0, 0.1)',
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Price'
                            }
                        }
                    }
                }
            });
        }
    </script>
@endpush


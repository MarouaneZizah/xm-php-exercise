@extends('layout')

@push('scripts')
    <script>
        $(document).ready(function() {
            $( ".datepicker" ).datepicker();
        });
    </script>
@endpush

@section('content')
        <div class="w-full max-w-md mx-auto mt-4">
            <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" action="{{url('historical-quotes')}}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="start-date">
                        Start Date
                    </label>
                    <input
                        class="datepicker appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="start-date"
                        name="start-date"
                        type="text"
                        placeholder="YYYY-MM-DD"
                        value="{{old('start-date')}}"
                        required
                    >
                    @error('start-date')
                    <p class="text-red-500 text-xs italic">{{$message}}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="end-date">
                        End Date
                    </label>
                    <input
                        class="datepicker appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="end-date"
                        name="end-date"
                        type="text"
                        placeholder="YYYY-MM-DD"
                        value="{{old('end-date')}}"
                        required
                    >
                    @error('end-date')
                    <p class="text-red-500 text-xs italic">{{$message}}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">
                        Email
                    </label>
                    <input
                        class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="email"
                        name="email"
                        type="email"
                        placeholder="example@example.com"
                        value="{{old('email')}}"
                        required
                    >
                    @error('email')
                    <p class="text-red-500 text-xs italic">{{$message}}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="symbol">
                        Symbol
                    </label>
                    <select
                        class="appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        id="symbol"
                        name="symbol"
                    >
                        <option>Choose a symbol</option>
                        @foreach($companies as $company)
                            <option value="{{$company->symbol}}" @if(old('symbol') === $company->symbol) selected @endif>
                                {{$company->symbol}} - {{$company->name}}
                            </option>
                        @endforeach
                    </select>
                    @error('symbol')
                    <p class="text-red-500 text-xs italic">{{$message}}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <button
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        type="submit"
                    >
                        Get Quotes
                    </button>
                </div>
            </form>
        </div>
@endsection

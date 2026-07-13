<x-app-layout>

<div class="container mt-4">

    <h2>Daftar Negara</h2>

    <a href="{{ route('countries.sync') }}" class="btn btn-primary mb-3">
        Sinkronisasi Data
    </a>

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Negara</th>
                <th>Kode</th>
                <th>Region</th>
                <th>Mata Uang</th>
                <th>Populasi</th>
            </tr>
        </thead>

        <tbody>

        @foreach($countries as $country)

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $country->country_name }}</td>
                <td>{{ $country->country_code }}</td>
                <td>{{ $country->region }}</td>
                <td>{{ $country->currency }}</td>
                <td>{{ number_format($country->population) }}</td>
            </tr>

        @endforeach

        </tbody>
    </table>

</div>

</x-app-layout>
<x-app-layout>

<div class="container mt-4">

    <h2>Economic Data</h2>

    <a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
        Kembali
    </a>

    <table class="table table-bordered table-striped">

        <thead>
            <tr>
                <th>No</th>
                <th>Country</th>
                <th>Year</th>
                <th>GDP</th>
                <th>Inflation</th>
                <th>Exports</th>
                <th>Imports</th>
            </tr>
        </thead>

        <tbody>

        @foreach($economicData as $data)

        <tr>

            <td>{{ $loop->iteration }}</td>

            <td>{{ $data->country->country_name ?? '-' }}</td>

            <td>{{ $data->year }}</td>

            <td>{{ number_format($data->gdp,2) }}</td>

            <td>{{ number_format($data->inflation,2) }}</td>

            <td>{{ number_format($data->exports,2) }}</td>

            <td>{{ number_format($data->imports,2) }}</td>

        </tr>

        @endforeach

        </tbody>

    </table>

</div>

</x-app-layout>
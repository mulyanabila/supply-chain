<x-app-layout>

<div class="max-w-xl mx-auto mt-8 bg-white rounded-xl shadow p-8">

    <h2 class="text-2xl font-bold mb-6">
        Add Country to Watchlist
    </h2>

    <form action="{{ route('watchlist.store') }}" method="POST">

        @csrf

        <div class="mb-6">

            <label class="block mb-2 font-semibold">

                Select Country

            </label>

            <select
                name="country_id"
                class="w-full border rounded-lg p-3">

                @foreach($countries as $country)

                    <option value="{{ $country->id }}">

                        {{ $country->country_name }}

                    </option>

                @endforeach

            </select>

        </div>

        <div class="flex gap-3">

            <button
                class="bg-emerald-600 text-white px-6 py-2 rounded-lg">

                Save

            </button>

            <a href="{{ route('watchlist.index') }}"
               class="bg-gray-300 px-6 py-2 rounded-lg">

                Cancel

            </a>

        </div>

    </form>

</div>

</x-app-layout>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pegawai</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">Tambah Pegawai</h1>

        <div class="bg-white shadow rounded-lg p-6">
            @if ($errors->any())
                <div class="mb-4 bg-red-100 text-red-800 px-4 py-3 rounded-lg">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('employees.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">
                        Nama Pegawai
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                           required>
                </div>

                <div>
                    <label for="phone" class="block font-medium text-sm text-gray-700">
                        Nomor HP
                    </label>
                    <input type="text"
                           name="phone"
                           id="phone"
                           value="{{ old('phone') }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                           placeholder="Contoh: 081234567890">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                        Simpan
                    </button>
                    <a href="{{ route('employees.index') }}"
                       class="px-4 py-2 bg-gray-500 text-white rounded-md">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">Dashboard Admin</h1>

        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-semibold mb-2">Selamat datang</h2>
            <p class="mb-4">Silakan pilih menu pengelolaan data.</p>

            <div class="flex gap-3">
                <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                    Data Pegawai
                </a>

                <a href="{{ route('activity-templates.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-md">
                    Manajemen Form Kegiatan
                </a>
            </div>
        </div>
    </div>
</body>

</html>
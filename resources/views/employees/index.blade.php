<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">Data Pegawai</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <form action="{{ route('employees.index') }}" method="GET" class="flex gap-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama atau nomor HP"
                        class="border border-gray-300 rounded-md px-3 py-2 w-72"
                    >
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                        Cari
                    </button>
                </form>

                <a href="{{ route('employees.create') }}"
                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md">
                    + Tambah Pegawai
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 border text-left">No</th>
                            <th class="px-4 py-3 border text-left">Nama</th>
                            <th class="px-4 py-3 border text-left">Nomor HP</th>
                            <th class="px-4 py-3 border text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($employees as $index => $employee)
                            <tr>
                                <td class="px-4 py-3 border">
                                    {{ $employees->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3 border">{{ $employee->name }}</td>
                                <td class="px-4 py-3 border">{{ $employee->phone ?? '-' }}</td>
                                <td class="px-4 py-3 border text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('employees.edit', $employee) }}"
                                           class="px-3 py-1 bg-yellow-500 text-white rounded-md">
                                            Edit
                                        </a>

                                        <form action="{{ route('employees.destroy', $employee) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="px-3 py-1 bg-red-600 text-white rounded-md">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-4 border text-center text-gray-500">
                                    Belum ada data pegawai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</body>
</html>
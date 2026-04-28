<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grup WhatsApp</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">Data Grup WhatsApp</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex gap-3">
            <a href="{{ route('calendar.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md">
                Dashboard
            </a>
            <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                Data Pegawai
            </a>
            <a href="{{ route('activity-templates.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-md">
                Form Kegiatan
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <form action="{{ route('whatsapp-groups.index') }}" method="GET" class="flex gap-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama grup atau group id"
                        class="border border-gray-300 rounded-md px-3 py-2 w-80"
                    >
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                        Cari
                    </button>
                </form>

                <a href="{{ route('whatsapp-groups.create') }}"
                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md">
                    + Tambah Grup
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 border text-left">No</th>
                            <th class="px-4 py-3 border text-left">Nama Grup</th>
                            <th class="px-4 py-3 border text-left">Group ID</th>
                            <th class="px-4 py-3 border text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($whatsappGroups as $index => $whatsappGroup)
                            <tr>
                                <td class="px-4 py-3 border">{{ $whatsappGroups->firstItem() + $index }}</td>
                                <td class="px-4 py-3 border">{{ $whatsappGroup->name }}</td>
                                <td class="px-4 py-3 border">{{ $whatsappGroup->group_id }}</td>
                                <td class="px-4 py-3 border text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('whatsapp-groups.edit', $whatsappGroup) }}"
                                           class="px-3 py-1 bg-yellow-500 text-white rounded-md">
                                            Edit
                                        </a>

                                        <form action="{{ route('whatsapp-groups.destroy', $whatsappGroup) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus grup ini?')">
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
                                    Belum ada data grup WhatsApp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $whatsappGroups->links() }}
            </div>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Form Kegiatan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">Manajemen Form Kegiatan</h1>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex gap-3">
            <a href="{{ route('calendar.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md">
                Dashboard
            </a>
            <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-slate-600 text-white rounded-md">
                Data Pegawai
            </a>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <form action="{{ route('activity-templates.index') }}" method="GET" class="flex gap-2">
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama kegiatan atau isi pesan"
                        class="border border-gray-300 rounded-md px-3 py-2 w-80"
                    >
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                        Cari
                    </button>
                </form>

                <a href="{{ route('activity-templates.create') }}"
                   class="inline-block px-4 py-2 bg-blue-600 text-white rounded-md">
                    + Tambah Template
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 border text-left">No</th>
                            <th class="px-4 py-3 border text-left">Nama Kegiatan</th>
                            <th class="px-4 py-3 border text-left">Isi Pesan WhatsApp</th>
                            <th class="px-4 py-3 border text-center">Perlu Daftar Pegawai</th>
                            <th class="px-4 py-3 border text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activityTemplates as $index => $activityTemplate)
                            <tr>
                                <td class="px-4 py-3 border">
                                    {{ $activityTemplates->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3 border">{{ $activityTemplate->name }}</td>
                                <td class="px-4 py-3 border whitespace-pre-line">
                                    {{ $activityTemplate->whatsapp_message ?: '-' }}
                                </td>
                                <td class="px-4 py-3 border text-center">
                                    {{ $activityTemplate->needs_employee_list ? 'Ya' : 'Tidak' }}
                                </td>
                                <td class="px-4 py-3 border text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('activity-templates.edit', $activityTemplate) }}"
                                           class="px-3 py-1 bg-yellow-500 text-white rounded-md">
                                            Edit
                                        </a>

                                        <form action="{{ route('activity-templates.destroy', $activityTemplate) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menghapus template ini?')">
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
                                <td colspan="5" class="px-4 py-4 border text-center text-gray-500">
                                    Belum ada template kegiatan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $activityTemplates->links() }}
            </div>
        </div>
    </div>
</body>
</html>
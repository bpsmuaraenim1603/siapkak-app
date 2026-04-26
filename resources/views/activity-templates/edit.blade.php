<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Template Kegiatan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto py-8 px-4">
        <h1 class="text-2xl font-bold mb-6">Edit Template Kegiatan</h1>

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

            <form action="{{ route('activity-templates.update', $activityTemplate) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">
                        Nama Kegiatan
                    </label>
                    <input type="text"
                           name="name"
                           id="name"
                           value="{{ old('name', $activityTemplate->name) }}"
                           class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                           required>
                </div>

                <div>
                    <label for="whatsapp_message" class="block font-medium text-sm text-gray-700">
                        Isi Pesan WhatsApp
                    </label>
                    <textarea
                        name="whatsapp_message"
                        id="whatsapp_message"
                        rows="8"
                        class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2"
                    >{{ old('whatsapp_message', $activityTemplate->whatsapp_message) }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox"
                           name="needs_employee_list"
                           id="needs_employee_list"
                           value="1"
                           {{ old('needs_employee_list', $activityTemplate->needs_employee_list) ? 'checked' : '' }}>
                    <label for="needs_employee_list" class="text-sm text-gray-700">
                        Kegiatan ini perlu daftar pegawai
                    </label>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                        Update
                    </button>
                    <a href="{{ route('activity-templates.index') }}"
                       class="px-4 py-2 bg-gray-500 text-white rounded-md">
                        Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kalender Kegiatan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .external-event {
            padding: 10px 12px;
            margin-bottom: 10px;
            background: #2563eb;
            color: white;
            border-radius: 8px;
            cursor: grab;
            font-size: 14px;
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .modal-backdrop-custom {
            background: rgba(0,0,0,0.4);
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
    <div class="max-w-375 mx-auto py-6 px-4">
        <div class="flex flex-col lg:flex-row gap-6">
            <div class="w-full lg:w-1/4">
                <div class="bg-white rounded-xl shadow p-5 mb-4">
                    <h1 class="text-2xl font-bold mb-2">Dashboard Kalender</h1>
                    <p class="text-sm text-gray-600">Kelola kegiatan, template, dan data pegawai dari satu tempat.</p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm">
                            Data Pegawai
                        </a>
                        <a href="{{ route('activity-templates.index') }}" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm">
                            Form Kegiatan
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-5">
                    <h2 class="text-lg font-semibold mb-3">Daftar Template Kegiatan</h2>
                    <p class="text-sm text-gray-500 mb-4">Bisa didrag ke kalender.</p>

                    <div id="external-events">
                        @forelse ($activityTemplates as $template)
                            <div
                                class="external-event"
                                data-template-id="{{ $template->id }}"
                                data-template-name="{{ $template->name }}"
                            >
                                {{ $template->name }}
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada template kegiatan.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-3/4">
                <div class="bg-white rounded-xl shadow p-5">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <div id="eventModal" class="fixed inset-0 hidden items-center justify-center z-50">
        <div class="absolute inset-0 modal-backdrop-custom"></div>

        <div class="relative bg-white w-full max-w-2xl rounded-xl shadow-xl p-6 z-10">
            <div class="flex items-center justify-between mb-4">
                <h2 id="modalTitle" class="text-xl font-bold">Tambah Event</h2>
                <button type="button" id="closeModalBtn" class="text-gray-500 text-2xl leading-none">&times;</button>
            </div>

            <form id="eventForm" class="space-y-4">
                <input type="hidden" id="event_id" name="event_id">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kegiatan</label>
                    <select id="activity_template_id" name="activity_template_id" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                        <option value="">Pilih kegiatan</option>
                        @foreach ($activityTemplates as $template)
                            <option value="{{ $template->id }}">{{ $template->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Judul Event</label>
                    <input type="text" id="title" name="title" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Kosongkan jika sama dengan nama kegiatan">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" id="event_date" name="event_date" class="w-full border border-gray-300 rounded-md px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Pegawai</label>
                    <select id="employee_ids" name="employee_ids[]" class="w-full border border-gray-300 rounded-md px-3 py-2" multiple size="8">
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">
                                {{ $employee->name }}{{ $employee->phone ? ' - ' . $employee->phone : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Tekan Ctrl atau Cmd untuk memilih lebih dari satu pegawai.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea id="notes" name="notes" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2"></textarea>
                </div>

                <div class="flex flex-wrap justify-between gap-2 pt-2">
                    <button type="button" id="deleteEventBtn" class="px-4 py-2 bg-red-600 text-white rounded-md hidden">
                        Hapus
                    </button>

                    <div class="flex gap-2 ml-auto">
                        <button type="button" id="cancelBtn" class="px-4 py-2 bg-gray-500 text-white rounded-md">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
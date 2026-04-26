<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kalender Kegiatan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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

    <script type="module">
        import { Calendar } from '@fullcalendar/core';
        import dayGridPlugin from '@fullcalendar/daygrid';
        import interactionPlugin, { Draggable } from '@fullcalendar/interaction';

        const calendarEl = document.getElementById('calendar');
        const externalEventsEl = document.getElementById('external-events');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const eventModal = document.getElementById('eventModal');
        const modalTitle = document.getElementById('modalTitle');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        const deleteEventBtn = document.getElementById('deleteEventBtn');
        const eventForm = document.getElementById('eventForm');

        const eventIdInput = document.getElementById('event_id');
        const activityTemplateInput = document.getElementById('activity_template_id');
        const titleInput = document.getElementById('title');
        const eventDateInput = document.getElementById('event_date');
        const employeeIdsInput = document.getElementById('employee_ids');
        const notesInput = document.getElementById('notes');

        let currentCalendarEvent = null;

        if (externalEventsEl) {
            new Draggable(externalEventsEl, {
                itemSelector: '.external-event',
                eventData: function(eventEl) {
                    return {
                        title: eventEl.dataset.templateName,
                        extendedProps: {
                            activity_template_id: eventEl.dataset.templateId,
                        }
                    };
                }
            });
        }

        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            locale: 'id',
            editable: true,
            droppable: true,
            selectable: true,
            height: 'auto',
            events: '{{ route('calendar.events') }}',

            dateClick: function(info) {
                openCreateModal(info.dateStr);
            },

            eventClick: function(info) {
                fetchEvent(info.event.id);
            },

            eventDrop: async function(info) {
                try {
                    const response = await fetch(`/calendar/events/${info.event.id}/date`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            event_date: info.event.startStr,
                        })
                    });

                    if (!response.ok) {
                        throw new Error('Gagal memperbarui tanggal event.');
                    }
                } catch (error) {
                    alert(error.message);
                    info.revert();
                }
            },

            drop: function(info) {
                const templateId = info.draggedEl.dataset.templateId;
                const templateName = info.draggedEl.dataset.templateName;
                openCreateModal(info.dateStr, templateId, templateName);
            }
        });

        calendar.render();

        function openModal() {
            eventModal.classList.remove('hidden');
            eventModal.classList.add('flex');
        }

        function closeModal() {
            eventModal.classList.add('hidden');
            eventModal.classList.remove('flex');
            resetForm();
        }

        function resetForm() {
            currentCalendarEvent = null;
            eventForm.reset();
            eventIdInput.value = '';
            deleteEventBtn.classList.add('hidden');

            Array.from(employeeIdsInput.options).forEach(option => {
                option.selected = false;
            });

            modalTitle.textContent = 'Tambah Event';
        }

        function openCreateModal(dateStr, templateId = '', templateName = '') {
            resetForm();
            modalTitle.textContent = 'Tambah Event';
            eventDateInput.value = dateStr;
            activityTemplateInput.value = templateId;
            titleInput.value = templateName;
            openModal();
        }

        async function fetchEvent(eventId) {
            try {
                const response = await fetch(`/calendar/events/${eventId}`);
                if (!response.ok) {
                    throw new Error('Gagal mengambil data event.');
                }

                const data = await response.json();

                resetForm();
                currentCalendarEvent = data;
                modalTitle.textContent = 'Edit Event';
                deleteEventBtn.classList.remove('hidden');

                eventIdInput.value = data.id;
                activityTemplateInput.value = data.activity_template_id;
                titleInput.value = data.title;
                eventDateInput.value = data.event_date;
                notesInput.value = data.notes ?? '';

                Array.from(employeeIdsInput.options).forEach(option => {
                    option.selected = data.employee_ids.includes(Number(option.value));
                });

                openModal();
            } catch (error) {
                alert(error.message);
            }
        }

        closeModalBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        eventModal.addEventListener('click', function(e) {
            if (e.target === eventModal) {
                closeModal();
            }
        });

        eventForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            const eventId = eventIdInput.value;
            const url = eventId
                ? `/calendar/events/${eventId}`
                : `/calendar/events`;

            const method = eventId ? 'PUT' : 'POST';

            const formData = {
                activity_template_id: activityTemplateInput.value,
                title: titleInput.value,
                event_date: eventDateInput.value,
                notes: notesInput.value,
                employee_ids: Array.from(employeeIdsInput.selectedOptions).map(option => option.value),
            };

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(formData),
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    console.log(errorData);
                    throw new Error('Gagal menyimpan event.');
                }

                closeModal();
                calendar.refetchEvents();
            } catch (error) {
                alert(error.message);
            }
        });

        deleteEventBtn.addEventListener('click', async function() {
            const eventId = eventIdInput.value;
            if (!eventId) return;

            const confirmed = confirm('Yakin ingin menghapus event ini?');
            if (!confirmed) return;

            try {
                const response = await fetch(`/calendar/events/${eventId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (!response.ok) {
                    throw new Error('Gagal menghapus event.');
                }

                closeModal();
                calendar.refetchEvents();
            } catch (error) {
                alert(error.message);
            }
        });
    </script>
</body>
</html>
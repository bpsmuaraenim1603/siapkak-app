import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin, { Draggable } from "@fullcalendar/interaction";

const calendarEl = document.getElementById("calendar");
const externalEventsEl = document.getElementById("external-events");
const csrfMeta = document.querySelector('meta[name="csrf-token"]');

if (calendarEl && csrfMeta) {
    const csrfToken = csrfMeta.getAttribute("content");

    const eventModal = document.getElementById("eventModal");
    const modalTitle = document.getElementById("modalTitle");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const deleteEventBtn = document.getElementById("deleteEventBtn");
    const eventForm = document.getElementById("eventForm");

    const eventIdInput = document.getElementById("event_id");
    const activityTemplateInput = document.getElementById(
        "activity_template_id",
    );
    const titleInput = document.getElementById("title");
    const eventDateInput = document.getElementById("event_date");
    const startTimeInput = document.getElementById("start_time");
    const sendAtInput = document.getElementById("send_at");
    const whatsappStatusInput = document.getElementById("whatsapp_status");
    const employeeIdsInput = document.getElementById("employee_ids");
    const notesInput = document.getElementById("notes");
    const sendWhatsappBtn = document.getElementById("sendWhatsappBtn");

    if (externalEventsEl) {
        new Draggable(externalEventsEl, {
            itemSelector: ".external-event",
            eventData: function (eventEl) {
                return {
                    title: eventEl.dataset.templateName,
                    extendedProps: {
                        activity_template_id: eventEl.dataset.templateId,
                    },
                };
            },
        });
    }

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        editable: true,
        droppable: true,
        selectable: true,
        height: "auto",
        events: async function (fetchInfo, successCallback, failureCallback) {
            try {
                const response = await fetch("/calendar/events", {
                    headers: {
                        Accept: "application/json",
                    },
                });

                if (!response.ok) {
                    throw new Error("Gagal memuat data event");
                }

                const data = await response.json();
                successCallback(data);
            } catch (error) {
                console.error(error);
                failureCallback(error);
            }
        },

        dateClick: function (info) {
            openCreateModal(info.dateStr);
        },

        eventClick: function (info) {
            fetchEvent(info.event.id);
        },

        eventDrop: async function (info) {
            try {
                const response = await fetch(
                    `/calendar/events/${info.event.id}/date`,
                    {
                        method: "PATCH",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            Accept: "application/json",
                        },
                        body: JSON.stringify({
                            event_date: info.event.startStr,
                        }),
                    },
                );

                if (!response.ok) {
                    throw new Error("Gagal memperbarui tanggal event.");
                }
            } catch (error) {
                alert(error.message);
                info.revert();
            }
        },

        drop: function (info) {
            const templateId = info.draggedEl.dataset.templateId;
            const templateName = info.draggedEl.dataset.templateName;
            openCreateModal(info.dateStr, templateId, templateName);
        },
    });

    calendar.render();

    function openModal() {
        eventModal.classList.remove("hidden");
        eventModal.classList.add("flex");
    }

    function closeModal() {
        eventModal.classList.add("hidden");
        eventModal.classList.remove("flex");
        resetForm();
    }

    function resetForm() {
        eventForm.reset();
        eventIdInput.value = "";
        deleteEventBtn.classList.add("hidden");
        whatsappStatusInput.value = "pending";
        sendWhatsappBtn.classList.add("hidden");

        Array.from(employeeIdsInput.options).forEach((option) => {
            option.selected = false;
        });

        modalTitle.textContent = "Tambah Event";
    }

    function openCreateModal(dateStr, templateId = "", templateName = "") {
        resetForm();
        modalTitle.textContent = "Tambah Event";
        eventDateInput.value = dateStr;
        activityTemplateInput.value = templateId;
        titleInput.value = templateName;
        openModal();
    }

    async function fetchEvent(eventId) {
        try {
            const response = await fetch(`/calendar/events/${eventId}`);
            if (!response.ok) {
                throw new Error("Gagal mengambil data event.");
            }

            const data = await response.json();

            resetForm();
            modalTitle.textContent = "Edit Event";
            deleteEventBtn.classList.remove("hidden");
            sendWhatsappBtn.classList.remove("hidden");

            eventIdInput.value = data.id;
            activityTemplateInput.value = data.activity_template_id;
            titleInput.value = data.title;
            eventDateInput.value = data.event_date;
            startTimeInput.value = data.start_time ?? "";
            sendAtInput.value = data.send_at ?? "";
            whatsappStatusInput.value = data.whatsapp_status ?? "pending";
            notesInput.value = data.notes ?? "";

            Array.from(employeeIdsInput.options).forEach((option) => {
                option.selected = data.employee_ids.includes(
                    Number(option.value),
                );
            });

            openModal();
        } catch (error) {
            alert(error.message);
        }
    }

    closeModalBtn.addEventListener("click", closeModal);
    cancelBtn.addEventListener("click", closeModal);

    eventModal.addEventListener("click", function (e) {
        if (e.target === eventModal) {
            closeModal();
        }
    });

    eventForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const eventId = eventIdInput.value;
        const url = eventId
            ? `/calendar/events/${eventId}`
            : `/calendar/events`;
        const method = eventId ? "PUT" : "POST";

        const formData = {
            activity_template_id: activityTemplateInput.value,
            title: titleInput.value,
            event_date: eventDateInput.value,
            start_time: startTimeInput.value,
            send_at: sendAtInput.value,
            notes: notesInput.value,
            employee_ids: Array.from(employeeIdsInput.selectedOptions).map(
                (option) => option.value,
            ),
        };

        try {
            const response = await fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
                body: JSON.stringify(formData),
            });

            if (!response.ok) {
                throw new Error("Gagal menyimpan event.");
            }

            closeModal();
            calendar.refetchEvents();
        } catch (error) {
            alert(error.message);
        }
    });

    deleteEventBtn.addEventListener("click", async function () {
        const eventId = eventIdInput.value;
        if (!eventId) return;

        const confirmed = confirm("Yakin ingin menghapus event ini?");
        if (!confirmed) return;

        try {
            const response = await fetch(`/calendar/events/${eventId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                },
            });

            if (!response.ok) {
                throw new Error("Gagal menghapus event.");
            }

            closeModal();
            calendar.refetchEvents();
        } catch (error) {
            alert(error.message);
        }
    });

    sendWhatsappBtn.addEventListener("click", async function () {
        const eventId = eventIdInput.value;
        if (!eventId) return;

        try {
            const response = await fetch(
                `/calendar/events/${eventId}/send-whatsapp`,
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrfToken,
                        Accept: "application/json",
                    },
                },
            );

            if (!response.ok) {
                throw new Error(
                    "Gagal memasukkan pengiriman WhatsApp ke antrean.",
                );
            }

            const data = await response.json();
            alert(data.message);
            calendar.refetchEvents();
        } catch (error) {
            alert(error.message);
        }
    });
}

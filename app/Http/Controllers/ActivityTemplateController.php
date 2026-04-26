<?php

namespace App\Http\Controllers;

use App\Models\ActivityTemplate;
use Illuminate\Http\Request;

class ActivityTemplateController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $activityTemplates = ActivityTemplate::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('whatsapp_message', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('activity-templates.index', compact('activityTemplates', 'search'));
    }

    public function create()
    {
        return view('activity-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp_message' => ['nullable', 'string'],
            'needs_employee_list' => ['nullable', 'boolean'],
        ]);

        $validated['needs_employee_list'] = $request->has('needs_employee_list');

        ActivityTemplate::create($validated);

        return redirect()
            ->route('activity-templates.index')
            ->with('success', 'Template kegiatan berhasil ditambahkan.');
    }

    public function show(ActivityTemplate $activityTemplate)
    {
        return redirect()->route('activity-templates.edit', $activityTemplate);
    }

    public function edit(ActivityTemplate $activityTemplate)
    {
        return view('activity-templates.edit', compact('activityTemplate'));
    }

    public function update(Request $request, ActivityTemplate $activityTemplate)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'whatsapp_message' => ['nullable', 'string'],
            'needs_employee_list' => ['nullable', 'boolean'],
        ]);

        $validated['needs_employee_list'] = $request->has('needs_employee_list');

        $activityTemplate->update($validated);

        return redirect()
            ->route('activity-templates.index')
            ->with('success', 'Template kegiatan berhasil diperbarui.');
    }

    public function destroy(ActivityTemplate $activityTemplate)
    {
        $activityTemplate->delete();

        return redirect()
            ->route('activity-templates.index')
            ->with('success', 'Template kegiatan berhasil dihapus.');
    }
}
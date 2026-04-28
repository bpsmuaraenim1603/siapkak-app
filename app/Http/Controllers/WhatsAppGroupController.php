<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppGroup;
use Illuminate\Http\Request;

class WhatsAppGroupController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search'));

        $whatsappGroups = WhatsAppGroup::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('group_id', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('whatsapp-groups.index', compact('whatsappGroups', 'search'));
    }

    public function create()
    {
        return view('whatsapp-groups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'string', 'max:255', 'unique:whatsapp_groups,group_id'],
        ]);

        WhatsAppGroup::create($validated);

        return redirect()
            ->route('whatsapp-groups.index')
            ->with('success', 'Grup WhatsApp berhasil ditambahkan.');
    }

    public function show(WhatsAppGroup $whatsappGroup)
    {
        return redirect()->route('whatsapp-groups.edit', $whatsappGroup);
    }

    public function edit(WhatsAppGroup $whatsappGroup)
    {
        return view('whatsapp-groups.edit', compact('whatsappGroup'));
    }

    public function update(Request $request, WhatsAppGroup $whatsappGroup)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'group_id' => ['required', 'string', 'max:255', 'unique:whatsapp_groups,group_id,' . $whatsappGroup->id],
        ]);

        $whatsappGroup->update($validated);

        return redirect()
            ->route('whatsapp-groups.index')
            ->with('success', 'Grup WhatsApp berhasil diperbarui.');
    }

    public function destroy(WhatsAppGroup $whatsappGroup)
    {
        $whatsappGroup->delete();

        return redirect()
            ->route('whatsapp-groups.index')
            ->with('success', 'Grup WhatsApp berhasil dihapus.');
    }
}
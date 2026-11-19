<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Event;
use App\Models\TicketCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EoController extends Controller
{
    public function dashboard(): View
    {
        $eo = Auth::user();
        $events = Event::with('ticketCategories')
            ->where('eo_id', $eo->id)
            ->latest()
            ->get();

        $paidOrdersQuery = DB::table('orders')
            ->join('events', 'orders.event_id', '=', 'events.id')
            ->where('events.eo_id', $eo->id);

        if (Schema::hasColumn('orders', 'payment_status')) {
            $paidOrdersQuery->whereIn('orders.payment_status', ['paid', 'completed', 'confirmed', 'settlement']);
        }

        $grossRevenue = (float) $paidOrdersQuery->sum('orders.total_amount');

        $totalTicketsSold = (int) DB::table('tickets')
            ->join('events', 'tickets.event_id', '=', 'events.id')
            ->where('events.eo_id', $eo->id)
            ->count();

        return view('eo.dashboard', compact('eo', 'events', 'grossRevenue', 'totalTicketsSold'));
    }

    public function storeEvent(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:100'],
            'event_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:20'],
            'end_time' => ['required', 'string', 'max:20'],
            'open_gate' => ['nullable', 'string', 'max:20'],
            'venue_name' => ['required', 'string', 'max:150'],
            'venue_address' => ['required', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'poster' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*.name' => ['required', 'string', 'max:100'],
            'categories.*.price' => ['required', 'numeric', 'min:0'],
            'categories.*.quota' => ['required', 'integer', 'min:1'],
        ]);

        $eo = Auth::user();
        $posterPath = null;

        if ($request->hasFile('poster')) {
            $posterPath = $request->file('poster')->store('events', 'public');
        }

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug . '-' . strtolower(Str::random(5));

        $eventCols = Schema::getColumnListing('events');
        $eventData = [
            'eo_id' => $eo->id,
            'title' => trim($validated['title']),
            'slug' => $slug,
            'description' => trim($validated['description']),
            'event_date' => $validated['event_date'],
            'venue_name' => trim($validated['venue_name']),
            'status' => 'pending',
        ];

        if (in_array('category', $eventCols, true)) $eventData['category'] = trim($validated['category']);
        if (in_array('start_time', $eventCols, true)) $eventData['start_time'] = $validated['start_time'];
        if (in_array('end_time', $eventCols, true)) $eventData['end_time'] = $validated['end_time'];
        if (in_array('open_gate', $eventCols, true)) $eventData['open_gate'] = $validated['open_gate'] ?: null;
        if (in_array('venue_address', $eventCols, true)) $eventData['venue_address'] = trim($validated['venue_address']);
        if (in_array('location', $eventCols, true)) $eventData['location'] = trim($validated['venue_address']);
        if (in_array('poster', $eventCols, true)) $eventData['poster'] = $posterPath;
        if (in_array('banner_url', $eventCols, true)) $eventData['banner_url'] = $posterPath ? '/storage/' . $posterPath : null;
        if (in_array('image', $eventCols, true)) $eventData['image'] = $posterPath;

        $event = Event::create($eventData);

        $catCols = Schema::getColumnListing('ticket_categories');
        foreach ($validated['categories'] as $cat) {
            $catData = [
                'event_id' => $event->id,
                'name' => trim($cat['name']),
                'price' => (float) $cat['price'],
                'quota' => (int) $cat['quota'],
            ];
            if (in_array('remaining_quota', $catCols, true)) $catData['remaining_quota'] = (int) $cat['quota'];
            if (in_array('sold', $catCols, true)) $catData['sold'] = 0;

            TicketCategory::create($catData);
        }

        AuditLog::create([
            'user_id' => $eo->id,
            'action' => 'EVENT_SUBMITTED',
            'description' => "EO {$eo->name} mengajukan event baru '{$event->title}' untuk moderasi Admin.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Pengajuan acara beserta poster & kategori tiket berhasil dikirim untuk ditinjau Admin.');
    }
}
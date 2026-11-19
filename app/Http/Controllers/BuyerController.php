<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class BuyerController extends Controller
{
    public function home(Request $request): View
    {
        $query = Event::with(['eo', 'ticketCategories'])
            ->where('status', 'published');

        $search = trim((string) ($request->query('q') ?? $request->query('search') ?? ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('venue_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $events = $query->orderBy('event_date', 'asc')->orderBy('id', 'asc')->get();

        $defaultCategories = ['Musik & Konser', 'Festival', 'Workshop'];
        $dbCategories = Schema::hasColumn('events', 'category')
            ? $events->pluck('category')->filter()->unique()->values()->all()
            : [];

        $categories = collect(array_merge($defaultCategories, $dbCategories))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return view('buyer.home', compact('events', 'categories'));
    }

    public function detail(Event $event): View
    {
        abort_if($event->status !== 'published', 404);
        $event->load(['eo', 'ticketCategories']);

        return view('buyer.detail', compact('event'));
    }

    public function tickets(Event $event): View
    {
        abort_if($event->status !== 'published', 404);
        $event->load(['eo', 'ticketCategories']);

        return view('buyer.ticket-select', compact('event'));
    }
}
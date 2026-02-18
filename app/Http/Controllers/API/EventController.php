<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class EventController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Event::class, 'event');
    }

    /**
     * Display a listing of the events.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $events = Event::with(['creator', 'participants'])
            ->when($request->status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->upcoming, function ($query) {
                return $query->where('start_time', '>', now());
            })
            ->orderBy('start_time')
            ->paginate($request->per_page ?? 15);

        return EventResource::collection($events);
    }

    /**
     * Store a newly created event.
     */
    public function store(Request $request): EventResource
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:draft,published,cancelled',
        ]);

        $event = $request->user()->createdEvents()->create($validated);

        return new EventResource($event->load(['creator', 'participants']));
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): EventResource
    {
        return new EventResource($event->load(['creator', 'participants']));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, Event $event): EventResource
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'sometimes|date|after:now',
            'end_time' => 'sometimes|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'status' => 'sometimes|in:draft,published,cancelled',
        ]);

        $event->update($validated);

        return new EventResource($event->load(['creator', 'participants']));
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event): Response
    {
        $event->delete();

        return response()->noContent();
    }

    /**
     * Join an event.
     */
    public function join(Request $request, Event $event): Response
    {
        $user = $request->user();

        if ($event->participants()->where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'Already joined'], 400);
        }

        if ($event->max_participants && $event->participants()->count() >= $event->max_participants) {
            return response()->json(['message' => 'Event is full'], 400);
        }

        $event->participants()->attach($user->id, ['status' => 'going']);

        return response()->json(['message' => 'Successfully joined']);
    }

    /**
     * Leave an event.
     */
    public function leave(Request $request, Event $event): Response
    {
        $event->participants()->detach($request->user()->id);

        return response()->json(['message' => 'Successfully left']);
    }

    /**
     * Update participation status.
     */
    public function updateStatus(Request $request, Event $event): Response
    {
        $validated = $request->validate([
            'status' => 'required|in:going,maybe,not_going',
        ]);

        $event->participants()->updateExistingPivot($request->user()->id, [
            'status' => $validated['status']
        ]);

        return response()->json(['message' => 'Status updated']);
    }
}
<?php

namespace App\Http\Middleware;

use App\Models\Event;
use App\Models\EventUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckEventCreator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $eventId = $request->route('event');

        $event = $eventId instanceof Event ? $eventId : Event::find($eventId);

        if (! $event) {
            abort(404, 'Event not found');
        }

        $eventMembership = EventUser::where('user_id', $user->id)
            ->where('group_id', $event->id)
            ->first();

        if (!$eventMembership->is_creator) {
            abort(403, 'You are not the creator of this event.');
        }

        return $next($request);
    }
}

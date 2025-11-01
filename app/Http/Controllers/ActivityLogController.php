<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of all system activity logs.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();

        // Only superadmins can view system logs
        if (! $user->isSuperAdmin()) {
            abort(403, 'No tienes permiso para ver los logs del sistema.');
        }

        // Build the query
        $query = Activity::with(['causer', 'subject'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhereHas('causer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Apply event filter
        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        // Apply log_name filter
        if ($request->filled('log_name')) {
            $query->where('log_name', $request->input('log_name'));
        }

        // Get activities (limit to last 500 for performance)
        $activities = $query->take(500)->get()->map(function ($activity) {
            return [
                'id' => $activity->id,
                'log_name' => $activity->log_name,
                'description' => $activity->description,
                'subject_type' => $activity->subject_type,
                'subject_id' => $activity->subject_id,
                'event' => $activity->event,
                'causer_type' => $activity->causer_type,
                'causer_id' => $activity->causer_id,
                'properties' => $activity->properties,
                'changes' => $activity->changes(),
                'causer' => $activity->causer ? [
                    'id' => $activity->causer->id,
                    'name' => $activity->causer->name,
                ] : null,
                'subject' => $activity->subject ? [
                    'id' => $activity->subject->id,
                    'name' => $activity->subject->name ?? 'N/A',
                ] : null,
                'created_at' => $activity->created_at->format('Y-m-d H:i:s'),
                'created_at_human' => $activity->created_at->diffForHumans(),
            ];
        });

        // If it's an AJAX request, return JSON
        if ($request->wantsJson()) {
            return response()->json($activities);
        }

        // Otherwise return the Inertia page
        return Inertia::render('ActivityLogs/Index', [
            'canManageUsers' => $user->isSuperAdmin(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        $search = request('search');
        $event = request('event');
        $action = request('action');

        $activities = Activity::with('causer')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('description', 'like', '%' . $search . '%')
                        ->orWhere('event', 'like', '%' . $search . '%')
                        ->orWhere('subject_type', 'like', '%' . $search . '%')
                        ->orWhere('subject_id', 'like', '%' . $search . '%')
                        ->orWhereHas('causer', function ($cq) use ($search) {
                            $cq->where('username', 'like', '%' . $search . '%');
                        });
                });
            })
            ->when($event, function ($query) use ($event) {
                $query->where('event', $event);
            })
            ->when($action, function ($query) use ($action) {
                $query->where(function ($q) use ($action) {
                    $q->where('event', 'like', '%' . $action . '%')
                        ->orWhere('description', 'like', '%' . $action . '%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('activity-logs', compact('activities'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
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
                $searchLike = '%' . $search . '%';

                $query->where(function ($q) use ($search) {
                    $searchLike = '%' . $search . '%';

                    $q->where('description', 'like', $searchLike)
                        ->orWhere('event', 'like', $searchLike)
                        ->orWhere('subject_type', 'like', $searchLike)
                        ->orWhere('subject_id', 'like', $searchLike)
                        ->orWhere('properties', 'like', $searchLike)
                        ->orWhereHas('causer', function ($cq) use ($search) {
                            $cq->where('username', 'like', '%' . $search . '%');
                        })
                        ->orWhereHasMorph('subject', [User::class], function ($sq) use ($searchLike) {
                            $sq->where('username', 'like', $searchLike);
                        })
                        ->orWhereHasMorph('subject', [Patient::class], function ($sq) use ($searchLike) {
                            $sq->where('first_name', 'like', $searchLike)
                                ->orWhere('last_name', 'like', $searchLike)
                                ->orWhere('middle_name', 'like', $searchLike)
                                ->orWhere('mobile_number', 'like', $searchLike)
                                ->orWhere('email_address', 'like', $searchLike);
                        })
                        ->orWhereHasMorph('subject', [Appointment::class], function ($sq) use ($searchLike) {
                            $sq->where('id', 'like', $searchLike)
                                ->orWhere('status', 'like', $searchLike)
                                ->orWhereHas('patient', function ($pq) use ($searchLike) {
                                    $pq->where('first_name', 'like', $searchLike)
                                        ->orWhere('last_name', 'like', $searchLike)
                                        ->orWhere('middle_name', 'like', $searchLike)
                                        ->orWhere('mobile_number', 'like', $searchLike);
                                });
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

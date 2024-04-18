<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller
{
    public function index()
    {
        $activities = Activity::all();
        $activitiesWithUser = $activities->map(function($activity) {
            $user = $activity->causer;
            $userName = $user ? $user->name : "Unknown user";

            $activity->user = $userName;

            return $activity;
        });

        $userActivities = \Auth::user()->actions;

        return Inertia::render("Audit/Page", [
            'activities' => $activitiesWithUser,
            'userActivities' => $userActivities
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\AuditResource;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class AuditController extends Controller
{
    public function index()
    {
        return Inertia::render("Audit/Page");
    }

    public function api(Activity $activity, Request $request)
    {
        // $sortFieldInput = $request->input('sort_field', 'id');
        // $sortOrder = $request->input('sort_order', 'desc');
        $searchInput = $request->search;
        $query = $activity->select('activity_log.*')
            ->join("users", 'activity_log.causer_id', 'users.id')
            ->orderBy("id", "desc");
        $perpage = $request->perpage ?? 15;

        if (isset($request->log_name)) {
            $query = $query->whereIn('log_name', $request->log_name);
        }

        if (isset($request->event)) {
            $query = $query->whereIn('event', $request->event);
        }

        if (!is_null($searchInput)) {
            $searchQuery = "%$searchInput%";
            $query = $query->where(function ($query) use ($searchQuery) {
                $query->where('log_name', 'like', $searchQuery)
                    ->orWhere('event', 'like', $searchQuery)
                    ->orWhere('users.name', 'like', $searchQuery);
            });
        }

        if ($perpage == "All") {
            $perpage = $query->count();
        }

        $query = $query->paginate($perpage);

        return AuditResource::collection($query);
    }
}

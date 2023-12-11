<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBranchCode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Retrieve the branch_code from the URL.
        $branchCode = $request->route('id');

        // Get the authenticated user's branch code using Eloquent's Find method.
        $userBranch = Branch::find(Auth::user()->branch_id);

        // Check if the user's branch code matches the branch_code from the URL.
        if ($userBranch && $userBranch->branch_code == $branchCode) {
            // If match, proceed with the request.
            return $next($request);
        } else if (!Auth::user()->hasRole('cabang')) {
            return $next($request);
        }

        // If there is no match, abort the request and return an unauthorized error response.
        abort(403);
    }
}

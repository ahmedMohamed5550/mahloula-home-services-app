<?php

namespace App\Helpers;

use App\Constants\Constants;
use Illuminate\Http\Request;

class Helpers
{
    public static function getLanguages()
    {
        return ['ar', 'en'];
    }

    public static function getPagination()
    {
        return request('paginate') ?? Constants::DASHBOARD_PAGINATION;
    }

    public static function ContextPagination()
    {
        $pagination = request('paginate');
        if ($pagination === 'off') {
            return PHP_INT_MAX;
        }

        return $pagination ?? Constants::DASHBOARD_PAGINATION;
    }

    public static function getCurrentLang()
    {
        return App()->getlocale();
    }

    public static function checkPermissions($permission, $role)
    {
        return in_array($permission->id, $role->permissions()->pluck('id')->toArray()) ? 'checked' : '';
    }

    public static function getPaginationAndSorting(Request $request)
    {
        $perPage = $request->input('per_page', Helpers::getPagination());
        $sortBy = $request->input('sortBy', 'id');
        $sortDirection = strtolower($request->input('sort', 'desc'));

        if (! in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        return compact('perPage', 'sortBy', 'sortDirection');
    }

    /**
     * Generate a unique cache key for the current patient's request.
     *
     * The cache key is based on the serialized request parameters and the authenticated user's ID.
     * This ensures the cache is specific to the user and request context.
     *
     * @param  \Illuminate\Http\Request  $request  The current incoming request.
     * @return string
     *                - Returns a string that uniquely identifies the cache key for the request.
     */
    public static function generateCacheKey(Request $request)
    {
        $serializedRequest = md5(serialize($request->all()));
        $user_id = auth()->user()->id;

        return "patients_index_{$user_id}_{$serializedRequest}";
    }
}

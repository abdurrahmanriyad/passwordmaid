<?php

if (!function_exists('trimArray')) {
    function trimArray($arr) {
        foreach ($arr as $key => $value) {
            if (empty(trim($value))) {
                unset($arr[$key]);
            }
        }

        return $arr;
    }
}

if (!function_exists('getAuthRecentGrpCacheKey')) {
    function getAuthRecentGrpCacheKey() {
        return auth()->user()->id . "_recent_groups";
    }
}

if (!function_exists('getAuthRecentSharedCacheKey')) {
    function getAuthRecentSharedCacheKey() {
        return auth()->user()->id . "_recent_shared_by_groups";
    }
}

if (!function_exists('showProjectsButton')) {
    function showProjectsButton() {
        return (!(request()->routeIs('app')) && auth()->check() && auth()->user()->hasVerifiedEmail());
    }
}

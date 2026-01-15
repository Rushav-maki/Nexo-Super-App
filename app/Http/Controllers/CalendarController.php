<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CalendarController extends Controller
{
    public function index($year = 2082)
    {
        $path = public_path("json/{$year}.json");

        if (!file_exists($path)) {
            abort(404, "Nepali calendar JSON for year {$year} not found.");
        }

        $json = file_get_contents($path);
        $calendar = json_decode($json, true);

        return view('user.calendar', compact('calendar', 'year'));
    }

    public function todayDate()
    {
        try {
            $res = Http::withoutVerifying()->get("https://sudhanparajuli.com.np/api/todaydate");
            return response()->json($res->json());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function bsToAd($year, $month, $day)
    {
        try {
            $res = Http::withoutVerifying()->get("https://sudhanparajuli.com.np/api/bs-to-ad/{$year}/{$month}/{$day}");
            return response()->json($res->json());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function adToBs($year, $month, $day)
    {
        try {
            $res = Http::withoutVerifying()->get("https://sudhanparajuli.com.np/api/ad-to-bs/{$year}/{$month}/{$day}");
            return response()->json($res->json());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function calculateAgeFromBs($year, $month, $day)
    {
        try {
            $res = Http::withoutVerifying()->get("https://sudhanparajuli.com.np/api/calculateage/bs/{$year}/{$month}/{$day}");
            return response()->json($res->json());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function calculateAgeFromAd($year, $month, $day)
    {
        try {
            $res = Http::withoutVerifying()->get("https://sudhanparajuli.com.np/api/calculateage/ad/{$year}/{$month}/{$day}");
            return response()->json($res->json());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}

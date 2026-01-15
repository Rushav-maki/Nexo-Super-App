<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;

class HealthController extends Controller
{
    public function index()
    {
        return view('user.health');
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'symptoms' => 'required|string|min:5',
        ]);

        try {
            $data = OpenAIService::analyzeSymptoms($validated['symptoms']);
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTravelTips(Request $request)
    {
        $destination = $request->query('destination');

        if (!$destination) {
            return response()->json(['error' => 'Destination required'], 400);
        }

        try {
            $tips = OpenAIService::generateTravelHealthTips($destination);
            return response()->json($tips);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getDoctors(Request $request)
    {
        $diagnosis = $request->query('diagnosis');
        $urgency = $request->query('urgency', 'Medium');

        if (!$diagnosis) {
            return response()->json(['error' => 'Diagnosis required'], 400);
        }

        try {
            $doctors = OpenAIService::generateDoctorRecommendations($diagnosis, $urgency);
            return response()->json($doctors);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

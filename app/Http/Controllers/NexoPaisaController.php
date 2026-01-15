<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NexoPaisaTransaction;

class NexoPaisaController extends Controller
{
    public function load()
    {
        $banks = [
            'Nabil Bank',
            'Global IME Bank',
            'NIC Asia Bank',
            'Machhapuchchhre Bank',
            'NMB Bank',
            'Prabhu Bank',
            'Sanima Bank',
            'Siddhartha Bank',
            'Bank of Kathmandu',
            'Century Commercial Bank',
            'Citizens Bank International',
            'Everest Bank',
            'Himalayan Bank',
            'Kumari Bank',
            'Laxmi Sunrise Bank',
            'Mega Bank Nepal',
            'Nayuta Development Bank',
            'Prime Commercial Bank',
            'Rastriya Banijya Bank',
            'Shangrila Development Bank',
        ];

        return view('user.load-nexo-paisa', compact('banks'));
    }

    public function loadPaisa(Request $request)
    {
        \Log::info('Load Paisa request received', [
            'all' => $request->all(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'user' => auth()->check() ? auth()->id() : 'not authenticated'
        ]);

        $validated = $request->validate([
            'bank' => 'required|string|min:1',
            'amount' => 'required|numeric|min:1|max:100000',
        ]);

        // Cast amount to float then int to ensure it's a number
        $validated['amount'] = (int) (float) $validated['amount'];

        \Log::info('Validation passed', $validated);

        $user = auth()->user();

        if (!$user) {
            \Log::error('No authenticated user');
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated'
            ], 401);
        }

        \Log::info('User found', ['user_id' => $user->id, 'current_balance' => $user->nexo_paisa]);

        // Simulate payment processing
        // In real app, integrate with payment gateway

        // Add to nexo_paisa
        $user->increment('nexo_paisa', $validated['amount']);

        \Log::info('Balance updated', ['new_balance' => $user->fresh()->nexo_paisa]);

        // Record transaction
        $transaction = NexoPaisaTransaction::create([
            'user_id' => $user->id,
            'type' => 'load',
            'amount' => $validated['amount'],
            'description' => "Loaded Nexo Paisa from {$validated['bank']}",
            'metadata' => [
                'bank' => $validated['bank'],
                'method' => 'bank_transfer'
            ]
        ]);

        \Log::info('Transaction created', ['transaction_id' => $transaction->id]);

        return response()->json([
            'status' => 'success',
            'message' => 'Nexo Paisa loaded successfully! Amount: ' . $validated['amount'] . ' NRP'
        ]);
    }
}

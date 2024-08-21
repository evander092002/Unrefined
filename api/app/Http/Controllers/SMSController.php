<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SMSController extends Controller
{
    public function sendSmsTwilio(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'phone' => 'required|regex:/^\+?[1-9]\d{1,14}$/',
            'message' => 'required|string|max:1600',
        ]);

        $twilioSid = '';
        $twilioAuthToken = '';
        $twilioPhoneNumber = '';

        $client = new Client($twilioSid, $twilioAuthToken);

        try {
            $client->messages->create(
                $request->input('phone'),
                [
                    'from' => $twilioPhoneNumber,
                    'body' => $request->input('message'),
                ]
            );

            return response()->json(['success' => 'Message sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message: ' . $e->getMessage()], 500);
        }
    }
}

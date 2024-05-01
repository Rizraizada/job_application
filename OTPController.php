<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OTPController extends Controller
{
    public function generateOTP(Request $request)
    {
        // Generate OTP
        $otp = rand(100000, 999999);

        // Log generated OTP
        Log::info('Generated OTP: ' . $otp);

        // Send OTP via SMS
        $response = $this->sendSMS($request->phone, $otp);

        if ($response === true) {
            // Retrieve sender ID from configuration
            $senderId = config('app.SMS_SENDER_ID');

            // Store OTP in session
            Session::put('otp', $otp);

            // Return response with success message and sender ID
            return response()->json(['success' => 'OTP has been sent to your phone.', 'otp' => $otp, 'sender_id' => $senderId]);
        } else {
            return response()->json(['error' => 'Failed to send OTP. Please try again later.'], 500);
        }
    }

    public function verifyOTP(Request $request)
    {
        $otp = Session::get('otp');

        // Log entered OTP
        Log::info('Entered OTP: ' . $request->otp);
        // Log stored OTP
        Log::info('Stored OTP: ' . $otp);

        if ($otp && $otp == $request->otp) {
            Session::forget('otp'); // Remove OTP from session once verified
            return response()->json(['success' => 'OTP verified successfully.']);

        } else {
            return response()->json(['error' => 'Invalid OTP.'], 422);
        }
    }

    private function sendSMS($phone, $otp)
    {
        // Retrieve API key and sender ID from configuration
        $apiKey = '44517101314545131710131454';
        $senderId ='01844532638';

        // Construct the API URL with parameters
        $apiUrl = 'http://sms.iglweb.com/api/v1/send';
        $url = "{$apiUrl}?api_key={$apiKey}&contacts={$phone}&senderid={$senderId}&msg=Your%20OTP%20is:%20{$otp}";

        // Create a new Guzzle client
        $client = new \GuzzleHttp\Client();

        try {
            // Send POST request to the API URL
            $response = $client->post($url);

            // Check if the request was successful
            if ($response->getStatusCode() == 200) {
                return true; // SMS sent successfully
            } else {
                // Handle unsuccessful response
                Log::error('Failed to send SMS: ' . $response->getBody()->getContents());
                return false;
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur during the request
            Log::error('Error sending SMS: ' . $e->getMessage());
            return false;
        }
    }
}

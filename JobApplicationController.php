<?php
// JobApplicationController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Session;

class JobApplicationController extends Controller
{
    public function submit(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'name' => 'required',
            'call_number1' => 'required',
            'email' => 'required|email',
            'present_address' => 'required',
            'permanent_address' => 'required',
            'picture' => 'required|image',
            'cv' => 'required|mimes:pdf,docx',
            'department' => 'required',
            'otp' => 'required'
        ]);

         if ($this->verifyOTP($request->otp)) {
            // OTP verified, proceed with job application submission
            $application = new JobApplication();
            $application->name = $request->name;
            $application->call_number1 = $request->call_number1;
            $application->call_number2 = $request->call_number2;
            $application->call_number3 = $request->call_number3;
            $application->email = $request->email;
            $application->present_address = $request->present_address;
            $application->permanent_address = $request->permanent_address;
            // Handle picture and cv file uploads
            $picturePath = $request->file('picture')->store('pictures');
            $cvPath = $request->file('cv')->store('cvs');
            $application->picture = $picturePath;
            $application->cv = $cvPath;
            $application->department = $request->department;
            $application->save();

            return response()->json(['success' => 'Job application submitted successfully.']);
        } else {
            // Invalid OTP
            return response()->json(['error' => 'Invalid OTP.'], 422);
        }
    }

    private function verifyOTP($otp)
    {
        $storedOTP = Session::get('otp');
        if ($storedOTP && $storedOTP == $otp) {
            // OTP is valid
            Session::forget('otp');
            return true;
        } else {
            // Invalid OTP
            return false;
        }
    }
}

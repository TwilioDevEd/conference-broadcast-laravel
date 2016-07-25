<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Twiml;

class BroadcastController extends Controller
{
    public function index()
    {
        return view('broadcast');
    }

    public function send(Request $request, Client $twilioClient)
    {
        $destinationNumbers = $request->input('numbers');
        $numbersArray = explode(',', $destinationNumbers);
        $recordingUrl = $request->input('recording_url');
        $twilioNumber = config('services.twilio')['number'];

        // The URL Twilio will request when the call is answered
        $path = str_replace($request->path(), '', $request->url()) .
            'broadcast/play?recording_url=' . $recordingUrl;

        foreach ($numbersArray as $number) {
            try {
                $twilioClient->calls->create(
                    trim($number), // The number of the phone receiving call
                    $twilioNumber, // The number of the phone initiating the call
                    ["url" => $path]
                );
            } catch (Exception $e) {
                return 'Error: ' . $e->getMessage();
            }
        }

        $request->session()->flash(
            'status',
            "Broadcast message sent!"
        );

        return redirect()->route('broadcast-index');
    }

    public function showPlay(Request $request)
    {
        $recordingUrl = $request->input('recording_url');
        $response = new Twiml();
        $response->play($recordingUrl);

        return response($response)->header('Content-Type', 'application/xml');
    }
}

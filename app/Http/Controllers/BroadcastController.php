<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Services_Twilio as TwilioRestClient;
use Services_Twilio_Twiml as TwilioTwiml;

class BroadcastController extends Controller
{
    public function index()
    {
        return view('broadcast');
    }

    public function send(Request $request, TwilioRestClient $client)
    {
        $destinationNumbers = $request->input('numbers');
        $numbersArray = explode(',', $destinationNumbers);
        $recordingUrl = $request->input('recording_url');
        $twilioNumber = config('services.twilio')['number'];

        $path = str_replace($request->path(), '', $request->url()).
            'broadcast/play?recording_url=' . $recordingUrl;

        foreach($numbersArray as $number) {
            try {
                $client->account->calls->create(
                    $twilioNumber, // The number of the phone initiating the call
                    trim($number), // The number of the phone receiving call
                    $path // The URL Twilio will request when the call is answered
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
        $response = new TwilioTwiml;
        $response->play($recordingUrl);

        return response($response)->header('Content-Type', 'application/xml');
    }
}

<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Services_Twilio as TwilioRestClient;

class RecordingController extends Controller
{
    public function index(TwilioRestClient $client)
    {
        $baseUrl = '';
        $recordings = array();

        foreach($client->account->recordings as $recording) {
            $entry = array(
                'url' => 'https://api.twilio.com' . $recording->uri,
                'date' => $recording->date_created
            );
            array_push($recordings, $entry);
        }

        return response()->json($recordings);
    }

    public function create(Request $request, TwilioRestClient $client)
    {
        $destinationNumber = $request->input('phone_number');
        $twilioNumber = config('services.twilio')['number'];

        $path = str_replace($request->path(), '', $request->url()) . 'recording/record';

        try {
            $client->account->calls->create(
                $twilioNumber, // The number of the phone initiating the call
                $destinationNumber, // The number of the phone receiving call
                $path // The URL Twilio will request when the call is answered
            );
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
        return "call_created";
    }
}

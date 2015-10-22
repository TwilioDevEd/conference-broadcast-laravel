<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Services_Twilio as TwilioRestClient;
use Services_Twilio_Twiml as TwilioTwiml;

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

    public function showRecord()
    {
        $response = new TwilioTwiml;
        $response->say(
            'Please record your message after the beep. Press star to end your recording.',
            ['voice' => 'alice', 'language' => 'en-GB']
        );
        $response->record(array(
            'action' => '/recording/hangup',
            'finishOnKey' => '*',
            'method' => 'GET'
        ));

        return response($response)->header('Content-Type', 'application/xml');
    }

    public function showHangup()
    {
        $response = new TwilioTwiml;
        $response->say(
            'Your recording has been saved. Good bye.',
            ['voice' => 'alice', 'language' => 'en-GB']
        );
        $response->hangup();

        return response($response)->header('Content-Type', 'application/xml');
    }
}

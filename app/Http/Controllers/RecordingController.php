<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Twilio\Twiml;

class RecordingController extends Controller
{
    public function index(Client $twilioClient)
    {
        $baseUrl = '';
        $responseRecordings = array();

        $recordings = $twilioClient->recordings->read();

        foreach ($recordings as $recording) {
            $recordingUri = str_replace('.json', '', $recording->uri);
            $entry = array(
                'url' => 'https://api.twilio.com' . $recordingUri,
                'date' => $recording->dateCreated->format('Y-m-d H:i:s')
            );
            array_push($responseRecordings, $entry);
        }

        return response()->json($responseRecordings);
    }

    public function create(Request $request, Client $client)
    {
        $destinationNumber = $request->input('phone_number');
        $twilioNumber = config('services.twilio')['number'];

        // The URL Twilio will request when the call is answered
        $path = str_replace($request->path(), '', $request->url()) . 'recording/record';

        try {
            $client->calls->create(
                $destinationNumber, // The number of the phone receiving call
                $twilioNumber,      // The number of the phone initiating the call
                [
                    "url" => $path
                ]
            );
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
        return "call_created";
    }

    public function showRecord()
    {
        $response = new Twiml();
        $response->say(
            'Please record your message after the beep.' .
            ' Press star to end your recording.',
            [
                'voice' => 'alice',
                'language' => 'en-GB'
            ]
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
        $response = new Twiml();
        $response->say(
            'Your recording has been saved. Good bye.',
            [
                'voice' => 'alice',
                'language' => 'en-GB'
            ]
        );
        $response->hangup();

        return response($response)->header('Content-Type', 'application/xml');
    }
}

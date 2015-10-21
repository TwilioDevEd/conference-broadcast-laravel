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
}

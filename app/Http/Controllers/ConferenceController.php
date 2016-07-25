<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Twilio\Twiml;

class ConferenceController extends Controller
{
    public function index()
    {
        $conferenceNumber = config('services.twilio')['rrNumber'];
        return view('conference', ['conferenceNumber' => $conferenceNumber]);
    }

    /**
     * Replies with a join call xml
     *
     * @return \Illuminate\Http\Response
     */
    public function showJoin()
    {
        $response = new Twiml();
        $response->say(
            'You are about to join the Rapid Response conference.',
            [
                'voice' => 'alice',
                'language' => 'en-GB'
            ]
        );
        $gather = $response->gather(
            [
                'numDigits' => 1,
                'action' => route('conference-connect', [], false),
                'method' => 'GET'
            ]
        );
        $gather->say('Press 1 to join as a listener.');
        $gather->say('Press 2 to join as a speaker.');
        $gather->say('Press 3 to join as the moderator.');

        return response($response)->header('Content-Type', 'application/xml');
    }

    /**
     * Replies with a call connect xml
     *
     * @return \Illuminate\Http\Response
     */
    public function showConnect(Request $request)
    {
        $muted = false;
        $moderator = false;
        $digits = $request->input('Digits');
        if ($digits === '1') {
            $muted = true;
        }
        if ($digits === '3') {
            $moderator = true;
        }

        $response = new Twiml();
        $response->say(
            'You have joined the conference.',
            [
                'voice' => 'alice',
                'language' => 'en-GB'
            ]
        );
        $dial = $response->dial();
        $dial->conference(
            'RapidResponseRoom',
            [
                'startConferenceOnEnter' => $moderator,
                'endConferenceOnExit' => $moderator,
                'muted' => $muted,
                'waitUrl' => 'http://twimlets.com/holdmusic?Bucket=com.twilio.music.ambient',
            ]
        );

        return response($response)->header('Content-Type', 'application/xml');
    }
}

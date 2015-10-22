<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Agent;
class RecordingControllerTest extends TestCase
{
    public function testIndex()
    {
        // Given
        $mockTwilioService = Mockery::mock('Services_Twilio')
                                ->makePartial();

        $mockTwilioAccount = Mockery::mock();

        $mockTwilioRecording = Mockery::mock();
        $mockTwilioRecording->uri = '/some/uri';
        $mockTwilioRecording->date_created = 'some_date';
        $mockTwilioRecording2 = Mockery::mock();
        $mockTwilioRecording2->uri = '/some/other/uri';
        $mockTwilioRecording2->date_created = 'some_other_date';

        $twilioRecordings = array($mockTwilioRecording, $mockTwilioRecording2);
        $mockTwilioAccount->recordings = $twilioRecordings;
        $mockTwilioService->account = $mockTwilioAccount;

        $this->app->instance(
            'Services_Twilio',
            $mockTwilioService
        );

        // When
        $response = $this->call('GET', route('recording-index'));
        $recordingsJSON = json_decode($response->getContent());
        // Then
        $this->assertCount(2, $recordingsJSON);
        $this->assertEquals($recordingsJSON[0]->url, 'https://api.twilio.com/some/uri');
        $this->assertEquals($recordingsJSON[1]->date, 'some_other_date');
    }

    public function testCreate()
    {
        // Given
        $this->startSession();
        $twilioNumber = config('services.twilio')['number'];

        $mockTwilioService = Mockery::mock('Services_Twilio')
                                ->makePartial();
        $mockTwilioAccount = Mockery::mock();
        $mockTwilioCalls = Mockery::mock();
        $mockTwilioService->account = $mockTwilioAccount;
        $mockTwilioAccount->calls = $mockTwilioCalls;

        $mockTwilioCalls
            ->shouldReceive('create')
            ->withAnyArgs();

        $this->app->instance(
            'Services_Twilio',
            $mockTwilioService
        );

        // When
        $response = $this->call(
            'POST',
            route('recording-create'),
            ['phone_number' => '+123456',
            '_token' => csrf_token()]
        );

        // Then
        $this->assertEquals($response->getContent(), 'call_created');
    }

    public function testShowRecord()
    {
        // When
        $this->startSession();
        $recordResponse = $this->call(
            'POST',
            route('recording-record'),
            ['_token' => csrf_token()]
        );
        $recordDocument = new SimpleXMLElement($recordResponse->getContent());

        // Then
        $this->assertNotNull($recordDocument->Say);
        $this->assertNotEmpty($recordDocument->Say);
        $this->assertNotNull($recordDocument->Record);
        $this->assertNotEmpty($recordDocument->Record);
        $this->assertEquals(strval($recordDocument->Say), 'Please record your message after the beep. Press star to end your recording.');
    }

    public function testShowHangup()
    {
        // When
        $hangupResponse = $this->call(
            'GET',
            route('recording-hangup')
        );
        $hangupDocument = new SimpleXMLElement($hangupResponse->getContent());

        // Then
        $this->assertNotNull($hangupDocument->Say);
        $this->assertNotEmpty($hangupDocument->Say);
        $this->assertNotNull($hangupDocument->Hangup);
        $this->assertNotEmpty($hangupDocument->Hangup);
        $this->assertEquals(strval($hangupDocument->Say), 'Your recording has been saved. Good bye.');
    }
}

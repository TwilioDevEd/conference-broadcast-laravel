<?php
use App\Agent;

class RecordingControllerTest extends TestCase
{
    public function testIndex()
    {
        // Given
        $mockTwilioClient = $this->getMockBuilder(\Twilio\Rest\Client::class)
            ->getMock();

        $mockRecordingDate = Mockery::mock();
        $mockRecordingDate2 = Mockery::mock();
        $mockTwilioRecording = Mockery::mock();
        $mockTwilioRecording->uri = '/some/uri';
        $mockTwilioRecording->dateCreated = $mockRecordingDate;
        $mockTwilioRecording2 = Mockery::mock();
        $mockTwilioRecording2->uri = '/some/other/uri';
        $mockTwilioRecording2->dateCreated = $mockRecordingDate2;

        $twilioRecordings = array($mockTwilioRecording, $mockTwilioRecording2);

        $mockRecordingDate
            ->shouldReceive('format')
            ->withAnyArgs()
            ->andReturn('some_date');
        $mockRecordingDate2
            ->shouldReceive('format')
            ->withAnyArgs()
            ->andReturn('some_other_date');

        $mockTwilioClient->recordings = Mockery::mock();
        $mockTwilioClient->recordings
            ->shouldReceive('read')
            ->andReturn($twilioRecordings);

        $this->app->instance(
            \Twilio\Rest\Client::class,
            $mockTwilioClient
        );

        // When
        $response = $this->call('GET', route('recording-index'));
        $recordingsJSON = json_decode($response->getContent());

        // Then
        $this->assertCount(2, $recordingsJSON);
        $this->assertEquals('https://api.twilio.com/some/uri', $recordingsJSON[0]->url);
        $this->assertEquals('some_other_date', $recordingsJSON[1]->date);
    }

    public function testCreate()
    {
        // Given
        $this->startSession();
        $twilioNumber = config('services.twilio')['number'];

        $mockTwilioClient = $this->getMockBuilder(\Twilio\Rest\Client::class)
            ->getMock();

        $mockTwilioClient->calls = Mockery::mock();

        $mockTwilioClient->calls
            ->shouldReceive('create')
            ->withAnyArgs();

        $this->app->instance(
            \Twilio\Rest\Client::class,
            $mockTwilioClient
        );

        // When
        $response = $this->call(
            'POST',
            route('recording-create'),
            [
                'phone_number' => '+123456',
                '_token' => csrf_token()
            ]
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

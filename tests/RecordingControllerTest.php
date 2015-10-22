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
        $twilioNumber = config('services.twilio')['number'];

        $mockTwilioService = Mockery::mock('Services_Twilio')
                                ->makePartial();
        $mockTwilioAccount = Mockery::mock();
        $mockTwilioCalls = Mockery::mock();
        $mockTwilioService->account = $mockTwilioAccount;
        $mockTwilioAccount->calls = $mockTwilioCalls;

        $mockTwilioCalls
            ->shouldReceive('create')
            ->with($twilioNumber, 'destination_number', 'some_url');

        $this->app->instance(
            'Services_Twilio',
            $mockTwilioService
        );

        // When
        $response = $this->call('POST', route('recording-create'));

        // Then
        $this->assertEquals($response->getContent(), 'call_create');
    }
}

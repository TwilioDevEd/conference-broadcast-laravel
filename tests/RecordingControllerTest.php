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
        $this->assertEquals(count($recordingsJSON), 2);
        $this->assertEquals($recordingsJSON[0]->url, 'https://api.twilio.com/some/uri');
        $this->assertEquals($recordingsJSON[1]->date, 'some_other_date');
    }
}

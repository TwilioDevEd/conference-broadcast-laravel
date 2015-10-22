<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Agent;
class BroadcastControllerTest extends TestCase
{
    public function testSend()
    {
        // Given
        $this->startSession();

        $mockTwilioService = Mockery::mock('Services_Twilio')
                                ->makePartial();
        $mockTwilioAccount = Mockery::mock();
        $mockTwilioCalls = Mockery::mock();
        $mockTwilioService->account = $mockTwilioAccount;
        $mockTwilioAccount->calls = $mockTwilioCalls;

        $mockTwilioCalls
            ->shouldReceive('create')
            ->withAnyArgs()
            ->twice();

        $this->app->instance(
            'Services_Twilio',
            $mockTwilioService
        );

        // When
        $response = $this->call(
            'POST',
            route('broadcast-send'),
            ['numbers' => '+123456,+45678',
            '_token' => csrf_token()]
        );
    }

    public function testShowPlay()
    {
        // When
        $this->startSession();
        $playResponse = $this->call(
            'POST',
            route('broadcast-play'),
            ['recording_url' => 'http://myurl.com',
            '_token' => csrf_token()]
        );
        $playDocument = new SimpleXMLElement($playResponse->getContent());
        // Then
        $this->assertNotNull($playDocument->Play);
        $this->assertEquals(strval($playDocument->Play), 'http://myurl.com');
    }
}

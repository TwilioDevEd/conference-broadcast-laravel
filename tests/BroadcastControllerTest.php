<?php
use App\Agent;

class BroadcastControllerTest extends TestCase
{
    public function testSend()
    {
        // Given
        $this->startSession();

        $mockTwilioClient = $this->getMockBuilder(\Twilio\Rest\Client::class)
            ->getMock();
        $mockTwilioCalls = Mockery::mock();
        $mockTwilioClient->calls = $mockTwilioCalls;

        $mockTwilioCalls
            ->shouldReceive('create')
            ->withAnyArgs()
            ->twice();

        $this->app->instance(
            \Twilio\Rest\Client::class,
            $mockTwilioClient
        );

        // When
        $response = $this->call(
            'POST',
            route('broadcast-send'),
            [
                'numbers' => '+123456,+45678',
                '_token' => csrf_token()
            ]
        );
    }

    public function testShowPlay()
    {
        // When
        $this->startSession();
        $playResponse = $this->call(
            'POST',
            route('broadcast-play'),
            [
                'recording_url' => 'http://myurl.com',
                '_token' => csrf_token()
            ]
        );
        $playDocument = new SimpleXMLElement($playResponse->getContent());

        // Then
        $this->assertNotNull($playDocument->Play);
        $this->assertEquals(strval($playDocument->Play), 'http://myurl.com');
    }
}

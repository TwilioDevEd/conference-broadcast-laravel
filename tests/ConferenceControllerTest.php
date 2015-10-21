<?php
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Agent;
class ConferenceControllerTest extends TestCase
{
    public function testShowJoin()
    {
        // When
        $joinResponse = $this->call('GET', route('conference-join'));
        $joinDocument = new SimpleXMLElement($joinResponse->getContent());
        // Then
        $this->assertNotNull($joinDocument->Say);
        $this->assertNotEmpty($joinDocument->Say);
        $this->assertNotNull($joinDocument->Gather);
        $this->assertNotEmpty($joinDocument->Gather);
        $this->assertEquals(strval($joinDocument->Say), 'You are about to join the Rapid Response conference.');
        $this->assertEquals(strval($joinDocument->Gather->children()[0]), 'Press 1 to join as a listener.');
    }

    public function testShowConnect()
    {
        // When
        $connectResponse = $this->call('GET', route('conference-connect', ['Digits' => '3']));
        $connectDocument = new SimpleXMLElement($connectResponse->getContent());
        // Then
        $this->assertNotNull($connectDocument->Say);
        $this->assertNotEmpty($connectDocument->Say);
        $this->assertNotNull($connectDocument->Dial);
        $this->assertNotEmpty($connectDocument->Dial);
        $this->assertEquals(strval($connectDocument->Say), 'You have joined the conference.');
    }
}

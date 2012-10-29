<?php

namespace Neblion\ScrumBundle\Tests\Controller;

use Neblion\ScrumBundle\Tests\WebTestCase;

class TeamControllerTest extends WebTestCase
{
    public function testShow()
    {
        $client = $this->login('admin', 'test');
        $crawler = $client->request('GET', '/team/1/show');
        // Test html template
        // Title of main widget-box
        $this->assertTrue($crawler->filter('html:contains("Team members")')->count() == 1);
        // 5 tr => 5 members for project->id = 1
        $this->assertEquals(5, $crawler->filter('table#validated-members tbody tr')->count());
    }
}

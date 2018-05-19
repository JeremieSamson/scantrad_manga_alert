<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('Scantrad Manga Alert', $crawler->filter('.navbar-brand')->text());
        $this->assertEquals('Soyez alerté en temps réel de la sortie de votre manga préféré', $crawler->filter('#alert')->text());
        $this->assertEquals('Manga', $crawler->filter("#form_manga .form-group label")->text());
        $this->assertEquals('select', $crawler->filter("#manga")->nodeName());
        $this->assertEquals(true, $crawler->filter("#manga")->children()->count() > 0);
    }
}

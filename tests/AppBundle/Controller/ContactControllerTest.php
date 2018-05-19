<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ContactControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/contact');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals('Adresse email*', $crawler->filter('.form-group label')->getNode(0)->textContent);
        $this->assertEquals('Sujet*', $crawler->filter('.form-group label')->getNode(1)->textContent);
        $this->assertEquals('Message*', $crawler->filter('.form-group label')->getNode(2)->textContent);

    }
}

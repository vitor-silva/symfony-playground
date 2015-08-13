<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Please sign in', $crawler->filter('h2.form-signin-heading')->text());

        $form = $crawler->selectButton('Login')->form();
        $crawler = $client->submit($form,
            array(
                '_username' => 'admin',
                '_password' => 'test',
            )
        );

        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();
        $this->assertContains(
            'Welcome',
            $client->getResponse()->getContent()
        );
    }
}

<?php

namespace Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServiceF_Test extends WebTestCase
{
    public function testCreateClub(): void
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/club/create');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');

        $form = $crawler->selectButton('Ajouter')->form([
            'club[nom]' => 'Club de Test',
            'club[ville]' => 'Marseille',
            'club[pays]' => 'France',
            'club[email]' => 'club@test.fr',
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/club'); 
        
        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Club de Test');
    }
}

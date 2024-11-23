<?php

namespace Unit;

use App\Entity\User;
use App\Entity\Club;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = $this->getMockForAbstractClass(User::class);
    }


    public function testGetSetNom(): void
    {
        $this->user->setNom('Joe');
        $this->assertEquals('Joe', $this->user->getNom());
    }



    public function testGetSetEmail(): void
    {
        $this->user->setEmail('john.joe@example.com');
        $this->assertEquals('john.joe@example.com', $this->user->getEmail());
        $this->assertEquals('john.joe@example.com', $this->user->getUserIdentifier());
    }

    public function testGetSetRoles(): void
    {
        $this->user->setRoles(['ROLE_ADMIN']);
        $this->assertEquals(['ROLE_ADMIN', 'ROLE_USER'], $this->user->getRoles());
    }
 

    public function testIsPresident(): void
    {
        $this->user->setRoles(['ROLE_PRESIDENT']);
        $this->assertTrue($this->user->isPresident());

        $this->user->setRoles([]);
        $this->assertFalse($this->user->isPresident());
    }
}

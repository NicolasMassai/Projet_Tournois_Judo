<?php

namespace Unit;

namespace App\Tests\Unit;

use App\Entity\Note;
use App\Entity\Combat;
use App\Service\NoteMoyenneService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase
{
    private $entityManager;
    private $noteMoyenneService;
    private $noteRepository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->noteRepository = $this->createMock(EntityRepository::class);
        $this->entityManager->method('getRepository')->willReturn($this->noteRepository);

        $this->noteMoyenneService = new NoteMoyenneService($this->entityManager);
    }

    public function testNoteMoyenneEtNombreVotantsAvecNotesIdentiques()
    {
        $combat = $this->createMock(Combat::class);
        $note1 = $this->createMock(Note::class);
        $note1->method('getNote')->willReturn(5);
        $note2 = $this->createMock(Note::class);
        $note2->method('getNote')->willReturn(5);
        $note3 = $this->createMock(Note::class);
        $note3->method('getNote')->willReturn(5);

        $this->noteRepository->method('findBy')->willReturn([$note1, $note2, $note3]);

        $result = $this->noteMoyenneService->NoteMoyenneEtNombreVotants($combat);

        $this->assertEquals(3, $result['totalVotants']);
        $this->assertEquals(5, $result['moyenne']);
    }

    public function testNoteMoyenneEtNombreVotantsAvecUneSeuleNote()
    {
        $combat = $this->createMock(Combat::class);
        $note1 = $this->createMock(Note::class);
        $note1->method('getNote')->willReturn(7);

        $this->noteRepository->method('findBy')->willReturn([$note1]);

        $result = $this->noteMoyenneService->NoteMoyenneEtNombreVotants($combat);

        $this->assertEquals(1, $result['totalVotants']);
        $this->assertEquals(7, $result['moyenne']);
    }

    public function testNoteMoyenneEtNombreVotantsAvecNotesNegatives()
    {
        $combat = $this->createMock(Combat::class);
        $note1 = $this->createMock(Note::class);
        $note1->method('getNote')->willReturn(-2);
        $note2 = $this->createMock(Note::class);
        $note2->method('getNote')->willReturn(-4);

        $this->noteRepository->method('findBy')->willReturn([$note1, $note2]);

        $result = $this->noteMoyenneService->NoteMoyenneEtNombreVotants($combat);

        $this->assertEquals(2, $result['totalVotants']);
        $this->assertEquals(-3, $result['moyenne']);
    }
    public function testNoteMoyenneEtNombreVotantsSansNotes()
{
    $combat = $this->createMock(Combat::class);

    $this->noteRepository->method('findBy')->willReturn([]);

    $result = $this->noteMoyenneService->NoteMoyenneEtNombreVotants($combat);

    $this->assertEquals(0, $result['totalVotants']);
    $this->assertNull($result['moyenne']);
}

}

<?php

namespace App\Controller;

use App\Entity\Tournament;
use App\Repository\TournamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/tournaments')]
class TournamentController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(TournamentRepository $repository): JsonResponse
    {
        return $this->json($repository->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(Tournament $tournament): JsonResponse
    {
        return $this->json($tournament);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $tournament = new Tournament();
        $tournament->setName($data['name'] ?? '');
        $tournament->setLocation($data['location'] ?? '');
        $tournament->setStartDate(new \DateTimeImmutable($data['startDate'] ?? 'now'));
        $tournament->setMaxTeams($data['maxTeams'] ?? null);

        $errors = $validator->validate($tournament);
        if (count($errors) > 0) {
            return $this->json(['errors' => (string) $errors], 400);
        }

        $em->persist($tournament);
        $em->flush();

        return $this->json($tournament, 201);
    }

    #[Route('/{id}', methods: ['PATCH'])]
    public function update(Tournament $tournament, Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        if (isset($data['name'])) {
            $tournament->setName($data['name']);
        }
        if (isset($data['location'])) {
            $tournament->setLocation($data['location']);
        }
        if (isset($data['startDate'])) {
            $tournament->setStartDate(new \DateTimeImmutable($data['startDate']));
        }
        if (isset($data['maxTeams'])) {
            $tournament->setMaxTeams($data['maxTeams']);
        }

        $em->flush();

        return $this->json($tournament);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(Tournament $tournament, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($tournament);
        $em->flush();

        return $this->json(null, 204);
    }
}

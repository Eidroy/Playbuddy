<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Swipes;
use App\Controller\SwipesController;
use App\Entity\Matches;

#[Route('/api', name: 'api_')]
class MatchesController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/matches', name: 'app_matches', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $matches = $this->em->getRepository(Matches::class)->findAll();
        $data = [];

        foreach ($matches as $match) {
            $data[] = [
                'id' => $match->getId(),
                'user_1_id' => $match->getUser1Id(),
                'user_2_id' => $match->getUser2Id(),
                'time' => $match->getTime()
            ];
        }

        return $this->json($data);
    }

    #[Route('/matches', name: 'app_matches_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $userId = $request->request->get('user_id');
        $swipedOnId = $request->request->get('swiped_on_id');
        $direction = $request->request->get('direction');
        $time = $request->request->get('time');

        $swipe = new Swipes();
        $swipe->setUserId($userId);
        $swipe->setSwipedOnId($swipedOnId);
        $swipe->setDirection($direction);

        $this->em->persist($swipe);
        $this->em->flush();

        if ($direction === 'left') {
            return $this->json(['status' => 'No match']);
        }

        $swipesRepository = $this->em->getRepository(Swipes::class);
        $mutualSwipe = $swipesRepository->findOneBy(['user_id' => $swipedOnId, 'swiped_on_id' => $userId, 'direction' => 'right']);

        if ($mutualSwipe) {
            $match = new Matches();
            $match->setUser1Id($userId);
            $match->setUser2Id($swipedOnId);
            $match->setTime(new \DateTime($time));

            $this->em->persist($match);
            $this->em->flush();

            return $this->json(['status' => 'Match created']);
        }

        return $this->json(['status' => 'No match']);
    }

    #[Route('/matches/{id}', name: 'app_matches_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $match = $this->em->getRepository(Matches::class)->find($id);

        if (!$match) {
            return $this->json(['status' => 'Match not found']);
        }

        $this->em->remove($match);
        $this->em->flush();

        return $this->json(['status' => 'Match deleted']);
    }

    #[Route('/matches/{id}', name: 'app_matches_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $matches = $this->em->getRepository(Matches::class)->findBy(['user_1_id' => $id]);
        $matches = array_merge($matches, $this->em->getRepository(Matches::class)->findBy(['user_2_id' => $id]));
        $data = [];
        $addedUserIds = [];
        foreach ($matches as $match) {
            $otherUserId = $match->getUser1Id() == $id ? $match->getUser2Id() : $match->getUser1Id();
            if (in_array($otherUserId, $addedUserIds)) {
                continue;
            }
            $addedUserIds[] = $otherUserId;
            $data[] = [
                'id' => $match->getId(),
                'user_1_id' => $match->getUser1Id(),
                'user_2_id' => $match->getUser2Id(),
                'time' => $match->getTime()
            ];
        }
    
        return $this->json($data);
        
    }
}

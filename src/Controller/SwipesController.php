<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Swipes;

#[Route('/api', name: 'api_')]
class SwipesController extends AbstractController
{
    #[Route('/swipes', name: 'app_swipes', methods: ['GET'])]
    public function index (entityManagerInterface $em): JsonResponse
    {
        $swipes = $em -> getRepository(Swipes::class) -> findAll();
        
        $data = [];
        
        foreach ($swipes as $swipe) {
            $data[] = [
                'id' => $swipe -> getId(),
                'user_id' => $swipe -> getUserId(),
                'swiped_on_id' => $swipe -> getSwipedOnId(),
                'direction' => $swipe -> getDirection(),
                'time'=> $swipe -> getTime()
            ];
        }

        return $this -> json($data);
    }

    #[Route('/swipes', name: 'app_swipes_create', methods: ['POST'])]
    public function create(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $swipe = new Swipes();
        $swipe->setUserId($request->request->get('user_id'));
        $swipe->setSwipedOnId($request->request->get('swiped_on_id'));
        $swipe->setDirection($request->request->get('direction'));
        $timeString = $request->get('time');
        $time = $timeString ? \DateTime::createFromFormat('Y-m-d H:i:s', $timeString) : null;
        if ($time) {
            $swipe->setTime($time);
        } else {
            $swipe->setTime(null);
        }

        $em->persist($swipe);
        $em->flush();

        $data = [
            'id' => $swipe->getId(),
            'user_id' => $swipe->getUserId(),
            'swiped_on_id' => $swipe->getSwipedOnId(),
            'direction' => $swipe->getDirection(),
            'time' => $swipe->getTime()
        ];

        return $this->json($data);
    }

    #[Route('/swipes/{id}', name: 'app_swipes_show', methods: ['GET'])]
    public function show(EntityManagerInterface $em, int $id): JsonResponse
    {
        $swipe = $em -> getRepository(Swipes::class) -> find($id);

        $data = [
            'id' => $swipe -> getId(),
            'user_id' => $swipe -> getUserId(),
            'swiped_on_id' => $swipe -> getSwipedOnId(),
            'direction' => $swipe -> getDirection(),
            'time' => $swipe -> getTime()
        ];

        return $this -> json($data);
    }

    #[Route('/swipes/{id}', name: 'app_swipes_update', methods: ['PUT', 'PATCH'])]
    public function update(EntityManagerInterface $em, Request $request, int $id): JsonResponse
    {
        $swipe = $em -> getRepository(Swipes::class) -> find($id);

        if (!$swipe) {
            return $this -> json(['message' => 'Swipe not found'], 404);
        }

        if (0 === strpos($request -> headers -> get('Content-Type'), 'application/json')) {
            $data = json_decode($request -> getContent(), true);
            $request -> request -> replace(is_array($data) ? $data : array());
        }

        $swipe -> setUserId($request -> request -> get('user_id'));
        $swipe -> setSwipedOnId($request -> request -> get('swiped_on_id'));
        $swipe -> setDirection($request -> request -> get('direction'));
        $timeString = $request->get('time');
        $time = \DateTime::createFromFormat('Y-m-d H:i:s', $timeString);
        $swipe -> setTime($time);
        $em -> flush();

        $data = [
            'id' => $swipe -> getId(),
            'user_id' => $swipe -> getUserId(),
            'swiped_on_id' => $swipe -> getSwipedOnId(),
            'direction' => $swipe -> getDirection(),
            'time' => $swipe -> getTime()
        ];

        return $this -> json($data);
    }

    #[Route('/swipes/{id}', name: 'app_swipes_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): JsonResponse
    {
        $swipe = $em -> getRepository(Swipes::class) -> find($id);

        if (!$swipe) {
            return $this -> json(['message' => 'Swipe not found'], 404);
        }

        $em -> remove($swipe);
        $em -> flush();

        return $this -> json(['message' => 'Swipe deleted']);
    }
}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;


#[Route('/api', name: 'api_')]
class UsersController extends AbstractController
{
    #[Route('/users', name: 'app_users', methods: ['GET'])]
    public function index (entityManagerInterface $em): JsonResponse
    {
        $users = $em -> getRepository(Users::class) -> findAll();
        $data = [];
        
        foreach ($users as $user) {
            $data[] = [
                'id' => $user -> getId(),
                'username' => $user -> getUsername(),
                'email' => $user -> getEmail(),
                'password' => $user -> getPassword(),
                'bio' => $user -> getBio(),
                'location' => $user -> getLocation(),
                'games' => $user -> getGames(),
                'platforms' => $user -> getPlatforms(),
                'skill_level' => $user -> getSkillLevel(),
                'profile_picture' => $user -> getProfilePicture()
            ];
        }

        return $this -> json($data);
    }
    
    #[Route('/users', name: 'app_users_create', methods: ['POST'])]
    public function create(EntityManagerInterface $em, Request $request): JsonResponse
    {
        $user = new Users();
        $user -> setUsername($request -> request -> get('username'));   
        $user -> setEmail($request -> request -> get('email'));
        $user -> setPassword($request -> request -> get('password'));
        $user -> setBio($request -> request -> get('bio'));
        $user -> setLocation($request -> request -> get('location'));
        $user -> setGames($request -> request -> get('games'));
        $user -> setPlatforms($request -> request -> get('platforms'));
        $user -> setSkillLevel($request -> request -> get('skill_level'));
        $user -> setProfilePicture($request -> request -> get('profile_picture'));

        $em -> persist($user);
        $em -> flush();

        $data = [
            'id' => $user -> getId(),
            'username' => $user -> getUsername(),
            'email' => $user -> getEmail(),
            'password' => $user -> getPassword(),
            'bio' => $user -> getBio(),
            'location' => $user -> getLocation(),
            'games' => $user -> getGames(),
            'platforms' => $user -> getPlatforms(),
            'skill_level' => $user -> getSkillLevel(),
            'profile_picture' => $user -> getProfilePicture()
        ];

        return $this -> json($data);
    }

    #[Route('/users/{id}', name: 'app_users_show', methods: ['GET'])]
    public function show(EntityManagerInterface $em, $id): JsonResponse
    {
        $user = $em -> getRepository(Users::class) -> find($id);

        $data = [
            'id' => $user -> getId(),
            'username' => $user -> getUsername(),
            'email' => $user -> getEmail(),
            'password' => $user -> getPassword(),
            'bio' => $user -> getBio(),
            'location' => $user -> getLocation(),
            'games' => $user -> getGames(),
            'platforms' => $user -> getPlatforms(),
            'skill_level' => $user -> getSkillLevel(),
            'profile_picture' => $user -> getProfilePicture()
        ];

        return $this -> json($data);
    }

    #[Route('/users/{id}', name: 'app_users_update', methods: ['PUT', 'PATCH'])]
    public function update(EntityManagerInterface $em, Request $request, int $id): JsonResponse
    {
        $user = $em -> getRepository(Users::class) -> find($id);

        if (!$user) {
            return $this -> json(['message' => 'User not found'], 404);
        }

        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
        }

        $user -> setUsername($request -> request -> get('username'));
        $user -> setEmail($request -> request -> get('email'));
        $user -> setPassword($request -> request -> get('password'));
        $user -> setBio($request -> request -> get('bio'));
        $user -> setLocation($request -> request -> get('location'));
        $user -> setGames($request -> request -> get('games'));
        $user -> setPlatforms($request -> request -> get('platforms'));
        $user -> setSkillLevel($request -> request -> get('skill_level'));
        $user -> setProfilePicture($request -> request -> get('profile_picture'));
        $em -> flush();

        $data = [
            'id' => $user -> getId(),
            'username' => $user -> getUsername(),
            'email' => $user -> getEmail(),
            'password' => $user -> getPassword(),
            'bio' => $user -> getBio(),
            'location' => $user -> getLocation(),
            'games' => $user -> getGames(),
            'platforms' => $user -> getPlatforms(),
            'skill_level' => $user -> getSkillLevel(),
            'profile_picture' => $user -> getProfilePicture()
        ];

        return $this -> json($data);
    }

    #[Route('/users/{id}', name: 'app_users_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, int $id): JsonResponse
    {
        $user = $em -> getRepository(Users::class) -> find($id);

        if (!$user) {
            return $this -> json(['message' => 'User not found'], 404);
        }

        $em -> remove($user);
        $em -> flush();

        return $this -> json(['message' => 'User deleted']);
    }

    #[Route('/users/{id}', name: 'app_users_profile_picture', methods: ['POST'])]
    public function profilePicture(EntityManagerInterface $em, Request $request, int $id): JsonResponse
    {
        $user = $em -> getRepository(Users::class) -> find($id);

        if (!$user) {
            return $this -> json(['message' => 'User not found'], 404);
        }

        $profilePicture = $request -> files -> get('profile_picture');
        $cloudinary = new Cloudinary([
            "cloud" => [
                "cloud_name" => "dlsx2xp32",
                "api_key" => "939582241287325",
                "api_secret" => "0Zri3GZaRG6b2fvhYliFJOPMVNI"],
            'url' => [
                'secure' => true
        ]]);
        $uploadResult = $cloudinary->uploadApi()->upload($profilePicture->getPathname(), [
            'folder' => 'PlayBuddy',
        ]);

        $user -> setProfilePicture($uploadResult['secure_url']);
        $em -> flush();

        $data = [
            'profile_picture' => $user -> getProfilePicture()
        ];

        return $this -> json($data);
    }
}

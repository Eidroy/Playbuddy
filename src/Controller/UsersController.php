<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;


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
       $user = $em->getRepository(Users::class)->find($id);
   
       if (!$user) {
           return $this->json(['message' => 'User not found'], 404);
       }
   
        if ($request->files->has('profile_picture')) {
            $profilePicture = $request->files->get('profile_picture');
            $cloudinary = new Cloudinary([
             "cloud" => [
                 "cloud_name" => "dlsx2xp32",
                 "api_key" => "939582241287325",
                 "api_secret" => "0Zri3GZaRG6b2fvhYliFJOPMVNI"
             ],
             'url' => [
                 'secure' => true
             ]
            ]);
        
            try {
             $uploadResult = $cloudinary->uploadApi()->upload($profilePicture->getPathname(), [
                 'folder' => 'PlayBuddy',
             ]);
             $user->setProfilePicture($uploadResult['secure_url']);
            } catch (\Exception $e) {
             return $this->json(['error' => 'Failed to upload profile picture: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
       
       // Update other user details if provided
       if ($request->request->has('username')) {
           $user->setUsername($request->request->get('username'));
       }
       if ($request->request->has('email')) {
           $user->setEmail($request->request->get('email'));
       }
       if ($request->request->has('password')) {
           // Assuming you hash the password before setting it
           $user->setPassword($request->request->get('password'));
       }
       if ($request->request->has('bio')) {
           $user->setBio($request->request->get('bio'));
       }
       if ($request->request->has('location')) {
           $user->setLocation($request->request->get('location'));
       }
       if ($request->request->has('games')) {
           $user->setGames($request->request->get('games'));
       }
       if ($request->request->has('platforms')) {
           $user->setPlatforms($request->request->get('platforms'));
       }
       if ($request->request->has('skill_level')) {
           $user->setSkillLevel($request->request->get('skill_level'));
       }
       
       $em->persist($user);
       $em->flush();
   
       $data = [
           'id' => $user->getId(),
           'username' => $user->getUsername(),
           'email' => $user->getEmail(),
           'password' => $user->getPassword(),
           'bio' => $user->getBio(),
           'location' => $user->getLocation(),
           'games' => $user->getGames(),
           'platforms' => $user->getPlatforms(),
           'skill_level' => $user->getSkillLevel(),
           'profile_picture' => $user->getProfilePicture()
       ];
   
       return $this->json($data);
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
}

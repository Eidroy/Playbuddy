<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Cloudinary;

#[Route('/api', name: 'api_')]
class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(EntityManagerInterface $em, Request $request): Response
    {
        $usernameOrEmail = $request->request->get('usernameOrEmail');
        $password = $request->request->get('password');

        $user = $em->getRepository(Users::class)->loadUserByIdenifier($usernameOrEmail);

        if (!$user) {
            return new Response('User not found', Response::HTTP_NOT_FOUND);
        }
        if ($user -> getPassword() !== $request -> request -> get('password')) {
            return $this -> json(['message' => 'Invalid password'], 401);
        }
        // return new Response('Login successful', Response::HTTP_OK);
        $token = bin2hex(random_bytes(32));
        
        $user -> setToken($token);
        $em -> persist($user);
        $em -> flush();

        return $this->json([
            'token' => $user->getToken(),
            'user_id' => $user->getId(),
            'username' => $user->getUsername(),
            'profile_picture' => $user->getProfilePicture(),
            'email' => $user->getEmail(),
        ]);
    }

#[Route('/register', name: 'app_register', methods: ['POST'])]
public function register(EntityManagerInterface $em, Request $request): Response
{
    $profilePicture = $request->files->get('profile_picture');
    if (!$profilePicture) {
        return $this->json(['error' => 'Profile picture is required'], Response::HTTP_BAD_REQUEST);
    }

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
    } catch (\Exception $e) {
        return $this->json(['error' => 'Failed to upload profile picture: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    $user = new Users();
    $user->setUsername($request->request->get('username'));
    $user->setPassword($request->request->get('password'));
    $user->setEmail($request->request->get('email'));
    $user->setBio($request->request->get('bio'));
    $user->setLocation($request->request->get('location'));
    $user->setGames($request->request->get('games'));
    $user->setPlatforms($request->request->get('platforms'));
    $user->setSkillLevel($request->request->get('skill_level'));
    $user->setProfilePicture($uploadResult['secure_url']);

    try {
        $em->persist($user);
        $em->flush();
    } catch (\Exception $e) {
        return $this->json(['error' => 'An error occurred while creating the user: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    return $this->json(['user created successfully']);
}
}
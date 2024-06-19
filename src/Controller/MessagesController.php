<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Messages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Users;

#[Route('/api', name: 'api_')]
class MessagesController extends AbstractController
{
   public function __construct(EntityManagerInterface $em)
   {
       $this->em = $em;
   }

    #[Route('/messages/conversation/{id}', name: 'app_messages_conversation', methods: ['GET'])]
    public function showConversation($id): Response
    {
        $messages = $this->em->getRepository(Messages::class)->findBy(['sender_id' => $id]);
        $messages = array_merge($messages, $this->em->getRepository(Messages::class)->findBy(['recipient_id' => $id]));
        $data = [];
        $conversation = [];

        foreach ($messages as $message) {
            if ($message->getSenderId() == $id) {
                $recipientId = $message->getRecipientId();
                $recipient = $this->em->getRepository(Users::class)->find($recipientId);
                $recipientUsername = $recipient->getUsername();
                $recipientProfilePicture = $recipient->getProfilePicture();
                $lastMessage = $message->getContent();
                $lastMessageTime = $message->getTime()->format('Y-m-d H:i:s');
                $conversation[$recipientId] = [
                    'username' => $recipientUsername,
                    'profile_picture' => $recipientProfilePicture,
                    'last_message' => $lastMessage,
                    'last_message_time' => $lastMessageTime
                ];
            } else {
                $senderId = $message->getSenderId();
                $sender = $this->em->getRepository(Users::class)->find($senderId);
                $senderUsername = $sender->getUsername();
                $senderProfilePicture = $sender->getProfilePicture();
                $lastMessage = $message->getContent();
                $lastMessageTime = $message->getTime()->format('Y-m-d H:i:s');
                $conversation[$senderId] = [
                    'username' => $senderUsername,
                    'profile_picture' => $senderProfilePicture,
                    'last_message' => $lastMessage,
                    'last_message_time' => $lastMessageTime
                ];
            }
        }
        // Sort the conversation based on the time of the last message
        usort($data, function ($a, $b) {
            return $b['last_message_time'] <=> $a['last_message_time'];
        });

        $data = array_values($conversation);
        return $this->json($data);
    }

    #[Route('/messages', name: 'app_messages_create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $message = new Messages();
        $message->setSenderId($request->request->get('sender_id'));
        $message->setRecipientId($request->request->get('recipient_id'));
        $message->setContent($request->request->get('message'));
        $time = $request->request->get('time');
        $message->setTime(\DateTime::createFromFormat('Y-m-d H:i:s', $time));

        $this->em->persist($message);
        $this->em->flush();

        $data = [
            'id' => $message->getId(),
            'sender_id' => $message->getSenderId(),
            'recipient_id' => $message->getRecipientId(),
            'message' => $message->getContent(),
            'time' => $message->getTime()
        ];

        return $this->json($data);
    }

    #[Route('/messages/conversation/{senderId}/{recipientId}', name: 'app_messages_conversation_between', methods: ['GET'])]
    public function showConversationBetween($senderId, $recipientId): Response
    {
        $messages = $this->em->getRepository(Messages::class)->findBy([
            'sender_id' => $senderId,
            'recipient_id' => $recipientId
        ]);

        $messages = array_merge($messages, $this->em->getRepository(Messages::class)->findBy([
            'sender_id' => $recipientId,
            'recipient_id' => $senderId
        ]));
        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'sender_id' => $message->getSenderId(),
                'recipient_id' => $message->getRecipientId(),
                'message' => $message->getContent(),
                'time' => $message->getTime()
            ];
        }

        usort($data, function ($a, $b) {
            return $a['id'] <=> $b['id'];
        });

        return $this->json($data);
    }

    #[Route('/messages/edit/{id}', name: 'app_messages_edit', methods: ['PUT', 'PATCH'])]
    public function editMessage(Request $request, $id): Response
    {
        $message = $this->em->getRepository(Messages::class)->find($id);
        if (!$message) {
            throw $this->createNotFoundException('Message not found');
        }
        $senderId = $message->getSenderId();
        if ($senderId != $request->request->get('sender_id')) {
            throw $this->createAccessDeniedException('You are not allowed to edit this message');
        }
        $message->setContent($request->request->get('message'));
        $this->em->flush();
        $data = [
            'id' => $message->getId(),
            'sender_id' => $message->getSenderId(),
            'recipient_id' => $message->getRecipientId(),
            'message' => $message->getContent(),
            'time' => $message->getTime()
        ];
        return $this->json($data);
    }

    #[Route('/messages/delete/{id}', name: 'app_messages_delete', methods: ['DELETE'])]
    public function deleteMessage(Request $request, $id): Response
    {
        $message = $this->em->getRepository(Messages::class)->find($id);
        if (!$message) {
            throw $this->createNotFoundException('Message not found');
        }
        $senderId = $message->getSenderId();
        if ($senderId != $request->request->get('sender_id')) {
            throw $this->createAccessDeniedException('You are not allowed to delete this message');
        }
        $this->em->remove($message);
        $this->em->flush();
        return $this->json(['message' => 'Message deleted successfully']);
    }

}

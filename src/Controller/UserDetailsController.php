<?php

namespace App\Controller;

use App\Entity\UserDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserDetailsController extends AbstractController
{
    #[Route('/user/details{userId}', name: 'app_user_details', methods: ['GET'])]
    public function search(EntityManagerInterface $entityManager, Request $request, int $userId): Response
    {
        $details = $entityManager->getRepository(UserDetails::class)->find($userId);
        return $this->render('user_details/index.html.twig', [
            'details' => $details,
            'controller_name' => 'UserDetailsController',
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('user')->orderBy('user.id','DESC');

        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
        return $this->render('user/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'UserController',
        ]);
    }
}

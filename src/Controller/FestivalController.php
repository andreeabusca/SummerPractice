<?php

namespace App\Controller;

use App\Entity\Festival;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FestivalController extends AbstractController
{
    #[Route('/festival', name: 'app_festival', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(Festival::class)->createQueryBuilder('festival')->orderBy('festival.id');

        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
        return $this->render('festival/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'FestivalController',
        ]);
    }
}

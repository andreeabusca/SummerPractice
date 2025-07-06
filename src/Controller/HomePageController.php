<?php

namespace App\Controller;

use App\Entity\Festival;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page')]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(Festival::class)->createQueryBuilder('festival')->orderBy('festival.start_date');
        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 12);
        return $this->render('home_page/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'HomePageController',
        ]);
    }
}

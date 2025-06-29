<?php

namespace App\Controller;

use App\Entity\Artist;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArtistController extends AbstractController
{
    #[Route('/artist', name: 'app_artist', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(Artist::class)->createQueryBuilder('artist')->orderBy('artist.name');

        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
        return $this->render('artist/index.html.twig', [
            'pagination'  => $pagination,
            'controller_name' => 'ArtistController',
        ]);
    }
}

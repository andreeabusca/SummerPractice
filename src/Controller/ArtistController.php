<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\FestivalArtist;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArtistController extends AbstractController
{
    #[Route('/artists', name: 'app_artist', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(Artist::class)->createQueryBuilder('artist')->orderBy('artist.name');

        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
        return $this->render('artist/index.html.twig', [
            'pagination'  => $pagination,
            'controller_name' => 'ArtistController',
        ]);
    }
    #[Route('/artists/{artistId}', name: 'app_artist_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $artistId, Request $request, PaginatorInterface $paginator): Response
    {
        $artist = $entityManager->find(Artist::class, $artistId);
        $concerts = $entityManager->getRepository(FestivalArtist::class)->findBy(['artist' => $artist]);
        $pagination = $paginator->paginate($concerts, $request->query->getInt('page', 1), 10);
        return $this->render('artist/show.html.twig', [
            'artist' => $artist,
            'pagination' => $pagination,
        ]);
    }
}

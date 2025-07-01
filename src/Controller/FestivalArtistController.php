<?php

namespace App\Controller;

use App\Entity\FestivalArtist;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FestivalArtistController extends AbstractController
{
    #[Route('/festival/artist', name: 'app_festival_artist')]
    public function index(EntityManagerInterface $entityManager,Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(FestivalArtist::class)->createQueryBuilder('concert')->orderBy('concert.id');

        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
        return $this->render('festival_artist/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'FestivalController',
        ]);
    }
    #[Route('/delete/concert/{concertId}', name: 'delete_concert', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, int $concertId): Response{

        $concert = $entityManager->getRepository(FestivalArtist::class)->find($concertId);
        $entityManager->remove($concert);
        $entityManager->flush();

        return $this->redirectToRoute('app_festival_artist');
    }

}

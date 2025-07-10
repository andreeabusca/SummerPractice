<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\FestivalArtist;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ArtistController extends AbstractController
{
    #[Route('/artists', name: 'app_artist', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $artistSearch = $request->query->get('artist');
        $queryBuilder = $entityManager->getRepository(Artist::class)->createQueryBuilder('artist')->orderBy('artist.name');

        if ($artistSearch) {
            $queryBuilder->andWhere('LOWER(artist.name) LIKE :artistName')
                ->setParameter('artistName', '%' . strtolower($artistSearch) . '%');
        }

        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
        return $this->render('artist/index.html.twig', [
            'pagination'  => $pagination,
            'artistSearch' => $artistSearch,
            'controller_name' => 'ArtistController',
        ]);
    }
    #[Route('/artists/show/{artistId}', name: 'app_artist_show', methods: ['GET'])]
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

    #[Route('/artists/create', name: 'app_artist_create', methods: ['GET','POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $artist = new Artist();

        $form = $this->createFormBuilder($artist)
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('musical_genre', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $artist->setMusicalGenre(strtolower($artist->getMusicalGenre()));
            $name = $artist->getName();
            $artist->setName(ucfirst($name));
            $entityManager->persist($artist);
            $entityManager->flush();
            return $this->redirectToRoute('app_artist_show', ['artistId' => $artist->getId()]);
        }else{
            return $this->render('artist/create.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/artists/edit/{artistId}', name: 'app_artist_edit', methods: ['GET','POST'])]
    public function edit(EntityManagerInterface $entityManager, int $artistId,  Request $request): Response{
        $artist = $entityManager->find(Artist::class, $artistId);
        if (!$artist) {
            throw $this->createNotFoundException('Artist not found.');
        }
        $form = $this->createFormBuilder($artist)
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('musical_genre', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $artist->setMusicalGenre(strtolower($artist->getMusicalGenre()));
            $name = $artist->getName();
            $artist->setName(ucfirst($name));
            $entityManager->flush();
            return $this->redirectToRoute('app_artist_show',['artistId' => $artistId]);
        }else{
            return $this->render('artist/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }
}

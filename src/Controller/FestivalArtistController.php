<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Festival;
use App\Entity\FestivalArtist;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FestivalArtistController extends AbstractController
{
    #[Route('/festival/artist', name: 'app_festival_artist')]
    public function index(EntityManagerInterface $entityManager,Request $request, PaginatorInterface $paginator): Response
    {

        $festivalSearch = $request->query->get('festival');
        $artistSearch = $request->query->get('artist');

        $queryBuilder = $entityManager->getRepository(FestivalArtist::class)
            ->createQueryBuilder('concert')
            ->join('concert.festival', 'f')
            ->join('concert.artist', 'a')
            ->addSelect('f', 'a');

        if ($festivalSearch) {
            $queryBuilder->andWhere('LOWER(f.name) LIKE :festivalName')
                ->setParameter('festivalName', '%' . strtolower($festivalSearch) . '%');
        }

        if ($artistSearch) {
            $queryBuilder->andWhere('LOWER(a.name) LIKE :artistName')
                ->setParameter('artistName', '%' . strtolower($artistSearch) . '%');
        }

        $queryBuilder->orderBy('concert.date', 'ASC');

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('festival_artist/index.html.twig', [
            'pagination' => $pagination,
            'festivalSearch' => $festivalSearch,
            'artistSearch' => $artistSearch,
        ]);


    }
    #[Route('/delete/concert/{concertId}', name: 'delete_concert', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, int $concertId): Response{

        $concert = $entityManager->getRepository(FestivalArtist::class)->find($concertId);
        $entityManager->remove($concert);
        $entityManager->flush();

        return $this->redirectToRoute('app_festival_artist');
    }

    #[Route('/festival/artist/concert/create',name: 'festival_artist_create', methods: ['GET','POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): Response{

        $festivalArtist = new FestivalArtist();
        $form = $this->createFormBuilder($festivalArtist)->add('festival', EntityType::class, [
                'class' => Festival::class,
                'choice_label' => 'name',
                'placeholder' => 'Select a Festival',
            ])
            ->add('artist', EntityType::class, [
                'class' => Artist::class,
                'choice_label' => 'name',
                'placeholder' => 'Select an Artist',
            ])
            ->add('date', DateTimeType::class)
            ->add('time_slot', TimeType::class)
            ->add('stage', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($festivalArtist);
            $entityManager->flush();
            return $this->redirectToRoute('app_festival_artist');
        }else{
            return $this->render('festival_artist/create.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }


}

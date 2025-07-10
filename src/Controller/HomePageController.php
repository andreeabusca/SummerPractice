<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\FestivalArtist;
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
        $festivalSearch = $request->query->get('festival');
        $artistSearch = $request->query->get('artist');

        $queryBuilder = $entityManager->getRepository(Festival::class)
            ->createQueryBuilder('f')
            ->leftJoin('f.concerts', 'fa')  // <-- Use 'concerts' here
            ->leftJoin('fa.artist', 'a')
            ->addSelect('fa', 'a')
            ->orderBy('f.start_date', 'ASC');

        if ($festivalSearch) {
            $queryBuilder->andWhere('LOWER(f.name) LIKE :festivalName')
                ->setParameter('festivalName', '%' . strtolower($festivalSearch) . '%');
        }

        if ($artistSearch) {
            $queryBuilder->andWhere('LOWER(a.name) LIKE :artistName')
                ->setParameter('artistName', '%' . strtolower($artistSearch) . '%');
        }

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('home_page/index.html.twig', [
            'pagination' => $pagination,
            'festivalSearch' => $festivalSearch,
            'artistSearch' => $artistSearch,
        ]);
    }
}

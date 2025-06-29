<?php

namespace App\Controller;

use App\Entity\Purchase;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'app_purchase')]
    public function index(EntityManagerInterface $entityManager,Request $request, PaginatorInterface $paginator): Response
    {
        $query = $entityManager->getRepository(Purchase::class)->createQueryBuilder('purchase')->leftJoin('purchase.user', 'user')->addSelect('user')->leftJoin('purchase.festival', 'festival')->addSelect('festival')->orderBy('purchase.id', 'DESC');

        $pagination = $paginator->paginate($query, $request->query->getInt('page', 1), 10);

        return $this->render('purchase/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
}

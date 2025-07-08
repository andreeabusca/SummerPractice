<?php

namespace App\Controller;

use App\Entity\Festival;
use App\Entity\Purchase;
use App\Entity\User;
use App\Repository\FestivalRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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

    #[Route('/purchase/new/{festivalId}/{userId}', name: 'app_purchase_new', methods: ['GET', 'POST'])]
    public function new(
        int $festivalId,
        int $userId,
        EntityManagerInterface $entityManager,
        Request $request,
        FestivalRepository $festivalRepo,
        UserRepository $userRepo
    ): Response {
        $festival = $festivalRepo->find($festivalId);
        $user = $userRepo->find($userId);
        $details = $user->getUserDetails();

        if (!$festival || !$user) {
            throw $this->createNotFoundException('Festival or User not found.');
        }

        $purchase = new Purchase();
        $purchase->setFestival($festival); // pre-fill the festival
        $purchase->setUser($user);         // optional: set user here too if needed

        $form = $this->createFormBuilder()
            ->add('quantity', IntegerType::class, [
                'label' => 'Number of Tickets',
                'data' => 1,
                'attr' => ['min' => 1],
            ])
            ->add('save', SubmitType::class, ['label' => 'Confirm Purchase'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $quantity = $form->get('quantity')->getData();

            for ($i = 0; $i < $quantity; $i++) {
                $newPurchase = new Purchase();
                $newPurchase->setFestival($festival);
                $newPurchase->setUser($user);
                $entityManager->persist($newPurchase);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home_page');
        }

        return $this->render('purchase/new.html.twig', [
            'form' => $form->createView(),
            'festival' => $festival,
            'user' => $user,
            'details' => $details,
        ]);
    }


}

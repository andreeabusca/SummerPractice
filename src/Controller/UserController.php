<?php

namespace App\Controller;

use App\Entity\User;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('user')->orderBy('user.id','DESC');

        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
        return $this->render('user/index.html.twig', [
            'pagination' => $pagination,
            'controller_name' => 'UserController',
        ]);
    }
    #[Route('/delete/{userId}', name: 'delete_user', methods: ['GET'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, int $userId): Response{

        $user = $entityManager->getRepository(User::class)->find($userId);
        foreach ($user->getPurchases() as $purchase) {
            $entityManager->remove($purchase);
        }
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user');
    }

    #[Route('/users/create', name: 'app_user_create', methods: ['GET','POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): Response{
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('email', \Symfony\Component\Form\Extension\Core\Type\EmailType::class)
            ->add('password',PasswordType::class)
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRole('normal');
            $user->setToken(bin2hex(string: random_bytes(16)));
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_details_create', ['userId' => $user->getId()]);
        }else{
            return $this->render('user/create.html.twig', [
                'form' => $form->createView(),
            ]);
        }

    }
}

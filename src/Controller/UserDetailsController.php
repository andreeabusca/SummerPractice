<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Entity\User;
use App\Entity\UserDetails;
use Doctrine\DBAL\Types\BooleanType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserDetailsController extends AbstractController
{
    #[Route('/user/details{userId}', name: 'app_user_details', methods: ['GET'])]
    public function search(EntityManagerInterface $entityManager, Request $request, int $userId, PaginatorInterface $paginator): Response
    {
        $details = $entityManager->getRepository(UserDetails::class)->find($userId);
        $purchases = $entityManager->getRepository(Purchase::class)->findBy(['user' => $userId]);
        $pagination = $paginator->paginate($purchases, $request->query->getInt('page', 1), 12);
        return $this->render('user_details/index.html.twig', [
            'details' => $details,
            'pagination' => $pagination,
            'controller_name' => 'UserDetailsController',
        ]);
    }

    #[Route('/user/details/create/{userId}', name: 'app_user_details_create', methods: ['GET','POST'])]
public function create(EntityManagerInterface $entityManager, Request $request, int $userId): Response
    {
        $user = $entityManager->getRepository(User::class)->find($userId);
        $details = new UserDetails();
        $details->setUserId($user);
        $form = $this->createFormBuilder($details)
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('age',IntegerType::class,[
                'label' => 'Age',
                'data' => 18, // default value
                'attr' => [
                    'min' => 18, // HTML5 client-side min attribute
                ],
            ])
            ->add('is_student', CheckboxType::class, [
                'required' => false,
                'label' => 'Are you a student?', // optional, for better UX
            ])
            ->add('save', SubmitType::class)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($details);
            $entityManager->flush();
            return $this->redirectToRoute('app_home_page', ['userId' => $userId]);
        }else{
            return $this->render('user_details/create.html.twig', [
                'form' => $form->createView(),
                'userId' => $userId,
            ]);
        }



    }
}

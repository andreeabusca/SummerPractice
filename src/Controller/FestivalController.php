<?php

namespace App\Controller;

use App\Entity\Festival;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FestivalController extends AbstractController
{
    #[Route('/festivals', name: 'app_festival', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager,Request $request, PaginatorInterface $paginator): Response
    {
        $festivalSearch = $request->query->get('festival');

        $queryBuilder = $entityManager->getRepository(Festival::class)->createQueryBuilder('festival')->orderBy('festival.id');

        if ($festivalSearch) {
            $queryBuilder->andWhere('LOWER(festival.name) LIKE :festivalName')
                ->setParameter('festivalName', '%' . strtolower($festivalSearch) . '%');
        }

        $pagination = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), 10);
        return $this->render('festival/index.html.twig', [
            'pagination' => $pagination,
            'festivalSearch' => $festivalSearch,
            'controller_name' => 'FestivalController',
        ]);
    }

    #[Route('/festivals/create', name: 'app_festival_create', methods: ['GET','POST'])]
    public function create(EntityManagerInterface $entityManager, Request $request): Response
    {
        $festival = new Festival();

        $form = $this->createFormBuilder($festival)
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('location', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('startDate', DateTimeType::class)
            ->add('endDate', DateTimeType::class)
            ->add('price', IntegerType::class)
            ->add('save', SubmitType::class);


        $form->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $startDate = $data->getStartDate();
            $endDate = $data->getEndDate();

            if ($startDate && $endDate && $endDate < $startDate) {
                $form->get('endDate')->addError(new FormError('End date must be after start date.'));
            }
        });
        $form = $form->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $festival->getLocation();
            $festival->setLocation(ucfirst($location));
            $entityManager->persist($festival);
            $entityManager->flush();
            return $this->redirectToRoute('app_festival');
        }else{
            return $this->render('festival/create.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }

    #[Route('/festivals/show/{festivalId}', name: 'app_festival_show', methods: ['GET'])]
    public function show(EntityManagerInterface $entityManager, int $festivalId, Request $request, PaginatorInterface $paginator): Response
    {
        $festival = $entityManager->find(Festival::class, $festivalId);
        if (!$festival) {
            throw $this->createNotFoundException('Festival not found.');
        }
        $concerts = $festival->getConcerts();
        $pagination = $paginator->paginate($concerts, $request->query->getInt('page', 1), 10);
        return $this->render('festival/show.html.twig', [
            'festival' => $festival,
            'pagination' => $pagination,
        ]);
    }

    #[Route('/festivals/edit/{festivalId}', name: 'app_festival_edit', methods: ['GET','POST'])]
    public function edit(EntityManagerInterface $entityManager, int $festivalId,  Request $request): Response{
        $festival = $entityManager->find(Festival::class, $festivalId);
        if (!$festival) {
            throw $this->createNotFoundException('Festival not found.');
        }
        $form = $this->createFormBuilder($festival)
            ->add('name', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('location', \Symfony\Component\Form\Extension\Core\Type\TextType::class)
            ->add('startDate', DateTimeType::class)
            ->add('endDate', DateTimeType::class)
            ->add('price', IntegerType::class)
            ->add('save', SubmitType::class);

        $form->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $startDate = $data->getStartDate();
            $endDate = $data->getEndDate();

            if ($startDate && $endDate && $endDate < $startDate) {
                $form->get('endDate')->addError(new FormError('End date must be after start date.'));
            }
        });
        $form = $form->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $location = $festival->getLocation();
            $festival->setLocation(ucfirst($location));
            $entityManager->flush();
            return $this->redirectToRoute('app_festival_show',['festivalId' => $festivalId]);
        }else{
            return $this->render('festival/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }


}

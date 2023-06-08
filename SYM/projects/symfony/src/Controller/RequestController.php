<?php

// src/Controller/RequestController.php

namespace App\Controller;

use App\Entity\Request;
use App\Form\RequestType;
use App\Repository\RequestRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class RequestController extends AbstractController
{
    private $requestRepository;
    private $entityManager;

    public function __construct(RequestRepository $requestRepository, EntityManagerInterface $entityManager)
    {
        $this->requestRepository = $requestRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/request', name: 'request')]
    public function submitRequest(HttpRequest $request): Response
    {
        $form = $this->createForm(RequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $requestData = $form->getData();

            $requestEntity = new Request();
            $requestEntity->setName($requestData->getName())
                ->setEmail($requestData->getEmail())
                ->setSubject($requestData->getSubject())
                ->setMessage($requestData->getMessage())
                ->setCreatedAt(new DateTimeImmutable()); // Create a DateTimeImmutable object here

            // Save the request entity to the database
            $this->entityManager->persist($requestEntity);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('request/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/request/list', name: 'request_show')]
    public function showRequests(): Response
    {
        $requests = $this->requestRepository->findAll();

        return $this->render('request/index.html.twig', [
            'requests' => $requests,
        ]);
    }

}

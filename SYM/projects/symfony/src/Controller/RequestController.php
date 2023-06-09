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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


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
                ->setCreatedAt(new DateTimeImmutable()) // Create a DateTimeImmutable object here
                ->setUser($this->getUser());


            $this->entityManager->persist($requestEntity);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_dashboard');
        }

        return $this->render('request/form.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/request/list', name: 'request_show')]
    public function showRequests(): Response
    {
        $user = $this->getUser();
        $requests = $this->requestRepository->findAll();
        if (!$user || $user->getEmail() !== 'admin@mail.com') {
            throw new AccessDeniedException();
        }


        return $this->render('request/index.html.twig', [
            'requests' => $requests,
        ]);


    }
#[Route('/request/my-requests', name: 'my_requests')]
public function showMyRequests(RequestRepository $requestRepository): Response{
        $user = $this->getUser();
    if (!$user) {
        throw $this->createAccessDeniedException('Доступ запрещен');
    }
    $requests = $requestRepository->findBy(['user' => $user]);
    return $this->render('request/my_requests.html.twig', [
        'requests' => $requests,
    ]);
}
    #[Route('/request/{id}/update-status', name: 'request_update_status', methods: ['POST'])]
    public function updateStatus(HttpRequest $httpRequest, int $id): Response
    {
        $user = $this->getUser();
        if (!$user || $user->getEmail() !== 'admin@mail.com') {
            throw new AccessDeniedException();
        }

        $status = $httpRequest->request->get('status');
        $requestEntity = $this->requestRepository->find($id);

        if (!$requestEntity) {
            throw $this->createNotFoundException('Request not found');
        }

        $requestEntity->setStatus($status);
        $this->entityManager->flush();

        return $this->redirectToRoute('request_show');
    }




}

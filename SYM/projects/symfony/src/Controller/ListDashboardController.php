<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ListDashboardController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    #[Route('/list/dashboard', name: 'app_list_dashboard')]
    public function index(): Response
    {
        $user = $this->getUser();

        // Check if the user is authenticated and has the required email
        if (!$user || $user->getEmail() !== 'admin@mail.com') {
            throw new AccessDeniedException();
        }

        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        return $this->render('list_dashboard/index.html.twig', [
            'users' => $users,
        ]);
    }
}

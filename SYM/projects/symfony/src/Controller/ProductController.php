<?php

// src/Controller/ProductController.php
namespace App\Controller;

// ...
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product/new', name: 'create_product')]
    public function createProduct(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_list');
        }

        return $this->render('product/new.html.twig', ['form' => $form->createView()]);

    }

    #[Route('/products', name: 'product_list')]
    public function listProducts(EntityManagerInterface $entityManager): Response
    {
        $products = $entityManager->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', ['products' => $products]);
    }
}

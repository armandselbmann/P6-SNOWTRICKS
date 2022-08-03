<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(TrickRepository $repository): Response
    {
        $trick = $repository->findAll(Trick::class);
        return $this->render('homepage/homepage.html.twig', [
            'controller_name' => 'HomepageController',
            'tricks' => $trick
        ]);
    }
}

<?php

namespace App\Controller;

use App\Repository\TrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    const TRICKS_DISPLAY_STARTING = 8;
    const TRICKS_PER_LOADING = 4;

    #[Route('/', name: 'homepage')]
    public function index(TrickRepository $trickRepository): Response
    {
        // Counting total number of Tricks
        $totalAllTricks = $trickRepository->countAllTricks();
        // Select first tricks to display
        $tricksToDisplay = $trickRepository->getFirstTricks(self::TRICKS_DISPLAY_STARTING);

        return $this->render('homepage/homepage.html.twig', [
            'totalAllTricks' => $totalAllTricks,
            'tricksToDisplay' => $tricksToDisplay,
            'totalDisplayTricks' => self::TRICKS_DISPLAY_STARTING,
            'tricksPerLoading' => self::TRICKS_PER_LOADING,
        ]);
    }

    #[Route('/getData', name: 'get_data', methods: 'POST')]
    public function getData(Request $request, TrickRepository $trickRepository)
    {
        // configuration
        $tricksAlreadyLoaded = $request->get('totalDisplayTricks');
        // selecting posts
        $tricksToDisplay = $trickRepository->getMoreTricks($tricksAlreadyLoaded, self::TRICKS_PER_LOADING);

        return $this->render('trick/index.html.twig', [
            'tricksToDisplay' => $tricksToDisplay,
        ]);
    }

}

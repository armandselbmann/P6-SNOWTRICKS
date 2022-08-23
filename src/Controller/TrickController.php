<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    #[Route('/trick/{id}', name: 'trick')]
    public function show(Trick $trick): Response
    {
        return $this->render('trick/trick.html.twig', [
            'trick' => $trick
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_trick', methods: ['GET'])]
    public function delete(Trick $trick, ManagerRegistry $doctrine): Response
    {
        $manager = $doctrine->getManager();

        $manager->remove($trick);
        $manager->flush();

        return $this->redirectToRoute('homepage');
    }

}

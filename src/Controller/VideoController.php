<?php

namespace App\Controller;

use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/video')]
class VideoController extends AbstractController
{

    #[Route('/delete/{id}', name: 'app_video_delete', methods: 'DELETE')]
    public function deleteVideo(Video $video, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification de la validité du token / Checking token validity
        if($this->isCsrfTokenValid('delete'.$video->getId(), $data['_token'])){

            // On supprime de la base de données / Delete in Database
            $entityManager->remove($video);
            $entityManager->flush();

            // On répond en json / Json response
            return new JsonResponse(['success' => 1]);
        }
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }

}

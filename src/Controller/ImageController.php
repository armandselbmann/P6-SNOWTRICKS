<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/image')]
class ImageController extends AbstractController
{

    #[Route('/delete/{id}', name: 'app_image_delete', methods: 'DELETE')]
    public function deleteImage(Image $image, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification de la validité du token / Checking token validity
        if($this->isCsrfTokenValid('delete'.$image->getId(), $data['_token'])){

            // On supprime le fichier / Delete file
            unlink($this->getParameter('images_directory') . '/' . $image->getName());
            // On supprime de la base de données / Delete in Database
            $entityManager->remove($image);
            $entityManager->flush();

            // On répond en json / Json response
            return new JsonResponse(['success' => 1]);
        }
        return new JsonResponse(['error' => 'Token invalide.'], 400);
    }

}

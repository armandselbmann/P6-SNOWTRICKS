<?php

namespace App\Controller;

use App\Entity\Comment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/comment')]
class CommentController extends AbstractController
{

    #[Route('/delete/{id}', name: 'app_comment_delete', methods: 'DELETE')]
    public function deleteComment(Comment $comment, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification de la validité du token / Checking token validity
        if($this->isCsrfTokenValid('delete'.$comment->getId(), $data['_token'])){

            // On supprime de la base de données / Delete in Database
            $entityManager->remove($comment);
            $entityManager->flush();

            // On répond en json / Json response
            return new JsonResponse(['success' => 1]);
        }
        return new JsonResponse(['error' => 'Token invalide'], 400);
    }

}

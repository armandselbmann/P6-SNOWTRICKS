<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Service\ImageService;
use App\Service\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/trick')]
class TrickController extends AbstractController
{

    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        TrickRepository $trickRepository,
        VideoService $videoService,
        ImageRepository $imageRepository,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response
    {
        $trick = new Trick();
        $imageService = new ImageService(
            $this->getParameter('images_directory'),
            $imageRepository,
            $entityManager,
            $trick);

        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageService->processingFeaturedImage($form->get('featured_image')->getData());
            $imageService->processingImages($form->get('images')->getData());
            $videoService->processingVideos($trick->getVideos());

            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUsers($this->getUser());

            $slug = strtolower($slugger->slug($trick->getName()));
            $trick->setSlug($slug);

            $trickRepository->add($trick, true);

            $this->addFlash('success', 'La figure vient d\'être enregistrée.');
            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'trickForm' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(
        Trick $trick,
        Request $request,
        EntityManagerInterface $entityManager,
        CommentRepository $commentRepository,
    ): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setTricks($trick);
            $comment->setUsers($this->getUser());
            $comment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire vient d\'être ajouté.');
            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()]);
        }

        $limitComments = 5;
        $page = (int)$request->query->get("page", 1);
        $idTrick = $trick->getId();
        $comments = $commentRepository->getPaginatedComments($idTrick, $page, $limitComments);
        $totalComments = $commentRepository->getTotalComments($idTrick);

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comments' => $comments,
            'totalComments' => $totalComments,
            'limit' => $limitComments,
            'page' => $page,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/edit/{slug}', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Trick $trick,
        TrickRepository $trickRepository,
        ImageRepository $imageRepository,
        VideoService $videoService,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $imageService = new ImageService(
            $this->getParameter('images_directory'),
            $imageRepository,
            $entityManager,
            $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageService->processingFeaturedImage($form->get('featured_image')->getData());
            $imageService->processingImages($form->get('images')->getData());
            $videoService->processingVideos($trick->getVideos());

            $trick->setUpdatedAt(new \DateTimeImmutable());

            $slug = strtolower($slugger->slug($trick->getName()));
            $trick->setSlug($slug);

            $trickRepository->add($trick, true);

            $this->addFlash('success', 'Modification effectuée.');
            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_trick_delete', methods: ['GET', 'POST'])]
    public function delete(Trick $trick, TrickRepository $trickRepository, Filesystem $filesystem): Response
    {
        $images = $trick->getImages();
        foreach($images as $image){
            $fileName = $this->getParameter('images_directory') . '/' . $image->getName();
            if($filesystem->exists($fileName)){
                $filesystem->remove($fileName);
            }
        }
        $name = $trick->getName();
        $trickRepository->remove($trick, true);

        $this->addFlash('info', "Vous venez de supprimer la figure $name" );
        return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
    }
}

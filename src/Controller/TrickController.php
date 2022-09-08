<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use App\Service\VideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        VideoService $videoService
    ): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // ---- FEATURED IMAGE PROCESSING ---- //

            // Récupération de l'image mise en avant / Featured image recovery
            $uploadedFeaturedImage = $form->get('featured_image')->getData();

            // Si il y a une image on traite / If there is an image we process
            if($uploadedFeaturedImage) {

                // Récupération du nom du fichier / File name recovery
                $originalFeaturedFilename = pathinfo($uploadedFeaturedImage->getClientOriginalName(), PATHINFO_FILENAME);

                // Génération d'un nouveau nom de fichier / Generating a new file name
                $newFeaturedFilename = $originalFeaturedFilename. '-' . md5(uniqid()) . '.' . $uploadedFeaturedImage->guessExtension();

                // Copie du fichier dans le dossier Upload / Copy file to upload folder
                $uploadedFeaturedImage->move(
                    $this->getParameter('images_directory'),
                    $newFeaturedFilename
                );

                // Stockage de l'image dans la base de données / Storing image file name in database
                $img = new Image();
                $img->setName($newFeaturedFilename);
                $img->setfeaturedImage(true);
                $trick->addImage($img);
            }

            // ----  IMAGE PROCESSING ---- //

            // Récupération des images transmises / Retrieval of transmitted images
            $uploadedImages = $form->get('images')->getData();

            foreach($uploadedImages as $image) {
                // Récupération du nom du fichier / File name recovery
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);

                // Génération d'un nouveau nom de fichier / Generating a new file name
                $newFilename = $originalFilename. '-' . md5(uniqid()) . '.' . $image->guessExtension();

                // Copie du fichier dans le dossier Upload / Copy file to upload folder
                $image->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );

                // Stockage de l'image dans la base de données / Storing image file name in database
                $img = new Image();
                $img->setName($newFilename);
                $trick->addImage($img);
                }

            // ---- VIDEO PROCESSING ---- //

            // Récupération de tous les liens sous forme de tableau / Retrieving all links as an array
            $videos = $trick->getVideos();

            foreach($videos as $video){
                // Récupération du lien / Link recovery
                $link = $video->getLink();

                // Vérification du lien / Link verification
                $checkVideoLink = $videoService->checkVideoLink($link);
                //Enregistrement du lien en base de données / Saving link in database
                $video->setLink($checkVideoLink);
            }

            // ---- SET DATE AND USER FOR THIS NEW TRICK ---- //

            $trick->setCreatedAt(new \DateTimeImmutable());
            $trick->setUsers($this->getUser());

            // ---- ADD TRICK, FLASH VALIDATION AND REDIRECTION ---- //

            $trickRepository->add($trick, true);

            $this->addFlash('success', 'La figure vient d\'être enregistrée.');

            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'trickForm' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_show', methods: ['GET'])]
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trickRepository->add($trick, true);

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'trickForm' => $form,
        ]);
    }

    #[Route('/delete/{{id}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $images = $trick->getImages();
        if($images){
            foreach($images as $image){
                $fileName = $this->getParameter('images_directory') . '/' . $image->getName();
                if(file_exists($fileName)){
                    unlink($fileName);
                }
            }
        }
        $name = $trick->getName();
        if ($this->isCsrfTokenValid('delete'.$trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
        }

        $this->addFlash('info', "Vous venez de supprimer la figure $name" );
        return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
    }
}

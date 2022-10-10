<?php

namespace App\Service;

use App\Entity\Image;
use App\Entity\Trick;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageService
{
    private string $targetDirectory;
    private ImageRepository $imageRepository;
    private EntityManagerInterface $entityManager;
    private Filesystem $filesystem;
    private Trick $trick;

    public function __construct(
        $targetDirectory,
        ImageRepository $imageRepository,
        EntityManagerInterface $entityManager,
        Filesystem $filesystem,
        Trick $trick)
    {
        $this->targetDirectory = $targetDirectory;
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
        $this->filesystem = $filesystem;
        $this->trick = $trick;
    }

    /**
     * Image processing
     *
     * @param $images
     * @return void
     */
    public function processingImages($images): void
    {
        foreach($images as $image) {
            self::uploadImage($image,false);
        }
    }

    /**
     * Feature image processing
     *
     * @param $featuredImage
     * @return void
     */
    public function processingFeaturedImage($featuredImage): void
    {
        if($featuredImage) {
            self::checkFeaturedImage();
            self::uploadImage($featuredImage,true);
        }
    }

    /**
     * Checking the featured image
     *
     * @return void
     */
    private function checkFeaturedImage(): void
    {
        // Vérification si image mise en avant déjà présente pour cette figure
        $checkFeaturedImage = $this->imageRepository->findOneBy(['tricks' => $this->trick->getId(), 'featuredImage' => true]);

        // Si oui on supprime cette image
        if ($checkFeaturedImage) {
            // On supprime le fichier
            $this->filesystem->remove($this->targetDirectory.'/'.$checkFeaturedImage->getName());
            // On supprime de la base de données
            $this->entityManager->remove($checkFeaturedImage);
            $this->entityManager->flush();
        }
    }

    /**
     * Upload image files
     *
     * @param UploadedFile $file
     * @param bool $featuredImg
     * @return void
     */
    public function uploadImage(UploadedFile $file, bool $featuredImg): void
    {
        // Récupération du nom du fichier / File name recovery
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // Génération d'un nouveau nom de fichier / Generating a new file name
        $newFilename = $originalFilename.'-'.md5(uniqid()).'.'.$file->guessExtension();
        // Copie du fichier dans le dossier Upload / Copy file to upload folder
        $file->move(
            $this->targetDirectory,
            $newFilename
        );
        // Stockage de l'image dans la base de données / Storing image file name in database
        $img = new Image();
        $img->setName($newFilename);
        if($featuredImg) {
            $img->setfeaturedImage(true);
        }
        $this->trick->addImage($img);
    }

}
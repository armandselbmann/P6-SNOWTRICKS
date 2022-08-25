<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\JWTService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    const SUBJECT_MAIL = 'Demande d\'inscription - Snowtricks';
    const TEMPLATE_MAIL = 'registration';

    /**
     * User registration
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param MailerService $mailer
     * @param JWTService $jwt
     * @return Response
     */
    #[Route('/registration', name: 'registration')]
    public function index(Request $request,
                          ManagerRegistry $doctrine,
                          UserPasswordHasherInterface $userPasswordHasher,
                          MailerService $mailer,
                          JWTService $jwt,
    ): Response
    {
        $manager = $doctrine->getManager();
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $manager->persist($user);
            $manager->flush();

            // Génération du JWT de l'utilisateur / Generation of the user's JWT
            // Création du header / Header creation
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            // Création du Payload / Payload creation
            $payload = [
                'user_id' => $user->getId()
            ];

            // Génération du token / Token generation
            $token = $jwt->generate($header, $payload, $this->getParameter('jwt_secret'), '3600');

            // Envoie du mail / Send mail
            $mailer->sendEmail(
                $user->getEmail(),
                self::SUBJECT_MAIL,
                self::TEMPLATE_MAIL,
                $user->getUsername(),
                $token
            );

            $this->addFlash(
                'success',
                'Votre demande a été envoyé avec succès. Validez votre inscription via le lien présent dans le mail que vous avez reçu.'
            );

            return $this->redirectToRoute('homepage');
        }

        return $this->render('registration/registration.html.twig', [
            'formRegistration' => $form->createView(),
        ]);
    }

    /**
     * Registration verification
     *
     * @param $token
     * @param JWTService $jwt
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $doctrine
     * @return Response
     */
    #[Route('/verif/{token}', name: 'verify_registration')]
    public function verifyRegistration($token,
                                       JWTService $jwt,
                                       UserRepository $userRepository,
                                       EntityManagerInterface $doctrine
    ): Response
    {
        // Vérification du token : validité, expiration, modification / Token verification : validity, expiration, modification
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->checkSignature($token, $this->getParameter('jwt_secret')))
        {
            // Récupération du Payload / Payload recovery
            $payload = $jwt->getPayload($token);
            // Récupération du user du token / Recovery of the token user
            $user = $userRepository->find($payload['user_id']);
            // Vérification de l'utilisateur et son activation du compte / User verification and account activation
            if($user && !$user->isIsActive())
            {
                $user->setIsActive(true);
                $doctrine->flush($user);
                $this->addFlash('success', 'Félicitation, vous venez d\'activer votre compte.');
                return $this->redirectToRoute('homepage');
            }
            $this->addFlash('warning', 'Vous avez déjà activé votre compte');
            return $this->redirectToRoute('homepage');
        }
        // Si le token n'est pas valide / If unvalidated token
        $this->addFlash('danger',
            'Le Token est invalide ou a expiré.');
        return $this->redirectToRoute('login');
    }
}

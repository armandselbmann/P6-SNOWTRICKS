<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Service\AvatarService;
use App\Service\JWTService;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param MailerService $mailer
     * @param JWTService $jwt
     * @param AvatarService $avatarService
     * @return Response
     * @throws Exception
     */
    #[Route('/registration', name: 'registration')]
    public function index(Request $request,
                          EntityManagerInterface $entityManager,
                          UserPasswordHasherInterface $userPasswordHasher,
                          MailerService $mailer,
                          JWTService $jwt,
                          AvatarService $avatarService,
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $avatarFile = $form->get('avatar')->getData();
            if ($avatarFile) {
                $avatarFileName = $avatarService->upload($avatarFile);
                $user->setAvatar($avatarFileName);
            }

            $hashedPassword = $userPasswordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            // G??n??ration du JWT de l'utilisateur / Generation of the user's JWT
            // Cr??ation du header / Header creation
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT'
            ];

            // Cr??ation du Payload / Payload creation
            $payload = [
                'user_id' => $user->getId()
            ];

            // G??n??ration du token / Token generation
            $token = $jwt->generate($header, $payload, $this->getParameter('jwt_secret'), '3600');

            // Cr??ation des donn??es du mail / Create email data
            $context = compact('token', 'user');

            // Envoie du mail / Send mail
            $mailer->sendEmail(
                $user->getEmail(),
                self::SUBJECT_MAIL,
                self::TEMPLATE_MAIL,
                $context
            );

            $this->addFlash(
                'success',
                'Votre demande a ??t?? envoy?? avec succ??s. Validez votre inscription via le lien pr??sent dans le mail que vous avez re??u.'
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
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/verif/{token}', name: 'verify_registration')]
    public function verifyRegistration($token,
                                       JWTService $jwt,
                                       UserRepository $userRepository,
                                       EntityManagerInterface $entityManager
    ): Response
    {
        // V??rification du token : validit??, expiration, modification / Token verification : validity, expiration, modification
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->checkSignature($token, $this->getParameter('jwt_secret')))
        {
            // R??cup??ration du Payload / Payload recovery
            $payload = $jwt->getPayload($token);
            // R??cup??ration du user du token / Recovery of the token user
            $user = $userRepository->find($payload['user_id']);
            // V??rification de l'utilisateur et son activation du compte / User verification and account activation
            if($user && !$user->isIsActive())
            {
                $user->setIsActive(true);
                $entityManager->flush($user);
                $this->addFlash('success', 'F??licitation, vous venez d\'activer votre compte.');
                return $this->redirectToRoute('homepage');
            }
            $this->addFlash('warning', 'Vous avez d??j?? activ?? votre compte');
            return $this->redirectToRoute('homepage');
        }
        // Si le token n'est pas valide / If unvalidated token
        $this->addFlash('danger',
            'Le Token est invalide ou a expir??.');
        return $this->redirectToRoute('login');
    }
}

<?php

namespace App\Controller;

use App\Form\ResetPasswordRequestType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    const SUBJECT_MAIL = 'Demande de réinitialisation du mot de passe - Snowtricks';
    const TEMPLATE_MAIL = 'reset-password';

    /**
     * Espace de connexion / Connection space
     *
     * @param AuthenticationUtils $authentificationUtils
     * @return Response
     */
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authentificationUtils): Response
    {
        //get the login error if there is one
        $error = $authentificationUtils->getLastAuthenticationError();

        //last username entered by the user
        $lastUsername = $authentificationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Mot de passe oublié / Forgot password
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @param EntityManagerInterface $entityManager
     * @param MailerService $mailer
     * @return Response
     */
    #[Route('/forgot-password', name: 'forgot_password')]
    public function forgotPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        MailerService $mailer
    ): Response
    {
        $form = $this->createForm(ResetPasswordRequestType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            // Récupération de l'utilisateur par son nom / Get user by his name
            $user = $userRepository->findOneByUsername($form->get('username')->getData());

            // Vérification de la variable user / User variable check
            if(!$user){
                $this->addFlash('danger', 'Cet utilisateur n\'existe pas.');
                return $this->redirectToRoute('forgot_password');
            }

            // Génération du token / Token generate
            $token = $tokenGenerator->generateToken();
            $user->setResetToken($token);
            $entityManager->persist($user);
            $entityManager->flush();

            // Génération du lien de réinitialisation / Generation of the reset link
            $urlToken = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

            // Création des données du mail / Create email data
            $context = compact('urlToken', 'user');

            // Envoie du mail / Send mail
            $mailer->sendEmail(
                $user->getEmail(),
                self::SUBJECT_MAIL,
                self::TEMPLATE_MAIL,
                $context
            );

            $this->addFlash('success', 'Nous venons de vous transmettre un email. Cliquez sur le lien 
            pour accèder à l\'espace de réinitialisation du mot de passe.');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('login/reset-password-request.html.twig', [
            'requestPassForm' => $form->createView()]);
    }

    /**
     * Réinitialisation du mot de passe / Reset password
     *
     * @param string $token
     * @param Request $request
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @return Response
     */
    #[Route('/reset-password/{token}', name:'reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): response
    {
        // Vérification du Token / Token checking
        $user = $userRepository->findOneByResetToken($token);

        if($user){
            $form = $this->createForm(ResetPasswordType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                // Vérification de la concordance des noms d'utilisateurs / Username match checking
                if($user->getUsername() <>
                    $form->get('username')->getData()) {
                    $this->addFlash('danger', 'Ce nom d\'utilisateur ne correspond pas au votre. 
                    Veuillez cliquer à nouveau sur le lien présent dans votre mail.');
                    return $this->redirectToRoute('forgot_password');
                }

                //Suppression du token / Delete token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword($user, $form->get('password')->getData())
                );

                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe vient d\'être mis à jour.');

                return $this->redirectToRoute('homepage');
            }

            return $this->render('login/reset-password.html.twig', ['resetPassForm' => $form->createView()]);
        }
        $this->addFlash('danger', 'Ce token n\'est pas valide');
        return $this->redirectToRoute('login');
    }

}

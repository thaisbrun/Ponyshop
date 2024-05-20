<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Entity\Favoris;
use App\Entity\Utilisateur;
use App\Form\AnnonceType;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Form\UtilisateurType;
use App\Repository\AnnonceRepository;
use App\Repository\FavorisRepository;
use App\Repository\UtilisateurRepository;
use App\Security\AuthAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\Curl\Util;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

//Ce controller est lié à la page de connexion et de profil, expliquant les informations principales de l'utilisateur.
class SecurityController extends AbstractController
{
    #[Route('security/{idutilisateur}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Utilisateur $user, UtilisateurRepository $userRepository): Response
    {
        if ($this->getUser() !== $user || $this->getUser() == null) {
            $this->addFlash('error', "Accès non autorisé");
            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        } else {
        $form = $this->createForm(UtilisateurType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($user, true);

            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('security/edit.html.twig', [
            'userEditForm' => $form,
            'utilisateur' => $user,
        ]);
    }
    }
    #[Route(path: 'security/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
         return $this->redirectToRoute('app_security_viewprofil');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    #[Route('security/mes_annonces/{idutilisateur}', name: 'app_security_mes_annonces', methods: ['GET'])]
    public function show_by_id_utilisateur(Utilisateur $user, AnnonceRepository $annonceRepository): Response
    {
        if ($this->getUser() !== $user || $this->getUser() == null) {
            $this->addFlash('error', "Accès non autorisé");
            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        } else {
        $listAnnonces = $annonceRepository->findBy(array('idutilisateurannonce' => $user));
        return $this->render('security/mes_annonces.html.twig', [
            'listAnnonces' => $listAnnonces,
        ]);
    }}
    #[Route('security/mes_favoris/{idutilisateur}', name: 'app_security_show_fav', methods: ['GET'])]
    public function show_fav(Utilisateur $user, FavorisRepository $favorisRepository): Response
    {
        if ($this->getUser() !== $user || $this->getUser() == null) {
            $this->addFlash('error', "Accès non autorisé");
            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        } else {
        //Trouver les annonces qui ont le meme idAnnonceUtilisateur que l'utilisateur connecté
        $listFavoris = $favorisRepository->findBy(array('idutilisateurfav' => $user));
        return $this->render('security/mes_favoris.html.twig', [
            'listFavoris' => $listFavoris,
        ]);
    }
    }
    #[Route(path: 'security/viewProfil', name: 'app_security_viewprofil')]
    public function viewProfil(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/viewProfil.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    #[Route(path: 'security/forgottenPassword', name: 'app_security_forgottenpassword')]
    public function forgottenPassword(Request $request, UtilisateurRepository $utilisateurRepository,
    TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $entityManager): Response
    {
        $formPassword = $this->createForm(ResetPasswordRequestFormType::class);
        $formPassword->handleRequest($request);
        if($formPassword->isSubmitted() && $formPassword->isValid())
        {
            $user = $utilisateurRepository->findOneBy(array('mail' =>$formPassword->get('mail')->getData()));
        }else{
            $this->addFlash('danger','Un problème est survenu');
            return $this->redirectToRoute('app_security_forgottenpassword');
        }
        return $this->render('security/reset_password_request.html.twig',
            [
                'formPassword' => $formPassword->createView(),
            ]);

    }
    #[Route(path: 'security/logout', name: 'app_security_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    #[Route(path:'security/delete/{idutilisateur}', name: 'app_security_delete', methods: ['GET','POST'])]
    public function delete(Utilisateur $user, UtilisateurRepository $utilisateurRepository): Response
    {
        if($this->getUser() !== $user || $this->getUser() == null) {
            $this->addFlash('error', "Accès non autorisé");
            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        } else {
            $utilisateurRepository->remove($user, true);

            return $this->redirectToRoute('homepage', [], Response::HTTP_SEE_OTHER);
        }
    }

}

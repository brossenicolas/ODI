<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\UserType;
use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller {

	public function loginAction(Request $request) {
        // Si aucun utilisateur est connecté
        $session = $request->getSession();
        if($session->get('isAuth'))
            return $this->redirectToRoute('productlist');

        $user = new User();
        // Création du formulaire
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        // Validation du formulaire
        if($form->isSubmitted() && $form->isValid()) {
            // Récupération de l'utilisateur
            $user = $form->getData();
            $repository = $this->getDoctrine()->getRepository('AppBundle:User');
            $res = $repository->findOneByUsername($user->getUsername());
            
            // Identifiants corrects
            if($res != null && $res->getPassword() == $user->getPassword()) {
                // Modification variable session
                $session->set('id', $res->getId());
                $session->set('isAuth', true);
                $session->set('username', $res->getUsername());
                $session->set('role', $res->getRoleId());
                
                return $this->redirectToRoute('productlist');
            }
            // Création d'un message flash si les identifiants sont incorrects
            $this->addFlash(
                'notice', 
                'Identifiant ou mot de passe incorrect.'
            );
        }

        return $this->render('user/login.html.twig', array('form' => $form->createView()));
    }


    public function logoutAction(Request $request) {
        // Nettoyage de la variable session
        $session = $request->getSession()->clear();
        return $this->redirectToRoute('productlist');
    }
}
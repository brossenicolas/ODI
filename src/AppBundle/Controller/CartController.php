<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Cart;
use AppBundle\Entity\CartProduct;
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CartController extends Controller {

    public function listAction(Request $request) {
        // Si un magasinier n'est pas connecté
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 2)
            return $this->redirectToRoute('productlist');

        // Récupération des paniers de l'utilisateur
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cart');
        $carts = $repository->findByUserId($session->get('id'));

        return $this->render('cart/list.html.twig', ['carts' => $carts]);
    }

    public function itemAction(Request $request, $id) {
        // Si aucun utilisateur est connecté
        $session = $request->getSession();
        if(!$session->get('isAuth'))
            return $this->redirectToRoute('productlist');

        // Récupération du panier
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cart');
        $cart = $repository->findOneById($id);

        $repository = $this->getDoctrine()->getRepository('AppBundle:CartProduct');
        $cartProducts = $repository->findByCartId($id);

        // Stockage de tous les produits du panier
        $products = array();
        foreach ($cartProducts as $cartProduct) {
            // Récupération d'un produit
            $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
            $product = $repository->findOneById($cartProduct->getProductId());
            array_push($products, $product);
        }
        // Si le panier exist et si le panier appartient au client connecté ou si c'est un magasinier
        if($cart && ($cart->getUserId() == $session->get('id') || ($session->get('role') == 1 && $cart->getStateId() == 2)))
            return $this->render('cart/item.html.twig', ['cart' => $cart, 'products' => $products]);
        else
             return $this->redirectToRoute('productlist');
    }

    public function addAction(Request $request) {
        // Si l'utilisateur connecté n'est pas un client
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 2)
            return $this->redirectToRoute('productlist');

        // Création d'un panier non validé
        $cart = new Cart();
        $cart->setUserId($session->get('id'));
        $cart->setStateId(1);

        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        return $this->redirectToRoute('cartlist');
    }

    public function validateAction(Request $request, $cartId) {
        // Si l'utilisateur connecté n'est pas un client
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 2)
            return $this->redirectToRoute('productlist');

        // Récupération du panier
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cart');
        $cart = $repository->findOneById($cartId);

        // Si le panier appartient au client connecté
        if($cart->getUserId() == $session->get('id')){
            // Validation du panier
            $cart->setStateId(2);

            $em = $this->getDoctrine()->getManager();
            $em->merge($cart);
            $em->flush();
        }
        return $this->redirectToRoute('cartlist');
    }

    public function addProductAction(Request $request, $productId, $cartId) {
        // Si l'utilisateur connecté n'est pas un client
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 2)
            return $this->redirectToRoute('productlist');

        // Récupération du panier
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cart');
        $cart = $repository->findOneById($cartId);

        // Si le panier appartient au client connecté
        if($cart->getUserId() == $session->get('id')){
            // Ajout du produit au panier
            $cartProduct = new CartProduct();
            $cartProduct->setCartId($cartId);
            $cartProduct->setProductId($productId);

            $em = $this->getDoctrine()->getManager();
            $em->persist($cartProduct);
            $em->flush();
        }
        return $this->redirectToRoute('productlist'); 
    }

    public function gestionAction(Request $request) {
        // Si l'utilisateur connecté n'est pas un magasinier
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 1)
            return $this->redirectToRoute('productlist');

        // Récupération de tous les paniers validés
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cart');
        $carts = $repository->findByStateId(2);
        return $this->render('cart/gestion.html.twig', ['carts' => $carts]);
    }

    public function treatAction(Request $request, $cartId) {
        // Si l'utilisateur connecté n'est pas un magasinier
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 1)
            return $this->redirectToRoute('productlist');

        // Récupération du panier
        $repository = $this->getDoctrine()->getRepository('AppBundle:Cart');
        $cart = $repository->findOneById($cartId);

        $repository = $this->getDoctrine()->getRepository('AppBundle:CartProduct');
        $cartProducts = $repository->findByCartId($cartId);

        // Pour les les produits dans le panier
        foreach ($cartProducts as $cartProduct) {
            $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
            $product = $repository->findOneById($cartProduct->getProductId());
            
            // Si la quantité en stock est égal à 0
            if($product->getQuantityStock() <= 0){
                // Création message flash
                $this->addFlash(
                    'notice', 
                    $product->getName() . ': Plus de produits en stock.'
                );
            }
        }

        // Si des messages flash on été crée
        if($request->getSession()->getFlashbag()->has('notice'))
            return $this->redirectToRoute('gestioncart');
        else {
            // Pour tous les produits du panier
            foreach ($cartProducts as $cartProduct) {
                $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
                $product = $repository->findOneById($cartProduct->getProductId());

                // Décrémenter la quantité en stock
                $product->setQuantityStock($product->getQuantityStock() - 1);
                $em = $this->getDoctrine()->getManager();
                $em->merge($product);
                $em->flush();
            }
        }

        // Traitement du panier
        $cart->setStateId(3);
        $em = $this->getDoctrine()->getManager();
        $em->merge($cart);
        $em->flush();

        return $this->redirectToRoute('gestioncart');
    }
}
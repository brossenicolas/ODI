<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ProductType;
use AppBundle\Entity\Product;
use AppBundle\Entity\Cart;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller {

	public function listAction(Request $request) {
        // Récupération des produits en stock et visible
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
        $query = $repository->createQueryBuilder('p')
        					->where('p.quantityStock > :quantityStock')
        					->setParameter('quantityStock' , '0')
                            ->andwhere('p.isVisible = :isVisible')
                            ->setParameter('isVisible' , '1')
        					->getQuery();
        $products = $query->getResult();

        // Si l'utilisateur connecté est un client
        $session = $request->getSession();
        if($session->get('isAuth') && $session->get('role') == 2){
            // Récupération des paniers non validés du client
            $repository = $this->getDoctrine()->getRepository('AppBundle:Cart');
            $query = $repository->createQueryBuilder('p')
                                ->where('p.userId = :userId')
                                ->setParameter('userId' , $session->get('id'))
                                ->andwhere('p.stateId = :stateId')
                                ->setParameter('stateId' , '1')
                                ->getQuery();
            $carts = $query->getResult();
            return $this->render('product/list.html.twig', ['products' => $products, 'carts' => $carts]);
        }
        else{
            return $this->render('product/list.html.twig', ['products' => $products]);
        }
    }

    public function itemAction($reference) {
        // Récupération du produit
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
        $product = $repository->findOneByReference($reference);
        if($product)
            return $this->render('product/item.html.twig', ['product' => $product]);
        else
             return $this->redirectToRoute('productlist');
    }

    public function addAction(Request $request) {
        // Si l'utilisateur connecté n'est pas un magasinier
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 1)
            return $this->redirectToRoute('productlist');

        $product = new Product();
        // Création du formulaire
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        // Formulaire valide
        if($form->isSubmitted() && $form->isValid()){
            // Récupéation de tous les champs
            $product = $form->getData();

            $file = $product->getPhoto();
            // Cryptage du nom de la photo
            $fileName = md5(uniqid()).'.'.$file->guessExtension();
            // Stockage de l'image
            $file->move(
                $this->getParameter('img_directory'),
                $fileName
            );
            $product->setPhoto($fileName);
            $product->setIsVisible(true);

            // Ajout du produit
            $em = $this->getDoctrine()->getManager();
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('productlist');
        }

        return $this->render('product/add.html.twig', array('form' => $form->createView()));
    }

    public function gestionAction(Request $request) {
        // Si l'utilisateur connecté n'est pas un magasinier
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 1)
            return $this->redirectToRoute('productlist');

        // Récupération de tous les produits
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
        $products = $repository->findAll();
        return $this->render('product/gestion.html.twig', ['products' => $products]);
    }

    public function updateAction(Request $request) {
        // Si l'utilisateur connecté n'est pas un magasinier
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 1)
            return $this->redirectToRoute('productlist');

        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');

        // Validation du formulaire
        if(!empty($_POST)){
            // Récupération du produit
            $product = $repository->findOneByReference($_POST['reference']);
            // Changement du produit
            $product->setPrice($_POST['price']);
            $product->setQuantityStock($_POST['quantityStock']);
            $product->setQuantityMin($_POST['quantityMin']);
            $product->setExpirationDate(new \DateTime($_POST['expirationDate']));
            if(isset($_POST['isVisible']))
                $product->setIsVisible(true);
            else
                $product->setIsVisible(false);

            $em = $this->getDoctrine()->getManager();
            $em->merge($product);
            $em->flush();

            return $this->redirectToRoute('gestionproduct');
        } 
        return $this->redirectToRoute('gestionproduct');
    }

    public function alertAction(Request $request) {
        // Si l'utilisateur connecté n'est pas un magasinier
        $session = $request->getSession();
        if(!$session->get('isAuth') || $session->get('role') != 1)
            return $this->redirectToRoute('productlist');

        // Récupération des produits dont le stock est inférieur à la quantité minimale
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
        $query = $repository->createQueryBuilder('p')
                            ->where('p.quantityStock < p.quantityMin')
                            ->getQuery();
        $productStocks = $query->getResult();

        // Récupération des produits périmés
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
        $query = $repository->createQueryBuilder('p')
                            ->where('p.expirationDate < :date')
                            ->setParameter('date' , date("Y-m-d"))
                            ->getQuery();
        $productDates = $query->getResult();

        return $this->render('product/alert.html.twig', ['productStocks' => $productStocks, 'productDates' => $productDates]);
    }

}
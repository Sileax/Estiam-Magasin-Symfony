<?php
/**
 * Created by PhpStorm.
 * User: valeriancrasnier
 * Date: 18/05/2017
 * Time: 18:04
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;

class StockRestController extends FOSRestController
{
    /**
     * @Rest\Get("/stock")
     */
    public function getAllProductsStockAction()
    {
        $db = $this->getDoctrine()->getManager()->getConnection();

        $query = $db->prepare('SELECT p.id, p.slug, p.reference, p.buyingPrice, p.sellingPrice, p.vat, c.name, s.availableProducts FROM products p LEFT JOIN category c ON p.idCategory = c.id LEFT JOIN stock s ON s.refProduct = p.id');
        $query->execute();
        $result = $query->fetchAll();
        if (count($result) == 0) {
            return new View("Il n'y a pas de produits présents en base de donnée", Response::HTTP_NOT_FOUND);
        }
        return $result;
    }

    /**
     * @Rest\Get("/stock/product/{id}")
     */
    public function idAction($id)
    {
        $db = $this->getDoctrine()->getManager()->getConnection();

        $query = $db->prepare('SELECT p.id, p.slug, p.reference, p.buyingPrice, p.sellingPrice, p.vat, c.name, s.availableProducts FROM products p LEFT JOIN category c ON p.idCategory = c.id LEFT JOIN stock s ON s.refProduct = p.id WHERE p.id = :id');
        $query->bindValue('id', $id);
        $query->execute();
        $result = $query->fetchAll();
        if (count($result) == 0) {
            return new View("Le produit avec l'id " . $id . " n'existe pas", Response::HTTP_NOT_FOUND);
        }
        return $result;
    }

    /**
     * @Rest\Post("/stock/")
     */
    public function postAction(Request $request)
    {
        $data = new User;
        $name = $request->get('name');
        $role = $request->get('role');
        if(empty($name) || empty($role))
        {
            return new View("NULL VALUES ARE NOT ALLOWED", Response::HTTP_NOT_ACCEPTABLE);
        }
        $data->setName($name);
        $data->setRole($role);
        $em = $this->getDoctrine()->getManager();
        $em->persist($data);
        $em->flush();
        return new View("User Added Successfully", Response::HTTP_OK);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: valeriancrasnier
 * Date: 18/05/2017
 * Time: 18:04
 */

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Entity\User;

class StockRestController extends FOSRestController
{
    /**
     * @Rest\Get("/api/stock")
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
     * @Rest\Get("/api/stock/product/{id}")
     */
    public function getSpecificProductStockAction($id)
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
     * @Rest\Put("/api/stock/product/{id}")
     */
    public function updateProductStockAction($id, Request $request)
    {
        $data = new Stock;
        $newStock = $request->get('newStock');
        $db = $this->getDoctrine()->getManager()->getConnection();
        $query = $db->prepare('SELECT p.id, p.slug, p.reference, p.buyingPrice, p.sellingPrice, p.vat, c.name, s.availableProducts FROM products p LEFT JOIN category c ON p.idCategory = c.id LEFT JOIN stock s ON s.refProduct = p.id WHERE p.id = :id');
        $query->bindValue('id', $id);
        $query->execute();
        $result = $query->fetchAll();

        if (count($result) == 0) {
            return new View("Produit non trouvé", Response::HTTP_NOT_FOUND);
        }
        else if(!empty($newStock)){
            $query = $db->prepare(
                'UPDATE stock SET availableProducts = :stock WHERE refProduct = :id'
            );
            $query->bindValue('id', $id);
            $query->bindValue('stock', $newStock);
            $query->execute();

            return new View("Le stock a été mis à jour", Response::HTTP_OK);
        }

        else return new View("Le nouveau stock ne peux pas être vide", Response::HTTP_NOT_ACCEPTABLE);
    }
}
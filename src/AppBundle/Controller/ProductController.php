<?php
/**
 * Created by PhpStorm.
 * User: valeriancrasnier
 * Date: 16/05/2017
 * Time: 17:30
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ProductController extends Controller
{
    /**
     * @Route("/create-product", name="create-product")
     */
    public function createProductAction(Request $request)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $errors = [];

        if ($request->getMethod() == 'POST') {
            $productName = $request->request->get('name');
            $productBuyingPrice = $request->request->get('buying-price');
            $productSellingPrice = $request->request->get('selling-price');
            $productVAT = $request->request->get('vat');
            $productReference = $request->request->get('reference');
            $productCategory = $request->request->get('category');
            $productStock = $request->request->get('stock');

            if (empty($productName)) {
                $errors[] = 'Nom du produit non renseigné';
            }

            if (empty($productBuyingPrice)) {
                $errors[] = 'Prix d\'achat produit non renseigné';
            }

            if (empty($productSellingPrice)) {
                $errors[] = 'Prix de vente produit non renseigné';
            }

            if (empty($productVAT)) {
                $errors[] = 'Taux de TVA du produit non renseigné';
            }

            if (empty($productReference)) {
                $errors[] = 'Reference du produit non renseignée';
            }

            if (empty($productCategory)) {
                $errors[] = 'Categorie du produit non renseignée';
            }

            if (empty($productStock)) {
                $errors[] = 'Stock du produit non renseigné';
            }


            if (empty($errors)) {

                $db = $this->getDoctrine()->getManager()->getConnection();

                $query = $db->prepare('SELECT COUNT(*) AS nproduct FROM products WHERE slug = :slug OR reference = :reference');
                $query->bindValue('slug', $productName);
                $query->bindValue('reference', $productReference);
                $query->execute();
                $result = $query->fetch();

                if ($result['nproduct'] > 0) {
                    $errors[] = 'Un produit avec ce nom ou cette réference existe déjà';
                } else {
                    $query = $db->prepare(
                        'INSERT INTO products (slug,reference,buyingPrice, sellingPrice, vat, idCategory) VALUES (:slug, :reference, :buyingPrice, :sellingPrice, :vat, :idCat)'
                    );
                    $query->bindValue('slug', $productName);
                    $query->bindValue('reference', $productReference);
                    $query->bindValue('buyingPrice', $productBuyingPrice);
                    $query->bindValue('sellingPrice', $productSellingPrice);
                    $query->bindValue('vat', $productVAT);
                    $query->bindValue('idCat', $productCategory);
                    $query->execute();

                    $query = $db->prepare('SELECT id FROM products WHERE slug = :slug');
                    $query->bindValue('slug', $productName);
                    $query->execute();
                    $result = $query->fetch();

                    $query = $db->prepare(
                        'INSERT INTO stock(refProduct, availableProducts) VALUES (:id, :stock)'
                    );
                    $query->bindValue('id', $result['id']);
                    $query->bindValue('stock', $productStock);
                    $query->execute();

                    return $this->redirectToRoute('list-products');
                }
            }
        }
        $db = $this->getDoctrine()->getManager()->getConnection();

        $query = $db->prepare('SELECT * FROM category ORDER BY id ASC');
        $query->execute();
        $result = $query->fetchAll();

        return $this->render('default/create-product.html.twig', [
            'categories' => $result,
            'errors' => $errors
        ]);

    }


    /**
     * @Route("/list-products", name="list-products")
     */
    public function listProductAction(Request $request)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $db = $this->getDoctrine()->getManager()->getConnection();

        $query = $db->prepare('SELECT * FROM category ORDER BY name ASC');
        $query->execute();
        $categories = $query->fetchAll();

        if ($request->query->get('category')) {
            $query = $db->prepare(
                'SELECT p.id, p.slug, p.reference, p.buyingPrice, p.sellingPrice, p.vat, c.name, s.availableProducts FROM products p LEFT JOIN category c ON p.idCategory = c.id LEFT JOIN stock s ON s.refProduct = p.id WHERE p.idCategory = :idCat'
            );
            $query->bindValue('idCat', $request->query->get('category'));
            $query->execute();
            $result = $query->fetchAll();

            return $this->render('default/list-products.html.twig', [
                'products' => $result,
                'categories' => $categories
            ]);

        }

        $query = $db->prepare('SELECT p.id, p.slug, p.reference, p.buyingPrice, p.sellingPrice, p.vat, c.name, s.availableProducts FROM products p LEFT JOIN category c ON p.idCategory = c.id LEFT JOIN stock s ON s.refProduct = p.id');
        $query->execute();
        $result = $query->fetchAll();

        return $this->render('default/list-products.html.twig', [
            'products' => $result,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/delete-product/{id}", name="delete-product")
     */
    public function deleteProductAction(Request $request, $id)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $db = $this->getDoctrine()->getManager()->getConnection();

        if ($id) {
            $query = $db->prepare(
                'DELETE FROM products WHERE id = :id'
            );
            $query->bindValue('id', $id);
            $query->execute();
            $query = $db->prepare(
                'DELETE FROM stock WHERE refProduct = :id'
            );
            $query->bindValue('id', $id);
            $query->execute();

            return $this->redirectToRoute('list-products');
        }

        return $this->redirectToRoute('list-products');
    }

    /**
     * @Route("/product/{id}", name="product-info")
     */
    public function getProductAction(Request $request, $id)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $errors = [];

        $db = $this->getDoctrine()->getManager()->getConnection();

        if ($request->getMethod() == 'POST') {
            $productName = $request->request->get('name');
            $productBuyingPrice = $request->request->get('buying-price');
            $productSellingPrice = $request->request->get('selling-price');
            $productVAT = $request->request->get('vat');
            $productReference = $request->request->get('reference');
            $productCategory = $request->request->get('category');
            $productStock = $request->request->get('stock');

            if (empty($productName)) {
                $errors[] = 'Nom du produit non renseigné';
            }

            if (empty($productBuyingPrice)) {
                $errors[] = 'Prix d\'achat produit non renseigné';
            }

            if (empty($productSellingPrice)) {
                $errors[] = 'Prix de vente produit non renseigné';
            }

            if (empty($productVAT)) {
                $errors[] = 'Taux de TVA du produit non renseigné';
            }

            if (empty($productReference)) {
                $errors[] = 'Reference du produit non renseignée';
            }

            if (empty($productStock)) {
                $errors[] = 'Stock du produit non renseigné';
            }


            $query = $db->prepare(
                'SELECT s.availableProducts FROM stock s LEFT JOIN products p ON p.id = s.refProduct WHERE p.id = :id'
            );
            $query->bindValue('id', $id);
            $query->execute();
            $result = $query->fetch();
            $currentStock = $result['availableProducts'];
            if ($session->get('role') === '1') {
                if (intval($currentStock) < intval($productStock)) {
                    $errors[] = 'Votre role ne vous permet pas d\'ajouter des produits au stock';
                }
            } else if ($session->get('role') === '2') {
                if (intval($currentStock) > intval($productStock)) {
                    $errors[] = 'Votre role ne vous permet pas de retirer des produits au stock';
                }
            }

            if (empty($errors)) {

                $query = $db->prepare(
                    'SELECT COUNT(*) as nstock FROM stock where refProduct = :id'
                );
                $query->bindValue('id', $id);
                $query->execute();
                $isInStock = $query->fetch();
                if ($isInStock['nstock'] == 0) {
                    $query = $db->prepare(
                        'INSERT INTO stock(refProduct, availableProducts) VALUES (:id, 0)'
                    );
                    $query->bindValue('id', $id);
                    $query->execute();
                }

                $query = $db->prepare(
                    'UPDATE products, stock SET products.slug = :slug, products.reference = :reference, products.buyingPrice = :buyingPrice, products.sellingPrice = :sellingPrice, products.vat = :vat, products.idCategory = :idCat, stock.availableProducts = :stock WHERE products.id = stock.refProduct AND products.id = :id'
                );
                $query->bindValue('slug', $productName);
                $query->bindValue('reference', $productReference);
                $query->bindValue('buyingPrice', $productBuyingPrice);
                $query->bindValue('sellingPrice', $productSellingPrice);
                $query->bindValue('vat', $productVAT);
                $query->bindValue('idCat', $productCategory);
                $query->bindValue('id', $id);
                $query->bindValue('stock', $productStock);
                $query->execute();

                return $this->redirectToRoute('list-products');
            }

        }
        $query = $db->prepare('SELECT p.id, p.slug, p.reference, p.buyingPrice, p.sellingPrice, p.vat, p.idCategory, c.name, s.availableProducts FROM products p LEFT JOIN category c ON p.idCategory = c.id LEFT JOIN stock s ON s.refProduct = p.id WHERE p.id = :id');
        $query->bindValue('id', $id);
        $query->execute();
        $products = $query->fetchAll();


        $query = $db->prepare('SELECT * FROM category ORDER BY id ASC');
        $query->execute();
        $categories = $query->fetchAll();

        return $this->render('default/get-product.html.twig', [
            'products' => $products,
            'errors' => $errors,
            'categories' => $categories
        ]);
    }
}
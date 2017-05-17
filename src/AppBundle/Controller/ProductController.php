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


class ProductController extends Controller
{
    /**
     * @Route("/create-product", name="create-product")
     */
    public function createProductAction(Request $request)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid))
        {
            return $this->redirectToRoute('homepage');
        }

        $errors = [];

        if ($request->getMethod() == 'POST')
        {
            $productName = $request->request->get('name');
            $productBuyingPrice = $request->request->get('buying-price');
            $productSellingPrice = $request->request->get('selling-price');
            $productVAT = $request->request->get('vat');
            $productReference = $request->request->get('category');
            $productCategory = $request->request->get('category');

            if (empty($productName))
            {
                $errors[] = 'Nom du produit non renseigné';
            }

            if (empty($productBuyingPrice))
            {
                $errors[] = 'Prix d\'achat produit non renseigné';
            }

            if (empty($productSellingPrice))
            {
                $errors[] = 'Prix de vente produit non renseigné';
            }

            if (empty($productVAT))
            {
                $errors[] = 'Taux de TVA du produit non renseigné';
            }

            if (empty($productReference))
            {
                $errors[] = 'Reference du produit non renseignée';
            }

            if (empty($productCategory))
            {
                $errors[] = 'Categorie du produit non renseignée';
            }


            if (empty($errors))
            {

                    $db = $this->getDoctrine()->getManager()->getConnection();

                    $query = $db->prepare('SELECT COUNT(*) AS nproduct FROM products WHERE slug = :slug OR reference = :reference');
                    $query->bindValue('slug', $productName);
                    $query->bindValue('reference', $productReference);
                    $query->execute();
                    $result = $query->fetch();

                    if ($result['nproduct'] > 0)
                    {
                        $errors[] = 'Un produit avec ce nom ou cette réference existe déjà';
                    }
                    else
                    {
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

                        return $this->redirectToRoute('homepage');
                    }
                }
        }
            $db = $this->getDoctrine()->getManager()->getConnection();

            $query = $db->prepare('SELECT * FROM category');
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
        public function listProductAction(Request $request){
            $session = $request->getSession();
            $sessid = $session->get('id');

            if (empty($sessid))
            {
                return $this->redirectToRoute('homepage');
            }

            $errors = [];

            $db = $this->getDoctrine()->getManager()->getConnection();

            $query = $db->prepare('SELECT * FROM products INNER JOIN category WHERE products.idCategory = category.id');
            $query->execute();
            $result = $query->fetchAll();

            return $this->render('default/list-products.html.twig', [
                'products' => $result
            ]);
        }

        /**
         * @Route("/product/{id}", name="product-info")
         */
        public function getProductAction(Request $request, $id){
            $session = $request->getSession();
            $sessid = $session->get('id');

            if (empty($sessid))
            {
                return $this->redirectToRoute('homepage');
            }

            $errors = [];

            $db = $this->getDoctrine()->getManager()->getConnection();

            if($request->getMethod() == 'POST'){
                $productName = $request->request->get('name');
                $productBuyingPrice = $request->request->get('buying-price');
                $productSellingPrice = $request->request->get('selling-price');
                $productVAT = $request->request->get('vat');
                $productReference = $request->request->get('category');
                $productCategory = $request->request->get('category');

                if (empty($productName))
                {
                    $errors[] = 'Nom du produit non renseigné';
                }

                if (empty($productBuyingPrice))
                {
                    $errors[] = 'Prix d\'achat produit non renseigné';
                }

                if (empty($productSellingPrice))
                {
                    $errors[] = 'Prix de vente produit non renseigné';
                }

                if (empty($productVAT))
                {
                    $errors[] = 'Taux de TVA du produit non renseigné';
                }

                if (empty($productReference))
                {
                    $errors[] = 'Reference du produit non renseignée';
                }

                if (empty($productCategory))
                {
                    $errors[] = 'Categorie du produit non renseignée';
                }


                if(empty($errors)){

                    $query = $db->prepare('SELECT COUNT(*) AS nproduct FROM products WHERE slug = :slug OR reference = :reference');
                    $query->bindValue('slug', $productName);
                    $query->bindValue('reference', $productReference);
                    $query->execute();
                    $result = $query->fetch();

                    if ($result['nproduct'] > 0) {
                        $errors[] = 'Un produit avec ce nom ou cette réference existe déjà';
                    } else{
                        $query = $db->prepare(
                            'UPDATE products (slug,reference,buyingPrice, sellingPrice, vat, idCategory) SET slug = :slug AND reference = :reference AND buyingPrice = :buyingPrice AND sellingPrice = :sellingPrice AND vat = :vat AND idCategory = :idCat WHERE products.id = :id)'
                        );
                        $query->bindValue('slug', $productName);
                        $query->bindValue('reference', $productReference);
                        $query->bindValue('buyingPrice', $productBuyingPrice);
                        $query->bindValue('sellingPrice', $productSellingPrice);
                        $query->bindValue('vat', $productVAT);
                        $query->bindValue('idCat', $productCategory);
                        $query->bindValue('id', $id);
                        $query->execute();
                    }
                    return $this->redirectToRoute('list-products');
                }
            }


            $query = $db->prepare('SELECT * FROM products INNER JOIN category WHERE products.idCategory = category.id AND products.id = :id');
            $query->bindValue('id', $id);
            $query->execute();
            $products = $query->fetchAll();

            $query = $db->prepare('SELECT * FROM category');
            $query->execute();
            $categories = $query->fetchAll();

            return $this->render('default/get-product.html.twig', [
                'products' => $products,
                'errors' => $errors,
                'categories' => $categories
            ]);
        }
}
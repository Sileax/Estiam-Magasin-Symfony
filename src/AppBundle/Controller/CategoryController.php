<?php
/**
 * Created by PhpStorm.
 * User: valeriancrasnier
 * Date: 17/05/2017
 * Time: 18:12
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends Controller
{
    /**
     * @Route("/create-category", name="create-category")
     */
    public function createCategoryAction(Request $request)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $errors = [];

        if ($request->getMethod() == 'POST') {
            $name = $request->request->get('name');

            if (empty($name)) {
                $errors[] = 'Nom de la catégorie non renseignée';
            }


            if (empty($errors)) {

                $db = $this->getDoctrine()->getManager()->getConnection();

                $query = $db->prepare('SELECT COUNT(*) AS ncategory FROM category WHERE name = :name');
                $query->bindValue('name', $name);
                $query->execute();
                $result = $query->fetch();

                if ($result['ncategory'] > 0) {
                    $errors[] = 'Une catégorie avec ce nom existe déjà';
                } else {
                    $query = $db->prepare(
                        'INSERT INTO category (name) VALUES (:name)'
                    );
                    $query->bindValue('name', $name);
                    $query->execute();

                    return $this->redirectToRoute('list-categories');
                }
            }
        }

        return $this->render('default/create-category.html.twig', [
            'errors' => $errors
        ]);

    }


    /**
     * @Route("/list-categories", name="list-categories")
     */
    public function listProductAction(Request $request)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $errors = [];

        $db = $this->getDoctrine()->getManager()->getConnection();

        $query = $db->prepare('SELECT c.id, c.name FROM category c');
        $query->execute();
        $result = $query->fetchAll();

        return $this->render('default/list-category.html.twig', [
            'categories' => $result
        ]);
    }

    /**
     * @Route("/category/{id}", name="category-info")
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
            $category = $request->request->get('name');

            if (empty($category)) {
                $errors[] = 'Nom du produit non renseigné';
            }


            if (empty($errors)) {

                $query = $db->prepare(
                    'UPDATE category c SET c.name = :name WHERE c.id = :id'
                );
                $query->bindValue('name', $category);
                $query->bindValue('id', $id);
                $query->execute();

                return $this->redirectToRoute('list-categories');
            }

        }
        $query = $db->prepare('SELECT c.name, c.id FROM category c WHERE c.id = :id');
        $query->bindValue('id', $id);
        $query->execute();
        $categories = $query->fetchAll();

        return $this->render('default/get-category.html.twig', [
            'errors' => $errors,
            'categories' => $categories
        ]);
    }

}
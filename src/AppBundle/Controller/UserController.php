<?php
/**
 * Created by PhpStorm.
 * User: valeriancrasnier
 * Date: 17/05/2017
 * Time: 15:56
 */

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class UserController extends Controller
{
    /**
     * @Route("/create-user", name="create-user")
     */
    public function createUserAction(Request $request)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $errors = [];

        if ($request->getMethod() == 'POST') {
            $username = $request->request->get('username');
            $role = $request->request->get('role');
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password-confirm');


            if (empty($username)) {
                $errors[] = 'Pseudo de l\'utilisateur non renseigné';
            }

            if (empty($role)) {
                $errors[] = 'Role de l\'utilisateur non renseigné';
            }

            if (empty($password)) {
                $errors[] = 'Mot de passe de l\'utilisateur non renseigné';
            }

            if (empty($password)) {
                $errors[] = 'Confirmation du mot de passe de l\'utilisateur non renseigné';
            }

            if ($password !== $passwordConfirm) {
                $errors[] = 'Les mots de passe ne correspondent pas';
            }


            if (empty($errors)) {

                $db = $this->getDoctrine()->getManager()->getConnection();

                $query = $db->prepare('SELECT COUNT(*) AS nuser FROM user WHERE username = :username');
                $query->bindValue('username', $username);
                $query->execute();
                $result = $query->fetch();

                if ($result['nuser'] > 0) {
                    $errors[] = 'Un utilisateur avec ce pseudo existe déjà';
                } else {
                    $query = $db->prepare(
                        'INSERT INTO user (username, roleId, password) VALUES (:username, :roleId, :password)'
                    );
                    $query->bindValue('username', $username);
                    $query->bindValue('roleId', $role);
                    $query->bindValue('password', sha1($password));
                    $query->execute();

                    return $this->redirectToRoute('list-users');
                }
            }
        }

        $db = $this->getDoctrine()->getManager()->getConnection();

        $query = $db->prepare('SELECT * FROM role ORDER BY id ASC');
        $query->execute();
        $result = $query->fetchAll();

        return $this->render('default/create-user.html.twig', [
            'roles' => $result,
            'errors' => $errors
        ]);

    }

    /**
     * @Route("/list-users", name="list-users")
     */
    public function listUserAction(Request $request)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $db = $this->getDoctrine()->getManager()->getConnection();

        $query = $db->prepare('SELECT u.id, u.username, r.roleType FROM user u LEFT JOIN role r ON u.roleId = r.id');
        $query->execute();
        $result = $query->fetchAll();

        return $this->render('default/list-users.html.twig', [
            'users' => $result
        ]);
    }

    /**
     * @Route("/user/{id}", name="user-info")
     */
    public function getUserAction(Request $request, $id)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $errors = [];

        $db = $this->getDoctrine()->getManager()->getConnection();

        if ($request->getMethod() == 'POST') {
            $username = $request->request->get('username');
            $password = $request->request->get('password');
            $passwordConfirm = $request->request->get('password-confirm');
            $role = $request->request->get('role');

            if (empty($username)) {
                $errors[] = 'Pseudo de l\'utilisateur non renseigné';
            }

            if (empty($role)) {
                $errors[] = 'Role de l\'utilisateur non renseigné';
            }

            if (empty($password)) {
                $errors[] = 'Mot de passe de l\'utilisateur non renseigné';
            }

            if (empty($password)) {
                $errors[] = 'Confirmation du mot de passe de l\'utilisateur non renseigné';
            }

            if ($password !== $passwordConfirm) {
                $errors[] = 'Les mots de passe ne correspondent pas';
            }


            if (empty($errors)) {

                    $query = $db->prepare(
                        'UPDATE user SET username = :username, roleId = :roleId, password = :password WHERE id = :id'
                    );
                    $query->bindValue('username', $username);
                    $query->bindValue('roleId', $role);
                    $query->bindValue('password', sha1($password));
                    $query->bindValue('id', $id);
                    $query->execute();

                    return $this->redirectToRoute('list-users');
            }
        }


        $query = $db->prepare('SELECT u.id, r.roleType, u.username, u.roleId FROM user u LEFT JOIN role r ON u.roleId = r.id WHERE u.id = :id');
        $query->bindValue('id', $id);
        $query->execute();
        $products = $query->fetchAll();


        $query = $db->prepare('SELECT * FROM role ORDER BY id ASC');
        $query->execute();
        $categories = $query->fetchAll();

        return $this->render('default/get-user.html.twig', [
            'users' => $products,
            'errors' => $errors,
            'roles' => $categories
        ]);
    }

    /**
     * @Route("/delete-user/{id}", name="delete-user")
     */
    public function deleteUserAction(Request $request, $id)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('login');
        }

        $db = $this->getDoctrine()->getManager()->getConnection();

        if ($id) {
            $query = $db->prepare(
                'DELETE FROM user WHERE id = :id'
            );
            $query->bindValue('id', $id);
            $query->execute();

            return $this->redirectToRoute('list-users');
        }

        return $this->redirectToRoute('list-users');
    }
}
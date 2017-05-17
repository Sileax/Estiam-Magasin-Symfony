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
    public function createProductAction(Request $request)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (empty($sessid)) {
            return $this->redirectToRoute('homepage');
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

            if (empty($password !== $passwordConfirm)) {
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

                    return $this->redirectToRoute('homepage');
                }
            }
        }


        return $this->render('default/create-user.html.twig', [
            'categories' => $result,
            'errors' => $errors
        ]);

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: valeriancrasnier
 * Date: 14/05/2017
 * Time: 17:12
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $sessid = $session->get('id');

        if (!empty($sessid)) {
            return $this->redirectToRoute('homepage');
        }

        $errors = [];
        $username = "";

        if ($request->getMethod() == 'POST') {
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            if (empty($username)) {
                $errors[] = "Missing username";
            }

            if (empty($password)) {
                $errors[] = "Missing password";
            }

            if (empty($errors)) {
                $db = $this->getDoctrine()->getManager()->getConnection();
                $query = $db->prepare(
                    'SELECT * FROM user WHERE username = :username AND password = :password'
                );
                $query->bindValue('username', $username);
                $query->bindValue('password', sha1($password));
                $query->execute();
                $user = $query->fetch();

                if ($user == false) {
                    $errors[] = "Username/password mismatch";
                } else {
                    $session->set('id', $user['id']);
                    $session->set('name', $user['username']);
                    return $this->redirectToRoute('homepage');
                }
            }
        }

        return $this->render('default/login.html.twig', [
            'errors' => $errors,
            'username' => $username
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request)
    {
        $session = $request->getSession();
        $session->invalidate();

        return $this->redirectToRoute('login');
    }
}
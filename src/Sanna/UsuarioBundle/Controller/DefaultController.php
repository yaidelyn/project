<?php

namespace Sanna\UsuarioBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('UsuarioBundle:Default:index.html.twig', array('name' => $name));
    }


    public function loginAction(){

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN') && !$this->get('security.context')->isGranted('ROLE_USER')) {
            $peticion = $this->get('request');
            $sesion = $peticion->getSession();
            $error = $peticion->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR,
                $sesion->get(SecurityContext::AUTHENTICATION_ERROR)
            );
            $lastUsername = (null === $sesion) ? '' : $sesion->get(SecurityContextInterface::LAST_USERNAME);
            $csrfToken = $this->has('form.csrf_provider')
                ? $this->get('form.csrf_provider')->generateCsrfToken('authenticate')
                : null;
            // echo $csrfToken;die;
            return $this->render('UsuarioBundle:Default:login.html.twig', array(
                'last_username' => $lastUsername,
                'error' => $error,
                'csrf_token' => $csrfToken
            ));
        }
        else {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Your user has been authenticated previously'
            );
            return $this->redirect($this->generateUrl('admin_site'));
        }

    }
}

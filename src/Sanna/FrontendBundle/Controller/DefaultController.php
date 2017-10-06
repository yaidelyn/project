<?php

namespace Sanna\FrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\UserComponent\UserComponent;

class DefaultController extends Controller
{
    var $user_component;

    public function __construct(){
        $this->user_component = new UserComponent($this);
    }

    public function indexAction()
    {
        $em = $this->get('doctrine')->getManager();
        $result = $this->user_component->getVars();
		$user = $this->get('security.context')->getToken()->getUser();
		 
		if($user!='anon.'){			
			$id = $user->getId();
			$result['datalogin'] = $this->user_component->getInformationInfo($id);
		}       
        $result['home'] = "active";
        $result["login"] = ($this->get('security.context')->isGranted('ROLE_ADMIN') || $this->get('security.context')->isGranted('ROLE_USER')) ? 1 : 0;
        $result['text'] = $this->getAllPages();
        //echo '<pre>';print_r($result);die;
        return $this->render('FrontendBundle:Default:index.html.twig', array('datos'=>$result));
    }
	public function contattiAction()
    {
        $em = $this->get('doctrine')->getManager();
        $result  = $this->user_component->getVars();
		$user = $this->get('security.context')->getToken()->getUser();
		if($user!='anon.'){
			$id = $user->getId();
			$result['datalogin'] = $this->user_component->getInformationInfo($id);
		}     
        $result["login"] = ($this->get('security.context')->isGranted('ROLE_ADMIN') || $this->get('security.context')->isGranted('ROLE_USER')) ? 1 : 0;
        $result['contatti'] = "active";
        return $this->render('FrontendBundle:Default:contact.html.twig',array('datos'=>$result));
    }

    public function chiSiamoAction(){
        $result  = $this->user_component->getVars();
		$user = $this->get('security.context')->getToken()->getUser();
		if($user!='anon.'){
			$id = $user->getId();
			$result['datalogin'] = $this->user_component->getInformationInfo($id);
		}     
        $result["login"] = ($this->get('security.context')->isGranted('ROLE_ADMIN') || $this->get('security.context')->isGranted('ROLE_USER')) ? 1 : 0;
        $result['chisiamo'] = "active";
        return $this->render('FrontendBundle:Default:chisiamo.html.twig',array('datos'=>$result));

    }

    public function linksAction(){
        $result  = $this->user_component->getVars();
		$user = $this->get('security.context')->getToken()->getUser();
		if($user!='anon.'){
			$id = $user->getId();
			$result['datalogin'] = $this->user_component->getInformationInfo($id);
		}     
        $result["login"] = ($this->get('security.context')->isGranted('ROLE_ADMIN') || $this->get('security.context')->isGranted('ROLE_USER')) ? 1 : 0;
        $result['links'] = "active";
        return $this->render('FrontendBundle:Default:links.html.twig',array('datos'=>$result));

    }

    public function consulenzaAction($id){
        $result  = $this->user_component->getVars();
		$user = $this->get('security.context')->getToken()->getUser();
		if($user!='anon.'){
			$id = $user->getId();
			$result['datalogin'] = $this->user_component->getInformationInfo($id);
		}     
        $result["login"] = ($this->get('security.context')->isGranted('ROLE_ADMIN') || $this->get('security.context')->isGranted('ROLE_USER')) ? 1 : 0;
        $result['consulenza'] = "active";
        $result['option'] = $id;
        return $this->render('FrontendBundle:Default:consulenza.html.twig',array('datos'=>$result));

    }
	
	public function serviziAction(){		
        $result  = $this->user_component->getVars();
		$user = $this->get('security.context')->getToken()->getUser();
		if($user!='anon.'){
			$id = $user->getId();
			$result['datalogin'] = $this->user_component->getInformationInfo($id);
		}     		
        $result["login"] = ($this->get('security.context')->isGranted('ROLE_ADMIN') || $this->get('security.context')->isGranted('ROLE_USER')) ? 1 : 0;		
        $result['servizi'] = "active";
        return $this->render('FrontendBundle:Default:servizi.html.twig',array('datos'=>$result));
    }


    public function getAllPages(){
        $em = $this->get('doctrine')->getManager();
        $pages= $em->getRepository('FrontendBundle:Page')->findAll();
        foreach($pages as $page){
            $data['id'] = $page->getId();
            $data['title'] = $page->getTitle();
            $data['text'] = $page-> getText();
            $data['resumen'] = $this->resumir($page->getText(),100, "...");

            $result[] = $data;
        }
        //echo '<pre>';print_r($result);die;
        return $result;
    }
    public function resumir($texto,$limite,$final){
        if(strlen($texto)<=$limite)
            return $texto;
        else{
            $texto= substr($texto, 0, $limite) . $final;
        }

        return $texto;
    }

}

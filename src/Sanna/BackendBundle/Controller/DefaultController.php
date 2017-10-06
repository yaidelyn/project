<?php

namespace Sanna\BackendBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\UserComponent\UserComponent;


class DefaultController extends Controller
{

    var $user_comp;

    public function __construct()
    {
        $this->user_comp = new UserComponent($this);
    }

    public function indexAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
		$username = $user->getUsername();

        $em = $this->getDoctrine()->getManager();
        //$events = $em->getRepository('UsuarioBundle:Event')->findAll();
		
		//echo $_SERVER["DOCUMENT_ROOT"];die;

        $data['events'] = $this->user_comp->showNotificacion($id);		

        //get personal info
        $data['user'] =  $this->user_comp->getInformationInfo($id);
		$name =  $data['user']['name'];
		
		if(!empty($data['events'])){
			
			$subject = 'Scadenza evento';	
			$email = \Swift_Message::newInstance()
					->setSubject("".$subject."")
					->setFrom("admin@studiopierpaolosanna.it")					
					->setTo("".$username."")
					->setBcc("admin@studiopierpaolosanna.it")
					//->setBcc("yosveni.escalona@gmail.com")
					->setBody(
						$this->renderView(
							'BackendBundle:Email:email_scadenza_event.html.twig',
							array('name_client'=>$name)
						),
						'text/html'
					);				
					
				$this->get('mailer')->send($email);
		
		}

       return $this->render('BackendBundle:Default:index.html.twig', array('result'=>array('data'=>$data)));
    }




}

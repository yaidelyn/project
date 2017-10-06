<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08/12/2016
 * Time: 12:40
 */

namespace Sanna\UsuarioBundle\Controller;


use Sanna\UsuarioBundle\Entity\Profile;
use Sanna\UsuarioBundle\Entity\User;
use Sanna\UsuarioBundle\Entity\TempEventUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints\Date;
use Sanna\UsuarioBundle\Entity\Alert;
use Sanna\UsuarioBundle\Entity\Event;
use Sanna\UsuarioBundle\Entity\Eventusers;
use Symfony\Component\UserComponent\UserComponent;
use Symfony\Component\Email\EmailComponent;

class UsuarioController extends Controller{

    var $user_comp;
    var $email_comp;

    public function __construct()
    {
        $this->user_comp = new UserComponent($this);
        $this->email_comp = new EmailComponent($this);
    }

    public function registerUserAction(Request $request){
        if($request->isMethod('POST')) {
            //echo '<pre>';print_r($_POST);die;
            $em = $this->get('doctrine')->getManager();
            if (isset($_POST["data"]["verif"])){
                $response = array("code" => 0);
                $email = $_POST["data"]["verif"];
                $em = $this->get('doctrine')->getManager();
                $user = $em->getRepository('UsuarioBundle:User')->findBy(array('email'=>$email));
                //print_r($user); die;
                if (!empty($user))
                    $response = array("code" => 1);
                return new Response(json_encode($response));
            }else{
                $em = $this->get('doctrine')->getManager();
                $au = $em->getRepository('UsuarioBundle:User')->findBy(array('email'=>$_POST['email']));
                //print_r($user); die;
                if (empty($au)){
                    $user = new User();
                    //create user
                    $user->setUsername($_POST['email']);
                    $user->setEmail($_POST['email']);
                    $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                    $password = $encoder->encodePassword($request->get('password'), $user->getSalt());
                    $user->setPassword($password);
                    //$user->setRoles(($_POST['role']==1)?'ROLE_USER':'ROLE_ADMIN');
                    $em->persist($user);
                    $em->flush();

                    //enviar email
                   // $this->email_comp->sendEmailComp('test@studiopierpaolosanna.it',$_POST['email'],'UsuarioBundle:Default:email.html.twig');
				   
				 $subject = "Dati d'accesso Sito Studio Pierpaolo Sanna";
				 $email_user = $_POST['email'];
				 $msg = "";
				 $name_client =$_POST['name'].' '.$_POST['lastname'];
				   
						//send email
				$email = \Swift_Message::newInstance()
					->setSubject("".$subject."")
					->setFrom("admin@studiopierpaolosanna.it")
					->setTo("".$email_user."")
					->setBcc("admin@studiopierpaolosanna.it")
					->setBody(
						$this->renderView(
							'BackendBundle:Email:email_template.html.twig',
							array('name_client'=>$name_client,'email_person'=>$_POST['email'],'password'=>$request->get('password'))
						),
						'text/html'
					);
					//$date = date("F j, Y, g:i a");
					$email2 = \Swift_Message::newInstance()
						->setSubject("".$subject."")
						->setFrom("admin@studiopierpaolosanna.it")
						->setTo("sitostudio@studiosanna.info")
						->setBcc("admin@studiopierpaolosanna.it")
						->setBody(
							$this->renderView(
								'BackendBundle:Email:email_admin.html.twig',
								array('email_person'=>$email_user)
							),
							'text/html'
						);	

					
					
					$this->get('mailer')->send($email);
					$this->get('mailer')->send($email2);

                    //add profile
                    $birthday = explode('/', $_POST["birthday"]);
                    $current_user = $em->find('UsuarioBundle:User', $user->getId());
                    $profile = new Profile();
                    $profile->setName($_POST['name']);
                    $profile->setLastname($_POST['lastname']);
                    //$profile->setBirthday(new \DateTime("$birthday[2]-$birthday[1]-$birthday[0]"));
                    $profile->setSex($_POST['gender']);
                    $profile->setLocation($_POST['location']);
                    $profile->setCountry($_POST['country']);
                    $profile->setPhone($_POST['phone']);
                    $profile->setMobile($_POST['mobile']);
                    $profile->setPartitaIva($_POST['partita']);
                    $profile->setCodiceFiscali($_POST['codice_f']);
                    $profile->setAvatar(($_POST['gender'] == 'M') ? "guy.png" : "girl.png");
                    $profile->setUser($current_user);
                    $em->persist($profile);
                    $em->flush();
                    return $this->redirect($this->generateUrl('list_all_user'));
                }
            }   
        }
		$user = $this->get('security.context')->getToken()->getUser();
		if (is_object($user)){
           $id = $user->getId();
           $data['user'] = $this->user_comp->getInformationInfo($id);
           $rol = $user->getRoles()[0];
       }
       else{
           $data['id'] = 0;
           $data['name'] = '';
           $data['firtname'] = '';
           $data['lastname'] = '';
           $data['phone'] = '';
           $data['mobile'] = '';
           $data['gender'] = '';
           $data['avatar'] = '';
           $data['location'] = '';
           $data['country'] = '';
           $data['birthday'] = '';
           $rol = 'Invitado';
       }	
        
        /*$id = $user->getId();
        $data['user'] = $this->user_comp->getInformationInfo($id);
        $rol = $user->getRoles()[0];*/
        return $this->render('UsuarioBundle:Usuario:register.html.twig', array('result'=>array('data'=>$data, 'rol'=>$rol)));
       
    }

    public function calendarAction(Request $request){
        $users = array();
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        //get personal info
        $data['user'] =  $this->user_comp->getInformationInfo($id);
        $em = $this->get('doctrine')->getManager();
        $res= $em->getRepository('UsuarioBundle:User')->findAll();
        foreach ($res as $key => $value) {
            $users[$key]['id'] = $value->getId();
            $users[$key]['name'] = $value->getUsername();
        }
        //echo '<pre>';print_r($data2);die;
        //$data = $this->getEventsAction(1);
        return $this->render('UsuarioBundle:Usuario:calendar.html.twig', array('result'=>array('data'=>$data, 'users'=>$users)));
    }

    public function getColor(){
        $aleat = rand(0, 29);
        $colors = array("#D9534F", "#ED9C28", "#6B787F", "#70AFC4", "#5E87B0", "#F8D347", "#8175C7", "#2A3542", "#01A89E", "#E56155", 
                        "#A9D96C", "#34A39B", "#FF6C60", "#F7464A", "#46BFBD", "#4DA74D", "#DB5E8C", "#A696CE", "#F0AD4E", "#4B739A", 
                        "#D1CF0D", "#3CA0DD", "#000000", "#FFF000", "#6160DB", "#FF4A4A", "#675FD6", "#F2A537", "#F90F9A", "#209731");
        return $colors[$aleat];
    }

    public function getEventsAction(){
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $response = $colors = array();
        $em = $this->getDoctrine()->getManager();
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $month = $_POST['month'];
            $year  = $_POST['year'];
            if ($month < 10){
                $date1 = "{$year}-0{$month}-01";
                $date2 = "{$year}-0{$month}-31";
            }
            else{
                $date1 = "{$year}-{$month}-01";
                $date2 = "{$year}-{$month}-31";
            }
            //Cargar eventos del año en curso
            //$events = $em->getRepository('UsuarioBundle:Event')->findAll();
            $events = $this->getDoctrine()->getManager()
                           ->createQuery("SELECT e FROM UsuarioBundle:Event e WHERE (e.start >= '$date1' AND e.start <= '$date2') OR (e.end >= '$date1' AND e.end <= '$date2')")->getResult();
            //print_r($events); die;
            foreach ($events as $key => $value) {
                $response[$key]['id'] = $value->getId();
                $response[$key]['title'] = $value->getTitleEvent();
                $color = $this->getColor();
                while (in_array($color, $colors))
                    $color = $this->getColor();
                $colors[] = $color;
                $response[$key]['color'] = $color;
                $response[$key]['description'] = $value->getDescription();
                $response[$key]['start'] = date_format($value->getStart(), 'Y-m-d');
                if($value->getEnd())
                    $response[$key]['end'] = date_format($value->getEnd(), 'Y-m-d');
            }
        }
        else{
            $idevest = $em->getRepository('UsuarioBundle:Eventusers')->findBy(array('userId'=>$id));
            foreach ($idevest as $key => $value) {
                $event = $em->getRepository('UsuarioBundle:Event')->findBy(array('id'=>$value->getEventId()));
                $response[$key]['id'] = $event[0]->getId();
                $response[$key]['title'] = $event[0]->getTitleEvent();
                $color = $this->getColor();
                while (in_array($color, $colors))
                    $color = $this->getColor();
                $colors[] = $color;
                $response[$key]['color'] = $color;
                $response[$key]['description'] = $event[0]->getDescription();
                $response[$key]['start'] = date_format($event[0]->getStart(), 'Y-m-d');
                if($event[0]->getEnd())
                    $response[$key]['end'] = date_format($event[0]->getEnd(), 'Y-m-d');
            }
        }
        //print_r($response); die;
        return new Response(json_encode($response));
    }

    public function setEventAction(){
        if(!empty($_POST)){
            //print_r($_POST); die;
            $em = $this->get('doctrine')->getManager();
            //$user = $this->get('security.context')->getToken()->getUser();
            //$id = $user->getId();
            $start = explode('/', $_POST["data"]["start"]);
            $end = ($_POST["data"]["end"]) ? explode('/', $_POST["data"]["end"]) : 0;
            $event = new Event();
            $event->setTitleEvent($_POST['data']['title']);
            $event->setSite($_POST['data']['site']);
            $event->setDescription($_POST['data']['descript']);
            $event->setStart(new \DateTime("$start[2]-$start[1]-$start[0]"));
            if ($end)
                $event->setEnd(new \DateTime("$end[2]-$end[1]-$end[0]"));
            //$event->setUser($user);
            $em->persist($event);
            $em->flush();

            if ($_POST['data']['ids'] == -1){
                $users = $em->getRepository('UsuarioBundle:User')->findAll();
                foreach ($users as $key => $value) {
					//echo 'Holaaa'.$value;
                    //$this->email_comp->sendEmailComp('test@studiopierpaolosanna.it', $value->getUsername(), 'BackendBundle:Email:email_template.html.twig');
				
				 $subject = 'Nuovo Evento';
				// $email_user = $value->getUsername();
				 $email_user = $value;
				 //$data_user =  $this->user_comp->getInformationInfo($value->getId());
				 $msg = "";
				 //$name_client =$_POST['name'].' '.$_POST['lastname'];
				   
				//send email
				$email = \Swift_Message::newInstance()
					->setSubject("".$subject."")
					->setFrom("admin@studiopierpaolosanna.it")					
					->setTo("".$email_user."")
					->setBcc("sitostudio@studiosanna.info")
					->setBcc("admin@studiopierpaolosanna.it")
					->setBody(
						$this->renderView(
							'BackendBundle:Email:email_create_event.html.twig',
							array('name_client'=>$email_user)
						),
						'text/html'
					);				
					
					$this->get('mailer')->send($email);
					
                    $eventusers = new Eventusers();
                    $eventusers->setUserId($value->getId());
                    $eventusers->setEventId($event->getId());
                    //Persistimos el objeto
                    $em->persist($eventusers);
                    //Insertarmos en la base de datos
                    $em->flush();
                }
            }
            else{
                foreach ($_POST['data']['ids'] as $key => $value) {
					//echo 'value '.$value;die;
                    $user = $em->getRepository('UsuarioBundle:User')->find($value);
                    //echo '<pre>';print_r($user);die;
                    //send email x user
                    //$this->email_comp->sendEmailComp('test@studiopierpaolosanna.it', $user->getUsername(), 'BackendBundle:Email:email_template.html.twig');
				 
				 $subject = 'Nuovo Evento';
				 $email_user = $user->getUsername();
				 $data_user =  $this->user_comp->getInformationInfo($user->getId());				 
				//send email
				$email = \Swift_Message::newInstance()
					->setSubject("".$subject."")
					->setFrom("admin@studiopierpaolosanna.it")
					->setTo("".$email_user."")
					->setBcc("admin@studiopierpaolosanna.it")
					->setBody(
						$this->renderView(
							'BackendBundle:Email:email_create_event.html.twig',
								array('name_client'=>$data_user['name'])
						),
						'text/html'
					);				
					
					$this->get('mailer')->send($email);
                    $eventusers = new Eventusers();
                    $eventusers->setUserId($value);
                    $eventusers->setEventId($event->getId());
                    //Persistimos el objeto
                    $em->persist($eventusers);
                    //Insertarmos en la base de datos
                    $em->flush();
                }
            }
            if (isset($_POST['data']['alerts']))
            foreach ($_POST['data']['alerts'] as $k => $v){
                if ($v){
                    $alert= new Alert();
                    $date = explode('/', $v);
                    $alert->setEventId($event->getId());
                    $alert->setDate(new \DateTime("$date[2]-$date[1]-$date[0]"));
                    //Persistimos el objeto
                    $em->persist($alert);
                    //Insertarmos en la base de datos
                    $em->flush();
                }
            }

            $array["id"] = $event->getId();
            $array["title"] = $_POST["data"]["title"];
            $array["start"] = "$start[2]-$start[1]-$start[0]";
            $array['description'] = $_POST['data']['descript'];
            if ($end)
                $array["end"] = "$end[2]-$end[1]-$end[0]";

            $response = array("code" => 1, 'event'=>$array, 'id'=>$array["id"]);
            return new Response(json_encode($response));
        }
    }

    public function delEventsAction(){
        if(!empty($_POST)){
            $name = '';
            $id = $_POST['data']['id'];
            $em = $this->get('doctrine')->getManager();
            if ($id == -1){ // TODOS LOS DEL MES VISIBLE
                $month = $_POST['data']['month'];
                $year  = $_POST['data']['year'];
                if ($month < 10){
                    $date1 = "{$year}-0{$month}-01";
                    $date2 = "{$year}-0{$month}-31";
                }
                else{
                    $date1 = "{$year}-{$month}-01";
                    $date2 = "{$year}-{$month}-31";
                }
                //Cargar eventos del año en curso
                //$idevest = $em->getRepository('UsuarioBundle:Event')->findAll();
                $events = $this->getDoctrine()->getManager()
                                ->createQuery("SELECT e FROM UsuarioBundle:Event e WHERE (e.start >= '$date1' AND e.start <= '$date2') OR (e.end >= '$date1' AND e.end <= '$date2')")->getResult();
                foreach ($events as $key => $value) {
                    $eu = $em->getRepository('UsuarioBundle:Eventusers')->findBy(array('eventId'=>$value->getId()));
                    $al = $em->getRepository('UsuarioBundle:Alert')->findBy(array('eventId'=>$value->getId()));
                    foreach ($eu as $k => $v) {
                        $em->remove($v);
                    }
                    foreach ($al as $t => $s) {
                        $em->remove($s);
                    }
                    $em->remove($value);
                }
                $em->flush();
            }
            else{
                $ev = $em->getRepository('UsuarioBundle:Event')->findBy(array('id'=>$id));
                $al = $em->getRepository('UsuarioBundle:Alert')->findBy(array('eventId'=>$id));
                $eu = $em->getRepository('UsuarioBundle:Eventusers')->findBy(array('eventId'=>$id));
                $name = $ev[0]->getTitleEvent();
                foreach ($al as $key => $value) {
                    $em->remove($value);
                }
                foreach ($eu as $k => $v) {
                    $em->remove($v);
                }
                $em->remove($ev[0]);
                $em->flush();
            }
            $response = array("code"=>1, 'event'=>$id, 'text'=>$name);
            return new Response(json_encode($response));
        }
    }

    public function editProfileUserAction($iduser){
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $em = $this->get('doctrine')->getManager();
        if(!empty($_POST)){
            //echo '<pre>';print_r($_POST);die;
            $user = $em->getRepository('UsuarioBundle:User')->find($iduser);
            if ($_POST['password']){
                $encoder = $this->get('security.encoder_factory')->getEncoder($user);
                $password = $encoder->encodePassword($_POST['password'], $user->getSalt());
                $user->setPassword($password);
                //$user->setRoles(($_POST['role']==1)?'ROLE_USER':'ROLE_ADMIN');
                $em->persist($user);
                $em->flush();
            }
            $profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array(
                'user'=>$iduser
            ));

            $avatar = (isset($_FILES['file']['name'])) ? $this->uploadFile($user->getEmail(), $_FILES) : 0;

            $birthday = explode('/', $_POST["birthday"]);
            $profile->setName($_POST['name']);
            $profile->setLastname($_POST['lastname']);
            //$profile->setBirthday(new \DateTime("$birthday[2]-$birthday[1]-$birthday[0]"));
            $profile->setSex($_POST['sexo']);
            $profile->setPhone($_POST['phone']);
            $profile->setMobile($_POST['mobile']);
            $profile->setLocation($_POST['location']);
            $profile->setCountry($_POST['country']);
			$profile->setPartitaIva($_POST['partita']);
            $profile->setCodiceFiscali($_POST['codice']);
            if ($avatar)
                $profile->setAvatar($avatar);

            $em->persist($profile);
            $em->flush();
            die($avatar);
        }else{
            //get personal info
            $data['user'] =  $this->user_comp->getInformationInfo($id);

            return $this->render('UsuarioBundle:Usuario:edit_profile_user.html.twig', array('result'=>array('data'=>$data)));

        }
    }

    //list all user
    public function listAllUserAction(){
        $em = $this->get('doctrine')->getManager();
        $user = $this->get('security.context')->getToken()->getUser();       
		if(is_object($user)){
			$id = $user->getId();
			$data['user'] =  $this->user_comp->getInformationInfo($id);
			$users = $em->getRepository('UsuarioBundle:Profile')->findAll();
			foreach($users as $profile){
				if($profile->getUser()->getId() != $id){
					$usr = $em->getRepository('UsuarioBundle:User')->find($profile->getUser()->getId());
					$obj['iduser'] = $profile->getUser()->getId();
					$obj['fullname'] = $profile->getName().' '.$profile->getLastname();
					$obj['email'] = $usr->getEmail();
					$obj['phone'] = $profile->getPhone();
					$obj['location'] = $profile->getLocation();
					$obj['sexo'] = $profile->getSex();
					$obj['pais'] = $profile->getCountry();
					$result[] = $obj;
				}
			}
			$data['profile'] = $result;
			//echo '<pre>';print_r($data);die;
			return $this->render('UsuarioBundle:Usuario:list_users.html.twig', array('result'=>array('data'=>$data)));
		}else{
			return $this->redirect($this->generateUrl('User_index'));
		}
       
    }

    public function deleteUserAction($id){
        if(isset($id)){
            $em = $this->get('doctrine')->getManager();
            $user = $em->getRepository('UsuarioBundle:User')->find($id);
            $profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array(
                'user'=>$id
            ));
            $em->remove($profile);
            $em->remove($user);
            $em->flush();

            $response = array("code" => 1, "success" => true,'mensaje'=>'OKKK!!!');
            return new Response(json_encode($response));
        }
    }

    public function editUserAction($iduser){
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $em = $this->get('doctrine')->getManager();
        if(!empty($_POST)){
            //echo '<pre>';print_r($_POST);die;
            $user = $em->getRepository('UsuarioBundle:User')->find($iduser);
            $profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array(
                'user'=>$iduser
            ));

            $profile->setName($_POST['data']['name']);
            $profile->setLastname($_POST['data']['lastname']);
            $profile->setSex($_POST['data']['sexo']);
            $profile->setPhone($_POST['data']['phone']);
            $profile->setCountry($_POST['data']['country']);
            $profile->setPartitaIva($_POST['data']['partita']);
            $profile->setCodiceFiscali($_POST['data']['codice']);

            $em->persist($profile);
            $em->flush();

            $response = array("code" => 1, "success" => true,'mensaje'=>'Update!!!');
            return new Response(json_encode($response));

        }else{
            //get personal info
            $data['user'] =  $this->user_comp->getInformationInfo($id);
            $data['profile'] =  $this->user_comp->getInformationInfo($iduser);
            $data['profile']['birthday'] = str_replace('-', '/', $data['profile']['birthday']);
            //$data['profile']['email'] = $user->getEmail();
            //print_r($data['profile']); die;

            return $this->render('UsuarioBundle:Usuario:edit_user.html.twig', array('result'=>array('data'=>$data)));
        }
    }

    function uploadFile($namefile, $file = array()){
        $allowedExtensions = array('jpg','jpeg','png','gif');
        $extension = $this->extension($file['file']['name']);
        $flag = (in_array($extension, $allowedExtensions))?true:false;
        if($flag){
            $path = $_SERVER["DOCUMENT_ROOT"].'/Sanna/web/bundles/common/img/avatars';
            if (!file_exists($path)) {
               mkdir($path, 0777, true);
            }
            $name = $namefile.'.'.$extension;
            if(move_uploaded_file($file['file']['tmp_name'],$path.'/'.$name)){
                list($ancho, $alto) = getimagesize($path.'/'.$name);
                //echo $ancho.'--->'.$alto; die;
                if ($ancho != $alto || $ancho != 45){
                    if ($extension == 'jpg' || $extension == 'jpeg')
                        $img_original = imagecreatefromjpeg($path.'/'.$name);
                    elseif($extension == 'png')
                        $img_original = imagecreatefrompng($path.'/'.$name);
                    else
                        $img_original = imagecreatefromgif($path.'/'.$name);
                    
                    $max_ancho = $max_alto = 45;

                    $x_ratio = $max_ancho / $ancho;
                    $y_ratio = $max_alto / $alto;

                    if (($x_ratio * $alto) < $max_alto){
                        $alto_final = ceil($x_ratio * $alto);
                        $ancho_final = $max_ancho;
                    }else{
                        $ancho_final = ceil($y_ratio * $ancho);
                        $alto_final = $max_alto;
                    }
                    
                    $tmp = imagecreatetruecolor($ancho_final, $alto_final);
                    imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);
                    imagedestroy($img_original);

                    $calidad=95;
                    imagejpeg($tmp, $path.'/'.$name, $calidad);
                }
                sleep(3);
            }
        }
        $file = $path.''.$file['file']['name'];
        return $name;
    }

    function extension($filename){
        return substr(strrchr($filename, '.'), 1);
    }
	public function sendEventAction(){
		//
		date_default_timezone_set('Europe/Rome');
		$em = $this->get('doctrine')->getManager();	
		$user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
		$data_user =  $this->user_comp->getInformationInfo($id);
		//echo '<pre>';print_r($data_user);die;
		$event = $em->getRepository('UsuarioBundle:Event')->find($_POST['id']);
		$subject = "Evento visualizzato";	
		$title_event = $_POST['title'];	
		$date = date("g:i a,j F ");
		//11:40, 15 marzo
		//$end_date = $event->getStart()->format('d/m/Y');
		//send email

        $event_user_view = $em->getRepository('UsuarioBundle:TempEventUser')->findOneBy(array(
            'userId'=>$data_user['id'],
            'title'=>$title_event
        ));
        // echo '<pre>';print_r($file_user_view);die;
        if(empty($event_user_view)) {

            $eventTemp = new TempEventUser();
            $eventTemp->setUserId($data_user['id']);
            $eventTemp->setTitle($title_event);
            $em->persist($eventTemp);
            $em->flush();

            $email = \Swift_Message::newInstance()
                ->setSubject("".$subject."")
                ->setFrom("admin@studiopierpaolosanna.it")
                ->setTo("admin@studiopierpaolosanna.it")
                ->setBcc("sitostudio@studiosanna.info")
                //->setBcc("yaidelyn@gmail.com")
                ->setBody(
                    $this->renderView(
                        'BackendBundle:Email:email_read_event.html.twig',
                        array('name'=>$data_user['name'],'event'=>$title_event,'date'=>$date)
                    ),
                    'text/html'
                );

            if($this->get('mailer')->send($email)){
                $response = array("code" => 1, "success" => true,'mensaje'=>'OKKK!!!');
                return new Response(json_encode($response));
            }
        }else{
            $response = array("code" => 1, "success" => true,'mensaje'=>'Not Send!!!');
            return new Response(json_encode($response));
        }

	}
}
<?php

namespace Sanna\BackendBundle\Controller;

use Sanna\BackendBundle\Entity\File;
use Sanna\BackendBundle\Entity\FileTemp;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\UserComponent\UserComponent;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Email\EmailComponent;

class UploadController extends Controller
{
    var $user_comp;

    public function __construct(){
        $this->user_comp = new UserComponent($this);
    }

    public function getNameFolder($lineal=0){
        $em = $this->get('doctrine')->getManager();
        $files = $em->getRepository('BackendBundle:File')->findAll();
        $data = array();
        foreach($files as $item){
            if($lineal)
                $data[] = $item->getNameFolder();
            else
                $data[] = array(
                    'id'=>$item->getId(),
                    'name_folder'=>$item->getNameFolder()
                );
        }
        return $data;
    }

   	public function  deleteAction(){
        if(!empty($_POST)){
		 if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $id = $_POST['data']['id'];
            $iduser = $_POST['data']['iduser'];
            $fullname = $_POST['data']['fullname'];
            $em = $this->get('doctrine')->getManager();
            $file = $em->getRepository('BackendBundle:File')->find($id);
            $user = $em->getRepository('UsuarioBundle:User')->find($iduser);
			$email_user = $user->getUsername();
            $dir = $_SERVER["DOCUMENT_ROOT"]."/web/upload/files/".$fullname;		
            $this->delTemp($dir.'/'.$file->getFile());
            $file->addUser($user);
            $em->remove($file);
            $em->flush();			
			
			//after send email delete			
            $subject = 'File eliminato';           
            $msg = "Ciaoo!!";    

			$response = array("code" => 1, 'message'=>'Document deleted successfully!!!');
			return new Response(json_encode($response));	
            
			}else{
				$response = array("code" => 1, 'message'=>"Spiacente lei non � autorizzato a fare questa operazione. Si prega di contattare l'amministratore");
				return new Response(json_encode($response));
			}	
           
        }
    }  

    public function  uploadAction(){
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $data['user'] = $this->user_comp->getInformationInfo($id);
        $userfiles = $result_keys = array();
        if(!empty($_POST)){
           
            $em = $this->get('doctrine')->getManager();
			//profile del user loguedo
			$profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array('user'=>$id)); 
            //si es un nuevo folder
            if($_POST['new']==0){
                $namefolder = $_POST['namefolder'];
            }elseif($_POST['new']==1){
                $namefolder = $_POST['oldfolder'];
            }
            //get Name User
            $saver = $band = 0;
            $users = explode(',', $_POST['users']);
			$subject = 'Aggiornamento file';
			 //echo '<pre>';print_r($users);die;
            $dir = $_SERVER["DOCUMENT_ROOT"].'/web/upload/files/';
            if ($_POST['namefile'])
                $namefile = $_POST['namefile'] .'.'.$this->extension($_FILES['file']['name']);
            else
                $namefile = $_FILES['file']['name']; 
		 if ($this->get('security.context')->isGranted('ROLE_ADMIN')){		
            foreach ($users as $key => $value) {				
                $user = $em->getRepository('UsuarioBundle:User')->find($value);
				//echo 'Holaaa '.print_r($user);die;
				$email_user = $user->getUsername();
                $profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array('user'=>$user->getId())); 
				$file = new File();
                if (!file_exists($dir.$profile->getName().' '.$profile->getLastName().'/'.$namefile)) {					                  
                    $file->setFile($namefile);
                    $file->setNameFolder($namefolder);
					//echo print_r($user);die;
                    $file->addUser($user);
                    $saver = 1;
                }
                $this->uploadFile($profile->getName().' '.$profile->getLastName(), $namefile, $_FILES, $dir);
				 $this->delTemp($dir.'/'.$namefile);
				if ($saver)
					$em->persist($file);
				$em->flush();
				
				$path = $profile->getName().' '.$profile->getLastName();			  
			   // $email_user = $user->getEmail();
				$msg = "Ciaoo!!";
				//$dir = $path.'/'.$namefile;
				$dir =$_SERVER["DOCUMENT_ROOT"].'/web/upload/files/'.$profile->getName().' '.$profile->getLastName().'/'.$namefile;
				//echo $email_user;die;
				   //send email			
				  $email = \Swift_Message::newInstance()
					->setSubject("".$subject."")
					->setFrom("admin@studiopierpaolosanna.it")
					->setTo("".$email_user."")               
					->setBcc("admin@studiopierpaolosanna.it")				
					->setBody(
						$this->renderView(
							'BackendBundle:Email:email_file.html.twig',
							array('data_client'=>$msg,'name_person'=>$path,'name_folder'=>$namefolder)
						),
						'text/html'
					);					
					/* if($this->get('mailer')->send($email)){
						$response = array("code" => 1, 'message'=>'Document uploaded successfully!!!');
						return new Response(json_encode($response));
					}*/	
					if($this->get('mailer')->send($email)){
						$exit++;
					}
				}
				if($exit>0){
					$response = array("code" => 1, 'message'=>'Document uploaded successfully!!!');
                    return new Response(json_encode($response));
				}				
			 }elseif($this->get('security.context')->isGranted('ROLE_USER')){
			 
				$dir =$_SERVER["DOCUMENT_ROOT"].'/web/upload/files/'.$profile->getName().' '.$profile->getLastName().'/'.$namefile;
				
				$file = new File();
                if (!file_exists($dir.$profile->getName().' '.$profile->getLastName().'/'.$namefile)) {					                  
                    $file->setFile($namefile);
                    $file->setNameFolder($namefolder);
					//echo print_r($user);die;
                    $file->addUser($user);
                    $saver = 1;
                }
                $this->uploadFile($profile->getName().' '.$profile->getLastName(), $namefile, $_FILES, $dir);
				 $this->delTemp($dir.'/'.$namefile);
				if ($saver)
					$em->persist($file);
				$em->flush();
				
				$fullname = $profile->getName().' '.$profile->getLastName();
				
				 $email = \Swift_Message::newInstance()
					->setSubject("".$subject."")
					->setFrom("admin@studiopierpaolosanna.it")
				    ->setTo("sitostudio@studiosanna.info")
					->setBcc("admin@studiopierpaolosanna.it")					
					->setBody(
                    $this->renderView(
                        'BackendBundle:Email:email_file2.html.twig',
                        array('data_client'=>$fullname,'name_person'=>$path,'name_folder'=>$namefolder)
                    ),
                    'text/html'
                )
                ->attach(\Swift_Attachment::fromPath(''.$dir.'')
                );
				if($this->get('mailer')->send($email)){
						$exit++;
				}
				 if($this->get('mailer')->send($email)){
                    $response = array("code" => 1, 'message'=>'Document uploaded successfully!!!');
                    return new Response(json_encode($response));
                }
			  }
        }else{
            $jsonencoder = new JsonEncoder();
            $array_userfiles = $this->getAllFilesUser();
            //echo '<pre>'; print_r($array_userfiles); die;
            if(!empty($array_userfiles)){
                $userfiles = $jsonencoder->encode($array_userfiles,$format = 'json');
                foreach($array_userfiles as $val=>$key){
                    $array_keys[] = array_keys($key);
                }
                $data['userfiles'] = $userfiles;
                $result_keys = $jsonencoder->encode($array_keys,$format = 'json');
                $data['keys'] = $result_keys;
            }else{
				 $result[0]['empty']['empty'][] = array(
                                'id'=>0,
                                'file'=>'empty'
                            );
				$userfiles = $jsonencoder->encode( $result,$format = 'json');
				$result_keys = $jsonencoder->encode($array_keys,$format = 'json');
                $data['userfiles'] = $userfiles;
                $data['keys'] = 0;
            }
            //echo '<pre>';print_r($userfiles);die;
            //echo $this->getAllFolderUser();
            if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
                $data['profiles'] = $this->user_comp->getAllUser();
            else{
                $em = $this->get('doctrine')->getManager();
                $profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array('user'=>$id));
                $aux['iduser'] = $id;
                $aux['fullname'] = $profile->getName().' '. $profile->getLastname();
                $data['profiles'][] = $aux;
                //print_r($data['profiles']); die;
            }
            $folder_aux = array();
            $folders = $this->getNameFolder(1);
            //print_r($folders); die;
            if(!empty($folders)){
                foreach ($folders as $key => $value) {
                    if (!in_array($value, $folder_aux)){
                        $data['folder'][]['name_folder'] = $value;
                        $folder_aux[] = $value;
                    }
                }
            }
           
			$data['user']['rol'] =($this->get('security.context')->isGranted('ROLE_USER'))?'ROLE_USER':'ROLE_ADMIN'; 
		   //echo '<pre>'; print_r($data); die;
            return $this->render('BackendBundle:Default:upload_file.html.twig', array('result'=>array('data'=>$data)));
            //return $this->render('BackendBundle:Email:email_template.html.twig', array('result'=>array('data'=>$data)));
        }
    }

    function uploadFile($name, $namefile, $file = array(), $dir){
        $allowedExtensions = array('docx','doc','pdf','xlsx','jpg','jpeg','tif');
        $flag = (in_array($this->extension($file['file']['name']),$allowedExtensions))?true:false;
        if($flag){
            $path = $dir.'/'.$name;
            if (!file_exists($path)) {
               mkdir($path, 0777, true);
            }
            if(move_uploaded_file($file['file']['tmp_name'], $dir.'/'.$namefile)){
                copy( $dir.'/'.$namefile , $path.'/'.$namefile);
                sleep(3);
            }
            else copy( $dir.'/'.$namefile, $path.'/'.$namefile);
        }
        $file = $path.''.$file['file']['name'];
        return $file;
    }

    function delTemp($file_temp){
        unlink($file_temp);
    }

    function extension($filename){
        return substr(strrchr($filename, '.'), 1);
    }

    public function getAllFolderUser(){
        $em = $this->get('doctrine')->getManager();
        $files = $em->getRepository('BackendBundle:File')->findAll();



       // echo '<pre>';print_r($files);die;
    }

    //get all files x user
    public function getAllFilesUser(){
        $em = $this->get('doctrine')->getManager();
        $result = $users_arr = $arra_folder = array();
        if ($this->get('security.context')->isGranted('ROLE_ADMIN')){
            $files = $em->getRepository('BackendBundle:File')->findAll();
            foreach ($files as $key => $value) {
                $users = $value->getUsers();
                foreach ($users as $k => $v) {
                    $iduser = $v->getId();
                    $profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array('user'=>$iduser));
                    $name = $profile->getName().' '.$profile->getLastName();
                    $folder = $value->getNameFolder();
                    if (in_array($iduser, $users_arr)){
                        $pos = array_search($iduser, $users_arr);
                        if(in_array($folder,$arra_folder)){
                            $result[$pos][$name][$folder][] = array(
                                'id'=>$value->getid(),
                                'file'=>$value->getFile()
                            );
                        }else{
                            $result[$pos][$name][$folder][] = array(
                                'id'=>$value->getid(),
                                'file'=>$value->getFile()
                            );
                            $arra_folder[] = $folder;
                        }
                    }
                    else{
                        unset($data);
                        $data[$name][$folder][] = array(
                            'id'=>$value->getid(),
                            'file'=>$value->getFile()
                        );
                        $result[] = $data;
                        $users_arr[] = $iduser;
                    }
                }
            }
        }else{
            //echo 'If 2';
            $user = $this->get('security.context')->getToken()->getUser();
            $iduser = $user->getId();
           // echo $iduser;die;
            $files = $em->getRepository('BackendBundle:File')->findAll();
            // echo '<pre>';print_r($files);die;
            //$files = $em->getRepository('BackendBundle:File')->findBy(array('users'=>$iduser));
            $profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array('user'=>$iduser));
           // echo '<pre>';print_r($files);die;
            $name = $profile->getName().' '.$profile->getLastName();
            foreach ($files as $key => $value) {
                $users = $value->getUsers();
                foreach ($users as $k => $v) {
                    $folder = $value->getNameFolder();
                    if ($iduser == $v->getId()){
                        if(in_array($folder,$arra_folder)){
                            $result[0][$name][$folder][] = array(
                                'id'=>$value->getid(),
                                'file'=>$value->getFile()
                            );
                        }else{
                            $result[0][$name][$folder][] = array(
                                'id'=>$value->getid(),
                                'file'=>$value->getFile()
                            );
                            $arra_folder[] = $folder;
                        }
                    }
                    else
                        unset($data);
                }
            }
        }
       // echo '<pre>';print_r($result);die;
        return $result;
    }

    public function fileAction(){
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $data['user'] = $this->user_comp->getInformationInfo($id);
        $userfiles = $result_keys = array();
       // $files = $this->getAllFilesUser();

        $jsonencoder = new JsonEncoder();
        $array_userfiles = $this->getAllFilesUser();
        $data['files'] = $array_userfiles;
        if(!empty($array_userfiles)){
            $userfiles = $jsonencoder->encode($array_userfiles,$format = 'json');
            foreach($array_userfiles as $val=>$key){
                $array_keys[] = array_keys($key);
            }
            $data['userfiles'] = $userfiles;
            $result_keys = $jsonencoder->encode($array_keys,$format = 'json');
            $data['keys'] = $result_keys;
        }else{
            $data['userfiles'] = $userfiles;
            $data['keys'] = $result_keys;
        }
        //echo '<pre>';print_r($data);die;
        return $this->render('BackendBundle:Default:file.html.twig', array('result'=>array('data'=>$data)));
    }
	
	public function sendEmailDocAction(){
		$em = $this->get('doctrine')->getManager();
		$user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        //$data['user'] = $this->user_comp->getInformationInfo($id);
		$profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array('user'=>$user->getId()));
        //echo $profile->getUser()->getUsername();die;
        $dirEmail = $profile->getUser()->getUsername();
        $nameFile = $_POST['data']['file'];
        //buscar si el archivo fue subido por visto por el usuario
        $file_user_view = $em->getRepository('BackendBundle:FileTemp')->findOneBy(array(
            'dirEmail'=>$dirEmail,
            'nameFile'=>$nameFile
        ));
       // echo '<pre>';print_r($file_user_view);die;
        if(empty($file_user_view)){

            $fileTemp = new FileTemp();
            $fileTemp->setDirEmail($dirEmail);
            $fileTemp->setNameFile($nameFile);
            $em->persist($fileTemp);
            $em->flush();

            //send email
            $email = \Swift_Message::newInstance()
                ->setSubject("File visualizzato ")
                ->setFrom("admin@studiopierpaolosanna.it")
                //->setTo("".$email_user."")
				->setTo("admin@studiopierpaolosanna.it")
				->setBcc("yosveni.escalona@gmail.com")
                ->setBody(
                    $this->renderView(
                        'BackendBundle:Email:email_file_ready.html.twig',
                        array('name_client'=>$profile->getName().' '.$profile->getLastName(),'name_folder'=>$_POST['data']['user'],'name_doc'=>$_POST['data']['file'])
                    ),'text/html');
					
                if($this->get('mailer')->send($email)){
                    $response = array("code" => 1, 'message'=>'Document deleted successfully!!!');
                    return new Response(json_encode($response));
                }			
		}else{
            $response = array("code" => 1, 'message'=>'Document was send!!!');
            return new Response(json_encode($response));
        }
	}
}
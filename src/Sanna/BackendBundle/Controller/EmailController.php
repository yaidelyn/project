<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 18/12/2016
 * Time: 1:50
 */

namespace Sanna\BackendBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\UserComponent\UserComponent;
/*inbox email*/
use Sanna\BackendBundle\PhpImap\Mailbox;
use Sanna\BackendBundle\PhpImap\Mailbox as ImapMailbox;
use Sanna\BackendBundle\PhpImap\IncomingMail;
use Sanna\BackendBundle\PhpImap\IncomingMailAttachment;
use Symfony\Component\Email\EmailComponent;

class EmailController extends Controller
{
    var $user_comp;
    var $email_comp;


    public function __construct()
    {
        $this->user_comp = new UserComponent($this);
        $this->email_comp = new EmailComponent($this);

    }

    public function inboxAction(){

        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $mailbox = new Mailbox('{localhost:110/pop3/novalidate-cert}INBOX', 'admin@studiopierpaolosanna.it', 'Admin1qazse4', __DIR__);
        /*get email*/
        // Read all messaged into an array:
        $mailsIds = $mailbox->searchMailbox('ALL');
        //echo '<pre>';print_r($mailsIds);die;
        //echo '<pre>';print_r($mailbox->searchMailbox('ALL'));die;

        if(!$mailsIds) {
            die('Mailbox is empty');
        }else{

            foreach($mailsIds as $item){
                $email['id'] = $mailbox->getMail($item)->id;
                $email['date'] =$mailbox->getMail($item)->date;
                $email['subject'] = $mailbox->getMail($item)->subject;
                $email['fromAddress'] = $mailbox->getMail($item)->fromAddress;
                $email['message'] = $mailbox->getMail($item)->textPlain;
                $to = $mailbox->getMail($item)->to;

                foreach($to as $key=>$value){
                    $keys[] = $key;
                }
                $email['keys'] = $keys;
                unset($keys);

                $array_inbox[] = $email;
            }

        }

        //echo '<pre>';print_r($array_inbox);die;

        // Get the first message and save its attachment(s) to disk:
        $mail = $mailbox->getMail($mailsIds[0]);

        //echo '<pre>';print_r($mail->getAttachments());
        //var_dump($mail->getAttachments());

        //get personal info
        $data['user'] =  $this->user_comp->getInformationInfo($id);
        $data['email'] = $array_inbox;

        //echo '<pre>';print_r($data);die;

        return $this->render('BackendBundle:Email:inbox.html.twig', array('result'=>array('data'=>$data)));

    }

    //view email inbox
    public function viewInboxAction(){
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $em = $this->get('doctrine')->getManager();
        $mailbox = new Mailbox('{localhost:110/pop3/novalidate-cert}INBOX', 'admin@studiopierpaolosanna.it', 'Admin1qazse4', __DIR__);

        $mailsIds = $mailbox->searchMailbox('ALL');

        if(!$mailsIds) {
            die('Mailbox is empty');
        }else{

            foreach($mailsIds as $item){
                $email['id'] = $mailbox->getMail($item)->id;
                $email['date'] =$mailbox->getMail($item)->date;
                $email['subject'] = $mailbox->getMail($item)->subject;
                $email['fromAddress'] = $mailbox->getMail($item)->fromAddress;
                $email['message'] = $mailbox->getMail($item)->textPlain;
                $to = $mailbox->getMail($item)->to;

                foreach($to as $key=>$value){
                    $keys[] = $key;
                }
                $email['keys'] = $keys;
                unset($keys);

                $array_inbox[] = $email;
            }
        }
        $data['user'] =  $this->user_comp->getInformationInfo($id);
        $data['email'] = $array_inbox;

        $data = $this->render('BackendBundle:Email:inbox_ajax_mail.html.twig', array(
            'result'=>array('data'=>$data)
        ));
        $status = 'success';
        $html = $data->getContent();

        $jsonArray = array(
            'status' => $status,
            'data' => $html,
        );

        $response = new Response(json_encode($jsonArray));
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');

        return $response;
    }

    //view message
    public function viewMessageAction(){
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $em = $this->get('doctrine')->getManager();
        $mailbox = new Mailbox('{localhost:110/pop3/novalidate-cert}INBOX', 'admin@studiopierpaolosanna.it', 'Admin1qazse4', __DIR__);

        $mail = $mailbox->getMail($_POST['id']);


        $email['id'] = $mailbox->getMail($_POST['id'])->id;
        $email['date'] =$mailbox->getMail($_POST['id'])->date;
        $email['subject'] = $mailbox->getMail($_POST['id'])->subject;
        $email['toaddress'] = 'test@sanna.net';
        $email['fromName'] = $mailbox->getMail($_POST['id'])->fromName;
        $email['fromAddress'] = $mailbox->getMail($_POST['id'])->fromAddress;
        $email['message'] = $mailbox->getMail($_POST['id'])->textPlain;
        //$email['message'] = htmlspecialchars($mailbox->getMail($_POST['id'])->textHtml);

       // echo '<pre>';print_r($mail);die;

        $data['user'] =  $this->user_comp->getInformationInfo($id);
        $data['email'] = $email;

        $status = 'error';
        $html = '';
        if($mail){

            $data = $this->render('BackendBundle:Email:inbox_ajax_mail.html.twig', array(
                'result'=>array('data'=>$data)
            ));
            $status = 'success';
            $html = $data->getContent();
        }

        $jsonArray = array(
            'status' => $status,
            'data' => $html,
        );


        $response = new Response(json_encode($jsonArray));
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');

        return $response;



    }

    public function composeEmailAction(){
        $user = $this->get('security.context')->getToken()->getUser();
        $id = $user->getId();
        $data['user'] =  $this->user_comp->getInformationInfo($id);
        if(empty($_POST)){


            $data = $this->render('BackendBundle:Email:email_compose.html.twig', array(
                'result'=>array('data'=>$data)
            ));
            $status = 'success';
            $html = $data->getContent();


        $jsonArray = array(
            'status' => $status,
            'data' => $html,
        );

        $response = new Response(json_encode($jsonArray));
        $response->headers->set('Content-Type', 'application/json; charset=utf-8');

        return $response;


        }else{

            echo '<pre>';print_r($_POST);die;
            $this->email_comp->sendEmailComp('test@sanna.net',$user->getEmail(),'UsuarioBundle:Default:email.html.twig');


        }
    }
	public function sendMailAction(){
		
		if(!empty($_POST)){		 
            $subject = $_POST['data']['subject'];
            $email_user = $_POST['data']['addressTo'];
            $msg = $_POST['data']['messaggio'];         

            //send email
            $email = \Swift_Message::newInstance()
                ->setSubject("".$subject."")
                ->setFrom("admin@studiopierpaolosanna.it")
                ->setTo("".$email_user."")
                ->setBody("".$msg."",'text/html');
				
                if($this->get('mailer')->send($email)){
                    $response = array("code" => 1, 'message'=>'Document uploaded successfully!!!');
                    return new Response(json_encode($response));
                }
		
		}
	}	
	public function deleteMailAction(){
		//echo '<pre>';print_r($_POST);die;
		//$mailbox = new Mailbox('{localhost:110/pop3/novalidate-cert}INBOX', 'test@studiopierpaolosanna.it', 'admin1qazse4', __DIR__);		
		$imap = imap_open("{localhost:110/pop3/novalidate-cert}INBOX", "admin@studiopierpaolosanna.it", "Admin1qazse4");
		imap_delete($imap, $_POST['id']);
		imap_expunge($imap);
		imap_close($imap);
		//imap_delete($mailbox,$_POST['id']);	
		 $response = array("code" => 1, 'message'=>'Document uploaded successfully!!!');
		return new Response(json_encode($response));
	
	}
	
} 
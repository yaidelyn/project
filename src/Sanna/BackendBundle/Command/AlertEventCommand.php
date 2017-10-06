<?php

namespace Sanna\BackendBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\UserComponent\UserComponent;

class AlertEventCommand  extends ContainerAwareCommand
{

    protected function configure(){
        $this->setName('sanna:alertevent')
            ->setDescription('descripción de lo que hace el comando')
            ->addArgument('my_argument', InputArgument::OPTIONAL, 'Explicamos el significado del argumento');
    }
    protected function execute(InputInterface $input, OutputInterface $output){       
		
		$result = $this->showNotiEvent();
       // $output->writeln('Holaaaa');
      //echo '<pre>';print_r($result);die;
	  $date = date("g:i a,j F ");
       if(!empty($result)){
	   
		 foreach($result as $item){
			$subject = 'Scaduto Evento';
            $email = \Swift_Message::newInstance()
                ->setSubject("".$subject."")
                ->setFrom("admin@studiopierpaolosanna.it")
                ->setTo("".$item['username']."")
                //->setTo("sitostudio@studiosanna.info")
                ->setBcc("sitostudio@studiosanna.info")            
                ->setBody($this->getContainer()->get('templating')->render('BackendBundle:Email:email_scadenza_event.html.twig',
					array('name_client'=>$item['fullname'],'event'=>$item['title'],'date'=>$date)),'text/html');
				
			$this->getContainer()->get('mailer')->send($email);

			}
			
		 }	

    }
   
	//show message and notifications
    public function showNotiEvent(){
        $result = array();       
	   $em = $this->getContainer()->get('doctrine')->getManager();
        $eu = $em->getRepository('UsuarioBundle:Eventusers')->findAll();
        $fecha = explode('-', date('Y-m-d'));      
        foreach ($eu as $key => $value) {
            $event = $em->getRepository('UsuarioBundle:Event')->find($value->getEventId());
			$user = $em->getRepository('UsuarioBundle:User')->find($value->getUserId());
			$profile = $em->getRepository('UsuarioBundle:Profile')->findOneBy(array('user'=>$value->getUserId()));
			//echo '<pre>';print_r($profile);die;
			if(!empty($profile))
				$fullname= $profile->getName().''.$profile->getLastname();
			else
				$fullname = "";
            $username = $user->getUsername();			
            $start = explode('-', date_format($event->getStart(), 'Y-m-d'));
            $ended = explode('-', date_format($event->getEnd(), 'Y-m-d'));
            if ($fecha[1] == $ended[1] && $ended[2] - $fecha[2] >= 0){
                $days = $ended[2] - $fecha[2];
                $result[] = array(
				   'fullname'=>$fullname,
                   'title'=>$event->getTitleEvent(),
                   'description'=> ($days > 0) ? "L'evento scade tra {$days} giorni" : "L'evento scade oggi",
				    'username'=> $username
                );
            }
            elseif($fecha[1] == $start[1] && $ended[1] - $fecha[1] == 1){ //Comienza en este mes y termina en el proximo
                $t1 = array('01', '03', '05', '07', '08', '10', '12');
                $t2 = array('04', '06', '09', '11');
                if (in_array($fecha[1], $t1))
                    $days = 31 - $fecha[2] + $ended[2];
                elseif (in_array($fecha[1], $t2))
                    $days = 30 - $fecha[2] + $ended[2];
                elseif (($fecha[0]%4==0 and $fecha[0]%100!=0) || $fecha[0]%400==0)
                    $days = 29 - $fecha[2] + $ended[2];
                else
                    $days = 28 - $fecha[2] + $ended[2];
                $result[] = array(
				   'fullname'=>$fullname,
                   'title'=>$event->getTitleEvent(),
                   'description'=> "L'evento scade tra {$days} giorni",
	           'username'=> $username
                );
            }
        }
		return $result;      
    }
} 
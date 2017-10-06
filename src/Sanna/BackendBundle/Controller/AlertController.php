<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 08/01/2017
 * Time: 19:37
 */

namespace Sanna\BackendBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\UserComponent\UserComponent;

class AlertController extends Controller
{
    var $obj_comp;

    public function __construct(){
        $this->obj_comp = new UserComponent($this);
    }

} 
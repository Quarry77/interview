<?php

namespace BioWare\Interview\MessengerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('BioWareInterviewMessengerBundle:Default:index.html.twig', array('name' => $name));
    }
}

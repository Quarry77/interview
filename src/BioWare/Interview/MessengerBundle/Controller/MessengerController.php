<?php

namespace BioWare\Interview\MessengerBundle\Controller;

use BioWare\Interview\MessengerBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MessengerController extends Controller
{
    public function indexAction()
    {
        $message = new Message();
        $message->setSenderId(0);
        $message->setReceipientId(1);
        $message->setText('Lorem ipsum dolor');

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();




        $id = 1;

        $dbData = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:Message')
            ->find($id);


        if (!$dbData) {
            throw $this->createNotFoundException(
                'No message found for id '.$id
            );
        }
        return new Response($dbData->getText());
    }
}

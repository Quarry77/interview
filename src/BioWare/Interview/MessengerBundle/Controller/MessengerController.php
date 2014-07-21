<?php

namespace BioWare\Interview\MessengerBundle\Controller;

use BioWare\Interview\MessengerBundle\Entity\Message;
use BioWare\Interview\MessengerBundle\Entity\FriendList;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MessengerController extends Controller
{
    public function indexAction()
    {
        $repository = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:User');
        $user = $repository->findOneByFacebookId('10154384963625075');

        // echo '<pre>';
        // \Doctrine\Common\Util\Debug::dump($user);
        // die();
        // $message = new Message();
        // $message->setSenderId(0);
        // $message->setReceipientId(1);
        // $message->setText('Lorem ipsum dolor');

        // $em = $this->getDoctrine()->getManager();
        // $em->persist($message);
        // $em->flush();




        // $id = 1;

        // $dbData = $this->getDoctrine()
        //     ->getRepository('BioWareInterviewMessengerBundle:Message')
        //     ->find($id);


        // if (!$dbData) {
        //     throw $this->createNotFoundException(
        //         'No message found for id '.$id
        //     );
        // }
        // return new Response($dbData->getText());
        $token = $this->get("security.context")->getToken()->getUser();
        echo '<pre>';
        \Doctrine\Common\Util\Debug::dump($token);
        die();
        return new Response('Sup dawg');
    }

    public function logoutAction()
    {
        $this->get("request")->getSession()->invalidate();
        $this->get("security.context")->setToken(null);
        // echo '<pre>';
        // \Doctrine\Common\Util\Debug::dump($user);
        // die();
        //return new Response($user->getUsername().' logged out');
        return new RedirectResponse($this->get("router")->generate("bio_ware_interview_messenger_homepage"));
    }

    public function addFriendAction($facebookId)
    {
        $userRepository = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:User');
        $user = $userRepository->findOneByFacebookId($facebookId);

        if($user == null) {
            $failureResponse = new Response('User not found, have they logged in yet?');
            $failureResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $failureResponse;
        }

        $baseId = $this->get("security.context")->getToken()->getUser()->getFacebookId();
        $friendId = $user->getFacebookId();

        $friendListRepository = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:FriendList');
        $matchingEntry = $friendListRepository->findMatchingEntryByIds($baseId, $friendId);

        $listEntry = new FriendList();
        $listEntry->setBaseId($baseId);
        $listEntry->setFriendId($friendId);

        $em = $this->getDoctrine()->getManager();
        $em->persist($listEntry);
        $em->flush();

        $successResponse = new Response('Friend Added');
        return $successResponse;
    }

    public function getFriendsListAction(Request $request)
    {
        echo '<pre>';
        \Doctrine\Common\Util\Debug::dump($request);
        die();
    }

    public function getMessagesAction($facebookId)
    {
        $token = $this->get("security.context")->getToken()->getUser();
        echo '<pre>';
        \Doctrine\Common\Util\Debug::dump($token->getUser());
        die();
    }

    public function addMessageAction(Request $request, $facebookId)
    {
        $message = $request->get('message');
    }
}

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

    /*
     * Add Friend
     * Add a friend to the users friends list given the friends Facebook ID
     *
     * @param facebookID - string provided throught the URL
     */
    public function addFriendAction($facebookId)
    {
        // Load the user table and find the user given the Facebook ID
        $userRepository = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:User');
        $user = $userRepository->findOneByFacebookId($facebookId);

        // If the user is not in the database return a bad request response
        if($user == null) {
            $failureResponse = new Response('User not found, have they logged in yet?');
            $failureResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $failureResponse;
        }

        // Load the logged in users Facebook ID from the OAuth token
        $baseId = $this->get("security.context")->getToken()->getUser()->getFacebookId();
        $friendId = $facebookId;

        // Compare the two IDs if they are the same, return a bad request response
        if($baseId == $friendId) {
            $failureResponse = new Response('You Can\'t Add Yourself');
            $failureResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $failureResponse;
        }

        // Load the friends list table and check if the entry already exists
        $friendListRepository = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:FriendList');
        $matchingEntry = $friendListRepository->findOneBy(array('baseId' => $baseId, 'friendId' => $friendId));

        // If the two users are already friends, return a success response
        if($matchingEntry != null) {
            $successResponse = new Response('Friend Already Added');
            return $successResponse;
        }

        // Otherwise, create a new entry and add it to the database
        $listEntry = new FriendList();
        $listEntry->setBaseId($baseId);
        $listEntry->setFriendId($friendId);
        $em = $this->getDoctrine()->getManager();
        $em->persist($listEntry);
        $em->flush();

        // Return a success response
        $successResponse = new Response('Friend Added');
        return $successResponse;
    }

    /*
     * Get Friends List
     * Get a list of the users friends in JSON format
     *
     * @return - JSON formatted array containing each friends name and Facebook ID
     */
    public function getFriendsListAction()
    {
        // Load a list of friends list entries based on the logged in users Facebook ID
        $baseId = $this->get("security.context")->getToken()->getUser()->getFacebookId();
        $friendListRepository = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:FriendList');
        $matchingEntries = $friendListRepository->findByBaseId($baseId);

        // Load the user table and for each friend, add the name and Facebook ID to an array
        $userRepository = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:User');
        $friends = Array();
        foreach ($matchingEntries as $key => $value) {
            $friend = $userRepository->findOneByFacebookId($value->getFriendId());

            $friends[$key] = Array('facebookId' => $friend->getFacebookId(), 'name' => $friend->getName());
        }

        // Return the friends list as JSON
        $successResponse = new Response(json_encode($friends));
        return $successResponse;
    }

    /*
     * Add Message
     * Get a list of the users friends in JSON format
     *
     * @param Request - Generated by symfony and used to get the data from the POST request.
     *                  The message must be stored in the data as 'message=<YourMessageHere>'
     * @param facebookID - string provided throught the URL
     */
    public function addMessageAction(Request $request, $facebookId)
    {
        // Loads the message to be added from the POST request
        $text = htmlspecialchars($request->get('message'));
        if($text == null || strlen($text) == 0) {
            $failureResponse = new Response('No Message Found');
            $failureResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $failureResponse;
        }

        // Load the user table and find the user given the Facebook ID
        $userRepository = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:User');
        $user = $userRepository->findOneByFacebookId($facebookId);

        // If the user is not in the database return a bad request response
        if($user == null) {
            $failureResponse = new Response('User not found, have they logged in yet?');
            $failureResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $failureResponse;
        }

        // Load the logged in users Facebook ID from the OAuth token
        $baseId = $this->get("security.context")->getToken()->getUser()->getFacebookId();
        $friendId = $facebookId;

        // Check to see if the logged in user is friends with the person they are trying to send a message to
        $friendListRepository = $this->getDoctrine()
            ->getRepository('BioWareInterviewMessengerBundle:FriendList');
        $matchingEntry = $friendListRepository->findOneBy(array('baseId' => $baseId, 'friendId' => $friendId));

        // If the two users are not friends, return a failure response
        if($matchingEntry == null) {
            $failureResponse = new Response('User Not Your Friend');
            $failureResponse->setStatusCode(Response::HTTP_BAD_REQUEST);
            return $failureResponse;
        }

        // Create a new message entry and add it to the database
        $messageEntry = new Message();
        $messageEntry->setSenderId($baseId);
        $messageEntry->setReceipientId($friendId);
        $messageEntry->setText($text);
        $messageEntry->setTimeCreated(time());
        $em = $this->getDoctrine()->getManager();
        $em->persist($messageEntry);
        $em->flush();

        // Return a success response
        $successResponse = new Response('Message Added');
        return $successResponse;
    }

    public function getMessagesAction($facebookId)
    {
        $token = $this->get("security.context")->getToken()->getUser();
        echo '<pre>';
        \Doctrine\Common\Util\Debug::dump($token->getUser());
        die();
    }
}

<?php

namespace BioWare\Interview\MessengerBundle\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUserProvider;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use BioWare\Interview\MessengerBundle\Entity\User;

class UserProvider extends OAuthUserProvider
{

    /**
     * Constructor
     * Overridden default constructor to add the doctrine entity manager
     *
     * @param em - Entity manager provided through the service definition
     */
    public function __construct($em) {
        $this->em = $em;
    }

    /**
     * Load User By OAuth User Response
     * Whenever a user log in, their data is saved in the security token and, if neccessary, added to the database
     *
     * @param response - UserResponseInterface provided through the HWI OAuth bundle
     * @return - the user data as the custom user class
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        // Search for a user matching the ID provided in the OAuth response
        $repository = $this->em->getRepository('BioWareInterviewMessengerBundle:User');
        $userData = $repository->findOneByFacebookId($response->getResponse()['id']);

        // If not found, add the new user data to the database
        $user = null;
        if($userData == null) {
            $user = new User($response->getResponse()['name']);
            $user->setFacebookId($response->getResponse()['id']);
            $user->setName($response->getResponse()['name']);
            if(array_key_exists('email', $response->getResponse())) {
                $user->setEmail($response->getResponse()['email']);
            } else { 
                $user->setEmail('');
            }

            $this->em->persist($user);
            $this->em->flush();

        // Otherwise, use the data already available
        } else {
            $user = new User($userData->getName());
            $user->setFacebookId($userData->getFacebookId());
            $user->setName($userData->getName());
            $user->setEmail($userData->getEmail());
        }

        // Return the user data to be saved in the security token
        return $user;
    }

    /**
     * Supports Class
     * Overridden function to tell the provider to use the custom user class
     *
     * @param class - string describing the namespace of the supported class
     * @return - the user class as a string
     */
    public function supportsClass($class)
    {
        return $class === 'BioWare\\Interview\\MessengerBundle\\Entity\\User';
    }

    /**
     * Refresh User
     * Overridden class to allow returning the custom user data
     *
     * @param user - UserInterface of user being refreshed
     * @return - the user data
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', get_class($user)));
        }

        return $user;
    }
}
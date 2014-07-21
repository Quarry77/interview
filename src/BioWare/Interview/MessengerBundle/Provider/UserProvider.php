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
    public function __construct($em) {
        $this->em = $em;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $repository = $this->em->getRepository('BioWareInterviewMessengerBundle:User');
        $userData = $repository->findOneByFacebookId($response->getResponse()['id']);

        //\Doctrine\Common\Util\Debug::dump($response->getResponse());
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
        } else {
            $user = new User($userData->getName());
            $user->setFacebookId($userData->getFacebookId());
            $user->setName($userData->getName());
            $user->setEmail($userData->getEmail());
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'BioWare\\Interview\\MessengerBundle\\Entity\\User';
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Unsupported user class "%s"', get_class($user)));
        }

        return $user;
    }
}
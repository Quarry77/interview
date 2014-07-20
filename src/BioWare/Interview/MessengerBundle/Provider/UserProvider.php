<?php

namespace BioWare\Interview\MessengerBundle\Provider;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use BioWare\Interview\MessengerBundle\Entity\User;

class UserProvider implements OAuthAwareUserProviderInterface, UserProviderInterface
{
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {

        $attr = $response->getResponse();
         // \Doctrine\Common\Util\Debug::dump($response);
          $user = new User($response->getUsername(), $attr['id'], $response->getRealName());
        // \Doctrine\Common\Util\Debug::dump($user);  
          
         return $user;
        
        // // make a call to your webservice here
        // $userData = ...
        // // pretend it returns an array on success, false if there is no user

        // if ($userData) {
        //     $password = '...';

        //     // ...

        //     return new WebserviceUser($username, $password, $salt, $roles);
        // }

        // throw new UsernameNotFoundException(
        //     sprintf('Username "%s" does not exist.', $username)
        // );
    }

    public function loadUserByUsername($username)
    {
        //var_dump($username);
    }
    public function loadUserById($username)
    {
        
    }

    public function refreshUser(UserInterface $user)
    {
        \Doctrine\Common\Util\Debug::dump($user);
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        // return $user;
        return $this->loadUserById($user->getId());
    }

    public function supportsClass($class)
    {
        return $class === 'BioWare\Interview\MessengerBundle\Entity\User';
    }
}
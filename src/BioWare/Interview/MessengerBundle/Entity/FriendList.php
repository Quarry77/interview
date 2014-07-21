<?php

namespace BioWare\Interview\MessengerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FriendList
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class FriendList
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="baseId", type="string", length=255)
     */
    private $baseId;

    /**
     * @var string
     *
     * @ORM\Column(name="friendId", type="string", length=255)
     */
    private $friendId;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set baseId
     *
     * @param string $baseId
     * @return FriendList
     */
    public function setBaseId($baseId)
    {
        $this->baseId = $baseId;

        return $this;
    }

    /**
     * Get baseId
     *
     * @return string 
     */
    public function getBaseId()
    {
        return $this->baseId;
    }

    /**
     * Set friendId
     *
     * @param string $friendId
     * @return FriendList
     */
    public function setFriendId($friendId)
    {
        $this->friendId = $friendId;

        return $this;
    }

    /**
     * Get friendId
     *
     * @return string 
     */
    public function getFriendId()
    {
        return $this->friendId;
    }
}

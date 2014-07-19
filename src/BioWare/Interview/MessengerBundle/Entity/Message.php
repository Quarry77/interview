<?php

namespace BioWare\Interview\MessengerBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Message
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
     * @var integer
     *
     * @ORM\Column(name="senderId", type="integer")
     */
    private $senderId;

    /**
     * @var integer
     *
     * @ORM\Column(name="receipientId", type="integer")
     */
    private $receipientId;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255)
     */
    private $text;


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
     * Set senderId
     *
     * @param integer $senderId
     * @return Message
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;

        return $this;
    }

    /**
     * Get senderId
     *
     * @return integer 
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * Set receipientId
     *
     * @param integer $receipientId
     * @return Message
     */
    public function setReceipientId($receipientId)
    {
        $this->receipientId = $receipientId;

        return $this;
    }

    /**
     * Get receipientId
     *
     * @return integer 
     */
    public function getReceipientId()
    {
        return $this->receipientId;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
}

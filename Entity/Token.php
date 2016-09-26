<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 04/06/16
 * Time: 15:41
 */

namespace Vss\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class EmailConfirmationToken
 * @package Vss\UserBundle\Entity
 *
 */
abstract class Token {

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    protected $token;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $expiresAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $used;

    /**
     * Token constructor.
     */
    public function __construct() {
        $this->used = false;
    }

    /**
     * @return boolean
     */
    public function isUsed() {
        return $this->used;
    }

    /**
     * @param boolean $used
     */
    public function setUsed($used) {
        $this->used = $used;
    }

    /**
     * @return \DateTime
     */
    public function getExpiresAt() {
        return $this->expiresAt;
    }

    /**
     * @param \DateTime $expiresAt
     */
    public function setExpiresAt($expiresAt) {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getToken() {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token) {
        $this->token = $token;
    }

}
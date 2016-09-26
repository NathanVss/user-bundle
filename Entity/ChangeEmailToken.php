<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 05/06/16
 * Time: 11:41
 */

namespace Vss\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Vss\UserBundle\Model\VssUserInterface;

/**
 * Class ChangeEmailToken
 * @package Vss\UserBundle\Entity
 *
 */
class ChangeEmailToken extends Token {

    /**
     * @var VssUserInterface
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $newEmail;

    /**
     * @return string
     */
    public function getNewEmail() {
        return $this->newEmail;
    }

    /**
     * @param string $newEmail
     */
    public function setNewEmail($newEmail) {
        $this->newEmail = $newEmail;
    }

    /**
     * @return VssUserInterface
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @param VssUserInterface $user
     */
    public function setUser($user) {
        $this->user = $user;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 04/06/16
 * Time: 15:43
 */

namespace Vss\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Vss\UserBundle\Model\VssUserInterface;

/**
 * Class EmailConfirmationToken
 * @package Vss\UserBundle\Entity
 *
 */
class EmailConfirmationToken extends Token {

    /**
     * @var VssUserInterface
     */
    protected $user;

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
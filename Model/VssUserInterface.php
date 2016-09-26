<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 04/06/16
 * Time: 15:45
 */

namespace Vss\UserBundle\Model;

use FOS\UserBundle\Model\UserInterface;

/**
 * Interface VssUserInterface
 * @package Vss\UserBundle\Model
 */
interface VssUserInterface extends UserInterface {

    /**
     * @return mixed
     */
    public function isEmailConfirmed();

    /**
     * @param boolean $emailConfirmed
     * @return null
     */
    public function setEmailConfirmed($emailConfirmed);

}
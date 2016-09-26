<?php
/**
 * Created by PhpStorm.
 * User: nathan
 * Date: 04/06/16
 * Time: 15:46
 */

namespace Vss\UserBundle\Model;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Vss\UsefulBundle\Utils\Email;
use Vss\UsefulBundle\Utils\Opts;
use Vss\UsefulBundle\Utils\Tokenizer;
use Vss\UserBundle\Entity\ChangeEmailToken;
use Vss\UserBundle\Entity\ChangePasswordToken;
use Vss\UserBundle\Entity\EmailConfirmationToken;
use Vss\UserBundle\Entity\Token;
use Vss\UserBundle\Model\Exception\InvalidEmailException;
use Vss\UserBundle\Model\Exception\TokenExpiredException;
use Vss\UserBundle\Model\Exception\TokenNotExistsException;
use Vss\UserBundle\Model\Exception\TokenUsedException;

/**
 * Class UserManager
 * @package Vss\UserBundle\Model
 */
class UserManager {

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $config;

    /**
     * UserManager constructor.
     * @param array $config
     * @param EntityManagerInterface $em
     */
    public function __construct(array $config, EntityManagerInterface $em) {
        $this->em = $em;
        $this->config = $config;
    }

    /**
     * @param $class
     * @param VssUserInterface $user
     * @param array $opts
     * @return Token
     */
    public function createUserToken($class, VssUserInterface $user, array $opts = []) {
        $rep = $this->em->getRepository($class);

        /** @var EmailConfirmationToken[] $existings */
        $existings = $rep->findBy(['user' => $user]);
        foreach ($existings as $existing) {
            $existing->setUsed(true);
            $this->em->persist($existing);
        }
        $this->em->flush();

        /** @var Token $token */
        $token = new $class();

        $tok = Tokenizer::random(32);
        while ($rep->findOneBy(['token' => $tok])) {
            $tok = Tokenizer::random(32);
        }

        $token->setToken($tok);
        $token->setCreatedAt(new \DateTime());
        $token->setUser($user);

        $expiresAt = new \DateTime();
        $durability = 3600 * 2;
        if ($value = Opts::is($opts, 'durability')) {
            $durability = $value;
        }
        $expiresAt->add(new \DateInterval('PT' . $durability . 'S'));
        $token->setExpiresAt($expiresAt);

        $this->em->persist($token);
        $this->em->flush();

        return $token;
    }

    /**
     * @param Token $token
     * @throws TokenExpiredException
     * @throws TokenUsedException
     */
    public function checkToken(Token $token) {

        if ($token->getExpiresAt()->getTimestamp() < time()) {
            throw new TokenExpiredException();
        }

        if ($token->isUsed()) {
            throw new TokenUsedException();
        }
    }

    /**
     * @param VssUserInterface $user
     * @param array $opts
     * @return EmailConfirmationToken
     */
    public function createEmailConfirmationToken(VssUserInterface $user, array $opts = []) {
        $class = $this->config['entity']['email_confirmation_token'];
        $token = $this->createUserToken($class, $user, $opts);
        return $token;
    }

    /**
     * @param $token
     * @throws TokenExpiredException
     * @throws TokenNotExistsException
     * @throws TokenUsedException
     */
    public function checkEmailConfirmationToken($token) {

        $class = $this->config['entity']['email_confirmation_token'];
        $rep = $this->em->getRepository($class);
        /** @var EmailConfirmationToken $entity */
        $entity = $rep->findOneBy(['token' => $token]);
        if (!$entity) {
            throw new TokenNotExistsException();
        }
        $this->checkToken($entity);

        $entity->setUsed(true);
        $entity->getUser()->setEmailConfirmed(true);

        $this->em->persist($entity);
        $this->em->persist($entity->getUser());
        $this->em->flush();
    }

    /**
     * @param VssUserInterface $user
     * @param array $opts
     * @return ChangePasswordToken
     */
    public function createChangePasswordToken(VssUserInterface $user, array $opts = []) {
        $class = $this->config['entity']['change_password_token'];
        $token = $this->createUserToken($class, $user, $opts);
        return $token;
    }

    /**
     * @param $token
     * @return ChangePasswordToken
     * @throws TokenExpiredException
     * @throws TokenNotExistsException
     * @throws TokenUsedException
     */
    public function checkChangePasswordToken($token) {
        $class = $this->config['entity']['change_password_token'];
        $rep = $this->em->getRepository($class);
        /** @var ChangePasswordToken $entity */
        $entity = $rep->findOneBy(['token' => $token]);
        if (!$entity) {
            throw new TokenNotExistsException();
        }
        $this->checkToken($entity);

        $entity->setUsed(true);
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }


    /**
     * @param VssUserInterface $user
     * @param $email
     * @param array $opts
     * @return ChangeEmailToken
     * @throws InvalidEmailException
     */
    public function createChangeEmailToken(VssUserInterface $user, $email, array $opts = []) {
        if (!Email::isEmailValid($email)) {
            throw new InvalidEmailException();
        }
        $class = $this->config['entity']['change_email_token'];
        /** @var ChangeEmailToken $token */
        $token = $this->createUserToken($class, $user, $opts);

        $token->setNewEmail($email);
        $this->em->persist($token);
        $this->em->flush();
        return $token;
    }

    /**
     * @param $token
     * @return ChangeEmailToken
     * @throws TokenExpiredException
     * @throws TokenNotExistsException
     * @throws TokenUsedException
     */
    public function checkChangeEmailToken($token) {
        $class = $this->config['entity']['change_email_token'];
        $rep = $this->em->getRepository($class);
        /** @var ChangeEmailToken $entity */
        $entity = $rep->findOneBy(['token' => $token]);
        if (!$entity) {
            throw new TokenNotExistsException();
        }
        $this->checkToken($entity);

        $entity->setUsed(true);
        $entity->getUser()->setEmail($entity->getNewEmail());
        $entity->getUser()->setEmailCanonical($entity->getNewEmail());

        $this->em->persist($entity->getUser());
        $this->em->persist($entity);
        $this->em->flush();
        return $entity;
    }
}
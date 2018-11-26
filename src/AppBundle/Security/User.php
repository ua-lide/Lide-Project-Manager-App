<?php
/**
 * Created by PhpStorm.
 * User: etudiant
 * Date: 26/11/18
 * Time: 11:10
 */

namespace AppBundle\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

final class User implements JWTUserInterface
{
    // Your own logic

    private $roles;
    private $username;

    public function __construct($username, array $roles)
    {
        $this->username = $username;
        $this->roles = $roles;
    }

    public static function createFromPayload($username, array $payload)
    {
        return new self(
            $username,
            $payload['roles'] // Added by default
        );
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
    }
}
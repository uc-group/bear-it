<?php

namespace App\Entity;

use App\User\Model\User\Avatar;
use App\Utils\Id;
use BearIt\User\Model\UserId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="bi_user")
 */
class User implements UserInterface
{
    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(type="string", length=36)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=80)
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=1000, nullable=true)
     */
    private $avatar;

    /**
     * @var int
     * @ORM\Column(type="integer", options={"unsigned": true})
     */
    private $githubId;

    /**
     * @param string $username
     * @param string $name
     */
    public function __construct(string $username, string $name = null)
    {
        $this->id = Id::generateUuid();
        $this->username = $username;
        $this->name = $name;
    }

    /**
     * @param array $githubResponse
     * @return User
     */
    public static function createGithubUser(array $githubResponse): User
    {
        $user = new self($githubResponse['login'] ?? null, $githubResponse['name'] ?? null);
        $user->githubId = $githubResponse['id'] ?? null;
        if (!empty($githubResponse['avatar_url'] ?? '')) {
            $user->avatar = $githubResponse['avatar_url'];
        } else if (!empty($githubResponse['gravatar_id'] ?? '')) {
            $user->avatar = Avatar::createRetroGravatar($githubResponse['gravatar_id'])->toString();
        }

        return $user;
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
    {
        return UserId::fromString($this->id);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name ?? $this->username;
    }

    /**
     * @return string
     */
    public function getAvatar(): string
    {
        if (!$this->avatar || !preg_match('~^https?://~', $this->avatar)) {
            return Avatar::createRetroGravatar(md5($this->username))->toString();
        }

        return $this->avatar ?? Avatar::createRetroGravatar(md5($this->username))->toString();
    }

    /**
     * @return int
     */
    public function getGithubId(): ?int
    {
        return $this->githubId;
    }

    /**
     * @return array
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @return string|null
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     * @deprecated
     */
    public function getUsername()
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        // No credentials \o/
    }
}

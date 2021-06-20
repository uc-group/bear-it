<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\User\Model\User\Avatar;
use App\Utils\Id;
use BearIt\User\Model\UserId;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'bi_user')]
class User implements UserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 80)]
    private string $username;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private ?string $name;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $avatar;

    #[ORM\Column(type: 'integer', nullable: true, options: ['unsigned' => true])]
    private ?int $githubId;

    public function __construct(string $username, string $name = null)
    {
        $this->id = Id::generateUuid();
        $this->username = $username;
        $this->name = $name;
    }

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

    public function getId(): UserId
    {
        return UserId::fromString($this->id);
    }

    public function getName(): string
    {
        return $this->name ?? $this->username;
    }

    public function getAvatar(): string
    {
        if (!$this->avatar || !preg_match('~^https?://~', $this->avatar)) {
            return Avatar::createRetroGravatar(md5($this->username))->toString();
        }

        return $this->avatar ?? Avatar::createRetroGravatar(md5($this->username))->toString();
    }

    public function getGithubId(): ?int
    {
        return $this->githubId;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @deprecated
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        // No credentials \o/
    }
}

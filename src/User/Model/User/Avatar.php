<?php

namespace App\User\Model\User;

use App\User\Exception\InvalidAvatarDefaultException;

class Avatar
{
    const DEFAULT_404 = '404';
    const DEFAULT_BLANK = 'blank';
    const DEFAULT_RANDOM = '@random';

    const DEFAULT_MP = 'mp';
    const DEFAULT_IDENTICON = 'identicon';
    const DEFAULT_MONSTERID = 'monsterid';
    const DEFAULT_WAVATAR = 'wavatar';
    const DEFAULT_RETRO = 'retro';
    const DEFAULT_ROBOHASH = 'robohash';

    const AVAILABLE_DEFAULTS = [
        self::DEFAULT_IDENTICON,
        self::DEFAULT_MONSTERID,
        self::DEFAULT_WAVATAR,
        self::DEFAULT_RETRO,
        self::DEFAULT_ROBOHASH,
        self::DEFAULT_MP
    ];

    const GRAVATAR_BASE_URL = 'https://www.gravatar.com/avatar';

    /**
     * @var string
     */
    private $url;

    /**
     * Avatar constructor.
     * @param string $url
     */
    private function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @param string $url
     * @return self
     */
    public static function fromString(string $url): self
    {
        return new self($url);
    }

    /**
     * @param string $hash
     * @param string|null $default
     * @return self
     * @throws InvalidAvatarDefaultException
     */
    public static function createGravatar(string $hash, string $default = null): self
    {
        if ($default === self::DEFAULT_RANDOM) {
            $default = array_rand(self::AVAILABLE_DEFAULTS);
        }

        if (!$default) {
            return new self(sprintf('%s/%s', self::GRAVATAR_BASE_URL, $hash));
        }

        if (
            !in_array($default, self::AVAILABLE_DEFAULTS) ||
            !in_array($default, [self::DEFAULT_BLANK, self::DEFAULT_404]) ||
            !preg_match('~^https?://~', $default)
        ) {
            throw new InvalidAvatarDefaultException($default);
        }

        return new self(sprintf('%s/%s?d=%s', self::GRAVATAR_BASE_URL, $hash, $default));
    }

    /**
     * @param string $hash
     * @return Avatar
     */
    public static function createRetroGravatar(string $hash)
    {
        return new self(sprintf('%s/%s?d=%s', self::GRAVATAR_BASE_URL, $hash, self::DEFAULT_RETRO));
    }

    /**
     * @param string $hash
     * @return Avatar
     */
    public static function createIdenticonGravatar(string $hash)
    {
        return new self(sprintf('%s/%s?d=%s', self::GRAVATAR_BASE_URL, $hash, self::DEFAULT_IDENTICON));
    }

    /**
     * @param string $hash
     * @return Avatar
     */
    public static function createMpGravatar(string $hash)
    {
        return new self(sprintf('%s/%s?d=%s', self::GRAVATAR_BASE_URL, $hash, self::DEFAULT_MP));
    }

    /**
     * @param string $hash
     * @return Avatar
     */
    public static function createRobohashGravatar(string $hash)
    {
        return new self(sprintf('%s/%s?d=%s', self::GRAVATAR_BASE_URL, $hash, self::DEFAULT_ROBOHASH));
    }

    /**
     * @param string $hash
     * @return Avatar
     */
    public static function createWavatarGravatar(string $hash)
    {
        return new self(sprintf('%s/%s?d=%s', self::GRAVATAR_BASE_URL, $hash, self::DEFAULT_WAVATAR));
    }

    /**
     * @param string $hash
     * @return Avatar
     */
    public static function createMonsteridGravatar(string $hash)
    {
        return new self(sprintf('%s/%s?d=%s', self::GRAVATAR_BASE_URL, $hash, self::DEFAULT_MONSTERID));
    }

    /**
     * @param string $hash
     * @return Avatar
     */
    public static function createRandomGravatar(string $hash)
    {
        try {
            return self::createGravatar($hash, self::DEFAULT_RANDOM);
        } catch (InvalidAvatarDefaultException $exception) {
            return self::createRetroGravatar($hash);
        }
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->url;
    }
}

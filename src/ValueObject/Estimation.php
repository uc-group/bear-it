<?php


namespace App\ValueObject;


class Estimation
{
    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $unit;

    /**
     * Estimation constructor.
     * @param string $value
     * @param string $unit
     */
    private function __construct(string $value, string $unit)
    {
        $this->value = $value;
        $this->unit = $unit;
    }

    /**
     * @param string $value
     * @param string $unit
     * @return Estimation
     */
    public static function create(string $value, string $unit): self
    {
        return new self($value, $unit);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getUnit(): string
    {
        return $this->unit;
    }
}
<?php

namespace App\Utils;

class KeyPrioritizedCollection
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var bool
     */
    private $sortKeys = true;

    /**
     * @var string
     */
    private $key;

    /**
     * PrioritizedCollection constructor.
     * @param string $key
     * @param array|null $order
     */
    public function __construct(string $key, $order = null)
    {
        $this->key = $key;
        if (is_array($order)) {
            $this->sortKeys = false;
            $this->data = array_fill_keys($order, []);
        }
    }

    public function add(array $element)
    {
        $this->data[$element[$this->key] ?? ''][] = $element;
    }

    public function toArray()
    {
        $result = [];
        foreach ($this->prepareCopy() as $elements) {
            foreach ($elements as $element) {
                $result[] = $element;
            }
        }

        return $result;
    }

    /**
     * @param string $key
     * @return array
     */
    public function toSortedArray(string $key)
    {
        $result = [];
        foreach ($this->prepareCopy() as $elements) {
            usort($elements, function ($a, $b) use ($key) {
                if (($a[$key] ?? null) == ($b[$key] ?? null)) {
                    return 0;
                }

                return ($a[$key] ?? null) > ($b[$key] ?? null) ? 1 : 0;
            });
            foreach ($elements as $element) {
                $result[] = $element;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    private function prepareCopy(): array
    {
        if ($this->sortKeys) {
            $copy = $this->data;
            ksort($copy);

            return $copy;
        }

        return $this->data;
    }
}

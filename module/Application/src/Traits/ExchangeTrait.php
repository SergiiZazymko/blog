<?php
/**
 * Created by PhpStorm.
 * User: sergii
 * Date: 16.06.19
 * Time: 22:03
 */

namespace Application\Traits;

/**
 * Trait ExchangeTrait
 * @package Application\Traits
 */
trait ExchangeTrait
{
    /**
     * @param $data
     */
    public function exchangeArray($data)
    {
        /**
         * @var string $key
         * @var string $value
         */
        foreach ($data as $key => $value) {
            if (property_exists(self::class, $key)) {
                $this->$key = $value;
            }
        }
    }

    /**
     *
     */
    public function getArrayCopy()
    {
        /** @var array $data */
        $data = [];

        /**
         * @var string $key
         * @var string $value
         */
        foreach ($this as $key => $value) {
            $data[$key] = $value;
        }
    }
}
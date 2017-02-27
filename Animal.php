<?php

/**
 * Base class for animals
 * @property-read string $name Name of the animal
 * @property-read string $birthdate Birth date of the animal
 * @property-read string $type Animal type
 */
abstract class Animal implements JsonSerializable {
    /**
     * Name of the animal
     * @var string
     */
    protected $name;
    
    /**
     * Birth date of the animal
     * @var string
     */
    protected $birthdate;
    
    /**
     * Animal type. Must be set in subclasses
     * @var string
     */
    protected $type = 'unknown';

    /**
     * Animal constructor
     * @param string $name
     * @param string $birthdate
     * @throws Exception
     */
    public function __construct(string $name, string $birthdate) {
        if (empty($name) || strlen($name) < 3) {
            throw new Exception('Incorrect animal name!');
        }
        $this->name = $name;
        $this->birthdate = date('Y-m-d', strtotime($birthdate));
    }
    
    /**
     * Getter for protected properties
     * @param string $name
     * @return string
     * @throws Exception
     */
    public function __get($name) {
        if (!isset($this->$name)) {
            throw new Exception("Property '$name' does not exist");
        }
        return $this->$name;
    }
    
    /**
     * @return array
     */
    public function jsonSerialize() {
        return [
            'class' => get_called_class(),
            'properties' => [
                'name' => $this->name,
                'birthdate' => $this->birthdate
            ]
        ];
    }
}

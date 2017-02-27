<?php

/**
 * Animal shelter
 */
class Shelter {
    /**
     * @var Animal[]
     */
    protected $animals = [];
    
    /**
     * Storage filename
     * @var string
     */
    protected $storage = 'shelter.json';

    /**
     * Places new animal to shelter
     * @param Animal $animal
     * @return $this
     */
    public function place(Animal $animal) {
        $this->animals[] = $animal;
        usort($this->animals, function(Animal $a, Animal $b) {
            if ($a->name == $b->name) {
                return 0;
            }
            return $a->name < $b->name ? -1 : 1;
        });
        return $this;
    }
    
    /**
     * Returns list of animals in shelter
     * @return Animal[]
     */
    public function getList() {
        return $this->animals;
    }
    
    /**
     * Transfers oldest animal of any or defined type from the shelter
     * @param string $type
     * @return Animal|null
     */
    public function transfer($type = null) {
        $oldestKey = null;
        $maxAge = 0;
        $now = strtotime('now');
        foreach ($this->animals as $key => $animal) {
            if ($type !== null && $animal->type !== $type) {
                continue;
            }
            $age = $now - strtotime($animal->birthdate);
            if ($age > $maxAge) {
                $maxAge = $age;
                $oldestKey = $key;
            }
        }
        if ($oldestKey === null) {
            return null;
        }
        $oldest = $this->animals[$oldestKey];
        unset($this->animals[$oldestKey]);
        return $oldest;
    }
    
    /**
     * Load animals from storage file
     * @return boolean
     */
    public function load() {
        if (!file_exists($this->storage)) {
            return false;
        }
        $animals = json_decode(file_get_contents($this->storage), true);
        foreach ($animals as $animal) {
            $class = new ReflectionClass($animal['class']);
            $instance = $class->newInstanceArgs($animal['properties']);
            $this->place($instance);
        }
        return true;
    }
    
    /**
     * Saves animals to storage file
     * @return $this
     */
    public function save() {
        file_put_contents($this->storage, json_encode($this->animals, JSON_PRETTY_PRINT));
        return $this;
    }
}

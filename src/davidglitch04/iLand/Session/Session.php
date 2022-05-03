<?php

namespace davidglitch04\iLand\Session;

use pocketmine\world\Position;

class Session
{
    private array $data = [];

    public function __construct(string $username)
    {
        $this->data['Username'] = $username;
        $this->data['A'] = null;
        $this->data['B'] = null;
    }

    public function setPositionA(Position $A): void
    {
        $this->data['A'] = $A;
    }

    public function setPositionB(Position $B): void
    {
        $this->data['B'] = $B;
    }
	
	public function getPositionA(): float
    {
        return $this->data['A'];
    }
	
	public function getPositionB(): float
    {
        return $this->data['B'];
    }

    public function isNull(string $location): bool{
        if(is_null($this->data[$location])){
            return true;
        } else{
            return false;
        }
    }
}

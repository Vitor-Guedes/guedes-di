<?php

namespace GuedesDI\Tests\Objects;

class User
{
    protected $account;

    public function __construct(Account $account)
    {
        $this->account = $account;    
    }

    public function getAccount()
    {
        return $this->account;
    }
}
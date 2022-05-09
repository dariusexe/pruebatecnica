<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Auth;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $loggedInUser;
    protected $user;
    protected $headers;

    public function setUp() : void
    {
        parent::setUp();
        $users = User::factory()->count(2)->create();
        $this->loggedInUser = $users[0];
        $this->user = $users[1];
        $this->headers = [
            'Authorization' => 'Bearer ' . Auth::attempt(['email' => $this->loggedInUser->email, 'password' => 'password'])
        ];
    }
}

<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected User $currentUser;

    protected function signIn(string $isAdmin = 'admin'): void
    {
        $this->currentUser = User::factory()
            ->createOne(['role' => 'admin']);

        Sanctum::actingAs($this->currentUser);
    }

    protected function currentUserId(): int
    {
        return $this->currentUser->id;
    }
    protected function takePostId($message): int
    {
        preg_match('/id:(\d+)/', $message, $matches);

        if (isset($matches)) {
            return ((int) $matches[1]);
        } else {
            dd('$id не найден');
        }
    }
}

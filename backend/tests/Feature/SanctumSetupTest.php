<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class SanctumSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_sanctum_access_token(): void
    {
        $user = User::factory()->create();

        $plainTextToken = $user->createToken('test-token')->plainTextToken;
        [$tokenId] = explode('|', $plainTextToken, 2);

        $token = PersonalAccessToken::findToken($plainTextToken);

        $this->assertNotNull($token);
        $this->assertSame((int) $tokenId, $token->id);
        $this->assertTrue($token->tokenable->is($user));
    }
}

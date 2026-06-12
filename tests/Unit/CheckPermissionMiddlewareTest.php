<?php

namespace Tests\Unit;

use App\Http\Middleware\CheckPermission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class CheckPermissionMiddlewareTest extends TestCase
{
    public function test_it_uses_sanctum_user_when_request_user_is_missing(): void
    {
        $user = new User();
        $user->role_id = Role::ADMIN;

        $request = Request::create('/api/real-logistics-invoices/list', 'GET');

        $guard = Mockery::mock();
        $guard->shouldReceive('user')->once()->andReturn($user);

        Auth::shouldReceive('guard')
            ->once()
            ->with('sanctum')
            ->andReturn($guard);

        $middleware = new CheckPermission();
        $response = $middleware->handle(
            $request,
            static fn () => response()->json(['ok' => true]),
            'list',
            'logistics'
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['ok' => true], $response->getData(true));
    }
}

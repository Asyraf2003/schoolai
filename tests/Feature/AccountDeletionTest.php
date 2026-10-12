<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('allows admin to delete teachers and students while retaining security audit evidence', function (string $role): void {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $account = User::factory()->create(['role' => $role]);
    $this->actingAs($admin)->delete(route('admin.accounts.destroy', $account))->assertRedirect(route('admin.accounts.index'));
    $this->assertDatabaseMissing('users', ['id' => $account->id]);
    $this->assertDatabaseHas('security_audit_logs', ['event' => 'account.deleted', 'auditable_id' => (string) $account->id]);
})->with([User::ROLE_GURU, User::ROLE_MURID]);

it('refuses deleting any admin including the current admin', function (): void {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $another = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $this->actingAs($admin)->delete(route('admin.accounts.destroy', $admin))->assertForbidden();
    $this->delete(route('admin.accounts.destroy', $another))->assertForbidden();
    expect(User::count())->toBe(2);
});

it('refuses account deletion to other roles', function (string $role): void {
    $account = User::factory()->create(['role' => User::ROLE_MURID]);
    $this->actingAs(User::factory()->create(['role' => $role]))->delete(route('admin.accounts.destroy', $account))->assertForbidden();
    $this->assertDatabaseHas('users', ['id' => $account->id]);
})->with([User::ROLE_GURU, User::ROLE_MURID]);

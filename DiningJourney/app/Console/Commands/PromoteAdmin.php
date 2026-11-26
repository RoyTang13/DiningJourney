<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('admin:promote {email : Email address of an existing account}')]
#[Description('Grant administrator access to an existing user account')]
class PromoteAdmin extends Command
{
    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->components->error('No account exists with that email address.');

            return self::FAILURE;
        }

        if ($user->isAdmin()) {
            $this->components->info("{$user->email} is already an administrator.");

            return self::SUCCESS;
        }

        $user->forceFill(['is_admin' => true])->save();
        $this->components->info("Administrator access granted to {$user->email}.");

        return self::SUCCESS;
    }
}

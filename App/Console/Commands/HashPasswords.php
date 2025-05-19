<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Human;
use Illuminate\Support\Facades\Hash;

class HashPasswords extends Command
{
    protected $signature = 'passwords:hash';

    protected $description = 'Hash all plain passwords in humans table';

    public function handle()
    {
        $humans = Human::all();

        foreach ($humans as $human) {
            if (strlen($human->password) < 60) {
                $human->password = Hash::make($human->password);
                $human->save();
                $this->info("Hashed password for user ID {$human->id}");
            }
        }

        $this->info('Done hashing passwords.');
    }
}

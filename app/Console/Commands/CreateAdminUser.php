<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
                            {--name= : The name of the admin user}
                            {--email= : The email of the admin user}
                            {--password= : The password for the admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for the application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('==============================================');
        $this->info('   Create Admin User - UrbanGreen');
        $this->info('==============================================');
        $this->newLine();

        // Get user input
        $name = $this->option('name') ?? $this->ask('Enter admin name');
        $email = $this->option('email') ?? $this->ask('Enter admin email');
        $password = $this->option('password') ?? $this->secret('Enter admin password');

        // Validate input
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('  - ' . $error);
            }
            return 1;
        }

        // Create admin user
        try {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
                'last_login_at' => now(),
            ]);

            $this->newLine();
            $this->info('✓ Admin user created successfully!');
            $this->newLine();
            $this->table(
                ['Field', 'Value'],
                [
                    ['ID', $user->id],
                    ['Name', $user->name],
                    ['Email', $user->email],
                    ['Role', $user->role],
                    ['Created', $user->created_at->format('Y-m-d H:i:s')],
                ]
            );
            $this->newLine();
            $this->info('You can now login with these credentials at: /login');
            $this->info('You will be redirected to the admin dashboard at: /admin/dashboard');
            $this->newLine();

            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to create admin user: ' . $e->getMessage());
            return 1;
        }
    }
}

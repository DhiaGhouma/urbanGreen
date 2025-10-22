<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Participation;
use App\Mail\ParticipationCompleted;
use Illuminate\Support\Facades\Mail;

class TestParticipationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test-participation {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the participation completed email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Participation Completed Email...');
        $this->newLine();

        // Get first participation with completed status
        $participation = Participation::with(['user', 'greenSpace'])
            ->where('statut', 'terminee')
            ->first();

        if (!$participation) {
            $this->warn('⚠️  No participation with status "terminee" found.');
            $this->info('Creating a test scenario with first participation...');
            
            $participation = Participation::with(['user', 'greenSpace'])->first();
            
            if (!$participation) {
                $this->error('❌ No participations found in database!');
                return 1;
            }
        }

        $this->info('📧 Participation Details:');
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $participation->id],
                ['User', $participation->user->name],
                ['Email', $participation->user->email],
                ['Green Space', $participation->greenSpace->name],
                ['Date', $participation->date->format('d/m/Y')],
                ['Status', $participation->statut],
            ]
        );

        $this->newLine();

        // Get email to send to
        $email = $this->argument('email') ?? $participation->user->email;

        $this->info("📤 Sending email to: {$email}");
        $this->newLine();

        try {
            Mail::to($email)->send(new ParticipationCompleted($participation));
            
            $this->info('✅ Email sent successfully!');
            $this->newLine();
            
            $mailer = config('mail.default');
            
            if ($mailer === 'log') {
                $this->warn('📝 Mail is configured to use LOG driver.');
                $this->info('   Check: storage/logs/laravel.log');
            } else {
                $this->info('📬 Check your inbox: ' . $email);
                $this->warn('   Don\'t forget to check SPAM folder!');
            }
            
            $this->newLine();
            $this->info('🎉 Test completed successfully!');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            
            $this->warn('💡 Troubleshooting:');
            $this->line('1. Check your .env file email configuration');
            $this->line('2. Make sure MAIL_USERNAME and MAIL_PASSWORD are correct');
            $this->line('3. For Gmail, use App Password (not regular password)');
            $this->line('4. Run: php artisan config:clear');
            
            return 1;
        }
    }
}

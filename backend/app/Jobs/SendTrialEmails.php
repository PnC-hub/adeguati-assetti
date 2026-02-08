<?php

namespace App\Jobs;

use App\Mail\TrialReminder;
use App\Mail\WelcomeTrial;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendTrialEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Get all users in trial with trial_ends_at set
        $trialUsers = DB::table('aa_users')
            ->where('piano', 'trial')
            ->whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '>=', now())
            ->get();

        foreach ($trialUsers as $user) {
            $trialEnd = Carbon::parse($user->trial_ends_at);
            $daysLeft = (int) now()->diffInDays($trialEnd, false);
            $daysSinceRegistration = (int) Carbon::parse($user->created_at)->diffInDays(now());

            // Check which email to send based on days
            $emailToSend = $this->determineEmail($daysSinceRegistration, $daysLeft);
            
            if ($emailToSend && !$this->alreadySent($user->id, $emailToSend)) {
                $this->sendEmail($user, $emailToSend, $daysLeft);
            }
        }
    }

    private function determineEmail(int $daysSinceRegistration, int $daysLeft): ?string
    {
        // Day 0: Welcome (handled in registration)
        // Day 3: First reminder
        if ($daysSinceRegistration == 3) return 'day3';
        // Day 7: Case study
        if ($daysSinceRegistration == 7) return 'day7';
        // Day 10: 4 days left
        if ($daysLeft == 4) return 'day10';
        // Day 13: Last day
        if ($daysLeft == 1) return 'day13';

        return null;
    }

    private function alreadySent(int $userId, string $emailType): bool
    {
        return DB::table('aa_email_logs')
            ->where('user_id', $userId)
            ->where('email_type', $emailType)
            ->exists();
    }

    private function sendEmail($user, string $emailType, int $daysLeft): void
    {
        try {
            Mail::to($user->email)->send(new TrialReminder($user, $daysLeft, $emailType));
            
            // Log the sent email
            DB::table('aa_email_logs')->insert([
                'user_id' => $user->id,
                'email_type' => $emailType,
                'sent_at' => now(),
                'status' => 'sent',
            ]);

            Log::info('Trial email sent', ['user_id' => $user->id, 'type' => $emailType]);
        } catch (\Exception $e) {
            Log::error('Failed to send trial email', [
                'user_id' => $user->id,
                'type' => $emailType,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Mail\ReminderMail;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Command\Command as CommandAlias;

class ReminderSenderCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'reminders:sendmail';

    /**
     * The console command description.
     */
    protected $description = 'Send reminders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('Starting sending reminders task.');

        $reminders = Reminder::whereDate('start_date', '=', Carbon::now())
            ->whereDate('end_date', '<=', Carbon::now())
            ->where('is_sent', false);

        if ($reminders->count() === 0) {
            $this->warn('No reminder(s) to send.');
            return CommandAlias::SUCCESS;
        }

        $sentCount = 0;
        foreach ($reminders->get() as $reminder) {
            try {
                Mail::to($reminder->user->email)->send(new ReminderMail($reminder));
                $this->info(sprintf('Mail sent to %s', $reminder->user->email));
                $sentCount++;
            } catch (\Exception $e) {
                Log::error(sprintf('[COMMAND] Reminder send failed for user %s | Error : %s | Trace : %s',
                    $reminder->user->email,
                    $e->getMessage(),
                    $e->getTraceAsString()
                ));

                return CommandAlias::FAILURE;
            }
        }

        $this->info(sprintf(
            'Finished sending reminders task, (%s) sent',
            $sentCount,
        ));

        return CommandAlias::SUCCESS;
    }
}

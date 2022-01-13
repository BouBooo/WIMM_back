<?php

namespace App\Console\Commands;

use App\Mail\ReminderMail;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ReminderSenderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
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

        foreach ($reminders->get() as $reminder) {
            try {
                Mail::to($reminder->user->email)->send(new ReminderMail($reminder));
                // TODO: Faire l'update en se greffant sur l'event post envoi de message avec succès
                $reminder->update([
                    'is_sent' => true
                ]);
                $this->info(sprintf('Mail sent to %s', $reminder->user->email));
            } catch(\Exception $e) {
                Log::error(sprintf('[COMMAND] Reminder send failed for user %s | Error : %s | Trace : %s',
                    $reminder->user->email,
                    $e->getMessage(),
                    $e->getTraceAsString()
                ));


            }
        }

        if ($reminders->count() === 0) {
            $this->warn('No reminder(s) to send.');
        }

        $this->info('Finished sending reminders task.');
        return 0;
    }
}

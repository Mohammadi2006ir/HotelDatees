<?php

namespace App\Console\Commands;

use App\Mail\ReserveRememberMail;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReservationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:reservation-reminders';
    protected $description = 'Send reservation reminders to users';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $now = Carbon::now();
        $twoDaysLater = $now->copy()->addHours(48); // Use copy() to avoid modifying $now

        // Get reservations that are within the next 48 hours
        $reservations = Reservation::where('check_in_date', '>=', $now->toDateTimeString())
            ->where('check_in_date', '<=', $twoDaysLater->toDateTimeString())
            ->get();

        foreach ($reservations as $reservation) {
            if ($reservation->user && $reservation->user->email) {
                Mail::to($reservation->user->email)->send(new ReserveRememberMail($reservation));
            } else {
                $this->error('No valid email for reservation ID: ' . $reservation->id);
            }
        }


        $this->info('Reservation reminders sent successfully!');
    }
}

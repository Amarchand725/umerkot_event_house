<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Modules\Event\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;

class DeveloperController extends Controller
{
    public function userCredentials(){
        $users = User::where('email', '!=', 'admin@gmail.com')->get();
        $userCredentials = [];
        foreach($users as $user){
            $plainPassword = Str::random(8); // generate random password
            $user->password = Hash::make($plainPassword); // generate random password
            $user->save();

            $userCredentials[] = [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $plainPassword
            ];
        }

        return $userCredentials;
    }

    public function sendTestEmail()
    {
        $testEmail = 'softwaredeveloper992@gmail.com';

        Mail::raw('This is a test email from Laravel.', function ($message) use ($testEmail) {
            $message->to($testEmail)
                    ->subject('Laravel Test Email');
        });

        return response()->json([
            'message' => 'Test email sent successfully.'
        ]);
    }

    public function invoice(){
        $event = Event::where('id', 5)->first();
        $event->load([
            'orders.invoice',
        ]);

        $pdf = Pdf::loadView(
            'back-office.events.partials.invoice-pdf',
            [
                'event' => $event,
            ]
        )->setPaper('a4', 'portrait');

        return $pdf->download(
            'invoice-' . $event->id . '.pdf'
        );
    }
}

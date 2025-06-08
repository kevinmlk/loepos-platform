<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class SupportController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required',
            'subject' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();

        $organization = $user->organization->name ?? 'Onbekende organisatie';
        $subject = $validated['subject'] ?? 'Ondersteuningsaanvraag';

        Mail::raw(
            "Organisatie: {$organization}\nNaam: {$user->name}\nE-mail: {$user->email}\n\nOnderwerp: {$subject}\n\nBericht:\n{$validated['message']}",
            function ($mail) use ($user, $organization, $subject) {
                $mail->to('info@loepos.be')
                     ->subject("Ondersteuning: {$subject}")
                     ->from($user->email, $user->name)
                     ->replyTo($user->email, $user->name);
            }
        );

        return back()->with('success', 'Uw bericht is succesvol verzonden. We nemen zo spoedig mogelijk contact met u op.');
    }
}

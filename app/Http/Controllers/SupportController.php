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
        ]);

        $user = auth()->user();

        $organization = $user->organization->name ?? 'Onbekende organisatie';

        Mail::raw(
            "Organisatie: {$organization}\nNaam: {$user->name}\nE-mail: {$user->email}\n\nBericht:\n{$validated['message']}",
            function ($mail) use ($user, $organization, $validated) {
                $mail->to('info@loepos.be')
                     ->subject('Ondersteuningsaanvraag');
            }
        );

        return back()->with('success', 'Je bericht is succesvol verzonden.');
    }
}

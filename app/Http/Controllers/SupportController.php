<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        Mail::raw(
            "Naam: {$validated['name']}\nE-mail: {$validated['email']}\n\nBericht:\n{$validated['message']}",
            function ($mail) use ($validated) {
                $mail->to('info@loepos.be')
                     ->subject('Ondersteuningsaanvraag');
            }
        );

        return back()->with('success', 'Je bericht is succesvol verzonden.');
    }
}

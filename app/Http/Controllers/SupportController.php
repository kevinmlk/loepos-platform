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

        try {
            // Log the support request for debugging
            \Log::info('Support request received', [
                'organization' => $organization,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'subject' => $subject,
                'message' => $validated['message'],
                'timestamp' => now()->toDateTimeString()
            ]);

            // Send the email
            Mail::raw(
                "Organisatie: {$organization}\nNaam: {$user->name}\nE-mail: {$user->email}\n\nOnderwerp: {$subject}\n\nBericht:\n{$validated['message']}",
                function ($mail) use ($user, $organization, $subject) {
                    $mail->to('info@loepos.be')
                         ->subject("Ondersteuning: {$subject}")
                         ->from($user->email, $user->name)
                         ->replyTo($user->email, $user->name);
                }
            );

            // Check if mail is using log driver
            if (config('mail.default') === 'log') {
                \Log::warning('Email is configured to use log driver. Email was logged but not actually sent.');
                return back()->with('success', 'Uw bericht is ontvangen en gelogd. Let op: E-mail is momenteel geconfigureerd voor logging, niet voor daadwerkelijke verzending.');
            }

            return back()->with('success', 'Uw bericht is succesvol verzonden. We nemen zo spoedig mogelijk contact met u op.');
            
        } catch (\Exception $e) {
            \Log::error('Failed to send support email', [
                'error' => $e->getMessage(),
                'user' => $user->email,
                'subject' => $subject
            ]);
            
            return back()->withErrors(['error' => 'Er is een fout opgetreden bij het verzenden van uw bericht. Probeer het later opnieuw of neem telefonisch contact op.']);
        }
    }
}

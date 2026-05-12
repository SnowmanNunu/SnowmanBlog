<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $ip = $request->ip();
        $key = 'subscribe:'.$ip;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()->with('error', __('Too many subscription attempts. Please try again later.'));
        }

        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        RateLimiter::hit($key, 3600);

        $email = $request->input('email');

        $subscriber = Subscriber::where('email', $email)->first();

        if ($subscriber) {
            if ($subscriber->isVerified()) {
                return back()->with('info', __('You are already subscribed.'));
            }
            $subscriber->update([
                'unsubscribe_token' => Str::random(32),
                'ip' => $ip,
                'verified_at' => now(),
            ]);
        } else {
            $subscriber = Subscriber::create([
                'email' => $email,
                'unsubscribe_token' => Str::random(32),
                'ip' => $ip,
                'verified_at' => now(),
            ]);
        }

        return back()->with('success', __('Thanks for subscribing! You will receive an email when new articles are published.'));
    }

    public function destroy(Request $request, string $token)
    {
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();

        if (! $subscriber) {
            return redirect()->route('blog.index')->with('error', __('Invalid unsubscribe link.'));
        }

        $subscriber->delete();

        return redirect()->route('blog.index')->with('success', __('You have been unsubscribed successfully.'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function store(Request )
    {
         = ->ip();
         = 'subscribe:' . ;

        if (RateLimiter::tooManyAttempts(, 3)) {
            return back()->with('error', __('Too many subscription attempts. Please try again later.'));
        }

        ->validate([
            'email' => 'required|email|max:255',
        ]);

        RateLimiter::hit(, 3600);

         = ->input('email');

         = Subscriber::where('email', )->first();

        if () {
            if (->isVerified()) {
                return back()->with('info', __('You are already subscribed.'));
            }
            ->update([
                'unsubscribe_token' => Str::random(32),
                'ip' => ,
                'verified_at' => now(),
            ]);
        } else {
             = Subscriber::create([
                'email' => ,
                'unsubscribe_token' => Str::random(32),
                'ip' => ,
                'verified_at' => now(),
            ]);
        }

        return back()->with('success', __('Thanks for subscribing! You will receive an email when new articles are published.'));
    }

    public function destroy(Request , string )
    {
         = Subscriber::where('unsubscribe_token', )->first();

        if (! ) {
            return redirect()->route('blog.index')->with('error', __('Invalid unsubscribe link.'));
        }

        ->delete();

        return redirect()->route('blog.index')->with('success', __('You have been unsubscribed successfully.'));
    }
}

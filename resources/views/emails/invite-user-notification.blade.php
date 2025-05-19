<x-mail::message>
    # You're Invited to Join {{ $businessName }}

    Hello {{ $user->name ?? 'there' }},

    {{ $inviterName }} has invited you to join the {!! $businessName !!} team on {{ config('app.name') }} as a
    member.

    To get started, please log in to your account, click on your Name on the bottom left, and navigate to
    Business. Then, select {!! $businessName !!} as your business.

    If you weren't expecting this invitation, feel free to ignore this email.

    Thanks,
    {{ config('app.name') }} Team
</x-mail::message>

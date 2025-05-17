<x-mail::message>
    # You're Invited to Join {{ $businessName }}

    Hello {{ $user->name ?? 'there' }},

    {{ $inviterName }} has invited you to join the **{{ $businessName }}** team on **{{ config('app.name') }}** as a
    member.

    To get started, please click the button below to set your password and activate your account.

    <x-mail::button :url="$url">
        Set Your Password
    </x-mail::button>

    If you weren't expecting this invitation, feel free to ignore this email.

    Thanks,<br>
    {{ config('app.name') }} Team
</x-mail::message>

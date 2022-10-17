@component('mail::message')

    # Med Manager

    <p>Dear {{ $user['name'] }},</p> <br>
    Your new account is all set to go! You can access it using the following credentials: <br><br>
    Domain Name: <a href="#">Med Manager</a> <br>
    Email: {{ $user['email'] }} <br>
    Password: {{ $user['password'] }} <br>

    <p>
        Please login to into the system<br>
    </p>

    <p>
        For any queries please contact Admin (ab.naser01@gmail.com).
    </p>

    <p>
        Regards,
        <br>
        Med Manager Authority
    </p>

    <p>
        Enter the below link to check <br>
        <a href="#">Click here >></a>
    </p>

    Thanks,<br>
    {{ config('app.name') }}

@endcomponent

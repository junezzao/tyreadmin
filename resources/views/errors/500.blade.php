<!DOCTYPE html>
<html>
    <head>
        <title>Error</title>
        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <!-- Tell the browser to be responsive to screen width -->
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <!-- Bootstrap 3.3.5 -->
        <link href="{{ asset('bootstrap/css/bootstrap.min.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
        <!-- Application Stylesheet -->
        <link href="{{ asset('css/app.css',env('HTTPS',false)) }}" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css',env('HTTPS',false)) }}">
    </head>
    <body>
        <section class="content">
            <div class="error-page">
                <h2 class="headline text-red">500</h2>
                <div class="error-content">
                    <h3><i class="fa fa-warning text-red"></i> Oops! Something went wrong.</h3>
                    <p>
                    Please report this issue by clicking <a href="mailto:{{ config('mail.tech_support.address') }}" alt="{{ config('mail.tech_support.name') }}">here</a> and we will work on fixing it right away (Please remember to include details about your session and how the error occured).
                    <br><br>
                    Meanwhile, you may <a href="{{ route('login') }}">return to the login page</a>.
                    </p>
                </div>
            </div>
        </section>
    </body>
</html>

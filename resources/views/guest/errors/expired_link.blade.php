<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Expired Link</title>

    {!! HTML::style('assets/css/bootstrap.min.css') !!}

    <style>
        body { padding-top: 100px; }
        h1 { font-size: 34px;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            body { padding-top: 50px; }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="row">
        <div class="col-md-6 col-md-push-3">
            <div class="text-center">
            {!! AssetsHelper::logo('error') !!}
                <h1>Link Expired</h1>
                <br />
                <p>
                    Hmmm... The link you followed has expired. Please contact {{ $company_name }} to resend another link.
                </p>
            </div>
        </div>
    </div>

</div>

</body>
</html>
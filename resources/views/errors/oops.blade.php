<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TokenMismatch</title>

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
                <h1>Server Error</h1>
                <br />
                <p>
                    <h4>OOPS!!!..............Upstream server error.</h4>
                </p>
            </div>
        </div>
    </div>

</div>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <title>{{ $invoice_details['campaign_name'] }} Invoice</title>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        .row {
            margin: 10px 0px 0px 0px !important;
            padding: 0px !important;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-XS-6">
            <h2>
                {{ $invoice_details['campaign_name'] }} Invoice
            </h2>
        </div>
    </div>
    <p><br></p>
    <div class="row">
        <div class="col-xs-4">
            <div class="media">
                <div class="media-left">
                    <h4><b>To: </b></h4>
                </div>
                <div class="media-body">
                    <h4 class="media-heading">{{ $invoice_details['client_name'] }}</h4>
                    <p>{{ $invoice_details['client_address'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-xs-4"></div>
        <div class="col-xs-4">
            <div class="media">
                <div class="media-left">
                    <h4><b>FROM: </b></h4>
                </div>
                <div class="media-body">
                    <h4 class="media-heading">{{ $invoice_details['client_name'] }}</h4>
                    <p>{{ $invoice_details['client_address'] }}</p>
                </div>
            </div>
        </div>
    </div>
    <p><br></p>
    <div class="row">
        <div class="col-xs-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">CampaignName</h5>
                    <p class="card-text">{{ $invoice_details['campaign_name'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Campaign Duration</h5>
                    <p class="card-text">{{ date('Y-m-d', strtotime($invoice_details['start_date'])) .' - '. date('Y-m-d', strtotime($invoice_details['end_date'])) }}</p>
                </div>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Invoice Number</h5>
                    <p class="card-text">{{ $invoice_details['invoice_number'] }}</p>
                </div>
            </div>
        </div>
        <div class="col-xs-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Invoice Date</h5>
                    <p class="card-text">{{ $invoice_details['invoice_date'] }}</p>
                </div>
            </div>
        </div>
    </div>
    <p><br></p>
    <div class="row">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Publishers</th>
                <th>Details</th>
                <th>MPO</th>
                <th>INV</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Gross</th>
                <th>V.Disc %</th>
                <th>V.Disc Amnt</th>
                <th>AG. Comm %</th>
                <th>AG. Comm Amnt</th>
                <th>Others</th>
                <th>VAT</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice_details['publishers_specific_details'] as $invoice_detail)
                <tr>
                    <td>{{ $invoice_detail['publisher'] }}</td>
                    <td>{{ $invoice_details['campaign_name'] }}</td>
                    <td></td>
                    <td></td>
                    <td>{{ $invoice_detail['quantity'] }}</td>
                    <td>{{ $invoice_detail['rate'] }}</td>
                    <td>{{ $invoice_detail['gross'] }}</td>
                    <td>{{ $invoice_detail['agency_percentage_discount'] }}</td>
                    <td>{{ $invoice_detail['agency_discount_value'] }}</td>
                    <td>{{ $invoice_detail['agency_commission_percentage'] }}</td>
                    <td>{{ $invoice_detail['agency_commission_value'] }}</td>
                    <td>{{ $invoice_detail['others'] }}</td>
                    <td>{{ $invoice_detail['vat'] }}</td>
                    <td>{{ $invoice_detail['total'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><b>Sub Totals:</b></td>
                <td>{{ $invoice_details['total_gross'] }}</td>
                <td></td>
                <td>{{ $invoice_details['total_value_amount'] }}</td>
                <td></td>
                <td>{{ $invoice_details['agency_commission_value'] }}</td>
                <td></td>
                <td>{{ $invoice_details['total_vat'] }}</td>
                <td>{{ number_format($invoice_details['total_net'],2) }}</td>
            </tr>
            <tr>
                <td><b>NET</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><b>NGN</b></td>
                <td><b>{{ number_format($invoice_details['total_net'],2) }}</b></td>
            </tr>
            </tbody>
        </table>
        <p>{{ ucfirst($invoice_details['total_net_word']).' Naira Only' }}</p>
        <span style="page-break-after:always;"></span>
    </div>
</div>
</body>
</html>



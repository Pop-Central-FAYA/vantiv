<!DOCTYPE html>
<html>
<head>
	<title>Export MPO PDF</title>
</head>
<body style="margin-top: 30px; padding: 10px;">

	<div class="file-header" style="width: 100%; text-align: center;">
        <h2 style="display : block"><b>{{ $data['company']->name }}</b></h2>
        <img src="{{ $data['company']->logo }}" style="width:100px; height:100px; display : inline-block;" alt=""><br>
		<h3>{{ $data['company']->address }}</h3>
	</div>
	<!-- SUB HEADER -->
	<div class="row" style="width: 100%; display: inline-block; border-bottom: 1px solid; padding: 10px 10px;box-shadow: 0 3px 1px -2px rgba(0,0,0,.2), 0 2px 2px 0 rgba(0,0,0,.14), 0 1px 5px 0 rgba(0,0,0,.12);">
		<div class="col" style="width: 70%; float: left;">
            <h4>The Advertising manager, <br> 
            @if($data['mpo_details']->vendor) 
                {{ $data['mpo_details']->vendor->name }}, <br>
                {{ $data['mpo_details']->vendor->street_address }}
            @else
                {{ $data['mpo_details']->publisher->long_name }},
            @endif<br> 
            </h4>
		</div>
		<div class="col" style="width: 30%; float: right; text-align: right; display: block;">
			<div style="clear: both;">
				<div style="float: left; margin-right: 5px; width: 140px;"><h4 style="margin: 4px 5px;">Mpo Number:</h4></div>
				<div style="float: left;"><h4 style="margin: 4px 5px;">{{ $data['mpo_details']->reference_number }}</h4></div>
			</div>
			<div style="clear: both;">
				<div style="float: left; margin-right: 5px; width: 140px;"><h4 style="margin: 4px 5px;">Client:</h4></div>
				<div style="float: left;"><h4 style="margin: 4px 5px;">{{ $data['mpo_details']->campaign->client->name }}</h4></div>
			</div>
			<div style="clear: both;">
				<div style="float: left; margin-right: 5px; width: 140px;"><h4 style="margin: 4px 5px;">Product:</h4></div>
				<div style="float: left;"><h4 style="margin: 4px 5px;">{{ $data['mpo_details']->campaign->product }}</h4></div>
			</div>
			<div style="clear: both;">
				<div style="float: left; margin-right: 5px; width: 140px;"><h4 style="margin: 4px 5px;">Campaign:</h4></div>
				<div style="float: left;"><h4 style="margin: 4px 5px;">{{ $data['mpo_details']->campaign->name }} </h4></div>
			</div>
			<div style="clear: both;">
				<div style="float: left; margin-right: 5px; width: 140px;"><h4 style="margin: 4px 5px;">Brand:</h4></div>
				<div style="float: left;"><h4 style="margin: 4px 5px;">{{ $data['mpo_details']->campaign->brand->name }}</h4></div>
			</div>
			<div style="clear: both;">
				<div style="float: left; margin-right: 5px; width: 140px;"><h4 style="margin: 4px 5px;">Order Date:</h4></div>
				<div style="float: left;"><h4 style="margin: 4px 5px;">{{ date('M d, Y', strtotime(now())) }}</h4></div>
			</div>
		</div>
	</div>
	<!-- SCHEDULES -->
	<div class="row schedules">
        <h4 style="text-align: center;">Schedule</h4>
        @foreach($data['time_belts'] as $time_belt)
            <div class="col">
                <h4 style="text-align: center;">PLEASE TRANSMIT ({{ $time_belt['duration'] }}SEC) SPOTS AS SCHEDULED BELOW</h4>
                <table border="1" style="width: 100%; border-collapse: collapse; border-spacing: unset;">
                    <thead>
                        <tr style="padding:3px; text-align: center; font-size: 13px !important;">
                            <th style="padding: 15px;">Months</th>
                            <th colspan="31" style="padding: 15px;">Insertion Schedule</th>
                            <th rowspan="2" style="padding: 15px;">Monthly Total</th>
                            <th rowspan="2" style="padding: 15px 40px;">Descriptions</th>
                        </tr>
                        <tr style="padding:3px; text-align: center; font-size: 13px !important;">
                            <th class="text-left rule"> Dates</th>
                            @foreach($data['day_numbers'] as $day_number)
                                <th style="padding: 10px; text-align: center;">
                                    {{ $day_number }}
                                </th>
                            @endforeach
                        </tr>
                    </thead> 
                    <tbody>
                        @foreach($time_belt['slots'] as $slot)
                            <tr style="padding:3px; text-align: center; font-size: 13px !important;">
                                <td style="padding:3px; text-align: center; font-size: 13px !important;">
                                    {{ $time_belt['month'] }}
                                </td> 
                                @foreach($data['day_numbers'] as $day)
                                    @if(isset($slot['exposures'][$day]))
                                        <td style="padding:3px; text-align: center; font-size: 13px !important;">
                                            {{ $slot['exposures'][$day] }}
                                        </td>
                                    @else
                                    <td style="padding:3px; text-align: center; font-size: 13px !important;">
                                
                                    </td>
                                    @endif
                                @endforeach
                                <td style="padding:3px; text-align: center; font-size: 13px !important;">
                                    {{ $slot['total_spots'] }}
                                </td>
                                <td style="padding:3px 6px; text-align: left; font-size: 13px !important;">
                                    Station : {{ $time_belt['station'] }} <br>
                                    Program : {{ $time_belt['program'] }} <br>
                                    Program Time : {{ $time_belt['program_time'] }} <br> 
                                    Daypart : {{ $time_belt['daypart'] }} <br>
                                    Material :  {{ $time_belt['slots'][0]['asset'] }}<br>
                                </td>
                            </tr> 
                        @endforeach
                        <tr style="padding:3px;">
                            <td colspan="32" style="padding:3px; font-size: 15px !important;"><h5>Total Number of Insertions</h5></td> 
                            <td style="padding:3px; text-align: center; font-size: 13px !important;"><h5>{{ $time_belt['total_insertions'] }}</h5></td> 
                            <td style="padding:3px; text-align: center; font-size: 13px !important;"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @endforeach
	</div>
	<!-- COSTING -->
	<div class="row costing">
		<h4 style="text-align: center;">COSTING</h4>
		<table border="1" style="width: 100%; border-collapse: collapse; border-spacing: unset;">
			<thead>
				<tr>
					<th style="font-weight: 500; background: #fafafa; color: rgba(0,0,0,0.87); text-align: left; padding: 15px; padding-top: 20px;">Inserts</th> 
					<th colspan="29" style="font-weight: 500; background: #fafafa; color: rgba(0,0,0,0.87); text-align: left; padding: 15px; padding-top: 20px;">Description</th> 
					<th colspan="2" style="font-weight: 500; background: #fafafa; color: rgba(0,0,0,0.87); text-align: left; padding: 15px; padding-top: 20px;">Rate (&#8358;)</th> 
					<th colspan="2" style="font-weight: 500; background: #fafafa; color: rgba(0,0,0,0.87); text-align: left; padding: 15px; padding-top: 20px;">Total Amount (&#8358;)</th>
				</tr>
			</thead> 
			<tbody>
                @foreach($data['time_belt_summary'] as $summary)
                    <tr>
                        <td style="vertical-align: middle; padding: 5px 20px !important;">{{ $summary['total_spot'] }}</td> 
                        <td colspan="29" style="vertical-align: middle; padding: 5px 20px !important;">
                        Publisher Name : {{ $summary['publisher_name'] }} <br>
                        Program : {{ $summary['program'] }} <br>
                        Program Time : {{ $summary['program_time'] }} <br>
                        Duration : {{ $summary['duration'] }} Seconds
                        </td> 
                        <td colspan="2" style="text-align: right; padding: 5px 20px;">{{ number_format($summary['rate'], 2) }}</td> 
                        <td colspan="2" style="text-align: right; padding: 5px 20px;">{{ number_format($summary['gross_total'], 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                	<td class="rule"></td> 
                	<td colspan="29" style="text-align: right; padding: 5px 20px;">Sub Total</td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;"></td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">{{ number_format($data['costSummary']['subTotal'], 2) }}</td>
                </tr> 
                <tr>
                	<td class="rule"></td> 
                	<td colspan="29" style="text-align: right; padding: 5px 20px;">Volume Discount</td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">{{ $data['costSummary']['volumeDiscount'] }}%</td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">{{ number_format($data['costSummary']['volumeDiscountValue'], 2) }}</td>
                </tr> 
                <tr>
                	<td class="rule"></td> 
                	<td colspan="29" style="text-align: right; padding: 5px 20px;"></td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;"></td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">{{ number_format($data['costSummary']['netTotalLessVolumeDiscount'], 2) }}</td>
                </tr> 
                <tr>
                	<td class="rule"></td> 
                	<td colspan="29" style="text-align: right; padding: 5px 20px;">Agency Commission</td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">15%</td>
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">{{ number_format($data['costSummary']['agencyCommission'], 2) }}</td>
                </tr> 
                <tr>
                	<td class="rule"></td> 
                	<td colspan="29" style="text-align: right; padding: 5px 20px;"></td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;"></td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">{{ number_format($data['costSummary']['netTotalLessAgencyCommission'], 2) }}</td>
                </tr> 
                <tr>
                	<td class="rule"></td> 
                	<td colspan="29" style="text-align: right; padding: 5px 20px;">VAT</td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">5%</td> 
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">{{ number_format($data['costSummary']['vat'], 2) }}</td>
                </tr> 
                <tr>
                	<td colspan="32" style="text-align: center;">Total Amount Payable</td>
                	<td colspan="2" style="text-align: right; padding: 5px 20px;">{{ number_format($data['costSummary']['totalPayable'], 2) }}</td>
                </tr>
            </tbody>
        </table>
	</div>
	<!-- TERMS -->
	<div class="row terms" style="width: 100%; display: inline-block; margin-top: 20px; margin-bottom: 20px;">
		<div class="col" style="width: 10%; float: left;">
			<b>TERMS.</b>
		</div>
		<div class="col" style="width: 90%; float: right; display: block;">
			<p style="font-size: 11px;">1. The contract exclusively governs this transaction</p> 
			<p style="font-size: 11px;"> 2. The rate on this contract cannot be reviewed upwards but 
                    may be reviewed downwards when expedient to do so. 
                    If the rate is reviewed downwards at any time during the 
                    life of this contract the new lower rate will henceforth apply.</p> 
            <p style="font-size: 11px;">3. This order can be discontinued at any time. It is agreed that both 
                    parties shall fully discharge all their financial obligations at the 
                    rate in force on the date of discontinuance</p> 
            <p style="font-size: 11px;"> 4. Bills for service must be itemized and rendered monthly</p> 
            <p style="font-size: 11px;">5. Bills/Certificate of Broadcast must be received within 10 days 
                    of such broadcast, failing which the agency reserves the right to 
                    decline such payment unless special circumstances necessitating the 
                    acceptance of such bills are proved to exist.</p> 
            <p style="font-size: 11px;">6. Do not use advert materials if they are faulty or damaged. 
                    Contact the agency immediately as there will be no payments for poor placements,</p> 
            <p style="font-size: 11px;">7. All invoices not received within 10 days after the end of each month of the 
                    campaign will not be honoured for payment.</p> 
            <p style="font-size: 11px;">8. Failure to execute the campaign as stipulated in this contract will result in 
                    cancellation of the particular transaction and full refund of all monies advanced in respect thereof.</p> 
            <p style="font-size: 11px;">9. This contract shall be subject to the provisions of all relevant statutory tax laws.</p> 
            <p style="font-size: 11px;">10. Relevant copies of invoices related to this contract may be sent 
                    to the client without recourse to the Agency.</p> 
            <p style="font-size: 11px;">11. This contract is subject to Client independently commissioned Monitoring Agency's 
                    Compliance Report; except in cases where the concerned media house(s) is/are not 
                    covered by the commissioned Monitoring Agency.</p>
            <p style="font-size: 11px;">12. All invoices must be accompanied with Original COT from the station.</p> 
            <p style="font-size: 11px;">13. Supplier should attach 2 photocopies of their invoice and COT.</p><p style="font-size: 11px;">14. Please DO NOT plough back any non compliant spot(s) without the approval of MediaReach.</p>
		</div>
	</div>
    <!-- SIGNATURE -->
    <br>
    <br>
    <div class="row signature" style="width: 80%; display: inline-block; margin-top: 20px; margin: 20px 100px;">
        @if($data['mpo_details']->requester === null)
            <div class="col" style="width: 40%; float: left; border-bottom: 1px solid #000; text-align: center; padding-bottom: 10px;">
                <span style="font-family: cursive;">{{ Auth::user()->full_name }}, 
                @if($data['mpo_details']->submitted_at) {{ date('M d, Y', strtotime($data['mpo_details']->submitted_at)) }}, @endif</span><br>
                <span style="font-size: 12px; font-style: italic;">{{ Auth::user()->email }},</span> <br>
                <span style="font-size: 12px; font-style: italic;">{{ Auth::user()->phone_number }}</span>
            </div>
        @else
            <div class="col" style="width: 40%; float: left; border-bottom: 1px solid #000; text-align: center; padding-bottom: 10px;">
                <span style="font-family: cursive;">{{ $data['mpo_details']->requester->full_name }}, 
                @if($data['mpo_details']->submitted_at) {{ date('M d, Y', strtotime($data['mpo_details']->submitted_at)) }}, @endif</span><br>
                <span style="font-size: 12px; font-style: italic;">{{ $data['mpo_details']->requester->email }},</span> <br>
                <span style="font-size: 12px; font-style: italic;">{{ $data['mpo_details']->requester->phone_number }}</span>
            </div>
        @endif

        @if($data['mpo_details']->mpo_accepter)
            <div class="col" style="width: 40%; float: right; border-bottom: 1px solid #000; text-align: center; padding-bottom: 10px;">
                <span style="font-family: cursive;">{{ $data['mpo_details']->mpo_accepter->full_name }} , 
                {{ date('M d, Y', strtotime($data['mpo_details']->accepted_at)) }},</span>, <br>
                <span style="font-size: 12px; font-style: italic;">{{ $data['mpo_details']->mpo_accepter->email }},</span> <br>
                <span style="font-size: 12px; font-style: italic;">{{ $data['mpo_details']->mpo_accepter->phone_number }}</span>
            </div>
        @else
            <div class="col" style="width: 40%; float: right; border-bottom: 1px solid #000; text-align: center; padding-bottom: 10px;">
                <span style="font-family: cursive;">Media House Rep (Name, Sign, TelNo, Email)</span>
            </div>
        @endif
	</div>
</body>
</html>
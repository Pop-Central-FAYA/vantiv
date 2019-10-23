@extends('mail.mail_layout')

@section('button')
<a href="{{ $mail_content['link'] }}" class="es-button" target="_blank" style="text-decoration:none;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:18px;color:#FFFFFF;border-style:solid;border-color:#44C1C9;border-width:10px 20px 10px 20px;display:inline-block;background:#44C1C9;border-radius:5px;font-weight:normal;font-style:normal;line-height:22px;width:auto;text-align:center;">
Go to MPO</a> 
@stop
@section('body_info')
<td align="center" style="padding:0;Margin:0;"></td> 
    </tr> 
    <tr style="border-collapse:collapse;"> 
    <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"> <p style="Margin:0;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333; text-align:left;"> 
    {!! $mail_content['sender'] !!},
   <br></p> </td> 
    </tr> 
    <tr style="border-collapse:collapse;"> 
    <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"> 
        <p style="Margin:0;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;">
            The MPO for campaign : {!! $mail_content['campaign'] !!} and client : {!! $mail_content['client'] !!}  that was sent for approval has been {!! $mail_content['action'] !!}
            by {!! $mail_content['reviewer'] !!}
            <br>
        </p> 
    </td> 
    </tr> 
    <tr style="border-collapse:collapse;"> 
    <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;">  
</td> 
@stop

@extends('mail.mail_layout')

@section('button')
    <a href='{{ $mpo_url }}' 
    class="es-button" target="_blank" 
    style="text-decoration:none;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:18px;color:#FFFFFF;border-style:solid;border-color:#44C1C9;border-width:10px 20px 10px 20px;display:inline-block;background:#44C1C9;border-radius:5px;font-weight:normal;font-style:normal;line-height:22px;width:auto;text-align:center;">
        View MPO
    </a> 
@stop
@section('body_info')
    <td align="center" style="padding:0;Margin:0;"></td> 
    <tr style="border-collapse:collapse;"> 
        <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"> 
            <p style="Margin:0;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;">
                Please click on the button above to view the mpo<br>
            </p>
        </td> 
    </tr> 
    <tr style="border-collapse:collapse;"> 
        <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;">  
    </tr>
@stop
@extends('mail.mail_layout')

@section('button')
<a href='{{ URL::to('proceed/password-change/' . $token) }}' class="es-button" target="_blank" style="text-decoration:none;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:18px;color:#FFFFFF;border-style:solid;border-color:#44C1C9;border-width:10px 20px 10px 20px;display:inline-block;background:#44C1C9;border-radius:5px;font-weight:normal;font-style:normal;line-height:22px;width:auto;text-align:center;">Reset Password</a> 
@stop
@section('body_info')
<td align="center" style="padding:0;Margin:0;"> <h4 style="Margin:0;line-height:24px;font-family:tahoma, verdana, segoe, sans-serif;font-size:20px;font-style:normal;font-weight:normal;color:#333333;">Reset Password on {!! AssetsHelper::brandName() !!}</h4> </td> 
    </tr> 
    <tr style="border-collapse:collapse;"> 
    <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"> <p style="Margin:0;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;">You've received this email because you asked to reset your password. Click this
    button and you'll be guided through a simple process.<br></p> </td> 
    </tr> 
    <tr style="border-collapse:collapse;"> 
    <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"> <p style="Margin:0;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;">If you didn't ask about resetting your password,
    <a href="mailto:info@fayamedia.com">let us know.</a><br></p> 
</td> 
@stop

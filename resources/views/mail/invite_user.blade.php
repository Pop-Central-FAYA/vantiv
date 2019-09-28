@extends('mail.mail_layout')

@section('button')
<a href='{{ URL::to('media-plan/summary/' . $user_mail_content['link']) }}' class="es-button" target="_blank" style="text-decoration:none;font-family:arial, 'helvetica neue', helvetica, sans-serif;font-size:18px;color:#FFFFFF;border-style:solid;border-color:#44C1C9;border-width:10px 20px 10px 20px;display:inline-block;background:#44C1C9;border-radius:5px;font-weight:normal;font-style:normal;line-height:22px;width:auto;text-align:center;">Accept Invite</a> 
@stop
@section('body_info')
<td align="center" style="padding:0;Margin:0;"> </td> 
    </tr> 
   



                    <tr style="border-collapse:collapse;"> 
                      <td align="center" style="padding:0;Margin:0;"> <h4 style="Margin:0;line-height:24px;font-family:tahoma, verdana, segoe, sans-serif;font-size:20px;font-style:normal;font-weight:normal;color:#333333;">{!! $user_mail_content['subject'] !!}</h4> </td> 
                     </tr> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"> <p style="Margin:0;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;">  You have been invited by {!! $user_mail_content['inviter'] !!} to be part of {!! $user_mail_content['companies'] !!} on the <a href="https://vantage.fayamedia.com/login">FAYA </a> Platform.<br></p> </td> 
                     </tr> 
                     <tr style="border-collapse:collapse;"> 
                      <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;"> <p style="Margin:0;font-size:14px;font-family:arial, 'helvetica neue', helvetica, sans-serif;line-height:21px;color:#333333;">  This invitation link is valid for --.<br></p> </td> 
                     </tr> 






    <tr style="border-collapse:collapse;"> 
    <td align="center" style="padding:0;Margin:0;padding-top:10px;padding-bottom:10px;">
</td> 
@stop

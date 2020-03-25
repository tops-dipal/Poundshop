<!doctype html>
<html>
<head>
<meta charset="utf-8">
<!-- utf-8 works for most cases -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Forcing initial-scale shouldn't be necessary -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- Use the latest (edge) version of IE rendering engine -->
<title>Welcome</title>
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600" rel="stylesheet"> 
</head>

<body width="620" height="100%" bgcolor="#e0e0e0" style="margin: 0;" yahoo="yahoo">
	<table cellpadding="0" cellspacing="0" border="0" height="100%" width="100%" bgcolor="#e0e0e0" style="border-collapse:collapse;font-family: 'Open Sans', sans-serif;">
		<tr>
			<tr>
				<td height="30"></td>
			</tr>
			<td>
				<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" bgcolor="#fff" style="max-width: 620px;border-radius: 10px;">
					<tr>
						<td width="80"></td>
						<td height="27"></td>
						<td width="80"></td>
					</tr>
					<tr>
						<td width="80"></td>
					  	<td style="text-align: center">
						  <img src="{{asset('img/logo-new.png')}}" alt="alt_text" border="0">
						</td>
						<td width="80"></td>
					</tr>
					<tr>
						<td width="80"></td>
						<td height="38"></td>
						<td width="80"></td>
					</tr>
					<!-- Body Section-->
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%">
								<tr>
									<td width="40"></td>
									<td style="font-size: 24px;color: #252525; font-weight: 600;font-family: 'Open Sans', sans-serif;">Hi, {{ $data['toName'] }}</td>
									<td width="40"></td>
								</tr>
								<tr>
									<td height="20" colspan="3" ></td>									
								</tr>
								<tr>
									<td width="40"></td>
									<td style="font-size: 16px;color: #555555; font-weight: 400;font-family: 'Open Sans', sans-serif;">
									
									<p style="font-size: 16px;color: #555555; font-weight: 400;font-family: 'Open Sans', sans-serif;">Click on below link for the Booking Material Receipt.</p>
									
									<p style="font-size: 16px;color: #555555; font-weight: 400;font-family: 'Open Sans', sans-serif;"><a href="{{URL::signedRoute('supplier_material_receipt.index',$data['id'] )}}">Booking Receipt</a></p>
									

									<p style="font-size: 16px;color: #555555; font-weight: 400;font-family: 'Open Sans', sans-serif;">our website <a href="www.poundshop.com" target="_blank">www.poundshop.com</a></p>

									<p style="font-size: 16px;color: #555555; font-weight: 400;font-family: 'Open Sans', sans-serif;">Our accounts/buying department telephone 
									<a href="tel:0121 647 2569">0121 647 2569</a></p>

									</td>
									<td width="40"></td>
								</tr>
								<tr>
									<td height="20" colspan="3" ></td>									
								</tr>
								<tr>
									<td width="40"></td>
									<td style="font-size: 16px;color: #555555; font-weight: 600;font-family: 'Open Sans', sans-serif;">Thank you</td>
									<td width="40"></td>
								</tr>
							</table>
						</td>
					</tr>
					<!-- Body Section END -->
					<tr>
						<td width="80"></td>
						<td height="30"></td>
						<td width="80"></td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%">
								<tr>
									<td width="20"></td>
									<td height="1" bgcolor="#e1e1e1"></td>
									<td width="20"></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="80"></td>
						<td height="30"></td>
						<td width="80"></td>
					</tr>
					<tr>
						<td colspan="3">
							<table cellspacing="0" cellpadding="0" border="0" align="center" width="100%" style="text-align: center;max-width: 200px">
								<tr>
									<td width="20"><a href="#"><img src="{{asset('img/facebook.png')}}" width="20" height="20" alt="alt_text" border="0"></a></td>
									<td width="20"><a href="#"><img src="{{asset('img/instagram.png')}}" width="20" height="20" alt="alt_text" border="0"></a></td>
									<td width="20"><a href="#"><img src="{{asset('img/twitter.png')}}" width="15" height="20" alt="alt_text" border="0"></a></td>
									<td width="20"><a href="#"><img src="{{asset('img/linkedin.png')}}" width="20" height="20" alt="alt_text" border="0"></a></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td width="80"></td>
						<td height="20"></td>
						<td width="80"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="15"></td>
		</tr>
		<tr>
			<td style="font-size: 12px; color: #747474;text-align: center;font-family: 'Open Sans', sans-serif;"> ©2019 Poundshop. All rights reserved.</td>
		</tr>		
	</table>
</body>
</html>

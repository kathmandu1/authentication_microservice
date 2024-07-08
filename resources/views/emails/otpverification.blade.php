{{-- <!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>OTP code</title>
    </head>
    <body>
        <h1> Otp Code</h1>
        <p>Hi {{ $optModal->email }},</p>
        <p>We've received a request for login OTP. Here's your OTP:</p>
        <p style="font-size: 24px; font-weight: bold;">{{ $optModal->otp }}</p>
        <p>If you didn't make this request, you can safely ignore this email.</p>
        <p>Thanks,<br>The Team</p>
    </body>
</html> --}}




<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your login</title>
    <!--[if mso]><style type="text/css">body, table, td, a { font-family: Arial, Helvetica, sans-serif !important; }</style><![endif]-->
</head>

<body style="font-family: Helvetica, Arial, sans-serif; margin: 0px; padding: 0px; background-color: #ffffff;">
    <table role="presentation" style="width: 100%; border-collapse: collapse; border: 0px; border-spacing: 0px; font-family: Arial, Helvetica, sans-serif; background-color: rgb(239, 239, 239);">
        <tbody>
            <tr>
                <td align="center" style="padding: 1rem 2rem; vertical-align: top; width: 100%;">
                    <table role="presentation" style="max-width: 600px; border-collapse: collapse; border: 0px; border-spacing: 0px; text-align: left;">
                        <tbody>
                            <tr>
                                <td style="padding: 40px 0px 0px;">
                                    <div style="text-align: center;" bis_skin_checked="1">
                                        <div style="padding-bottom: 20px;" bis_skin_checked="1"><img src="https://merodiscounts.com/frontend/images/logo.png" alt="Company" style="width: 156px;"></div>
                                    </div>
                                    <div style="padding: 20px; background-color: rgb(255, 255, 255);" bis_skin_checked="1">
                                        <div style="color: rgb(0, 0, 0); text-align: left;" bis_skin_checked="1">
                                            <h1 style="margin: 1rem 0">Verification code</h1>
                                            <p style="padding-bottom: 16px">Please use the verification code below to
                                                sign in.</p>
                                            <p style="padding-bottom: 16px"><strong style="font-size: 130%; letter-spacing: 4px;">{{ $optModal->otp }}</strong></p>
                                            <p style="padding-bottom: 16px">If you didn’t request this, you can ignore
                                                this email.</p>
                                            <p style="padding-bottom: 16px">Thanks,<br>Merodiscounts.com</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table align="center" border="0" cellpadding="0" cellspacing="0" class="row row-5"
						role="presentation" style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="100%">
						<tbody>
							<tr>
								<td>
									<table align="center" border="0" cellpadding="0" cellspacing="0"
										class="row-content stack" role="presentation"
										style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; color: #000000; width: 650px;"
										width="650">
										<tbody>
											<tr>
												<td class="column column-1"
													style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; font-weight: 400; text-align: left; padding-bottom: 60px; padding-top: 20px; vertical-align: top; border-top: 0px; border-right: 0px; border-bottom: 0px; border-left: 0px;"
													width="100%">
													<table border="0" cellpadding="10" cellspacing="0"
														class="social_block block-1" role="presentation"
														style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"
														width="100%">
														<tr>
															<td class="pad">
																<div align="center" class="alignment">
																	<table border="0" cellpadding="0" cellspacing="0"
																		class="social-table" role="presentation"
																		style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; display: inline-block;"
																		width="188px">
																		<tr>
																			<td style="padding:0 15px 0 0px;"><a
																					href="https://www.facebook.com/MeroDiscounts"
																					target="_blank"><img alt="Facebook"
																						height="32"
																						src="{{ asset('frontend/images/facebook2x.png')}}"
																						style="display: block; height: auto; border: 0;"
																						title="Facebook"
																						width="32" /></a></td>
																			<td style="padding:0 15px 0 0px;"><a
																					href="https://twitter.com/MeroDiscounts"
																					target="_blank"><img alt="Twitter"
																						height="32"
																						src="{{ asset('frontend/images/twitter2x.png')}}"
																						style="display: block; height: auto; border: 0;"
																						title="Twitter"
																						width="32" /></a></td>
																			<td style="padding:0 15px 0 0px;"><a
																					href="https://www.instagram.com/merodiscounts"
																					target="_blank"><img alt="Instagram"
																						height="32"
																						src="{{ asset('frontend/images/instagram2x.png')}}"
																						style="display: block; height: auto; border: 0;"
																						title="Instagram"
																						width="32" /></a></td>
																			<td style="padding:0 15px 0 0px;"><a
																					href="https://www.youtube.com/@merodiscounts"
																					target="_blank"><img alt="Youtube"
																						height="32"
																						src="{{ asset('frontend/images/youtube2x.png')}}"
																						style="display: block; height: auto; border: 0;"
																						title="Youtube"
																						width="32" /></a></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
													</table>
													<table border="0" cellpadding="10" cellspacing="0"
														class="text_block block-2" role="presentation"
														style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;"
														width="100%">
														<tr>
															<td class="pad">
																<div style="font-family: sans-serif">
																	<div class=""
																		style="font-size: 12px; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif; mso-line-height-alt: 18px; color: #555555; line-height: 1.5;">
																		<a href="https://www.merodiscounts.com"
																			style="margin: 0; font-size: 14px; text-align: center; mso-line-height-alt: 21px;">
																			Merodiscounts.com</a>
																		<p
																			style="margin: 0; font-size: 14px; text-align: center; mso-line-height-alt: 21px;">
																			Gyanodhaya Pustakalaya Marg, Jhamsikhel, Lalitpur
																		</p>
																	</div>
																</div>
															</td>
														</tr>
													</table>
													<table border="0" cellpadding="10" cellspacing="0"
														class="divider_block block-3" role="presentation"
														style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"
														width="100%">
														<tr>
															<td class="pad">
																<div align="center" class="alignment">
																	<table border="0" cellpadding="0" cellspacing="0"
																		role="presentation"
																		style="mso-table-lspace: 0pt; mso-table-rspace: 0pt;"
																		width="60%">
																		<tr>
																			<td class="divider_inner"
																				style="font-size: 1px; line-height: 1px; border-top: 1px dotted #C4C4C4;">
																				<span> </span></td>
																		</tr>
																	</table>
																</div>
															</td>
														</tr>
													</table>
													<table border="0" cellpadding="10" cellspacing="0"
														class="text_block block-4" role="presentation"
														style="mso-table-lspace: 0pt; mso-table-rspace: 0pt; word-break: break-word;"
														width="100%">
														<tr>
															<td class="pad">
																<div style="font-family: sans-serif">
																	<div class=""
																		style="font-size: 12px; font-family: 'Lato', Tahoma, Verdana, Segoe, sans-serif; mso-line-height-alt: 14.399999999999999px; color: #4F4F4F; line-height: 1.2;">
																		<p
																			style="margin: 0; font-size: 12px; text-align: center; mso-line-height-alt: 14.399999999999999px;">
																			<span style="font-size:14px;"><a href="https://merodiscounts.com/privacy-policy"
																					rel="noopener"
																					style="text-decoration: none; color: #fc4704;"
																					target="_blank"><strong>Privacy & Policy</strong></a>
																				|  <span
																					style="background-color:transparent;font-size:14px;">01-5970389</span></span>
																		</p>
																	</div>
																</div>
															</td>
														</tr>
													</table>
												</td>
											</tr>
										</tbody>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>

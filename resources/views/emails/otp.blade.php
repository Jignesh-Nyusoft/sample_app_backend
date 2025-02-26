<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
<style>
 .brand {
  background: #1d4a35 !important;
  color: white;
  padding: 10px 0 10px 10px;
  border-radius: 2px;
}

</style>
</head>
<body>
    <div style="font-family: Helvetica,Arial,sans-serif;min-width:1000px;overflow:auto;line-height:2">
        <div style="margin:50px auto;width:70%;padding:20px 0">
          <div class="brand" style="border-bottom:1px solid #eee">
            <a href="" style="font-size:1.4em;color: #fff;text-decoration:none;font-weight:600">Green Clusta</a>
          </div>
          <p style="font-size:1.1em">Hi,</p>
          <p>Thank you for choosing Your Green Clusta. Use the following OTP to complete your Sign Up procedures. OTP is valid for 10 minutes</p>
          <h2 style="background: #1d4a35;margin: 0 auto;width: max-content;padding: 0 10px;color: #fff;border-radius: 4px;">{{$otp}}</h2>
          <p style="font-size:0.9em;">Regards,<br />GreenClusta</p>
          <hr style="border:none;border-top:1px solid #eee" />
          <div style="float:right;padding:8px 0;color:#aaa;font-size:0.8em;line-height:1;font-weight:300">
            <p>GreenClusta Inc</p>
            {{-- <p>1600 Amphitheatre Parkway</p> --}}
            {{-- <p>California</p> --}}
          </div>
        </div>
      </div>
</body>
</html>

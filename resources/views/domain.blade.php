<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script scr="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

<body>
    <div class="container-fluid px-1 py-5 mx-auto">
        <div class="row d-flex justify-content-center">
            <div class="col-xl-7 col-lg-8 col-md-9 col-11 text-center">
                <h3>{{ env('APP_NAME') }}</h3>
                <div class="card">
                    <h1 class="text-center"> الرجاء الاتظار قليلا ثم رز الرابط اسفل الصفحة </h1>
                    <a href="http://{{ $domain }}" target="_blank"  class="link btn btn-primary btn-sm text-white disabled">زر الموقع</a>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(function () {
            setTimeout(
            function() 
            {
                $('.link').removeClass('disabled')
            }, 10000);
            
        })
    </script>
</body>

</html>

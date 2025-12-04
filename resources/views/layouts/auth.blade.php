<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ config('app.name') }} | Log in</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-2/bower_components/bootstrap/dist/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-2/bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-2/dist/css/AdminLTE.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ asset('AdminLTE-2/plugins/iCheck/square/blue.css') }}">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    
    <style>
        body.login-page {
            background: url('{{ asset('images/login.png') }}') no-repeat center center fixed;
            background-size: cover;
        }

        /* Rounded login box */
        .login-box {
            border-radius: 5px;
            overflow: hidden;
        }

        .login-box-body {
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Rounded input fields */
        .form-control {
            border-radius: 5px !important;
            height: 45px;
        
            padding: 12px 40px 12px 20px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #065F46;
            box-shadow: 0 0 0 0.2rem rgba(6, 95, 70, 0.25);
            outline: none;
        }

        /* Form control feedback positioning */
        .has-feedback .form-control {
            padding-right: 42.5px;
        }

        .form-control-feedback {
            right: 15px;
            color: #065F46;
            line-height: 46px;
        }

        /* Custom button styling with #065F46 color */
        .btn-primary {
            background-color: #065F46 !important;
            border-color: #065F46 !important;
            border-radius: 5px !important;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-primary:hover,
        .btn-primary:focus,
        .btn-primary:active {
            background-color: #047857 !important;
            border-color: #047857 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(6, 95, 70, 0.3);
        }

        /* Error styling */
        .has-error .form-control {
            border-color: #dd4b39;
        }

        .has-error .form-control:focus {
            border-color: #dd4b39;
            box-shadow: 0 0 0 0.2rem rgba(221, 75, 57, 0.25);
        }

        .help-block {
            margin-top: 8px;
            font-size: 12px;
        }

        /* Login logo styling */
        .login-logo a {
            font-size: 35px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        /* Login box message */
        .login-box-msg {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }

        /* Additional responsiveness */
        @media (max-width: 768px) {
            .login-box {
                margin: 7% auto;
                width: 90%;
            }
        }
    </style>

    @stack('css')
    </head>
<body class="hold-transition login-page">
    
    @yield('login')

    <!-- jQuery 3 -->
    <script src="{{ asset('AdminLTE-2/bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('AdminLTE-2/bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>
    <!-- iCheck -->
    <script src="{{ asset('AdminLTE-2/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- Validator -->
    <script src="{{ asset('js/validator.min.js') }}"></script>
    <script>
        $(function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_square-blue',
                increaseArea: '20%' /* optional */
            });
        });
        $('.form-login').validator();
    </script>
</body>
</html>
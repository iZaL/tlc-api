<!DOCTYPE html>

<html lang="en">

<!-- begin::Head -->
<head>
    <meta charset="utf-8" />
    <title>Metronic | Login Page - 6</title>
    <meta name="description" content="Latest updates and statistic charts">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">

    <!--begin::Web font -->
    <script src="https://ajax.googleapis.com/ajax/libs/webfont/1.6.16/webfont.js"></script>
    <script>
      WebFont.load({
        google: {"families":["Poppins:300,400,500,600,700","Roboto:300,400,500,600,700"]},
        active: function() {
          sessionStorage.fonts = true;
        }
      });
    </script>

    <!--end::Web font -->

    <!--begin::Global Theme Styles -->
    <link href="/admin/css/vendors.bundle.css" rel="stylesheet" type="text/css" />

    <!--RTL version:<link href="../../../assets/vendors/base/vendors.bundle.rtl.css" rel="stylesheet" type="text/css" />-->
    <link href="/admin/css/style.bundle.css" rel="stylesheet" type="text/css" />

    <!--RTL version:<link href="../../../assets/demo/default/base/style.bundle.rtl.css" rel="stylesheet" type="text/css" />-->

</head>

<!-- end::Head -->

<!-- begin::Body -->
<body class="m--skin- m-header--fixed m-header--fixed-mobile m-aside-left--enabled m-aside-left--skin-dark m-aside-left--fixed m-aside-left--offcanvas m-footer--push m-aside--offcanvas-default">

<!-- begin:: Page -->
<div class="m-grid m-grid--hor m-grid--root m-page">
    <div class="m-grid__item m-grid__item--fluid m-grid m-grid--desktop m-grid--ver-desktop m-grid--hor-tablet-and-mobile m-login m-login--6" id="m_login">
        <div class="m-grid__item   m-grid__item--order-tablet-and-mobile-2  m-grid m-grid--hor m-login__aside " style="background-image: url(/admin/img/bg-4.jpg);">
            <div class="m-grid__item">
                <div class="m-login__logo">
                    <a href="#">
                        <img src="/admin/img/logo-4.png">
                    </a>
                </div>
            </div>
            <div class="m-grid__item m-grid__item--fluid m-grid m-grid--ver">
                <div class="m-grid__item m-grid__item--middle">
                    <span class="m-login__title">Trans Logistics Company</span>
                </div>
            </div>
            <div class="m-grid__item">
                <div class="m-login__info">
                    <div class="m-login__section">
                        <a href="#" class="m-link">&copy 2018 TLC</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="m-grid__item m-grid__item--fluid  m-grid__item--order-tablet-and-mobile-1  m-login__wrapper">

            <!--begin::Head-->
            <div class="m-login__head">
                <span>Don't have an account?</span>
                <a href="#" class="m-link m--font-danger">Sign Up</a>
            </div>

            <!--end::Head-->

            <!--begin::Body-->
            <div class="m-login__body">

                <!--begin::Signin-->
                <div class="m-login__signin">
                    <div class="m-login__title">
                        <h3>Login</h3>
                    </div>

                    <!--begin::Form-->

                    <form class="m-login__form m-form" method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @csrf

                        <div class="form-group m-form__group">
                            <input id="email" type="email" class="m-input form-control {{ $errors->has('email') ? 'is-invalid' : '' }}" name="email" value="{{ old('email') }}"  autofocus placeholder="Email">

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                            @endif

                        </div>

                        <div class="form-group m-form__group">
                            <input class="form-control m-input m-login__form-input--last" id="password" type="password" class="form-control m-input m-login__form-input--last {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password"  placeholder="Password">
                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>


                        <div class="m-login__action">
                            <button type="submit" id="m_login_signin_submit" class="btn btn-primary m-btn m-btn--pill m-btn--custom m-btn--air m-login__btn m-login__btn--primary" dusk="submit">Login</button>
                        </div>

                    </form>

                    <!--end::Form-->

                    <!--begin::Action-->
                    <div class="m-login__action">
                        <a href="#" class="m-link">
                            <span>Forgot Password ?</span>
                        </a>
                    </div>

                    <!--end::Action-->

                </div>

                <!--end::Signin-->
            </div>

            <!--end::Body-->
        </div>
    </div>
</div>

<!-- end:: Page -->

<!--begin::Global Theme Bundle -->
<script src="/admin/js/vendors.bundle.js" type="text/javascript"></script>
<script src="/admin/js/scripts.bundle.js" type="text/javascript"></script>

<!--end::Global Theme Bundle -->
</body>
<!-- end::Body -->
</html>
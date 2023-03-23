@extends('backend.layouts.blank')

@section('content')

		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-4 login-signin-on d-flex flex-row-fluid" id="kt_login">
				<div class="d-flex flex-center flex-row-fluid bgi-size-cover bgi-position-top bgi-no-repeat" style="background-image: url('{{ static_asset('assets/dashboard/media/bg/bg-3.jpg') }}');">
					<div class="overflow-hidden text-center login-form p-7 position-relative">
						<!--begin::Login Header-->
						<div class="mb-5 d-flex flex-center">
							<a href="#">
                                @if(get_setting('system_logo_black') != null)
                                    <img src="{{ uploaded_asset(get_setting('system_logo_black')) }}" alt="{{ get_setting('site_name') }}" class="max-h-75px" style="width:100%">
                                @else
                                    <img src="{{ static_asset('assets/img/logo.svg') }}" alt="{{ get_setting('site_name') }}" class="max-h-75px">
                                @endif
							</a>
						</div>
						<!--end::Login Header-->
						<!--begin::Login Sign in form-->
						<div class="login-signin">
							<div class="mb-20">
								<h3>{{ translate('Welcome to') }} @if(get_setting('site_name')) {{ get_setting('site_name') }} @else {{ translate('Asya Fawry') }}  @endif</h3>
								<div class="text-muted font-weight-bold">{{ translate('Login to your account.') }}</div>
							</div>
							@if(env('DEMO_MODE') == 'On')
								<div class="mb-10">
									<table class="kt-form" style="padding: 10px !important;margin: 0;width: 100%;border: 1px solid #eee;">
										<tbody>
											<tr>
												<td colspan="3" style="text-align: center;background: #eee;color: #000;font-size: 24px;font-weight: bold;text-transform: uppercase;padding: 10px;">
													{{translate('Demo Login Details')}}
												</td>
											</tr>
											<tr>
												<td colspan="3" style="text-align: left;background: #fff000;padding: 10px;color: #000;border: 1px solid #ccc;">
													{{translate("If any user from below didn't work for any reason, it may be a visitor has changed it's data, the data will be reset again every 12 hours")}}.
												</td>
											</tr>
											<tr>
												<td style="text-align: left;border-bottom: 1px solid #eee;font-weight:bold;padding: 0 10px;">
													{{translate('ADMIN')}}
													<br />
													<span id="login_admin" class="text-muted font-size-xs font-weight-normal">{{translate('Click to Copy')}}</span>
												</td>
												<td style="text-align: left;border-bottom: 1px solid #eee;padding: 0 10px">admin@cargo.com</td>
												<td style="text-align: right;border-bottom: 1px solid #eee;">123456</td>
											</tr>
											<tr>
												<td style="border-bottom: 1px solid #eee;text-align: left;font-weight:bold;padding: 0 10px;">
													{{translate('EMPLOYEE')}}
													<br />
													<span id="login_employee" class="text-muted font-size-xs font-weight-normal">{{translate('Click to Copy')}}</span>
												</td>
												<td style="border-bottom: 1px solid #eee;text-align: left;padding: 0 10px">employee@cargo.com</td>
												<td style="border-bottom: 1px solid #eee;text-align: right;">123456</td>
											</tr>
											<tr>
												<td style="border-bottom: 1px solid #eee;text-align: left;font-weight:bold;padding: 0 10px;">
													{{translate('BRANCH MANAGER')}}
													<br />
													<span id="login_branch" class="text-muted font-size-xs font-weight-normal">{{translate('Click to Copy')}}</span>
												</td>
												<td style="border-bottom: 1px solid #eee;text-align: left;padding: 0 10px">branch@cargo.com</td>
												<td style="border-bottom: 1px solid #eee;text-align: right;">123456</td>
											</tr>
											<tr>
												<td style="border-bottom: 1px solid #eee;text-align: left;font-weight:bold;padding: 0 10px;">
													{{translate('DRIVER/CAPTAIN')}}
													<br />
													<span id="login_driver" class="text-muted font-size-xs font-weight-normal">{{translate('Click to Copy')}}</span>
												</td>
												<td style="border-bottom: 1px solid #eee;text-align: left;padding: 0 10px">driver@cargo.com</td>
												<td style="border-bottom: 1px solid #eee;text-align: right;">123456</td>
											</tr>
											<tr>
												<td style="border-bottom: 1px solid #eee;text-align: left;font-weight:bold;padding: 0 10px;">
													{{translate('CLIENT')}}
													<br />
													<span id="login_client" class="text-muted font-size-xs font-weight-normal">{{translate('Click to Copy')}}</span>
												</td>
												<td style="text-align: left;padding: 0 10px">client@cargo.com</td>
												<td style="text-align: right;">123456</td>
											</tr>
										</tbody>
									</table>
								</div>
							@endif
                            <form class="form" method="POST" role="form" action="{{ route('login') }}">
                                @csrf
								<div class="mb-5 form-group">
                                    <input id="email" type="email" class="form-control h-auto form-control-solid py-4 px-8 {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ translate('Email') }}">
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
								</div>
								<div class="mb-5 form-group">
                                    <input id="password" type="password" class="form-control h-auto form-control-solid py-4 px-8 {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="{{ translate('Password') }}">
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
								</div>
								<div class="flex-wrap form-group d-flex justify-content-between align-items-center">
									<div class="checkbox-inline">
										<label class="m-0 checkbox text-muted">
                                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
										<span></span>{{translate('Remember me')}}</label>
									</div>
                                    @if(env('MAIL_USERNAME') != null && env('MAIL_PASSWORD') != null)
                                        <a href="{{ route('password.request') }}" class="text-muted text-hover-primary">{{translate('Forgot password ?')}}</a>
                                    @endif
								</div>
								<button type="submit" class="py-4 mx-4 my-3 btn btn-primary font-weight-bold px-9">{{ translate('Login') }}</button>
							</form>
							@if (\App\Addon::where('activated', 1)->count() > 0)
								@foreach(\File::files(base_path('resources/views/backend/inc/addons/login')) as $path)
									@include('backend.inc.addons.login.'.str_replace('.blade','',pathinfo($path)['filename']))
								@endforeach
							@endif
						</div>
						<!--end::Login Sign in form-->

					</div>
				</div>
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->



@endsection

@section('script')
    <script type="text/javascript">
        function autoFill(){
            $('#email').val('admin@cargo.com');
            $('#password').val('123456');
        }


		// Class Initialization
		$(document).ready(function() {
			autoFill();

			$('body').on('click','#login_admin', function(e){
				$('#email').val('admin@cargo.com');
				$('#password').val('123456');
				$('#signin_submit').trigger('click');
			});
			$('body').on('click','#login_employee', function(e){
				$('#email').val('employee@cargo.com');
				$('#password').val('123456');
				$('#signin_submit').trigger('click');
			});
			$('body').on('click','#login_driver', function(e){
				$('#email').val('driver@cargo.com');
				$('#password').val('123456');
				$('#signin_submit').trigger('click');
			});
			$('body').on('click','#login_branch', function(e){
				$('#email').val('branch@cargo.com');
				$('#password').val('123456');
				$('#signin_submit').trigger('click');
			});
			$('body').on('click','#login_client', function(e){
				$('#email').val('client@cargo.com');
				$('#password').val('123456');
				$('#signin_submit').trigger('click');
			});

		});
    </script>
@endsection

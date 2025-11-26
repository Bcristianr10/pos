@extends('layouts.admin')

@section('title', __('settings.Update_Settings'))
@section('content-header', __('settings.Update_Settings'))

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('settings.store') }}" method="post">
                @csrf

                {{-- <div class="col-12 d-flex">
                    <div class="col-6 form-group">
                        <label for="app_name">{{ __('settings.app_name') }}</label>
                        <input type="text" name="app_name" class="form-control @error('app_name') is-invalid @enderror"
                            id="app_name" placeholder="{{ __('settings.App_name') }}"
                            value="{{ old('app_name', config('settings.app_name')) }}">
                        @error('app_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-6 form-group">
                        <label for="app_description">{{ __('settings.app_description') }}</label>
                        <textarea name="app_description" class="form-control @error('app_description') is-invalid @enderror"
                            id="app_description" placeholder="{{ __('settings.app_description') }}">{{ old('app_description', config('settings.app_description')) }}</textarea>
                        @error('app_description')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div> --}}

                <div class="col-12 d-flex">
                    <div class="col-4 form-group">
                        <label for="warning_quantity">{{ __('settings.bussiness_name') }}</label>
                        <input type="text" name="bussiness_name"
                            class="form-control @error('bussiness_name') is-invalid @enderror" id="bussiness_name"
                            placeholder="{{ __('settings.bussiness_name') }}"
                            value="{{ old('bussiness_name', config('settings.bussiness_name')) }}">
                        @error('bussiness_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-4 form-group">
                        <label for="warning_quantity">{{ __('settings.business_email') }}</label>
                        <input type="text" name="business_email"
                            class="form-control @error('business_email') is-invalid @enderror" id="business_email"
                            placeholder="{{ __('settings.business_email') }}"
                            value="{{ old('business_email', config('settings.business_email')) }}">
                        @error('business_email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-4 form-group">
                        <label for="warning_quantity">{{ __('settings.business_phone') }}</label>
                        <input type="text" name="business_phone"
                            class="form-control @error('business_phone') is-invalid @enderror" id="business_phone"
                            placeholder="{{ __('settings.business_phone') }}"
                            value="{{ old('business_phone', config('settings.business_phone')) }}">
                        @error('business_phone')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-12 d-flex">
                    <div class="col-4 form-group">
                        <label for="warning_quantity">{{ __('settings.business_address') }}</label>
                        <input type="text" name="business_address"
                            class="form-control @error('business_address') is-invalid @enderror" id="business_address"
                            placeholder="{{ __('settings.business_address') }}"
                            value="{{ old('business_address', config('settings.business_address')) }}">
                        @error('business_address')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-4 form-group">
                        <label for="warning_quantity">{{ __('settings.business_ruc') }}</label>
                        <input type="text" name="business_ruc"
                            class="form-control @error('business_ruc') is-invalid @enderror" id="business_ruc"
                            placeholder="{{ __('settings.business_ruc') }}"
                            value="{{ old('business_ruc', config('settings.business_ruc')) }}">
                        @error('business_ruc')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-4 form-group">
                        <label for="warning_quantity">{{ __('settings.business_dv') }}</label>
                        <input type="text" name="business_dv"
                            class="form-control @error('business_dv') is-invalid @enderror" id="business_dv"
                            placeholder="{{ __('settings.business_dv') }}"
                            value="{{ old('business_dv', config('settings.business_dv')) }}">
                        @error('business_dv')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-12 d-flex">
                    <div class="col-6 form-group">
                        <label for="currency_symbol">{{ __('settings.Currency_symbol') }}</label>
                        <input type="text" name="currency_symbol"
                            class="form-control @error('currency_symbol') is-invalid @enderror" id="currency_symbol"
                            placeholder="{{ __('settings.Currency_symbol') }}"
                            value="{{ old('currency_symbol', config('settings.currency_symbol')) }}">
                        @error('currency_symbol')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    {{-- <div class="col-4 form-group">
                        <label for="warning_quantity">{{ __('settings.warning_quantity') }}</label>
                        <input type="text" name="warning_quantity"
                            class="form-control @error('warning_quantity') is-invalid @enderror" id="warning_quantity"
                            placeholder="{{ __('settings.warning_quantity') }}"
                            value="{{ old('warning_quantity', config('settings.warning_quantity')) }}">
                        @error('warning_quantity')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div> --}}

                    <div class="col-6 form-group">
                        <label for="warning_quantity">{{ __('settings.tax_percentage') }}</label>
                        <input type="text" name="tax_percentage"
                            class="form-control @error('tax_percentage') is-invalid @enderror" id="tax_percentage"
                            placeholder="{{ __('settings.tax_percentage') }}"
                            value="{{ old('tax_percentage', config('settings.tax_percentage')) }}">
                        @error('tax_percentage')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-12 d-flex">
                    <div class="col-4 form-group">
                        <label for="bussiness_printer">{{ __('settings.bussiness_printer') }}</label>
                        <input type="text" name="bussiness_printer"
                            class="form-control @error('bussiness_printer') is-invalid @enderror" id="bussiness_printer"
                            placeholder="{{ __('settings.bussiness_printer') }}"
                            value="{{ old('bussiness_printer', config('settings.bussiness_printer')) }}">
                        @error('bussiness_printer')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="ml-4 btn btn-primary">{{ __('settings.Change_Setting') }}</button>
            </form>
        </div>
    </div>
@endsection

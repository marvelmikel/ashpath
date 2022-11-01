@extends('admin.layouts.master')
@section('title', 'General Settings')

@php
    //dd( getEscrowWallet() );
@endphp

@section('head_style')
    <link rel="stylesheet" type="text/css" href="{{ asset('public/backend/tagify/tagify.css') }}">
@endsection

@section('page_content')

    <!-- Main content -->
    <div class="row">
        <div class="col-md-3 settings_bar_gap">
            @include('admin.common.settings_bar')
        </div>
        <div class="col-md-9">
            <div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">Admin Security Form</h3>
                </div>

                <form action="{{ url(\Config::get('adminPrefix').'/settings/escrow') }}" method="post" class="form-horizontal" id="">
                    {!! csrf_field() !!}
                    <div class="box-body">

                        <div class="form-group">
                            <label class="col-sm-4 control-label" for="inputEmail3">Escrow Wallet Id</label>
                            <div class="col-sm-6">
                                <input type="text" name="escrow_wallet_id" class="form-control" id="escrow_wallet_id" value="{{ !empty($result['escrow_wallet_id']) ? $result['escrow_wallet_id'] : '' }}" placeholder="Escrow Wallet Id">
                            </div>
                        </div>

                       

                        <button type="submit" class="btn btn-theme pull-right"
                            id="escrow_settings-submit">
                            <i class="fa fa-spinner fa-spin" style="display: none;"></i> <span
                                id="escrow-settings-submit-text">Submit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('extra_body_scripts')
    <script src="{{ asset('public/dist/js/jquery.validate.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/dist/js/jquery-validation-1.17.0/dist/additional-methods.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/backend/tagify/tagify.min.js') }}" type="text/javascript"></script>
  <script src="{{ asset('public/backend/tagify/jQuery.tagify.min.js') }}" type="text/javascript"></script>
  
    <script type="text/javascript">
        $(window).on('load', function() {
            $(".admin_access_ip_setting, .admin_2fa").select2({});
        });

    var input = document.querySelector('input[name=admin_access_ips]');
    new Tagify(input)
    </script>
@endpush

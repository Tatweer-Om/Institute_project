@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.winlos_lang',[],session('locale')) }}</title>
@endpush
<!-- ============================================================== -->
<!-- Start right Content here -->
<style>
       #percentage_win_container, #percentage_los_container {
        display: none;
    }
</style>
<!-- ============================================================== -->
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.winlos_lang',[],session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.pages_lang',[],session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.winlos_lang',[],session('locale')) }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>

            <!-- end page title -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#add_winlos_modal">
                                {{ trans('messages.add_data_lang',[],session('locale')) }}
                            </button>
                        </div>
                        <div class="card-body">

                            <table id="all_winlos" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{ trans('messages.total_trade',[],session('locale')) }}</th>
                                        <th style="text-align: center;">{{ trans('messages.win_percentage',[],session('locale')) }}</th>

                                        <th style="text-align: center;">{{ trans('messages.loss_percentage',[],session('locale')) }}</th>
                                        <th style="text-align: center;">{{ trans('messages.added_by',[],session('locale')) }}</th>

                                        <th style="text-align: center;">{{ trans('messages.actions_lang',[],session('locale')) }}</th>
                                    </tr>
                                </thead>

                                <tbody>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div> <!-- end col -->
            </div> <!-- end row -->
        </div> <!-- container-fluid -->
    </div>
    <!-- End Page-content -->

    <div class="modal fade" id="add_winlos_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" class="add_winlos" method="POST">
                        @csrf
                        <input type="hidden" class="winlos_id" name="winlos_id">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="total_trade" class="form-label">{{ trans('messages.total_trade_lang',[],session('locale')) }}</label>
                                    <input class="form-control total_trade isnumber" name="total_trade" type="text" id="total_trade">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="win" class="form-label">{{ trans('messages.win_lang',[],session('locale')) }}</label>
                                    <input class="form-control win isnumber" name="win" type="text" id="win">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="loss" class="form-label">{{ trans('messages.loss_lang',[],session('locale')) }}</label>
                                    <input class="form-control loss isnumber" name="loss" type="text" id="loss">
                                </div>
                            </div>
                        </div>

                        <!-- Win percentage input (initially hidden) -->
                        <div class="row">
                            <div class="col-md-3" id="percentage_win_container">
                                <div class="mb-3">
                                    <label for="percentage_win" class="form-label">{{ trans('messages.percentage_win_lang',[],session('locale')) }}</label>
                                    <input class="form-control percentage_win isnumber" name="percentage_win" type="text" id="percentage_win" readonly>
                                </div>
                            </div>

                            <!-- Loss percentage input (initially hidden) -->
                            <div class="col-md-3" id="percentage_los_container">
                                <div class="mb-3">
                                    <label for="percentage_los" class="form-label">{{ trans('messages.percentage_los_lang',[],session('locale')) }}</label>
                                    <input class="form-control percentage_los isnumber" name="percentage_los" type="text" id="percentage_los" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                    <textarea class="form-control notes" name="notes" id="notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                    <button type="submit" class="btn btn-primary submit_form">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    @include('layouts.footer_content')
</div>
<!-- end main content-->

</div>
<!-- END layout-wrapper -->
@include('layouts.footer')
@endsection


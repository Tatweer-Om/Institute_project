@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.teacher_lang',[],session('locale')) }}</title>
@endpush
<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.teacher_lang',[],session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.pages_lang',[],session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.teacher_lang',[],session('locale')) }}</li>
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
                            <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#add_teacher_modal">
                                {{ trans('messages.add_data_lang',[],session('locale')) }}
                            </button>
                        </div>
                        <div class="card-body">

                            <table id="all_teacher" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">#</th>
                                        <th style="text-align: center;">{{ trans('messages.teacher_lang',[],session('locale')) }}</th>
                                        <th style="text-align: center;">{{ trans('messages.contact_lang',[],session('locale')) }}</th>
                                        <th style="text-align: center;">{{ trans('messages.notes_lang',[],session('locale')) }}</th>
                                        <th style="text-align: center;">{{ trans('messages.added_by_lang',[],session('locale')) }}</th>
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

    <div class="modal fade" id="add_teacher_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" class="add_teacher" method="POST" >
                        @csrf
                        <input type="hidden" class="teacher_id" name="teacher_id">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">{{ trans('messages.first_name_lang',[],session('locale')) }}</label>
                                    <input class="form-control first_name" name="first_name" type="text" id="first_name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="second_name" class="form-label">{{ trans('messages.second_name_lang',[],session('locale')) }}</label>
                                    <input class="form-control second_name" name="second_name" type="text" id="second_name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">{{ trans('messages.last_name_lang',[],session('locale')) }}</label>
                                    <input class="form-control last_name" name="last_name" type="text" id="last_name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="civil_number" class="form-label">{{ trans('messages.civil_number',[],session('locale')) }}</label>
                                    <input class="form-control civil_number isnumber" name="civil_number" type="text" id="civil_number">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="teacher_number" class="form-label">{{ trans('messages.teacher_number',[],session('locale')) }}</label>
                                    <input class="form-control teacher_number isnumber" name="teacher_number" type="text" id="teacher_number">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="teacher_email" class="form-label">{{ trans('messages.teacher_email',[],session('locale')) }}</label>
                                    <input class="form-control teacher_email" name="teacher_email" type="text" id="teacher_email">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                    <textarea class="form-control notes" name="notes" id="notes" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="signature" class="form-label">{{ trans('messages.signature_lang', [], session('locale')) }}</label>
                                    <canvas id="signature-pad" class="signature-pad" width="300" height="100" style="border: 1px solid #000;"></canvas>
                                    <br>
                                    <button type="button" id="clear-signature" class="btn btn-warning">{{ trans('messages.clear_signature', [], session('locale')) }}</button>
                                    <input type="hidden" name="signature" id="signature" class="form-control">
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


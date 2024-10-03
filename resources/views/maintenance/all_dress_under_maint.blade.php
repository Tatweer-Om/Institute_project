@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.maintenance_lang',[],session('locale')) }}</title>
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
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.maintenance_lang',[],session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.pages_lang',[],session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.maintenance_lang',[],session('locale')) }}</li>
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
                            {{-- <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#add_maint_modal">
                                {{ trans('messages.add_data_lang',[],session('locale')) }}
                            </button> --}}
                        </div>
                        <div class="card-body">

                            <table id="all_maint" class="table table-bordered dt-responsive  nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>{{ trans('messages.dress_detail_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.maint_issue_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.issue_notes_lang',[],session('locale')) }}</th>
                                        <th>{{ trans('messages.added_by_lang',[],session('locale')) }}</th>
                                        {{-- <th>{{ trans('messages.created_at_lang',[],session('locale')) }}</th> --}}
                                        <th>{{ trans('messages.actions_lang',[],session('locale')) }}</th>
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

    <!-- Static Backdrop Modal -->
   <!-- Add Maintenance Modal -->
<div class="modal fade" id="maint_complete_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addMaintLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaintLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="#" method="POST" class="maint_comp" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" class="maint_id" name="maint_id">
                    <div class="mb-3">
                        <label for="maint_cost" class="form-label">{{ trans('messages.maint_cost_lang',[],session('locale')) }}</label>
                        <input class="form-control isnumber" name="maint_cost" type="text" id="maint_cost">
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                <textarea class="form-control notes" name="notes" rows="5"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                        <button type="submit" class="btn btn-primary">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Return Maintenance Modal -->








<!-- end main content-->

</div>
<!-- END layout-wrapper -->
@include('layouts.footer')
@endsection


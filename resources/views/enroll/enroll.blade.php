@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.enrollment',[],session('locale')) }}</title>
@endpush

<div class="main-content">

    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.enrollment',[],session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.enrollment',[],session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.enrollment_list',[],session('locale')) }}</li>
                            </ol>
                        </div>

                    </div>
                </div>

            </div>
<form action="" class="enroll_list">
    @csrf
    <div class="card">
        <div class="row">
            <div class="col-lg-4">
                <div class="card-body">
                    <div class="d-grid">
                        <label for="course_id" class="form-label">{{ trans('messages.course_id_lang',[],session('locale')) }}</label>
                        <select class="form-control course_id" name="course_id">
                            <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                            @foreach ($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" id="enroll_id" class="enroll_id" name="enroll_id" hidden>
                    <div id="external-events" class="mt-2">
                        <br>
                        <p class="text-muted">{{ trans('messages.course_details',[],session('locale')) }}</p>
                        <div class="external-event fc-event text-success bg-success-subtle" data-class="bg-success">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>{{ trans('messages.course_start_date',[],session('locale')) }}:
                            <span id="start_date"></span>
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                            {{ trans('messages.course_end_date',[],session('locale')) }} <span id="end_date"></span>
                        </div>
                        <div class="external-event fc-event text-info bg-info-subtle" data-class="bg-info">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>{{ trans('messages.duration_month',[],session('locale')) }}
                            : <span id="duration_months"></span>
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>{{ trans('messages.hours',[],session('locale')) }}: <span id="duration_hours"></span>
                        </div>
                        <div class="external-event fc-event text-warning bg-warning-subtle" data-class="bg-warning">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>{{ trans('messages.start_time',[],session('locale')) }}: <span id="start_time"></span>
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>{{ trans('messages.end_time',[],session('locale')) }}: <span id="end_time"></span>
                        </div>
                        <div class="external-event fc-event text-danger bg-danger-subtle" data-class="bg-danger">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>{{ trans('messages.original_price',[],session('locale')) }}: <span id="course_price"></span>
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>{{ trans('messages.discounted_price',[],session('locale')) }}: <span id="discounted_price"></span>

                        </div>
                        <div class="external-event fc-event text-dark bg-dark-subtle" data-class="bg-dark">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>{{ trans('messages.teacher',[],session('locale')) }}: <span id="teacher"></span>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5">
                        <div class="col-lg-12 col-sm-6">
                            <img src="assets/images/undraw-calendar.svg" alt="" class="img-fluid d-block">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-body">
                    <div class="d-grid">
                        <label for="student_id" class="form-label">{{ trans('messages.student_id_lang',[],session('locale')) }}</label>

                        <div class="row">
                            <div class="col-lg-8">
                                <select class="form-control student_id" name="student_id" >
                                    <option value="">{{ trans('messages.choose_lang',[],session('locale')) }}</option>
                                    @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->full_name ?? '' }}( Phone:{{ $student->student_number ?? '' }})</option>
                                    @endforeach

                                </select>
                            </div>
                            <div class="col-lg-4">
                               <a type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add_student_modal">{{ trans('messages.add_student',[],session('locale')) }}</a>
                            </div>
                        </div>


                    </div>

                    <div id="external-events" class="mt-2">
                        <br>
                        <p class="text-muted">{{ trans('messages.student_details',[],session('locale')) }}</p>
                        <div class="external-event fc-event text-success bg-success-subtle" data-class="bg-success">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                            {{ trans('messages.student_name',[],session('locale')) }}: <span id="full_name"></span>
                        </div>
                        <div class="external-event fc-event text-info bg-info-subtle" data-class="bg-info">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                            {{ trans('messages.phone',[],session('locale')) }}: <span id="phone"></span>
                        </div>
                        <div class="external-event fc-event text-warning bg-warning-subtle" data-class="bg-warning">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                            {{ trans('messages.email',[],session('locale')) }}: <span id="email"></span>

                        </div>
                        <div class="external-event fc-event text-danger bg-danger-subtle" data-class="bg-danger">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                            {{ trans('messages.civil_number',[],session('locale')) }}: <span id="civil_number"></span>
                        </div>
                        <div class="external-event fc-event text-dark bg-dark-subtle" data-class="bg-dark">
                            <i class="mdi mdi-checkbox-blank-circle font-size-11 me-2"></i>
                            {{ trans('messages.dob_lang',[],session('locale')) }}: <span id="dob"></span>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-5">
                        <div class="col-lg-12 col-sm-6">
                            <img src="assets/images/undraw-calendar.svg" alt="" class="img-fluid d-block">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card-body">
                    <div class="d-grid">
                        <label for="student_id" class="form-label">{{ trans('messages.special_discount_lang',[],session('locale')) }}</label>

                        <div class="row">
                            <div class="col-lg-8">
                                <input class="form-control discount isnumber" name="discount" type="text" id="discount">


                            </div>
                            <div class="col-lg-4">
                                <button class="btn btn-info" type="submit">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                             </div>

                        </div>



                        <div class="card mt-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3">{{ trans('messages.current_offers',[],session('locale')) }}</h5>

                                <div class="list-group list-group-flush">
                                    <!-- Offers will be populated here by AJAX -->
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>

                    <div class="row justify-content-center mt-5">
                        <div class="col-lg-12 col-sm-6">
                            <img src="assets/images/undraw-calendar.svg" alt="" class="img-fluid d-block">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>


        <!-- New Row for the Second Card Body -->

    </div>

</form>


            <div class="table-responsive mb-4">
                <table class="table align-middle datatable dt-responsive table-check nowrap " id="all_enroll" style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;">
                    <thead>
                      <tr>
                        <th scope="col" style="text-align: center;">{{ trans('messages.sr_number', [], session('locale')) }}</th>
                        <th scope="col" style="text-align: center;">{{ trans('messages.student_detail', [], session('locale')) }}</th>
                        <th scope="col" style="text-align: center;">{{ trans('messages.course_detail', [], session('locale')) }}</th>
                        <th scope="col" style="text-align: center;">{{ trans('messages.price_detail', [], session('locale')) }}</th>
                        <th scope="col" style="text-align: center;">{{ trans('messages.added_by', [], session('locale')) }}</th>
                        <th style="width: 80px; min-width: 80px; text-align: center;">{{ trans('messages.action', [], session('locale')) }}</th>

                      </tr>
                    </thead>
                    <tbody>


                    </tbody>
                </table>
                <!-- end table -->
            </div>
            <!-- end table responsive -->

        </div> <!-- container-fluid -->
    </div>


    <div class="modal fade" id="add_student_modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{ trans('messages.add_data_lang',[],session('locale')) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" class="add_student" method="POST" >
                        @csrf
                        <input type="hidden" class="student_id" name="student_id">
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
                                    <label for="student_number" class="form-label">{{ trans('messages.student_number',[],session('locale')) }}</label>
                                    <input class="form-control student_number isnumber" name="student_number" type="text" id="student_number">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="student_email" class="form-label">{{ trans('messages.student_email',[],session('locale')) }}</label>
                                    <input class="form-control student_email" name="student_email" type="text" id="student_email">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="dob" class="form-label">{{ trans('messages.dob',[],session('locale')) }}</label>
                                    <input class="form-control dob datepick" name="dob" type="text" id="dob">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div>
                                     <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" value="1" name="gender" id="male" checked="">
                                        <label class="form-check-label" for="male">
                                            {{ trans('messages.male_lang',[],session('locale')) }}
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" value="2" name="gender" id="female">
                                        <label class="form-check-label" for="female">
                                            {{ trans('messages.female_lang',[],session('locale')) }}
                                        </label>
                                    </div>
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

@include('layouts.footer')
@endsection


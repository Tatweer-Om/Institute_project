@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.dashboard', [], session('locale')) }}</title>
@endpush

<div class="main-content">



    <div class="page-content">
        <div class="container-fluid">

            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0 font-size-18">{{ trans('messages.dashboard', [], session('locale')) }}</h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('messages.dashboard', [], session('locale')) }}</a></li>
                                <li class="breadcrumb-item active">{{ trans('messages.dashboard', [], session('locale')) }}</li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-h-100">
                        <!-- card body -->
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">{{ trans('messages.total_students', [], session('locale')) }}</span>
                                    <h4 class="mb-3">
                                        <span class="counter-value" data-target="{{ $student_count ?? '' }}"></span>
                                    </h4>
                                </div>

                                <div class="col-6">
                                    <div id="mini-chart1" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                </div>
                            </div>
                            <div class="text-nowrap">
                                <span class="badge bg-success-subtle text-success">{{ $one_student ?? '' }}</span>
                                <span class="ms-1 text-muted font-size-13">{{ trans('messages.latest_added_student', [], session('locale')) }}</span>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-h-100">
                        <!-- card body -->
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">{{ trans('messages.number_of_teachers', [], session('locale')) }}</span>
                                    <h4 class="mb-3">
                                        <span class="counter-value" data-target="{{ $teacher_count ?? '' }}">{{ $teacher_count ?? '' }}</span>
                                    </h4>
                                </div>
                                <div class="col-6">
                                    <div id="mini-chart2" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                </div>
                            </div>
                            <div class="text-nowrap">
                                <span class="badge bg-danger-subtle text-danger">{{ $one_teacher ?? '' }}</span>
                                <span class="ms-1 text-muted font-size-13">{{ trans('messages.latest_added_teacher', [], session('locale')) }}</span>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col-->

                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-h-100">
                        <!-- card body -->
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">{{ trans('messages.total_enrollments', [], session('locale')) }}</span>
                                    <h4 class="mb-3">
                                        <span class="counter-value" data-target="{{ $enrollment_count ?? '' }}"></span>
                                    </h4>
                                </div>
                                <div class="col-6">
                                    <div id="mini-chart3" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                </div>
                            </div>
                            <div class="text-nowrap">
                                <span class="badge bg-success-subtle text-success">{{ $one_enrollment ?? '' }}</span>
                                <span class="ms-1 text-muted font-size-13">{{ trans('messages.latest_enrollment', [], session('locale')) }}</span>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->

                <div class="col-xl-3 col-md-6">
                    <!-- card -->
                    <div class="card card-h-100">
                        <!-- card body -->
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-6">
                                    <span class="text-muted mb-3 lh-1 d-block text-truncate">{{ trans('messages.total_courses', [], session('locale')) }}</span>
                                    <h4 class="mb-3">
                                        <span class="counter-value" data-target="{{ $course_count ?? '' }}"></span>
                                    </h4>
                                </div>
                                <div class="col-6">
                                    <div id="mini-chart4" data-colors='["#5156be"]' class="apex-charts mb-2"></div>
                                </div>
                            </div>
                            <div class="text-nowrap">
                                <span class="badge bg-success-subtle text-success">{{ $one_course ?? '' }}</span>
                                <span class="ms-1 text-muted font-size-13">{{ trans('messages.latest_added_course', [], session('locale')) }}</span>
                            </div>
                        </div><!-- end card body -->
                    </div><!-- end card -->
                </div><!-- end col -->
            </div><!-- end row-->




            <div class="row">

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">{{ trans('messages.total_students', [], session('locale')) }}</h4> <br>

                        </div><!-- end card header -->

                        <div class="card-body px-0">
                            <div class="tab-content">
                                <div class="tab-pane active" id="transactions-all-tab" role="tabpanel">
                                    <div class="table-responsive px-3" data-simplebar style="max-height: 352px;">
                                        <table class="table align-middle table-nowrap table-borderless">

                                            <tbody>
                                                @foreach ($latest_students as $student)


                                                <tr>
                                                    <td style="width: 50px;">
                                                        <div class="font-size-22 text-success">
                                                            <i class="bx bx-down-arrow-circle d-block"></i>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div>
                                                            <h5 class="font-size-14 mb-1">{{ trans('messages.student_name', [], session('locale')) }}</h5>
                                                            <p class="text-muted mb-0 font-size-12">{{ $student->first_name ?? '' }}</p>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="text-end">
                                                            <h5 class="font-size-14 mb-1">{{ trans('messages.student_number', [], session('locale')) }}</h5>
                                                            <p class="text-muted mb-0 font-size-12">{{ $student->student_number ?? '' }}</p>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="text-end">
                                                            <h5 class="font-size-14  mb-1">{{ trans('messages.student_email', [], session('locale')) }}</h5>
                                                            <p class="text-muted mb-0 font-size-12">{{ $student->student_email ?? '' }}</p>
                                                        </div>
                                                    </td>
                                                </tr>

                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                            </div>
                            <!-- end tab content -->
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">{{ trans('messages.latest_enrollment', [], session('locale')) }}</h4>

                        </div><!-- end card header -->

                        <div class="card-body px-0">
                            <div class="tab-content">
                                <div class="tab-pane active" id="transactions-all-tab" role="tabpanel">
                                    <div class="table-responsive px-3" data-simplebar style="max-height: 352px;">
                                        <table class="table align-middle table-nowrap table-borderless">
                                            <tbody>
                                                @foreach ($latest_enrollments as $enroll)


                                                <tr>
                                                    <td style="width: 50px;">
                                                        <div class="font-size-22 text-success">
                                                            <i class="bx bx-down-arrow-circle d-block"></i>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div>
                                                            <h5 class="font-size-14 mb-1">{{ trans('messages.student_name', [], session('locale')) }}</h5>
                                                            <p class="text-muted mb-0 font-size-12">{{ $enroll->student_name ?? '' }}</p>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="text-end">
                                                            <h5 class="font-size-14 mb-1">{{ trans('messages.course_name', [], session('locale')) }}</h5>
                                                            <p class="text-muted mb-0 font-size-12">{{ $enroll->course_name ?? '' }}</p>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="text-end">
                                                            <h5 class="font-size-14 mb-1">{{ trans('messages.added_on', [], session('locale')) }}</h5>
                                                            <p class="text-muted mb-0 font-size-12">{{ $enroll->created_at ? \Carbon\Carbon::parse($enroll->created_at)->format('Y-m-d') : '' }}</p>
                                                        </div>
                                                    </td>
                                                </tr>

                                                @endforeach

                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                            </div>
                            <!-- end tab content -->
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-header align-items-center d-flex">
                            <h4 class="card-title mb-0 flex-grow-1">{{ trans('messages.latest_courses', [], session('locale')) }}</h4>

                        </div><!-- end card header -->

                        <div class="card-body px-0">
                            <div class="tab-content">
                                <div class="tab-pane active" id="transactions-all-tab" role="tabpanel">
                                    <div class="table-responsive px-3" data-simplebar style="max-height: 352px;">
                                        <table class="table align-middle table-nowrap table-borderless">
                                            <tbody>
                                                @foreach ($latest_courses as $course)


                                                <tr>
                                                    <td style="width: 50px;">
                                                        <div class="font-size-22 text-success">
                                                            <i class="bx bx-down-arrow-circle d-block"></i>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div>
                                                            <h5 class="font-size-14 mb-1">{{ trans('messages.course_name', [], session('locale')) }}</h5>
                                                            <p class="text-muted mb-0 font-size-12">{{ $course->course_name ?? '' }}</p>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="text-end">
                                                            <h5 class="font-size-14 mb-1">{{ trans('messages.course_price', [], session('locale')) }}</h5>
                                                            <p class="text-muted mb-0 font-size-12">{{ $course->course_price ?? '' }}</p>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <div class="text-end">
                                                            <h5 class="font-size-14  mb-1">{{ trans('messages.course_dates', [], session('locale')) }}</h5>
                                                            <p class="text-muted mb-0 font-size-12">{{ $course->start_date ?? '' }} <br>
                                                                {{ $course->end_date ?? '' }}

                                                            </p>
                                                        </div>
                                                    </td>
                                                </tr>

                                                @endforeach


                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                            </div>
                            <!-- end tab content -->
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->
                </div>

                <!-- end col -->

                <!-- end col -->
            </div><!-- end row -->
        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->


    @include('layouts.footer_content')
</div>
<!-- end main content-->

@include('layouts.footer')
@endsection

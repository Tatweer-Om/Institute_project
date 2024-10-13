@extends('layouts.header')

@section('main')
@push('title')
<title> {{ trans('messages.setting',[],session('locale')) }}</title>
@endpush


    <div class="main-content">

        <div class="page-content">
            <div class="container-fluid">

                <!-- start page title -->
                <div class="row">
                    <div class="col-12">
                        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                            <h4 class="mb-sm-0 font-size-18">{{ trans('messages.setting',[],session('locale')) }}  </h4>

                            <div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="#"> {{ trans('messages.setting',[],session('locale')) }} </a></li>
                                    <li class="breadcrumb-item active"><a >{{ trans('messages.setting',[],session('locale')) }}</a></li>
                                </ol>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- end page title -->

                <div class="row">
                    <div class="col-lg-12">

                        <div class="row">
                            <div class="col-xl-12">
                                <div class="card">
                                    <div class="card-header align-items-center d-flex" >
                                        {{-- <h4 class="card-title mb-0 flex-grow-1">Customer Booking Details  </h4> --}}
                                        <div class="flex-shrink-0">
                                            <ul class="nav justify-content-end nav-tabs-custom rounded card-header-tabs" role="tablist">
                                                <li class="nav-item">
                                                    <a class="nav-link active" data-bs-toggle="tab" href="#setting1" role="tab">
                                                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                                        <span class="d-none d-sm-block"> {{ trans('messages.about_company',[],session('locale')) }}</span>
                                                    </a>
                                                </li>


                                            </ul>
                                        </div>
                                    </div><!-- end card header -->

                                    <div class="card-body">
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <!-- First Tab Pane -->
                                            <div class="tab-pane fade show active" id="setting1" role="tabpanel">
                                                {{-- <a href="#" class="btn btn-success">إضافة مستندات</a> --}}
                                                <div class="table-responsive">
                                                    <div class="table-responsive">
                                                        <div class="table align-middle dt-responsive table-check nowrap" style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;" id="all_profile_docs_2">
                                                            <form action="#" class="add_setting" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                <input type="hidden" class="setting_id" value="{{$setting->id ?? ''}}" name="setting_id">
                                                                <div class="row">
                                                                    <div class="col-md-8">

                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <div class="mb-3">
                                                                                    <label for="company_name" class="form-label">{{ trans('messages.company_name_lang',[],session('locale')) }}</label>
                                                                                    <input class="form-control company_name" value="{{ $setting->company_name ?? '' }}" name="company_name" type="text" id="company_name">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="mb-3">
                                                                                    <label for="company_email" class="form-label">{{ trans('messages.company_email_lang',[],session('locale')) }}</label>
                                                                                    <input class="form-control company_email" value="{{ $setting->company_email ?? '' }}" name="company_email" type="text" id="company_email">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="mb-3">
                                                                                    <label for="company_phone" class="form-label">{{ trans('messages.company_phone_lang',[],session('locale')) }}</label>
                                                                                    <input class="form-control company_phone isnumber" value="{{ $setting->company_phone ?? '' }}" name="company_phone" type="text" id="company_phone">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="mb-3">
                                                                                    <label for="company_cr" class="form-label">{{ trans('messages.company_cr_lang',[],session('locale')) }}</label>
                                                                                    <input class="form-control company_cr" value="{{ $setting->company_cr ?? '' }}" name="company_cr" type="text" id="company_cr">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <div class="mb-3">
                                                                                    <label for="company_address" class="form-label">{{ trans('messages.company_address_lang',[],session('locale')) }}</label>
                                                                                    <input class="form-control company_address" value="{{ $setting->company_address ?? '' }}" name="company_address" type="text" id="company_address">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="mb-3">
                                                                                    <label for="notes" class="form-label">{{ trans('messages.notes_lang',[],session('locale')) }}</label>
                                                                                    <textarea class="form-control notes"  name="notes" rows="5">{{ $setting->notes ?? '' }}</textarea>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div class="row">
                                                                            <div class="col-md-7 text-center cursor-pointer" id="ad_cover_container">
                                                                                <!-- Show existing image or a placeholder -->
                                                                                <img src="{{ isset($setting->logo) ? asset('images/logo/' . $setting->logo) : asset('custom_images/dummy/cover-image-icon.png') }}"
                                                                                     id="ad_cover_preview"
                                                                                     class="img-fluid"
                                                                                     alt="Cover Preview">
                                                                            </div>
                                                                            <input type="file" name="logo" id="ad_cover" accept="image/*" class="d-none">
                                                                        </div>
                                                                    </div>


                                                                </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                                                            <button type="submit" class="btn btn-primary submit_form">{{ trans('messages.submit_lang',[],session('locale')) }}</button>
                                                        </div>
                                                        </form>

                                                        </table>
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- Second Tab Pane -->
                                            <div class="tab-pane fade" id="setting2" role="tabpanel">
                                                {{-- <a href="#" class="btn btn-success">إضافة مستندات</a> --}}
                                                <div class="table-responsive">
                                                    <div class="table align-middle dt-responsive table-check nowrap" style="border-collapse: collapse; border-spacing: 0 8px; width: 100%;" id="all_profile_docs_2">
                                                        <form  class="add_avail">
                                                            @csrf
                                                            <input type="hidden" value="{{ $setting->id ?? ''}}" class="setting_id" name="setting_id">
                                                            <div class="row">
                                                                <div class="col-md-8">

                                                                    <div class="row">

                                                                        <div class="col-md-4">
                                                                            <div class="mb-3">
                                                                                <label for="dress_avail" class="form-label">{{ trans('messages.dress_avail_lang',[],session('locale')) }}</label>
                                                                                <input class="form-control dress_avail isnumber" value="{{ $setting->dress_available ?? '' }}" name="dress_avail" type="text" id="dress_avail">
                                                                            </div>
                                                                        </div>

                                                                    </div>

                                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('messages.close_lang',[],session('locale')) }}</button>
                                                                        <button type="submit" class="btn btn-primary submit_form">{{ trans('messages.submit_lang',[],session('locale')) }}</button>



                                                                </div>


                                                            </div>
                                                    </div>

                                                    </form>

                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end card-body -->

                                </div><!-- end card -->
                            </div><!-- end col -->
                        </div>
                    </div>


                </div>

            </div>
            <!-- end row -->
            <!-- end row -->
        </div> <!-- container-fluid -->
    </div>




    @include('layouts.footer')
@endsection

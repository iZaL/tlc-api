@extends('layouts.app')

@section('scripts')
    @parent
    <script src="/admin/custom/fullcalendar/fullcalendar.bundle.js" type="text/javascript"></script>
    <script src="/admin/js/dashboard.js" type="text/javascript"></script>
@endsection

@section('content')
    <div class="m-portlet  m-portlet--unair">
        <div class="m-portlet__body  m-portlet__body--no-padding">
            <div class="row m-row--no-padding m-row--col-separator-xl">
                <div class="col-md-12 col-lg-6 col-xl-3">
                    <div class="m-widget24">
                        <div class="m-widget24__item">
                            <h4 class="m-widget24__title">
                                Total Frofit
                            </h4><br>
                            <span class="m-widget24__desc">
                                All Customs Value
                            </span>
                            <span class="m-widget24__stats m--font-brand">
                                $18M
                            </span>
                            <div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
                                <div class="progress-bar m--bg-brand" role="progressbar" style="width: 78%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="m-widget24__change">
                                Change
                            </span>
                            <span class="m-widget24__number">
                                78%
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-lg-6 col-xl-3">

                    <div class="m-widget24">
                        <div class="m-widget24__item">
                            <h4 class="m-widget24__title">
                                New Feedbacks
                            </h4><br>
                            <span class="m-widget24__desc">
                                Customer Review
                            </span>
                            <span class="m-widget24__stats m--font-info">
                                1349
                            </span>
                            <div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
                                <div class="progress-bar m--bg-info" role="progressbar" style="width: 84%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="m-widget24__change">
                                Change
                            </span>
                            <span class="m-widget24__number">
                                84%
                            </span>
                        </div>
                    </div>

                </div>
                <div class="col-md-12 col-lg-6 col-xl-3">

                    <div class="m-widget24">
                        <div class="m-widget24__item">
                            <h4 class="m-widget24__title">
                                New Orders
                            </h4><br>
                            <span class="m-widget24__desc">
                                Fresh Order Amount
                            </span>
                            <span class="m-widget24__stats m--font-danger">
                                567
                            </span>
                            <div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
                                <div class="progress-bar m--bg-danger" role="progressbar" style="width: 69%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="m-widget24__change">
                                Change
                            </span>
                            <span class="m-widget24__number">
                                69%
                            </span>
                        </div>
                    </div>

                </div>
                <div class="col-md-12 col-lg-6 col-xl-3">

                    <div class="m-widget24">
                        <div class="m-widget24__item">
                            <h4 class="m-widget24__title">
                                New Users
                            </h4><br>
                            <span class="m-widget24__desc">
                                Joined New User
                            </span>
                            <span class="m-widget24__stats m--font-success">
                                276
                            </span>
                            <div class="m--space-10"></div>
                            <div class="progress m-progress--sm">
                                <div class="progress-bar m--bg-success" role="progressbar" style="width: 90%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <span class="m-widget24__change">
                                Change
                            </span>
                            <span class="m-widget24__number">
                                90%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
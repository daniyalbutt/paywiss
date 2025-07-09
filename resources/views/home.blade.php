@extends('layouts.front-app')
@section('title', 'Dashboard')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-9 col-xxl-9">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header flex-wrap border-0 pb-0 align-items-end">
                            <div class="mb-3 me-3">
                                <h5 class="fs-20 text-black font-w500">Last Payment</h5>
                                <span class="text-num text-black fs-30 font-w500">
                                    {{ $last_payment != null ? $last_payment->price : '0' }}
                                </span>
                            </div>
                            <div class="me-3 mb-3">
                                <p class="fs-14 mb-1">VALID THRU</p>
                                <span class="text-black fs-15">
                                    {{ $last_payment != null ? date('m/Y', strtotime($last_payment->updated_at)) : '0' }}
                                </span>
                            </div>
                            <div class="me-3 mb-3">
                                <p class="fs-14 mb-1">CARD HOLDER</p>
                                <span class="text-black fs-15">{{ $last_payment != null ? $last_payment->client->name : '' }}</span>
                            </div>
                            <span class="fs-18 text-black font-w500 me-3 mb-3">{{ $last_payment != null ? $last_payment->getCard() : ' ' }}</span>
                        </div>
                        <div class="card-body">
                            <div class="progress default-progress">
                                <div class="progress-bar bg-gradient-5 progress-animated" style="width: 50%; height:20px;" role="progressbar">
                                    <span class="sr-only">50% Complete</span>
                                </div>
                            </div>
                            <div class="row mt-4 pt-3">
                                <div class="col-xl-6 col-xxl-5 col-lg-6">
                                    <div class="row">
                                        <div class="col-sm-6 col-7">
                                            <h4 class="card-title">Monthly Summary</h4>
                                            <ul class="card-list mt-3">
                                                <li class="mb-2"><span class="bg-success circle"></span><span class="ms-0">Completed</span><span class="text-black fs-18">{{$completed_percentage}}%</span></li>
                                                <li class="mb-2"><span class="bg-light circle"></span><span class="ms-0">Pending</span><span class="text-black fs-18">{{$pending_percentage}}%</span></li>
                                                <li class="mb-2"><span class="bg-danger circle"></span><span class="ms-0">Declined</span><span class="text-black fs-18">{{$declined_percentage}}%</span></li>
                                            </ul>
                                        </div>
                                        <div class="col-sm-6 col-5">
                                            <canvas id="pieChart" data-one="{{$completed_percentage}}" data-two="{{$pending_percentage}}" data-three="{{$declined_percentage}}"></canvas>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-xxl-7 col-lg-6">
                                    <div id="line-chart" class="bar-chart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header d-block d-sm-flex border-0">
                            <div class="me-3">
                                <h4 class="card-title mb-2">Payment History</h4>
                            </div>
                        </div>
                        <div class="card-body tab-content p-0">
                            <div class="tab-pane fade active show" id="monthly" role="tabpanel">
                                <div id="accordion-one" class="accordion style-1">
                                    @foreach($data as $key => $value)
                                    <div class="accordion-item">
                                        <div class="accordion-header {{ $value->status != 2 ? 'collapsed' : '' }}" data-bs-toggle="collapse" data-bs-target="#default_collapseOne{{$value->id}}">
                                            <div class="d-flex align-items-center">
                                                <div class="user-info">
                                                    <h6 class="fs-14 font-w700 mb-0"><a href="javascript:void(0)">{{ $value->client->name }}</a></h6>
                                                    <span class="fs-14">{{ $value->client->email }}</span>
                                                </div>
                                            </div>
                                            <span>{{ $value->created_at->format('d M, Y') }} <br> {{ $value->created_at->format('g:i A') }}</span>
                                            <span>${{ $value->price }} - {{ $value->merchants != null ? $value->merchants->getMerchant() : '' }}<br>{{ $value->merchants != null ? $value->merchants->name : '' }} - {{ Illuminate\Support\Str::limit($value->client->brand->name, 20) }}</span>
                                            <span class="badge badge-primary badge-sm" onclick="withJquery('{{ route('pay', [$value->unique_id]) }}')" style="cursor: pointer;">COPY LINK</span>
                                            <a class="btn {{ $value->get_badge_status() }} btn-sm light" href="javascript:void(0);">{{ $value->get_status() }}</a>
                                            <span class="accordion-header-indicator"></span>
                                        </div>
                                        <div id="default_collapseOne{{$value->id}}" class="collapse accordion_body {{ $value->status != 2 ? '' : 'show' }}" data-bs-parent="#accordion-one">
                                            <div class="payment-details accordion-body-text">
                                                <div class="me-3 mb-3">
                                                    <p class="fs-12 mb-2">ID Payment</p>
                                                    <span class="font-w500">#{{ $value->id }}</span>
                                                </div>
                                                <div class="me-3 mb-3">
                                                    <p class="fs-12 mb-2">Payment Method</p>
                                                    <span class="font-w500">
                                                        @if($value->status == 2)
                                                        {{ $value->return_response != null ? $value->getCardBrand() : '' }}
                                                        @else
                                                        NONE
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="me-3 mb-3">
                                                    <p class="fs-12 mb-2">Invoice Date</p>
                                                    <span class="font-w500">{{ $value->created_at->format('d M, Y g:i A') }}</span>
                                                </div>
                                                <div class="me-3 mb-3">
                                                    <p class="fs-12 mb-2">Date Paid</p>
                                                    <span class="font-w500">{{ $value->updated_at->format('d M, Y g:i A') }}</span>
                                                </div>
                                                <div class="info mb-3">
                                                    <svg class="me-3" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M12 1C9.82441 1 7.69767 1.64514 5.88873 2.85384C4.07979 4.06253 2.66989 5.7805 1.83733 7.79049C1.00477 9.80047 0.786929 12.0122 1.21137 14.146C1.6358 16.2798 2.68345 18.2398 4.22183 19.7782C5.76021 21.3166 7.72023 22.3642 9.85401 22.7887C11.9878 23.2131 14.1995 22.9953 16.2095 22.1627C18.2195 21.3301 19.9375 19.9202 21.1462 18.1113C22.3549 16.3023 23 14.1756 23 12C22.9966 9.08368 21.8365 6.28778 19.7744 4.22563C17.7122 2.16347 14.9163 1.00344 12 1ZM12 21C10.22 21 8.47992 20.4722 6.99987 19.4832C5.51983 18.4943 4.36628 17.0887 3.68509 15.4442C3.0039 13.7996 2.82567 11.99 3.17294 10.2442C3.5202 8.49836 4.37737 6.89471 5.63604 5.63604C6.89472 4.37737 8.49836 3.5202 10.2442 3.17293C11.99 2.82567 13.7996 3.0039 15.4442 3.68509C17.0887 4.36627 18.4943 5.51983 19.4832 6.99987C20.4722 8.47991 21 10.22 21 12C20.9971 14.3861 20.0479 16.6736 18.3608 18.3608C16.6736 20.048 14.3861 20.9971 12 21Z" fill="#fff"/>
                                                        <path d="M12 9C11.7348 9 11.4804 9.10536 11.2929 9.29289C11.1054 9.48043 11 9.73478 11 10V17C11 17.2652 11.1054 17.5196 11.2929 17.7071C11.4804 17.8946 11.7348 18 12 18C12.2652 18 12.5196 17.8946 12.7071 17.7071C12.8947 17.5196 13 17.2652 13 17V10C13 9.73478 12.8947 9.48043 12.7071 9.29289C12.5196 9.10536 12.2652 9 12 9Z" fill="#fff"/>
                                                        <path d="M12 8C12.5523 8 13 7.55228 13 7C13 6.44771 12.5523 6 12 6C11.4477 6 11 6.44771 11 7C11 7.55228 11.4477 8 12 8Z" fill="#fff"/>
                                                    </svg>
                                                    <p class="mb-0 fs-14">{{ $value->package }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>  
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-xxl-3">
            <div class="row">
                <div class="col-md-12 col-sm-6">
                    <div class="card bg-blue action-card">
                        <div class="card-body text-white">
                            <img src="{{ asset('images/circle.png') }}" class="mb-4" alt="">
                            <h2 class="text-white fs-30">${{ $month_paid }}</h2>
                            <p class="fs-16">{{ date('F') }}</p>
                            <span>Last ID #{{ $last != null ? $last->id : '' }} - ${{ $last != null ? $last->price : '' }}</span>
                            <div class="ic-card">
                                <a href="javascript:;">
                                    <i class="bg-danger">
                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M22.1667 1.16669H5.83342C4.59574 1.16669 3.40875 1.65835 2.53358 2.53352C1.65841 3.40869 1.16675 4.59568 1.16675 5.83335V14C1.16675 14.3094 1.28966 14.6062 1.50846 14.825C1.72725 15.0438 2.024 15.1667 2.33341 15.1667H8.16675V25.6667C8.1662 25.8898 8.22965 26.1085 8.34959 26.2966C8.46952 26.4848 8.6409 26.6346 8.84342 26.7284C9.0464 26.8218 9.27195 26.855 9.49325 26.824C9.71455 26.7929 9.92228 26.699 10.0917 26.5534L13.4167 23.7067L16.7417 26.5534C16.9531 26.7341 17.222 26.8334 17.5001 26.8334C17.7782 26.8334 18.0471 26.7341 18.2584 26.5534L21.5834 23.7067L24.9084 26.5534C25.1197 26.7341 25.3887 26.8334 25.6667 26.8334C25.8355 26.8322 26.0023 26.7964 26.1567 26.7284C26.3593 26.6346 26.5306 26.4848 26.6506 26.2966C26.7705 26.1085 26.834 25.8898 26.8334 25.6667V5.83335C26.8334 4.59568 26.3418 3.40869 25.4666 2.53352C24.5914 1.65835 23.4044 1.16669 22.1667 1.16669ZM3.50008 12.8334V5.83335C3.50008 5.21452 3.74591 4.62102 4.1835 4.18344C4.62108 3.74585 5.21458 3.50002 5.83342 3.50002C6.45225 3.50002 7.04575 3.74585 7.48333 4.18344C7.92092 4.62102 8.16675 5.21452 8.16675 5.83335V12.8334H3.50008ZM24.5001 23.135L22.3417 21.28C22.1304 21.0993 21.8615 20.9999 21.5834 20.9999C21.3053 20.9999 21.0364 21.0993 20.8251 21.28L17.5001 24.1267L14.1751 21.28C13.9638 21.0993 13.6948 20.9999 13.4167 20.9999C13.1387 20.9999 12.8697 21.0993 12.6584 21.28L10.5001 23.135V5.83335C10.4986 5.01375 10.2813 4.20898 9.87008 3.50002H22.1667C22.7856 3.50002 23.3791 3.74585 23.8167 4.18344C24.2542 4.62102 24.5001 5.21452 24.5001 5.83335V23.135ZM22.1667 7.00002C22.1667 7.30944 22.0438 7.60619 21.825 7.82498C21.6062 8.04377 21.3095 8.16669 21.0001 8.16669H14.0001C13.6907 8.16669 13.3939 8.04377 13.1751 7.82498C12.9563 7.60619 12.8334 7.30944 12.8334 7.00002C12.8334 6.6906 12.9563 6.39386 13.1751 6.17506C13.3939 5.95627 13.6907 5.83335 14.0001 5.83335H21.0001C21.3095 5.83335 21.6062 5.95627 21.825 6.17506C22.0438 6.39386 22.1667 6.6906 22.1667 7.00002ZM22.1667 11.6667C22.1667 11.9761 22.0438 12.2729 21.825 12.4916C21.6062 12.7104 21.3095 12.8334 21.0001 12.8334H14.0001C13.6907 12.8334 13.3939 12.7104 13.1751 12.4916C12.9563 12.2729 12.8334 11.9761 12.8334 11.6667C12.8334 11.3573 12.9563 11.0605 13.1751 10.8417C13.3939 10.6229 13.6907 10.5 14.0001 10.5H21.0001C21.3095 10.5 21.6062 10.6229 21.825 10.8417C22.0438 11.0605 22.1667 11.3573 22.1667 11.6667ZM22.1667 16.3334C22.1667 16.6428 22.0438 16.9395 21.825 17.1583C21.6062 17.3771 21.3095 17.5 21.0001 17.5H14.0001C13.6907 17.5 13.3939 17.3771 13.1751 17.1583C12.9563 16.9395 12.8334 16.6428 12.8334 16.3334C12.8334 16.0239 12.9563 15.7272 13.1751 15.5084C13.3939 15.2896 13.6907 15.1667 14.0001 15.1667H21.0001C21.3095 15.1667 21.6062 15.2896 21.825 15.5084C22.0438 15.7272 22.1667 16.0239 22.1667 16.3334Z" fill="#fff"></path>
                                        </svg>
                                    </i>
                                    <span>Declined <br>{{ $total_declined }}</span>
                                </a>
                                <a href="javascript:;">
                                    <i class="bg-success">
                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <g opacity="0.98" clip-path="">
                                                <path d="M9.77812 2.0125C10.1062 2.69062 9.81641 3.51094 9.13828 3.83906C7.25156 4.74688 5.65469 6.15781 4.51719 7.92422C3.35234 9.73437 2.73438 11.8344 2.73438 14C2.73438 20.2125 7.7875 25.2656 14 25.2656C20.2125 25.2656 25.2656 20.2125 25.2656 14C25.2656 11.8344 24.6477 9.73437 23.4883 7.91875C22.3563 6.15234 20.7539 4.74141 18.8672 3.83359C18.1891 3.50547 17.8992 2.69063 18.2273 2.00703C18.5555 1.32891 19.3703 1.03906 20.0539 1.36719C22.4 2.49375 24.3852 4.24375 25.7906 6.44219C27.2344 8.69531 28 11.3094 28 14C28 17.7406 26.5453 21.257 23.8984 23.8984C21.257 26.5453 17.7406 28 14 28C10.2594 28 6.74297 26.5453 4.10156 23.8984C1.45469 21.2516 1.22342e-07 17.7406 1.66948e-07 14C1.99034e-07 11.3094 0.765625 8.69531 2.21484 6.44219C3.62578 4.24922 5.61094 2.49375 7.95156 1.36719C8.63516 1.04453 9.45 1.3289 9.77812 2.0125Z" fill="#fff"></path>
                                                <path d="M8.67896 13.2726C8.41099 13.0047 8.27974 12.6547 8.27974 12.3047C8.27974 11.9547 8.41099 11.6047 8.67896 11.3367L12.1188 7.89685C12.6219 7.39373 13.2891 7.12029 13.9946 7.12029C14.7 7.12029 15.3727 7.3992 15.8704 7.89685L19.3102 11.3367C19.8461 11.8726 19.8461 12.7367 19.3102 13.2726C18.7743 13.8086 17.9102 13.8086 17.3743 13.2726L15.3563 11.2547L15.3563 19.0258C15.3563 19.7804 14.7438 20.3929 13.9891 20.3929C13.2344 20.3929 12.6219 19.7804 12.6219 19.0258L12.6219 11.2492L10.604 13.2672C10.079 13.8031 9.21489 13.8031 8.67896 13.2726Z" fill="#fff"></path>
                                            </g>
                                            <defs>
                                                <clipPath id="clip11">
                                                    <rect width="28" height="28" fill="white" transform="matrix(1.19249e-08 -1 -1 -1.19249e-08 28 28)"></rect>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                    </i>
                                    <span>Completed <br> {{ $total_completed }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-sm-6">
                    <div class="card">
                        <div class="card-header d-block d-sm-flex border-0">
                            <div>
                                <h4 class="card-title mb-2">Customer List</h4>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @foreach($customer as $key => $value)
                            <div class="invoice-list">
                                <div class="me-auto">
                                    <h6 class="fs-15 font-w600 mb-0"><a href="javascript:;" class="text-black">{{ $value->name }}</a></h6>
                                </div>
                                <span class="fs-15 text-black font-w600">${{ $value->get_total_amount() }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
    function withJquery(link){
	    var temp = $("<input>");
        $("body").append(temp);
        temp.val(link).select();
        document.execCommand("copy");
        temp.remove();
        console.timeEnd('time1');
    }

    var data = {!! json_encode($graph_data->toArray()) !!};

    var data_array = [];
    var data_day = [];
    for (var i = 0; i < data.length; i++) {
        data_array.push(data[i].price);
        data_day.push(data[i].invoice_date);
    }

    console.log(data_day);

    var lineChart = function(){
		var options = {
		  	series: [{
				name: "Completed",
				data: data_array
			}],
		  	chart: {
		  		height: 170,
		  		type: 'line',
		  		toolbar:{
					show:false
		  		},
		  		zoom: {
					enabled: false
		  		}
			},
			colors:['#68e365'],
			dataLabels: {
		  		enabled: false
			},
			stroke: {
		  		curve: 'smooth',
		  		width:3
			},
			legend:{
				show:false
			},
			grid: {
				xaxis: {
					lines: {
						show: true
					}
				},
			},
			xaxis: {
		  		categories: data_day,
			},
			yaxis:{
				show:false
			}
		};
		
		var chart = new ApexCharts(document.querySelector("#line-chart"), options);
		chart.render();
	}
</script>
@endpush
@extends('layouts.front-app')
@section('content')

<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Payments</a></li>
		</ol>
	</div>
	<div class="row page-titles p-0 pt-3 pb-3">
		<div class="col-md-12">
			<form method="get" action="{{ route('payment.index') }}">
				<div class="box">
					<div class="box-header">
						<div class="row">
							<div class="col-md">
								<input type="text" name="name" class="form-control" placeholder="Name" value="{{ Request::get('name') }}">
							</div>
							<div class="col-md">
								<input type="text" name="email" class="form-control" placeholder="Email" value="{{ Request::get('email') }}">
							</div>
							<div class="col-md">
								<input type="text" name="phone" class="form-control" placeholder="Phone" value="{{ Request::get('phone') }}">
							</div>
							<div class="col-md">
								<select class="form-control" name="status">
									<option value="" {{ Request::get('status') == null ? 'selected' : '' }}>All</option>
									<option value="2" {{ !is_null(Request::get('status')) && Request::get('status') == 2 ? 'selected' : '' }}>SUCCESS</option>
									<option value="0" {{ !is_null(Request::get('status')) && Request::get('status') == 0 ? 'selected' : '' }}>PENDING</option>
									<option value="1" {{ !is_null(Request::get('status')) && Request::get('status') == 1 ? 'selected' : '' }}>DECLINED</option>
								</select>
							</div>
							<div class="col-md-2">
								<button class="btn btn-primary" type="submit" style="width: 100%;">Search</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">Payments</h4>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table table-responsive-md">
							<thead>
								<tr>
									<th><strong>CUSTOMER</strong></th>
									<th><strong>PACKAGE / PRICE</strong></th>
									<th><strong>BRAND / MERCHANT</strong></th>
									<th><strong>STATUS</strong></th>
									<th><strong>CREATED AT</strong></th>
									<th class="text-end"><strong>Action</strong></th>
								</tr>
							</thead>
							<tbody>
								@foreach($data as $key => $value)
								<tr>
									<td>
										<div class="d-flex align-items-center">
											<span class="w-space-no">{{ $value->client->name }} <br> {{ $value->client->email }}</span>
										</div>
									</td>
									<td>{{ $value->package }}<br>${{ $value->price }}</td>
									<td><span class="badge light badge-info">{{ $value->client->brand->name }}</span> <br> <span class="badge light badge-secondary mt-1">{{ $value->merchants != null ? $value->merchants->name : '' }} - {{ $value->merchants != null ? $value->merchants->getMerchant() : '' }}</span></td>
									<td><span class="badge light {{ $value->get_badge_status() }}">{{ $value->get_status() }}</span></td>
									<td>{{ $value->created_at->format('d M, Y g:i A') }}</td>
									<!-- <td>{{ $value->updated_at->format('d M, Y g:i A') }}</td> -->
									<td class="text-end">
										<span class="badge badge-primary" onclick="withJquery('{{ route('pay', [$value->unique_id]) }}')" style="cursor: pointer;">COPY LINK</span><br>
										<div class="d-flex justify-content-end mt-2">
											@if(($value->status == 2) || ($value->status == 1))
											<a href="{{ route('show.response', $value->id) }}" class="btn btn-info shadow btn-xs sharp me-1"><i class="fas fa-eye"></i></a>
											@endif
											<a onclick="return confirm('Are you sure?')"  href="{{ route('payment.delete', ['id' => $value->id]) }}" class="btn btn-danger shadow btn-xs sharp"><i class="fa fa-trash"></i></a>
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						{{ $data->appends(request()->except('page'))->links() }}
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
</script>
@endpush
@extends('layouts.front-app')
@section('content')

<div class="container-fluid">
	<div class="row page-titles">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
			<li class="breadcrumb-item"><a href="javascript:void(0)">Invoices</a></li>
			<li class="breadcrumb-item active"><a href="javascript:void(0)">Create Invoice</a></li>
		</ol>
	</div>
	<div class="row">
		<div class="col-lg-12 col-12">
		    <div class="card">
		        <div class="card-header">
					<h4 class="card-title">Invoice Form</h4>
		        </div>
		        <!-- /.box-header -->
				<div class="card-body">
					<div class="basic-form">
						<form class="form" method="post" action="{{ route('payment.store') }}">
							@csrf
							<div class="box-body">
								@if($errors->any())
									{!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
								@endif
								<div class="row">
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Name</label>
											<input type="text" class="form-control" name="name" required value="{{ old('name') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">E-mail</label>
											<input type="email" class="form-control" name="email" required value="{{ old('email') }}">
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group mb-3">
											<label class="form-label">Contact Number</label>
											<input type="text" class="form-control" name="phone" required value="{{ old('phone') }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Brand Name</label>
											<select name="brand_name" id="brand_name" class="form-control" required>
												<option value="">Select Brand</option>
												@foreach($brands as $key => $value)
												<option value="{{ $value->id }}" {{ old('brand_name') == $value->id ? 'selected' : ' ' }}>{{ $value->name }}</option>
												@endforeach
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Package</label>
											<input type="text" class="form-control" name="package" required value="{{ old('package') }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Amount ($)</label>
											<input step="any" type="number" class="form-control" required="" name="price" value="{{ old('price') }}">
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group mb-3">
											<label class="form-label">Merchant</label>
											<select name="merchant" class="form-control" id="merchant" required>
												<option value="0">STRIPE</option>
												<!-- <option value="1">SQUARE</option>
												<option value="2">STRIPE - One Step Marketing</option> -->
												<option value="3">FETCH</option>
												<option value="4">AUTHORIZE</option>
											</select>
										</div>
									</div>
									<div class="col-md-12">
										<div class="form-group mb-3">
											<label class="form-label">Discription</label>
											<textarea class="form-control" name="description" id="description" cols="30" rows="2">{{ old('description') }}</textarea>
										</div>
									</div>
								</div>
							</div>
							<!-- /.box-body -->
							<div class="box-footer">
								<button type="button" class="btn btn-warning me-1">
								<i class="ti-trash"></i> Cancel
								</button>
								<button type="submit" class="btn btn-primary">
								<i class="ti-save-alt"></i> Save
								</button>
							</div>
						</form>
					</div>
				</div>
		    </div>
		    <!-- /.box -->			
		</div>
	</div>
</div>
@endsection

@push('scripts')
@endpush
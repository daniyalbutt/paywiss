@extends('layouts.front-app')
@section('content')

<div class="container-fluid">
	<div class="row page-titles">
		<div class="col-md-8">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
				<li class="breadcrumb-item active"><a href="javascript:void(0)">Scrapped Lead</a></li>
			</ol>
		</div>
	</div>
	<div class="row page-titles p-0 pt-3 pb-3">
		<div class="col-md-12">
			<form method="get" action="{{ route('scrapped.index') }}">
				<div class="box">
					<div class="box-header">
						<div class="row">
							<div class="col-md">
							    <ul class="scrapper-search">
							        <li>
							            <input type="text" name="name" class="form-control" placeholder="Name" value="{{ Request::get('name') }}">
							        </li>
							        <li>
							            <input type="date" name="date" class="form-control" placeholder="Date" value="{{ Request::get('date') }}">
							            <select name="status" class="status form-control">
							                <option value="0" {{ Request::get('status') == 0 ? 'selected' : '' }}>All</option>
							                <option value="1" {{ Request::get('status') == 1 ? 'selected' : '' }}>By Ascending</option>
							                <option value="2" {{ Request::get('status') == 2 ? 'selected' : '' }}>By Descending</option>
							            </select>
							        </li>
							    </ul>
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
					<h4 class="card-title">Scrapped Lead - {{ $data->total() }}</h4>
				</div>
				<div class="card-body">
					@if($errors->any())
					{!! implode('', $errors->all('<div class="alert alert-danger">:message</div>')) !!}
					@endif
					@if(session()->has('success'))
					<div class="alert alert-success">
						{{ session()->get('success') }}
					</div>
					@endif
					<div class="row">
    					@foreach($data as $key => $value)
    					<div class="col-md-4">
    					    <div class="scrapped-box mb-4" id="{{ $value->serial }}" 
							@can('edit scrapped')
							onclick="openStatus(this, {{ $value->serial }}, '{{ json_decode($value->data)->Summary->Mark }}')"
							@endcan
							>
        					    <ul>
        					        <li><strong>Wordmark</strong></li>
        					        <li>{{ json_decode($value->data)->Summary->Mark }}</li>
        					    </ul>
        					    <ul>
        					        <li><strong>Serial</strong></li>
        					        <li>{{ $value->serial }}</li>
        					    </ul>
        					    <ul>
        					        <li><strong>Name</strong></li>
        					        <li>{{ array_key_exists('Attorney Name', json_decode($value->data, true)['Attorney']) ? json_decode($value->data, true)['Attorney']['Attorney Name'] : '--' }}</li>
        					    </ul>
        					    <ul>
        					        <li><strong>Phone</strong></li>
        					        <li>{{ array_key_exists('Phone', json_decode($value->data, true)['Attorney']) ? json_decode($value->data, true)['Attorney']['Phone'] : '--' }}</li>
        					    </ul>
        					    <ul>
        					        <li><strong>Email</strong></li>
        					        <li>{{ array_key_exists('Correspondent e-mail', json_decode($value->data, true)['Attorney']) ? json_decode($value->data, true)['Attorney']['Correspondent e-mail'] : '--' }}</li>
        					    </ul>
        					    <div class="status">
            					    @if($value->scrape_status != null)
            					    <p class="alert alert-primary mb-0">{{ $value->scrape_status->status }}</p>
            					    @endif
        					    </div>
    					    </div>
    					</div>
    					@endforeach
    					<div class="col-md-12">
					        {!! $data->withQueryString()->links() !!}
    					</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="post" class="w-100" id="status-form" action="{{ route('scrape.status') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status - <span></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="serial" id="serial" value="">
                    <textarea name="status" id="status" class="form-control" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openStatus(a, id, name){
        $('.modal-title span').text(name);
        $('#exampleModalCenter').find('#serial').val(id);
        $('#exampleModalCenter').modal('toggle');
    }
    $('#status-form').submit(function(e){
        e.preventDefault();
        var url = $(this).attr("action");
        let formData = new FormData(this);
        $.ajax({
            type:'POST',
            url: url,
            data: formData,
            contentType: false,
            processData: false,
            success: (response) => {
                if(response.status){
                    $('#status').val('');
                    $('#exampleModalCenter').modal('toggle');
                    $('#'+response.serial + ' .status').html('<p class="alert alert-primary mb-0">'+response.message+'</p>');
                }
            },error: function(response){
                console.log(response);
            }
        });
    });
</script>
@endpush
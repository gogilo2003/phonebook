@extends('admin::layout.main')

@section('title')
    PhoneBook::Dashboard
@endsection

@section('page_title')
    <i class="material-icons">dashboard</i> Dashboard
@endsection

@section('sidebar')
    @parent
    @include('phonebook::sidebar')
@endsection

@section('content')
    <form method="post" action="{{route('admin-phonebook-upload')}}" role="form" accept-charset="UTF-8" enctype="multipart/form-data">
    	<div class="row">
	    	<div class="col-md-3 form-group{!! $errors->has('file') ? ' has-error':'' !!}">
	    		<label for="file" class="btn btn-outline-primary btn-round btn-block">Select a file to Upload</label>
	    		<input type="file" id="file" name="file" class="custom-control custom-file">
	    		{!! $errors->has('file') ? '<span class="text-danger">'.$errors->first('file').'</span>' : ''!!}
	    		<p class="help-block">Upload a contacts file here (*.csv)</p>
	    	</div>
	    	<div class="col-md-9">
		    	<input type="hidden" name="_token" value="{{csrf_token()}}">
		    	<button type="submit" class="btn btn-primary btn-round"><span class="material-icons">cloud_upload</span> Upload</button>
		    </div>
	    </div>
    </form>
    <p>{{ $contacts->count() }} Contacts</p>
    <table class="table" id="contactsDataTable">
    	<thead>
    		<tr>
    			<th></th>
    			<th>First Name</th>
    			<th>Last Name</th>
    			<th>Display Name</th>
    			<th>Phone Numbers</th>
    			<th>Emails</th>
    		</tr>
    	</thead>
    	<tbody>
    		@foreach($contacts as $key => $contact)
    		<tr>
    			<td>{{ $loop->iteration }}</td>
    			<td>{{ $contact->first_name }}</td>
    			<td>{{ $contact->last_name }}</td>
    			<td>{{ $contact->display_name }}</td>
    			<td>{{ implode(', ',$contact->telephones()->pluck('value')->toArray()) }}</td>
    			<td>{{ implode(', ',$contact->emails()->pluck('value')->toArray()) }}</td>
    		</tr>
    		@endforeach
    	</tbody>
    </table>
@endsection

@push('scripts_bottom')
	<script type="text/javascript">
		$(document).ready(function(){
			$('#contactsDataTable').dataTable();
		})
	</script>
@endpush
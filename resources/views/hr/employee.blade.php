{{-- Extends layout --}}
@extends('layout.default')

{{-- Content --}}
@section('content')

<link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" rel="stylesheet">


    {{-- Dashboard 1 --}}

    <div class="row">
        <table class="table table-bordered data-table">
	        <thead>
	            <tr>
	                <th>No</th>
	                <th>Name</th>
	                <th width="100px">Action</th>
	            </tr>
	        </thead>
	        <tbody>
	        </tbody>
	    </table>
    </div>

@endsection

{{-- Scripts Section --}}
@section('scripts')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('js/pages/widgets.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
	  $(function () {
	    
	    var table = $('.data-table').DataTable({
	        processing: true,
	        serverSide: true,
	        ajax: "{{ route('employee.getIdentityCard') }}",
	        columns: [
	            {data: 'id', name: 'id'},
	            {data: 'name', name: 'name'},
	            {data: 'action', name: 'action', orderable: false, searchable: false},
	        ]
	    });
	    
	  });
	</script>
@endsection

@extends('layouts.master')

@section('title', 'Registration')
@section('extrastyle')
<link href="{{ URL::asset('assets/css/select2.min.css')}}" rel="stylesheet">
<link href="{{ URL::asset('assets/css/switchery.min.css')}}" rel="stylesheet">

@endsection

@section('content')

<!-- page content -->
<div class="right_col" role="main">
	<div class="">

		<div class="clearfix"></div>
		<!-- row start -->
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div class="x_panel">
					<div class="x_title">
						<h2>Registration<small> Student semester registration</small></h2>

						<div class="clearfix"></div>
					</div>
					<div class="x_content">
						@if (count($errors) > 0)
						 <div class="alert alert-danger">
								 <strong>Whoops!</strong> There were some problems with your input.<br><br>
								 <ul>
										 @foreach ($errors->all() as $error)
												 <li>{{ $error }}</li>
										 @endforeach
								 </ul>
						 </div>
				 @endif
						<form class="form-horizontal form-label-left" novalidate method="post" action="{{URL::route('student.registration.store')}}">
							<input type="hidden" name="_token" value="{{ csrf_token() }}">
							<div class="row">
								<div class="col-md-4">
									<div class="item form-group">
										<label for="department">Department <span class="required">*</span>
										</label>

										{!!Form::select('department_id', $departments, null, ['placeholder' => 'Pick a department','class'=>'select2_single department form-control has-feedback-left','required'=>'required','id'=>'department_id'])!!}
										<i class="fa fa-home form-control-feedback left" aria-hidden="true"></i>
										<span class="text-danger">{{ $errors->first('department_id') }}</span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="item form-group">
										<label for="session">Session <span class="required">*</span>
										</label>
										{!!Form::select('session', $sessions, null, ['placeholder' => 'Pick a Session','class'=>'select2_single session form-control has-feedback-left','required'=>'required' ,'id'=>'session'])!!}
										<i class="fa fa-clock-o form-control-feedback left" aria-hidden="true"></i>
										<span class="text-danger">{{ $errors->first('session') }}</span>
									</div>
								</div>
								<div class="col-md-4">
									<div class="item form-group">
										<label for="levelTerm">Semester <span class="required">*</span>
										</label>
										{!!Form::select('levelTerm', $semesters, null, ['placeholder' => 'Pick a Semester','class'=>'select2_single semester form-control has-feedback-left','required'=>'required'])!!}
										<i class="fa fa-info form-control-feedback left" aria-hidden="true"></i>
										<span class="text-danger">{{ $errors->first('levelTerm') }}</span>
									</div>

								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="table-responsive">
										<table id="studentList" class="table table-striped table-bordered table-hover">
											<thead>
												<tr>

													<th>Id No</th>
													<th>Name</th>
													<th>Register? <div class="pull-right"><input type="checkbox" id="allcheck" class="js-switch allCheck" name="allcheck">All Select</div></th>

												</tr>
											</thead>
											<tbody>

												<tbody>
												</table>
											</div>
										</div>
									</div>


								</div>

								<div class="ln_solid"></div>
								<div class="row">
										<button id="btnsave" type="submit" class="btn btn-lg btn-success pull-right"><i class="fa fa-check"> Submit</i></button>
								</div>

							</form>
						</div>
					</div>
					<!-- row end -->
					<div class="clearfix"></div>

				</div>
			</div>
			<!-- /page content -->
			@endsection
			@section('extrascript')
			<script src="{{ URL::asset('assets/js/select2.full.min.js')}}"></script>
			<script src="{{ URL::asset('assets/js/switchery.min.js')}}"></script>
			<!-- validator -->
			<script>
			$(document).ready(function() {
				 $('#btnsave').hide();
			$(".department").select2({
				placeholder: "Pick a department",
				allowClear: true
			});

			$(".session").select2({
				placeholder: "Pick a Session",
				allowClear: true
			});
			$(".semester").select2({
				placeholder: "Pick a semester",
				allowClear: true
			});

			//get students lists
			$('#session').on('change',function (){
				var dept= $('#department_id').val();
				var session = $(this).val();
				if(!dept){
					new PNotify({
						title: 'Validation Error!',
						text: 'Please Pic A Department!',
						type: 'error',
						styling: 'bootstrap3'
					});
				}
				else {
					$.ajax({
						url:'/students/'+dept+'/'+session,
						type: 'get',
						dataType: 'json',
						success: function(data) {
							//console.log(data);
							$("#studentList").find("tr:gt(0)").remove();
							if(data.students.length>0)
							{
								$('#btnsave').show();
							}
							else {
								$('#btnsave').hide();
							}
							$.each(data.students, function(key, value) {
								addRow(value.id,value.firstName+' '+value.middleName+' '+value.lastName,value.idNo);
							});
							var elems = Array.prototype.slice.call(document.querySelectorAll('.tb-switch'));
							elems.forEach(function(html) {
								var switchery = new Switchery(html);
							});
						},
						error: function(data){
							var respone = JSON.parse(data.responseText);
							$.each(respone.message, function( key, value ) {
								new PNotify({
									title: 'Error!',
									text: value,
									type: 'error',
									styling: 'bootstrap3'
								});
							});
						}
					});
				}
			});
			});
			//add row to table
			function addRow(id,stdname,idNo) {
				 var table = document.getElementById('studentList');
				 var rowCount = table.rows.length;
				 var row = table.insertRow(rowCount);


				 var cell2 = row.insertCell(0);
				 var regiNo = document.createElement("label");

				 regiNo.innerHTML=idNo;
				 cell2.appendChild(regiNo);
				 var hdregi = document.createElement("input");
				 hdregi.name="ids[]";
				 hdregi.value=id;
				 hdregi.type="hidden";
				 cell2.appendChild(hdregi);

				 var cell4 = row.insertCell(1);
				 var name = document.createElement("label");
				 name.innerHTML=stdname;
				 cell4.appendChild(name);

				 var cell5 = row.insertCell(2);
				 var chkbox = document.createElement("input");
				 chkbox.type = "checkbox";
				 chkbox.checked=false;
				 chkbox.className="js-switch tb-switch";
				 chkbox.name="registeredIds["+id+"]";
				 chkbox.size="3";
				 cell5.appendChild(chkbox);
			};
			//make all checkbox checked
			$('.allCheck').on('change',function() {
				 $('.tb-switch').trigger('click');
			});
			</script>
			@endsection

	function escapeHtml(text) {
	return text
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#039;");
	}
	function getTemplate(id,firstname, lastname, active, role)	
	{
		
		var circleclass='circlegray';
		if(active==1){
			circleclass='circlegreen';
		}

		var fname = escapeHtml(firstname);
		var lname = escapeHtml(lastname);
		var rowtemplate = '<tr data-id="'+id+'" ><td class="checkbox"><input class="rowcheck" type="checkbox" data-id="'+id+'"></td><td class="username">'+fname+' '+lname+'</td><td><div class="'+circleclass+'"></div></td><td>'+role+'</td><td><div><a  id="editbutton" class="btn btn-info"  aria-label="Edit"><i class="fa fa-pencil editbutton" aria-hidden="true" data-id="'+id+'" data-active="'+active+'"></i></a><a class="btn btn-danger"  aria-label="Delete"><i class="fa fa-trash-o deletebutton" aria-hidden="true"  data-id="'+id+'"></i></a></div></td></tr>';
		return rowtemplate;
	}

	function clearModal()
	{
			$("#firstnameModal").val('');
			$("#lastnameModal").val('');
			$("#firstnameModal").css('border','1px solid #ced4da');
			$("#lastnameModal").css('border','1px solid #ced4da');
			$("#roleModal").val('user');
			$("#addModal").find('.modal-title').text('Add user');
			
			$("#activeswitch").prop('checked', true);
			$("#activeswitch").attr('checked', true);
			$("#adduser").attr('data-action',"add");
			$("#adduser").text("Add user");
				
	}

		function getRows(add)
	{

						if(add){


							$.ajax(
											{
												url:"user.php",
												type:'POST',
												data:{action:'getusers'},
												success:function(data)
												{
													JSON.parse(data).forEach((element,idx) => {
														
														$("table tbody").innerHTML="";
														if(idx+1==JSON.parse(data).length)
														$("table tbody").append(getTemplate(element.id,element.firstname,element.lastname,element.active,element.role));

													});
												}
											}
										);
							

					}
					else {
						$("table tbody").innerHTML="";
							$.ajax(
											{
												url:"user.php",
												type:'POST',
												data:{action:'getusers'},
												success:function(data)
												{
													JSON.parse(data).forEach(element => {
														
														$("table tbody").innerHTML="";
										
														$("table tbody").append(getTemplate(element.id,element.firstname,element.lastname,element.active,element.role));

													});
												}
											}
										);			
					}
	}

$(document).on('hidden.bs.modal', '#addModal', function (e) {
	clearModal();
});


$(document).on('input', '#firstnameModal', function (e) {
  var text = e.target.value;
  var reg = /\s\s+/g;
  if (text==null || text=='' || reg.test(text) || text==' ') {
	  $(this).css('border','1px solid red');
	  return false;}
  else {
	   $(this).css('border','1px solid #ced4da');
	   return true;}
});



$(document).on('input', '#lastnameModal', function (e) {
  var text = e.target.value;
var reg = /\s\s+/g;
  if (text==null || text=='' || reg.test(text) || text==' ') {
	  $(this).css('border','1px solid red');
	  return false;}
  else {
	   $(this).css('border','1px solid #ced4da');
	   return true;}
});

$(document).on('click', '#addbutton, #addbuttonsecond', function (e) {
	$("#firstnameModal").val('');
	$("#lastnameModal").val('');
	$("#firstnameModal").css('border','1px solid #ced4da');
	$("#lastnameModal").css('border','1px solid #ced4da');
	$("#roleModal").val('user');
	$("#addModal").find('.modal-title').text('Add user');
	$("#activeswitch").prop('checked', true);
	$("#activeswitch").attr('checked', true);
	Array.from($("#activeswitch"))[0].checked=true;
	$("#addModal").modal("show");
});


function updateAfterGroupOperations(selectidbool)
{
	$("tbody").children().remove();
						getRows(false);
						$("#checkAll").attr('checked',false);
						Array.from($("#checkAll")).forEach(element=>{element.checked=false});


						if(selectidbool) {
							$("#selectgroupoperation").val('placeholder').change();
						}
						else  {
							$("#selectgroupoperationsecond").val('placeholder').change();
						};
}
	$(document).on('click', '#groupoperationok, #groupoperationoksecond', 	  function (e) {


		var table= $('#checkAll').closest('table');
		var ids=[];
		var groupoperationselect = false;
  		Array.from($('tr td input:checkbox',table)).forEach(element=>{
			  if(element.checked) ids.push(element.dataset.id);
		  });
		  console.log(e.target.id);
		  
		 var selectvalue = e.target.id=="groupoperationok"? $("#selectgroupoperation").val() : $("#selectgroupoperationsecond").val();
		 var selectidbool= e.target.id=="groupoperationok"? true : false;
		  if(selectvalue=='placeholder')
		  {
			  $("#modalbodytext").text('Please select an option');
			  $("#informationModal").modal('show');
		  }
		 if(selectvalue=='active')
		 {

			 if(ids.length==0) { $("#modalbodytext").text('Please check some rows');$("#informationModal").modal('show');return }
			 $.ajax(
				{
					url:"user.php",
					type:'POST',
					data:{action:'changeuserstatus',status:1,ids:ids},
					success:function(data)
					{

						updateAfterGroupOperations(selectidbool);
						
					}
				}
			)
		 }

		 if(selectvalue=='notactive')
		 {
			 if(ids.length==0) { $("#modalbodytext").text('Please check some rows');$("#informationModal").modal('show');return }
			 $.ajax(
				{
					url:"user.php",
					type:'POST',
					data:{action:'changeuserstatus',status:0,ids:ids},
					success:function(data)
					{

						updateAfterGroupOperations(selectidbool);
						
					}
				}
			)
		 }

		 if(selectvalue=='delete')
		 {
			  if(ids.length==0) { $("#modalbodytext").text('Please check some rows');$("#informationModal").modal('show');return }
			 $.ajax(
				{
					url:"user.php",
					type:'POST',
					data:{action:'deleteusers',ids:ids},
					success:function(data)
					{

						updateAfterGroupOperations(selectidbool);
						
					}
				}
			)
		 }
		

	}); 

	$(document).on('click', '.editbutton', function (e) {



		$.ajax(
				{
					url:"user.php",
					type:'POST',
					data:{action:'getuser',userid:e.target.getAttribute("data-id")},
					success:function(data)
					{

						let user =	JSON.parse(data);
						$("#firstnameModal").val(user.firstname);
						$("#lastnameModal").val(user.lastname);
						$("#roleModal").val(user.role);
						//$("#adduser").addClass('editaction');
						$("#adduser").attr('data-action',"edit");
						$("#adduser").text("Save/Update");
						$("#userid").val(e.target.getAttribute("data-id"));
						$("#firstnameModal").css('border','1px solid #ced4da');
						$("#lastnameModal").css('border','1px solid #ced4da');
						$("#addModal").find('.modal-title').text('Edit user');
						if(e.target.getAttribute("data-active")=="1")
					{
						$("#activeswitch").prop('checked',true);
						$("#activeswitch").attr('checked',true);
					}
					else 
					{
						$("#activeswitch").prop('checked',false);
						$("#activeswitch").attr('checked',false);
					}
						$('#addModal').modal('show');
						
					}
				}
			)
	}); 

	
$(document).on('change', 'input.rowcheck[type="checkbox"]',function() {

	var table= $(this).closest('table');
	var checkboxes=[];
    if($(this).is(":checked")) {
        Array.from($('tr td input:checkbox',table)).forEach(element=>{
			if(element.checked) checkboxes.push(true);
		});

		if(checkboxes.length==Array.from($('tr td input:checkbox',table)).length)
	{
		$("#checkAll").attr('checked',true);
		Array.from($('#checkAll'))[0].checked=true;
	}
   	
    }
	else {
		$("#checkAll").attr('checked',false);
		Array.from($('#checkAll'))[0].checked=false;

	}
});
	$(document).on('click', '.deletebutton', function (e) {


	var deluseridvalue = e.target.getAttribute("data-id")
	$.ajax(
			{
				url:"user.php",
				type:'POST',
				data:{action:'getuser',userid:deluseridvalue},
				success:function(data)
				{

				
					$("#deluserid").val(JSON.parse(data).id);
					$("#delusertextid").text(JSON.parse(data).id);
					$('#deluserModal').modal('show');
					
				}
			}
		)
	}); 

$(document).ready(function(){

	getRows(false);
	 $("#checkAll").click(function(e) {
	 	var table= $(e.target).closest('table');
    	$('tr td input:checkbox',table).attr('checked',e.target.checked);
		Array.from($('tr td input:checkbox',table)).forEach(element=>{element.checked=e.target.checked});
   	}); 



	$("#adduser").click(function(){

	var switchvalue = $("#activeswitch").prop('checked') ? "1":"0";
    var reg = /\s\s+/g;
	if(
		$("#firstnameModal").val()=='' 
		||$("#firstnameModal").val()==' ' 
		|| $("#lastnameModal").val()==''
		|| $("#lastnameModal").val()==' '
		|| $("#lastnameModal").val()==null 
		|| $("#firstnameModal").val()==null
		|| reg.test($("#firstnameModal").val())
		|| reg.test($("#lastnameModal").val())
	)


	{
		var firstnametext =$("#firstnameModal").val();
		var lastnametext =$("#lastnameModal").val();
		if (firstnametext==null || firstnametext=='' || reg.test(firstnametext) || firstnametext==' ') {
			$("#firstnameModal").css('border','1px solid red');
		}

		if (lastnametext==null || lastnametext=='' || reg.test(lastnametext) || lastnametext==' ') {
			$("#lastnameModal").css('border','1px solid red');
		}

		return
	};
	if($("#adduser").attr("data-action")=='add')
		{
			
			$.ajax(
			{
				url:"user.php",
				type:'POST',
				data:{action:'adduser',firstname:$("#firstnameModal").val(),lastname:$("#lastnameModal").val(),active:switchvalue,role:$("#roleModal").val()},
				success:function(data)
				{
					var q = JSON.parse(data);
					if(q.status)
					{
						$("table tbody").innerHTML="";
						getRows(true);
						$('#addModal').modal('hide');
						console.log(q.user);
					}
					else {
						$("#modalbodyerror").text(q.error.code+''+q.error.message);
						$("#errorModal").modal('show');
						console.log(q.error);
					}
				}
			});
		}
		else {
			$.ajax(
			{
				url:"user.php",
				type:'POST',
				data:{action:'edituser',userid:$("#userid").val(),firstname:$("#firstnameModal").val(),lastname:$("#lastnameModal").val(),active:switchvalue,role:$("#roleModal").val()},
				success:function(data)
				{

					var q = JSON.parse(data);
					if(q.status)
					{
							$("tbody").children().remove();
							getRows(false);
							$('#addModal').modal('hide');
							console.log(q.user);
					}
					else {
						$("#modalbodyerror").text(q.error.code+''+q.error.message);
						$("#errorModal").modal('show');
						console.log(q.error);
					}
				
				}
			}
		)
		}


	});   

	$("#deluser").click(function(){
		$.ajax(
			{
				url:"user.php",
				type:'POST',
				data:{action:'deleteuser',userid:$("#deluserid").val()},
				success:function(data)
				{

					var q = JSON.parse(data);
					if(q.status)
					{
						$("tbody").children().remove();
						getRows(false);
						$('#deluserModal').modal('hide');
						console.log(q.user);
					}
					else {
						$("#modalbodyerror").text(q.error.code+''+q.error.message);
						$("#errorModal").modal('show');
						console.log(q.error);
					}


				}
			}
		)
	})





    


});

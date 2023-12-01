/*
   Author: Barbara Emke
   Date:   November 14, 2023
*/

//opens respective forms
function openForm(){
	const popup = document.getElementById('message_form');

	//makes popup visible
	popup.style.display = "block"; 
}

//closes respective forms
function closeForm(){
	const popup = document.getElementById('message_form');

	//makes popup visible
	popup.style.display = "none"; 
}

//searches each row to see if employee is part of selected company
function filterEmployees(){
  var found = 0;
  const records = document.querySelectorAll('.employee-record, .employee-record[hidden]');

  //gets selected company name and checkbox status
  const companySelected = document.getElementById('company').value;
  const showDeactivated = document.getElementById('show_deactivated');
  
  //makes visible all records with speciified company if checkbox is checked
  if(showDeactivated.checked){
	  records.forEach(record => {
		  var company = record.querySelector('.company_name, .company_name[hidden]').textContent;

		  if (companySelected === company || companySelected === ""){
			  record.style.display = 'table-row';
			  found = 1;
		  } else {
			  record.style.display = 'none';
		  }

	  });
	  
  }
  //makes visible all records with speciified company that are currently active if checkbox is checked
  else{
	  records.forEach(record => {
		  var status = record.querySelector('.pass, .pass[hidden]').textContent;
		  var company = record.querySelector('.company_name, .company_name[hidden]').textContent;

		  if (status !== "" && (companySelected === company || companySelected === "")){
			  record.style.display = 'table-row';
			  found = 1;
		  } else {
			  record.style.display = 'none';
		  }
	  });
	  
  }

  if(found === 0){
	  document.getElementById("no-results").style.display = 'block';
  }
  else{
	  document.getElementById("no-results").style.display = 'none';
  }
}

function goBack(){
  window.location.href = 'view_employees.php';
}

//searches each timesheet to see if date falls within range specified
function filterTimesheets(){
  var found = 0;
  const records = document.querySelectorAll('.employee_timesheet, .employee_timesheet[hidden]');

  //gets date range and makes them date objects
  const fromDateStr = document.getElementById('from_date').value;
  const fromDate = new Date(fromDateStr);
  const toDateStr = document.getElementById('to_date').value;
  const toDate = new Date(toDateStr);
  
  //loops through each review checking date range
  records.forEach(record => {
	  var dateStr = record.querySelector('.date, .date[hidden]').textContent;
	  var date = new Date(dateStr);

	  if (fromDate <= date && date <= toDate){
		  document.getElementById("no-results").style.display = 'none';
		  record.style.display = 'table-row';
		  found = 1;
	  } else {
		  record.style.display = 'none';
	  }
  });
  
  
  if(found === 0){
	  document.getElementById("no-results").style.display = 'block';
  }
}

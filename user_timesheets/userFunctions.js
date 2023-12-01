/*
   Author: Barbara Emke
   Date:   November 14, 2023
*/

//Navigates to timesheet submission form
function navigateToForm(){
    window.location.href = 'timesheet_form.php';
}

//searches each timesheet to see if date falls within range specified
function filterTimesheets(){
    var found = 0;
	const records = document.querySelectorAll('.timesheet_record, .timesheet_record[hidden]');

    //gets date range and makes them date objects
    const fromDateStr = document.getElementById('from_date').value;
    const fromDate = new Date(fromDateStr);
    const toDateStr = document.getElementById('to_date').value;
    const toDate = new Date(toDateStr);
	
	//loops through each review checking date range
	records.forEach(record => {
		var dateStr = record.querySelector('.heading, .heading[hidden]').textContent;
        var date = new Date(dateStr);

		if (fromDate <= date && date <= toDate){
			document.getElementById("no-results").style.display = 'none';
			record.style.display = 'flex';
			found = 1;
		} else {
			record.style.display = 'none';
		}
	});
	
	
	if(found === 0){
		document.getElementById("no-results").style.display = 'block';
	}
}
/*
   Author: Barbara Emke
   Date:   November 14, 2023
*/

//searches each row to see if log is of a particular event or in a date range
function filterLogs(){
    var found = 0;
	const records = document.querySelectorAll('.event_record, .event_record[hidden]');

    //gets selected event type 
    const eventSelected = document.getElementById('event_type').value;
    console.log(eventSelected);

	//gets date range and makes them date objects
    const fromDateStr = document.getElementById('from_date').value;
    const from_date = new Date(fromDateStr);
  	from_date.setHours(0, 0, 0, 0);
  	from_date.setDate(from_date.getDate() + 1);
    const toDateStr = document.getElementById('to_date').value;
    const to_date = new Date(toDateStr);
  	to_date.setHours(0, 0, 0, 0);
  	to_date.setDate(to_date.getDate() + 1);
    console.log("from:");
    console.log(from_date);
    console.log("To:");
    console.log(to_date);

	
	//makes visible all records with speciified event type and within a date range
	records.forEach(record => {
		var eventType = record.querySelector('.eventType, .eventType[hidden]').textContent;
        var dateStr = record.querySelector('.date, .date[hidden]').textContent;
        const dateTime = new Date(dateStr);
      	const recDate = new Date(dateTime.getFullYear(), dateTime.getMonth(), dateTime.getDate());
        //dateOb.setHours(0, 0, 0, 0);
        console.log("date");
        console.log(recDate);

		if ((eventSelected === eventType || eventSelected === "") && (from_date <= recDate && recDate <= to_date)){
			record.style.display = 'table-row';
			found = 1;
	    } else {
		    record.style.display = 'none';
	    }

	});

    if(found === 0){
		document.getElementById("no-results").style.display = 'block';
	}
  	else{
     	 document.getElementById("no-results").style.display = 'none';
    }
}

function formatDate(inputDate) {
    let dateObject = new Date(inputDate);
    let year = dateObject.getFullYear();
    let month = String(dateObject.getMonth() + 1).padStart(2, '0');
    let day = String(dateObject.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }
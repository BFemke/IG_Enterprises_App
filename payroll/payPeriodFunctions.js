/*
   Author: Barbara Emke
   Date:   November 14, 2023
*/

function generateAllReports(from_date, to_date){
    // Construct the URL with query parameters
    const queryString = `from_date=${from_date}&to_date=${to_date}&download=${true}`;

    // Redirect to the new PHP page with the query string
    window.location.href = `generatePayPeriod.php?${queryString}`;
}

function generateEmployeeReport(employee_id, from_date, to_date) {
    // Construct the URL with query parameters
    const queryString = `employee_id=${employee_id}&from_date=${from_date}&to_date=${to_date}`;

    // Redirect to the new PHP page with the query string
    window.location.href = `generatePayPeriod.php?${queryString}`;
}
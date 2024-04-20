function filterPhase(phase) {
    input = document.getElementById("searchBox");
    input.value = '';
    dateBox = document.getElementById('dateBox');
    dateBox.value = '';
    var table, tr, i;
    table = document.getElementById("projectTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        if (i == 0) continue;
        if (phase == "" || tr[i].className == phase) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}

function searchTable() {
    dateBox = document.getElementById('dateBox');
    dateBox.value = '';
    document.getElementById('btnradio1').checked = true;
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchBox");
    filter = input.value.toUpperCase();
    table = document.getElementById("projectTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function filterDate() {
    input = document.getElementById("searchBox");
    input.value = '';
    document.getElementById('btnradio1').checked = true;
    dateBox = document.getElementById('dateBox');
    if (dateBox.value == '') {
        const table = document.getElementById('projectTable');
        const tableRows = table.getElementsByTagName('tr');
        for (let i = 1; i < tableRows.length; i++) {
            tableRows[i].style.display = '';
        }
        return;
    }
    var date = new Date(dateBox.value);
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();

    const startDate = `${day}/${month}/${year}`;
    console.log(startDate);

    const table = document.getElementById('projectTable');
    const tableRows = table.getElementsByTagName('tr');

    for (let i = 1; i < tableRows.length; i++) {
        const tableData = tableRows[i].getElementsByTagName('td')[1];
        const rowStartDate = tableData.textContent.trim();

        if (rowStartDate == startDate) {
            tableRows[i].style.display = '';
        } else {
            tableRows[i].style.display = 'none';
        }
    }
}

document.getElementById('dateBox').addEventListener('input', filterDate);

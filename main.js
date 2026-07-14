
// login authentication with alert for invalid inputs
function auth (){
    var org = document.getElementById("org").value;
    var ID = document.getElementById("wID").value;
    var password = document.getElementById("password").value;

    if (org == "admin" && password == "user"){
        window.location.replace("./dashboard.php");
    } else {
        alert("Invalid Info");
        return;
    }
}

//user pref bio text counter
document.addEventListener('DOMContentLoaded', () =>{
    console.log('Script running');

    const bio = document.getElementById('bio');
    const counter = document.getElementById('bio_counter');
    const maxLength = 150;

    console.log('bio:', bio);
    console.log('counter', counter);

    bio.addEventListener('input', () => {
        counter.textContent = `${bio.value.length} / ${maxLength}`;
    });
});


//mock calendar srt up 
// to be added: editEvent function, addEvent function, insert event into databse, 
// secondary side day panel event listener, color coded events w key
$(document).ready(function(){
    $('#calendar').fullCalendar({
        header: {
            left: 'prev, next today',
            center: 'title',
            right: 'month,basicWeek,basicDay'
        },
        defaultDate: '2026-7-1',
        navLinks: true,
        editable: true,
        eventLimit: true,
        events:[
            {
                title: 'All Day Event Tester',
                start: '2026-7-01'
            },
            {
                id: 999,
                title: 'Repeating Event',
                start: '2026-7-16T16:00:00'
            },
            {
                title: 'Click for Google',
                url: 'https://google.com/',
                start: '2026-7-28'
            }
        ]
    });
});






{
  _id: ObjectId("..."),
  org_code: "csclub",           // what they type into the "org" field
  org_name: "Computer Science Club",
  created_at: ISODate("...")
}



{
  _id: ObjectId("..."),
  org_id: ObjectId("..."),      // ref to organizations
  wID: "W00123456",
  password_hash: "$2y$10$...",
  role: "member",               // "member" | "eboard" | "admin"
  fname: "Jane",
  lname: "Doe",
  school: "CAS",
  year: "Junior",
  eboard_position: null,        // e.g. "Secretary" if role is eboard
  profile_pic: "/media/blue_star.png",
  bio: "",
  attendance_points: 0,
  created_at: ISODate("...")
}

{
  _id: ObjectId("..."),
  org_id: ObjectId("..."),
  title: "General Body Meeting",
  start: ISODate("2026-07-20T18:00:00"),
  end: ISODate("2026-07-20T19:00:00"),
  all_day: false,
  color: "#35bae7",              // matches your color-coded key idea
  url: null,
  created_by: ObjectId("..."),   // user_id
  created_at: ISODate("...")
}

{
  _id: ObjectId("..."),
  org_id: ObjectId("..."),
  user_id: ObjectId("..."),
  event_id: ObjectId("..."),
  attended: true,
  points_awarded: 10,
  recorded_at: ISODate("...")
}


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




//mock calendar srt up 
// to be added: editEvent function, addEvent function, insert event into databse, 
// secondary side day panel event listener, color coded events w key
// $(document).ready(function(){
//     $('#calendar').fullCalendar({
//         header: {
//             left: 'prev, next today',
//             center: 'title',
//             right: 'month,basicWeek,basicDay'
//         },
//         defaultDate: '2026-7-1',
//         navLinks: true,
//         editable: true,
//         eventLimit: true,
//         events:[
//             {
//                 title: 'All Day Event Tester',
//                 start: '2026-7-01'
//             },
//             {
//                 id: 999,
//                 title: 'Repeating Event',
//                 start: '2026-7-16T16:00:00'
//             },
//             {
//                 title: 'Click for Google',
//                 url: 'https://google.com/',
//                 start: '2026-7-28'
//             }
//         ]
//     });
// });




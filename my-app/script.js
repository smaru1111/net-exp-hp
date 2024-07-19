document.addEventListener('DOMContentLoaded', function() {
    fetchStudents();

    document.getElementById('studentForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('manage_students.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text()).then(() => {
            fetchStudents();
            this.reset();
        });
    });

    document.getElementById('timetableForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch('manage_timetable.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text()).then(() => {
            this.reset();
            if (document.getElementById('studentSelect').value) {
                displayTimetable();
            }
        });
    });
});

function fetchStudents() {
    fetch('manage_students.php', {
        method: 'POST',
        body: new URLSearchParams('action=fetch')
    }).then(response => response.json()).then(students => {
        const select = document.getElementById('studentSelect');
        select.innerHTML = '<option value="">選択してください</option>';
        students.forEach(student => {
            const option = document.createElement('option');
            option.value = student.student_number;
            option.textContent = student.name;
            select.appendChild(option);
        });
    });
}

function displayTimetable() {
    const select = document.getElementById('studentSelect');
    const student_number = select.value;
    const timetableDiv = document.getElementById('timetable');

    timetableDiv.innerHTML = ''; // 既存の内容をクリア

    if (student_number) {
        fetch('manage_students.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'fetch' })
        }).then(response => response.json()).then(students => {
            const student = students.find(s => s.student_number === student_number);
            if (student) {
                fetch('manage_timetable.php', {
                    method: 'POST',
                    body: new URLSearchParams({ action: 'fetch', student_id: student.id })
                }).then(response => response.json()).then(timetable => {
                    timetable.forEach(entry => {
                        const cell = document.createElement('div');
                        cell.textContent = `${entry.day} ${entry.period}限 ${entry.subject}`;
                        timetableDiv.appendChild(cell);
                    });
                });
            }
        });
    }
}

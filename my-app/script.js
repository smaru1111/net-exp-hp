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

    document.getElementById('editForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        
        fetch('manage_timetable.php', {
            method: 'POST',
            body: formData
        }).then(response => response.text()).then(() => {
            closeModal();
            displayTimetable();
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
                    // テーブルの構造を生成
                    const days = ['月', '火', '水', '木', '金'];
                    const periods = [1, 2, 3, 4, 5, 6];

                    const table = document.createElement('table');
                    const thead = document.createElement('thead');
                    const tbody = document.createElement('tbody');

                    const headerRow = document.createElement('tr');
                    headerRow.appendChild(document.createElement('th')); // 空のヘッダ
                    days.forEach(day => {
                        const th = document.createElement('th');
                        th.textContent = day;
                        headerRow.appendChild(th);
                    });
                    thead.appendChild(headerRow);
                    table.appendChild(thead);

                    periods.forEach(period => {
                        const row = document.createElement('tr');
                        const periodCell = document.createElement('td');
                        periodCell.textContent = `${period}限`;
                        row.appendChild(periodCell);

                        days.forEach(day => {
                            const cell = document.createElement('td');
                            const entry = timetable.find(e => e.day === day && e.period === period);
                            if (entry) {
                                cell.textContent = entry.subject;
                                cell.addEventListener('click', function() {
                                    openModal(student.id, entry);
                                });
                            }
                            row.appendChild(cell);
                        });

                        tbody.appendChild(row);
                    });

                    table.appendChild(tbody);
                    timetableDiv.appendChild(table);
                });
            }
        });
    }
}

function openModal(studentId, entry) {
    document.getElementById('edit_student_id').value = studentId;
    document.getElementById('edit_day').value = entry.day;
    document.getElementById('edit_period').value = entry.period;
    document.getElementById('edit_subject').value = entry.subject;
    document.getElementById('editModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

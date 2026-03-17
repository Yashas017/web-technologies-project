let dataArray = [];

const form = document.getElementById("dataForm");


form.addEventListener("submit", function(e) {
    e.preventDefault();

    const name = document.getElementById("name").value;
    const age = document.getElementById("age").value;
    const email = document.getElementById("email").value;

    const data = { name, age, email };
    dataArray.push(data);

    form.reset();
    showPopup();
});

function showPopup() {
    const popup = document.getElementById("popup");
    popup.classList.add("show");

    setTimeout(() => {
        popup.classList.remove("show");
    }, 2000);
}


function openNewPage() {
    let newWindow = window.open("", "_blank");

    let tableHTML = `
        <html>
        <head>
            <title>Data Table</title>
            <style>
                body {
                    font-family: Arial;
                    background: #f4f4f4;
                    text-align: center;
                }
                table {
                    border-collapse: collapse;
                    width: 80%;
                    margin: 20px auto;
                    background: white;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
                }
                th, td {
                    padding: 12px;
                    border: 1px solid #ddd;
                }
                th {
                    background: #4facfe;
                    color: white;
                }
                button {
                    padding: 10px 15px;
                    margin: 10px;
                    border: none;
                    background: #4facfe;
                    color: white;
                    cursor: pointer;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>

        <h2>Stored Data</h2>

        <button onclick="copyTable()">Copy Table</button>
        <button onclick="downloadCSV()">Download CSV</button>

        <table>
            <tr>
                <th>Name</th>
                <th>Age</th>
                <th>Email</th>
            </tr>
    `;

    dataArray.forEach(item => {
        tableHTML += `
            <tr>
                <td>${item.name}</td>
                <td>${item.age}</td>
                <td>${item.email}</td>
            </tr>
        `;
    });

    tableHTML += `
        </table>

        <script>
            function copyTable() {
                let text = "";
                document.querySelectorAll("table tr").forEach(row => {
                    let rowData = [];
                    row.querySelectorAll("td, th").forEach(col => {
                        rowData.push(col.innerText);
                    });
                    text += rowData.join("\\t") + "\\n";
                });
                navigator.clipboard.writeText(text);
                alert("Copied!");
            }

            function downloadCSV() {
                let csv = "Name,Age,Email\\n";
                document.querySelectorAll("table tr").forEach((row, i) => {
                    if(i === 0) return;
                    let cols = row.querySelectorAll("td");
                    let rowData = [];
                    cols.forEach(col => rowData.push(col.innerText));
                    csv += rowData.join(",") + "\\n";
                });

                let blob = new Blob([csv], { type: "text/csv" });
                let a = document.createElement("a");
                a.href = URL.createObjectURL(blob);
                a.download = "data.csv";
                a.click();
            }
        <\/script>

        </body>
        </html>
    `;

    newWindow.document.write(tableHTML);
}


function copyData() {
    navigator.clipboard.writeText(JSON.stringify(dataArray, null, 2));
    alert("Array copied!");
}


function downloadCSV() {
    let csv = "Name,Age,Email\n";
    dataArray.forEach(item => {
        csv += `${item.name},${item.age},${item.email}\n`;
    });

    let blob = new Blob([csv], { type: "text/csv" });
    let a = document.createElement("a");
    a.href = URL.createObjectURL(blob);
    a.download = "data.csv";
    a.click();
}
$(document).ready(function () {
    let id = "";
    let pelanggan = "";
    let alamat = "";
    let telp = "";

    $("#submit").click(function (e) { 
        e.preventDefault();
        id = $("#id").val();
        pelanggan = $("#pelanggan").val();
        alamat = $("#alamat").val();
        telp = $("#telp").val();

        if (id == "") {
            insertData();
        } else {
            updateData();
        }

        $("#id").val("");
        $("#pelanggan").val("");
        $("#alamat").val("");
        $("#telp").val("");
    });

    function selectData() {
        $.ajax({
            type: "get",
            url: "php/select.php",
            dataType: "json",
            success: function (response) {
                let out = "";
                let No = 1;
                $.each(response, function (key, val) { 
                     out += `<tr>
                        <td>${No++}</td>
                        <td>${val.pelanggan}</td>
                        <td>${val.alamat}</td>
                        <td>${val.telp}</td>
                     </tr>`;
                });

                $("#isidata").html(out);
            }
        });
    }

    function insertData() {
        let dataPelanggan = {
            pelanggan : pelanggan,
            alamat : alamat,
            telp : telp
        }

        $.ajax({
            type: "POST",
            url: "php/insert.php",
            data: JSON.stringify(dataPelanggan),
            // dataType: "dataType",
            success: function (response) {
                let out = `<p>${response}</p>`
                $("#msg").html(out);
            }
        });

        selectData();
    }

    function deleteData() {
        alert("delete");
    }

    function updateData() {
        alert("update");
    }

    selectData();
});
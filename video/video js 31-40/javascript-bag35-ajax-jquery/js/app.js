$(document).ready(function () {

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
        alert("insert");
    }

    function deleteData() {
        alert("delete");
    }

    function updateData() {
        alert("update");
    }

    selectData();

});
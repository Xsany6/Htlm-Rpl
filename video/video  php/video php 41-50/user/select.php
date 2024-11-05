<?php 
    
    $jumlah = $data_base -> row_count("SELECT iduser FROM tbluser");
    
    $LENGTH = 4;
    $AMOUNT = ceil($jumlah/$LENGTH);
    
    if (isset($_GET["page"])) {
        $page = $_GET["page"];
        $start = ($page*$LENGTH) - $LENGTH;
        
    } else {
        $start = 0;

    }
    
    $SQL = "SELECT * FROM tbluser ORDER BY user ASC LIMIT $start,$LENGTH";
    
    $rows = $data_base -> get_all($SQL);
    $id = 1 + $start;

?>

<div class="float-start me-4">
    <a class="btn btn-primary" href="?folder=user&menu=insert" role="button">TAMBAH DATA</a>
</div>
<h3>User</h3>
<table class="table table-bordered w-70">
    <thead>
        <tr>
            <th>#</th>
            <th>User</th>
            <th>Email</th>
            <th>Level</th>
            <th>Delete</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php 
                
                    if ($row["aktif"] == 1) {
                        $status = "AKTIF";

                    } else {
                        $status = "BANNED";

                    }

                ?>
                <td><?php echo $id++ ?></td>
                <td><?php echo $row["user"] ?></td>
                <td><?php echo $row["email"] ?></td>
                <td><?php echo $row["level"] ?></td>
                <td><a href="?folder=user&menu=delete&id=<?php echo $row['iduser'] ?>">Delete</a></td>
                <td><a href="?folder=user&menu=update&id=<?php echo $row['iduser'] ?>"><?php echo $status ?></a></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?php 

    for ($i = 1; $i <= $AMOUNT; $i++) {
        echo "<a href='?folder=user&menu=select&page=$i'>".$i."</a>";
        echo "&nbsp &nbsp &nbsp";
    }

?>
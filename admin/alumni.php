<?php include('db_connect.php'); ?>

<div class="container-fluid">
    <div class="col-lg-12">
        <div class="row mb-4 mt-4">
            <div class="col-md-12">
                <!-- Optional: You can place a header or title here -->
            </div>
        </div>
        
        <div class="row">
            <!-- FORM Panel -->
            <form action="import_alumni.php" method="post" enctype="multipart/form-data">
                <div class="col-md-12 mb-3">
                    <input type="file" name="excel_file" accept=".xls,.xlsx" required>
                    <button type="submit" name="import" class="btn btn-primary">Import Excel Data</button>
                </div>
            </form>

            <!-- Table Panel -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <b>List of Alumni</b>
                    </div>
                    <div class="card-body">
                        <table class="table table-condensed table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">#</th>
                                    <th>Avatar</th>
                                    <th>Name</th>
                                    <th>Course Graduated</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1;
                                $alumni = $conn->query("SELECT a.*, c.course, CONCAT(a.lastname, ', ', a.firstname, ' ', a.middlename) as name 
                                                         FROM alumnus_bio a 
                                                         INNER JOIN courses c ON c.id = a.course_id 
                                                         ORDER BY name ASC");
                                while($row = $alumni->fetch_assoc()):
                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $i++; ?></td>
                                    <td class="text-center">
                                        <div class="avatar">
                                            <img src="assets/uploads/<?php echo htmlspecialchars($row['avatar']); ?>" alt="">
                                        </div>
                                    </td>
                                    <td>
                                        <p><b><?php echo ucwords(htmlspecialchars($row['name'])); ?></b></p>
                                    </td>
                                    <td>
                                        <p><b><?php echo htmlspecialchars($row['course']); ?></b></p>
                                    </td>
                                    <td class="text-center">
                                        <?php if($row['status'] == 1): ?>
                                            <span class="badge badge-primary">Verified</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Not Verified</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary view_alumni" type="button" data-id="<?php echo $row['id']; ?>">View</button>
                                        <button class="btn btn-sm btn-outline-danger delete_alumni" type="button" data-id="<?php echo $row['id']; ?>">Delete</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- Table Panel -->
        </div>
    </div>
</div>

<style>
    td {
        vertical-align: middle !important;
    }
    td p {
        margin: unset;
    }
    img {
        max-width: 100px;
        max-height: 150px;
    }
    .avatar {
        display: flex;
        border-radius: 100%;
        width: 100px;
        height: 100px;
        align-items: center;
        justify-content: center;
        border: 3px solid;
        padding: 5px;
    }
    .avatar img {
        max-width: calc(100%);
        max-height: calc(100%);
        border-radius: 100%;
    }
</style>

<script>
    $(document).ready(function(){
        $('table').dataTable(); // Initialize DataTables
    });

    // View alumni details
    $('.view_alumni').click(function(){
        uni_modal("Bio", "view_alumni.php?id=" + $(this).attr('data-id'), 'mid-large');
    });

    // Confirm and delete alumni
    $('.delete_alumni').click(function(){
        _conf("Are you sure to delete this alumni?", "delete_alumni", [$(this).attr('data-id')]);
    });

    // Function to delete alumni
    function delete_alumni(id) {
        start_load();
        $.ajax({
            url: 'ajax.php?action=delete_alumni',
            method: 'POST',
            data: {id: id},
            success: function(resp) {
                if (resp == 1) {
                    alert_toast("Data successfully deleted", 'success');
                    setTimeout(function(){
                        location.reload();
                    }, 1500);
                }
            }
        });
    }
</script>

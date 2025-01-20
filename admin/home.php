<?php include 'db_connect.php' ?>
<style>
    .cards-container {
        display: flex;
        justify-content: space-between; /* Equal space between the cards */
        align-items: stretch; /* Ensure all cards have equal height */
        flex-wrap: nowrap; /* Prevent cards from wrapping to the next line */
    }
    .card {
        flex: 1 1 0; /* Each card will take equal space */
        margin: 10px; /* Add space between the cards */
        height: 150px; /* Set a fixed height for the cards */
        min-width: 200px; /* Set a minimum width for responsiveness */
        max-width: 250px; /* Maximum width for the cards */
    }
    .card-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%; /* Make the card content stretch to the full height */
    }
    .summary_icon {
        font-size: 3rem;
        color: #ffffff96;
    }
    .text-white h4, .text-white p {
        margin: 0;
    }
</style>

<div class="container-fluid">
    <div class="row mt-3 ml-3 mr-3">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <?php echo "Welcome back ". $_SESSION['login_name']."!" ?>
                    <hr>
                    <div class="cards-container">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <span class="summary_icon"><i class="fa fa-users"></i></span>
                                <div>
                                    <h4><b><?php echo $conn->query("SELECT * FROM alumnus_bio where status = 1")->num_rows; ?></b></h4>
                                    <p><b>Alumni</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <span class="summary_icon"><i class="fa fa-users"></i></span>
                                <div>
                                    <h4><b><?php echo $conn->query("SELECT * FROM student_bio where status = 1")->num_rows; ?></b></h4>
                                    <p><b>Student</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <span class="summary_icon"><i class="fa fa-comments"></i></span>
                                <div>
                                    <h4><b><?php echo $conn->query("SELECT * FROM forum_topics")->num_rows; ?></b></h4>
                                    <p><b>Forum Topics</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <span class="summary_icon"><i class="fa fa-briefcase"></i></span>
                                <div>
                                    <h4><b><?php echo $conn->query("SELECT * FROM careers")->num_rows; ?></b></h4>
                                    <p><b>Posted Jobs</b></p>
                                </div>
                            </div>
                        </div>
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <span class="summary_icon"><i class="fa fa-calendar-day"></i></span>
                                <div>
                                    <h4><b><?php echo $conn->query("SELECT * FROM events where date_format(schedule,'%Y-%m-%d') >= '".date('Y-m-d')."' ")->num_rows; ?></b></h4>
                                    <p><b>Upcoming Events</b></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>              
        </div>
    </div>
</div>

<script>
    $('#manage-records').submit(function(e){
        e.preventDefault()
        start_load()
        $.ajax({
            url:'ajax.php?action=save_track',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            success:function(resp){
                resp=JSON.parse(resp)
                if(resp.status==1){
                    alert_toast("Data successfully saved",'success')
                    setTimeout(function(){
                        location.reload()
                    },800)
                }
            }
        })
    })
    $('#tracking_id').on('keypress',function(e){
        if(e.which == 13){
            get_person()
        }
    })
    $('#check').on('click',function(e){
        get_person()
    })
    function get_person(){
        start_load()
        $.ajax({
            url:'ajax.php?action=get_pdetails',
            method:"POST",
            data:{tracking_id : $('#tracking_id').val()},
            success:function(resp){
                if(resp){
                    resp = JSON.parse(resp)
                    if(resp.status == 1){
                        $('#name').html(resp.name)
                        $('#address').html(resp.address)
                        $('[name="person_id"]').val(resp.id)
                        $('#details').show()
                        end_load()
                    }else if(resp.status == 2){
                        alert_toast("Unknown tracking id.",'danger');
                        end_load();
                    }
                }
            }
        })
    }
</script>

<?php 
include 'admin/db_connect.php'; 
?>
<style>
#portfolio .img-fluid{
    width: calc(100%);
    height: 30vh;
    z-index: -1;
    position: relative;
    padding: 1em;
}
.gallery-list{
    cursor: pointer;
    border: unset;
    flex-direction: inherit;
}
.gallery-img,.gallery-list .card-body {
    width: calc(50%);
}
.gallery-img img{
    border-radius: 5px;
    min-height: 50vh;
    max-width: calc(100%);
}
span.hightlight{
    background: yellow;
}
.carousel,.carousel-inner,.carousel-item{
    min-height: calc(100%);
}
header.masthead,header.masthead:before {
    min-height: 50vh !important;
    height: 50vh !important;
}
.row-items{
    position: relative;
}
.masthead{
    min-height: 23vh !important;
    height: 23vh !important;
}
.masthead:before{
    min-height: 23vh !important;
    height: 23vh !important;
}
</style>

<header class="masthead">
    <div class="container-fluid h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-8 align-self-end mb-4 page-title">
                <h3 class="text-white">Job List</h3>
                <hr class="divider my-4" />
                <div class="row col-md-12 mb-2 justify-content-center">
                    <button class="btn btn-primary btn-block col-sm-4" type="button" id="new_career"><i class="fa fa-plus"></i> Post a Job Opportunity</button>
                </div>   
            </div>
        </div>
    </div>
</header>

<div class="container mt-3 pt-2">
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="filter-field"><i class="fa fa-search"></i></span>
                        </div>
                        <input type="text" class="form-control" placeholder="Filter" id="filter" aria-label="Filter" aria-describedby="filter-field">
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block btn-sm" id="search">Search</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    $event = $conn->query("SELECT c.*,u.name from careers c inner join users u on u.id = c.user_id order by id desc");
    while($row = $event->fetch_assoc()):
        $trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
        unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
        $desc = strtr(html_entity_decode($row['description']), $trans);
        $desc = str_replace(array("<li>", "</li>"), array("", ","), $desc);
    ?>
    <div class="card job-list" data-id="<?php echo $row['id'] ?>">
        <div class="card-body">
            <div class="row align-items-center justify-content-center text-center h-100">
                <div class="">
                    <h3><b class="filter-txt"><?php echo ucwords($row['job_title']) ?></b></h3>
                    <div>
                        <span class="filter-txt"><small><b><i class="fa fa-building"></i> <?php echo ucwords($row['company']) ?></b></small></span>
                        <span class="filter-txt"><small><b><i class="fa fa-map-marker"></i> <?php echo ucwords($row['location']) ?></b></small></span>
                    </div>
                    <hr>
                    <larger class="truncate filter-txt"><?php echo strip_tags($desc) ?></larger>
                    <br>
                    <hr class="divider" style="max-width: calc(80%)">
                    <span class="badge badge-info float-left px-3 pt-1 pb-1">
                        <b><i>Posted by: <?php echo $row['name'] ?></i></b>
                    </span>
                    <button class="btn btn-primary float-right read_more" data-id="<?php echo $row['id'] ?>">Read More</button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php endwhile; ?>
</div>

<!-- Modal Structure -->
<div id="uni_modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content will be dynamically loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Function for opening the modal with content loaded dynamically
function uni_modal(title, url, size) {
    $.ajax({
        url: url,
        error: function(err) {
            console.log(err);
            alert("An error occurred.");
        },
        success: function(resp) {
            if (typeof resp === 'string') {
                $('#uni_modal .modal-title').html(title);
                $('#uni_modal .modal-body').html(resp);
                if (size != '') {
                    $('#uni_modal .modal-dialog').addClass(size);
                } else {
                    $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md");
                }
                $('#uni_modal').modal('show');
            }
        }
    });
}

// New job posting modal
$('#new_career').click(function(){
    uni_modal("New Job Hiring", "manage_career.php", 'modal-lg');
});

// View job details modal
$('.read_more').click(function(){
    uni_modal("Career Opportunity", "view_jobs.php?id=" + $(this).attr('data-id'), 'modal-lg');
});

// Search functionality
$('#filter').keypress(function(e){
    if(e.which == 13)
        $('#search').trigger('click');
});

$('#search').click(function(){
    var txt = $('#filter').val();
    start_load();
    if(txt == ''){
        $('.job-list').show();
        end_load();
        return false;
    }
    $('.job-list').each(function(){
        var content = "";
        $(this).find(".filter-txt").each(function(){
            content += ' ' + $(this).text();
        });
        if((content.toLowerCase()).includes(txt.toLowerCase()) == true){
            $(this).toggle(true);
        } else {
            $(this).toggle(false);
        }
    });
    end_load();
});
</script>

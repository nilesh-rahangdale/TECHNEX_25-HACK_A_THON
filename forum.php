<?php 
include 'admin/db_connect.php'; 
?>
<style>
#portfolio .img-fluid {
    width: calc(100%);
    height: 30vh;
    z-index: -1;
    position: relative;
    padding: 1em;
}
.gallery-list {
    cursor: pointer;
    border: unset;
    flex-direction: inherit;
}
.gallery-img, .gallery-list .card-body {
    width: calc(50%);
}
.gallery-img img {
    border-radius: 5px;
    min-height: 50vh;
    max-width: calc(100%);
}
span.hightlight {
    background: yellow;
}
.carousel, .carousel-inner, .carousel-item {
    min-height: calc(100%);
}
header.masthead, header.masthead:before {
    min-height: 50vh !important;
    height: 50vh !important;
}
.row-items {
    position: relative;
}
.masthead {
    min-height: 23vh !important;
    height: 23vh !important;
}
.masthead:before {
    min-height: 23vh !important;
    height: 23vh !important;
}
</style>

<header class="masthead">
    <div class="container-fluid h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-8 align-self-end mb-4 page-title">
                <h3 class="text-white">Forum List</h3>
                <hr class="divider my-4" />
                <div class="row col-md-12 mb-2 justify-content-center">
                    <button class="btn btn-primary btn-block col-sm-4" type="button" id="new_forum">
                        <i class="fa fa-plus"></i> Create New Topic
                    </button>
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
                        <input type="text" class="form-control" id="filter" placeholder="Filter" aria-label="Filter" aria-describedby="filter-field">
                    </div>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-primary btn-block btn-sm" id="search">Search</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    $event = $conn->query("SELECT f.*, u.name FROM forum_topics f INNER JOIN users u ON u.id = f.user_id ORDER BY f.id DESC");
    while($row = $event->fetch_assoc()):
        $trans = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
        unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
        $desc = strtr(html_entity_decode($row['description']), $trans);
        $desc = str_replace(array("<li>", "</li>"), array("", ","), $desc);
        $count_comments = $conn->query("SELECT * FROM forum_comments WHERE topic_id = ".$row['id'])->num_rows;
    ?>
    <div class="card Forum-list" data-id="<?php echo $row['id'] ?>">
        <div class="card-body">
            <div class="row align-items-center justify-content-center text-center h-100">
                <div class="">
                    <?php if($_SESSION['login_id'] == $row['user_id']): ?>
                    <div class="dropdown float-right mr-4">
                        <a class="text-dark" href="javascript:void(0)" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fa fa-ellipsis-v"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <a class="dropdown-item edit_forum" data-id="<?php echo $row['id'] ?>" href="javascript:void(0)">Edit</a>
                            <a class="dropdown-item delete_forum" data-id="<?php echo $row['id'] ?>" href="javascript:void(0)">Delete</a>
                        </div>
                    </div>
                    <?php endif; ?>
                    <h3><b class="filter-txt"><?php echo ucwords($row['title']) ?></b></h3>
                    <hr>
                    <larger class="truncate filter-txt"><?php echo strip_tags($desc) ?></larger>
                    <br>
                    <hr class="divider" style="max-width: calc(80%)">
                    <span class="badge badge-info float-left px-3 pt-1 pb-1">
                        <b><i>Topic Created by: <span class="filter-txt"><?php echo $row['name'] ?></span></i></b>
                    </span>
                    <span class="badge badge-secondary float-left px-3 pt-1 pb-1 ml-2">
                        <b><i class="fa fa-comments"></i> <i><?php echo $count_comments ?> Comments</i></b>
                    </span>
                    <button class="btn btn-primary float-right view_topic" data-id="<?php echo $row['id'] ?>">View Topic</button>
                </div>
            </div>
        </div>
    </div>
    <br>
    <?php endwhile; ?>
</div>

<!-- Modal for Uni_Modal -->
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
// Dynamic modal loading function
function uni_modal(title, url, size = '') {
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

// Handling creation of a new forum topic
$('#new_forum').click(function() {
    uni_modal("New Topic", "manage_forum.php", 'mid-large');
});

// Viewing a specific forum topic
$('.view_topic').click(function() {
    location.replace('index.php?page=view_forum&id=' + $(this).attr('data-id'));
});

// Editing a forum topic
$('.edit_forum').click(function() {
    uni_modal("Edit Topic", "manage_forum.php?id=" + $(this).attr('data-id'), 'mid-large');
});

// Deleting a forum topic with confirmation
$('.delete_forum').click(function() {
    _conf("Are you sure to delete this Topic?", "delete_forum", [$(this).attr('data-id')], 'mid-large');
});

// Deleting the forum topic via AJAX
function delete_forum(id) {
    start_load();
    $.ajax({
        url: 'admin/ajax.php?action=delete_forum',
        method: 'POST',
        data: {id: id},
        success: function(resp) {
            if (resp == 1) {
                alert_toast("Topic successfully deleted", 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            }
        }
    });
}

// Search functionality with real-time filtering
$('#filter').keypress(function(e) {
    if (e.which == 13) {
        $('#search').trigger('click');
    }
});

$('#search').click(function() {
    var txt = $('#filter').val();
    start_load();
    if (txt == '') {
        $('.Forum-list').show();
        end_load();
        return false;
    }
    $('.Forum-list').each(function() {
        var content = "";
        $(this).find(".filter-txt").each(function() {
            content += ' ' + $(this).text();
        });
        if ((content.toLowerCase()).includes(txt.toLowerCase())) {
            $(this).toggle(true);
        } else {
            $(this).toggle(false);
        }
    });
    end_load();
});
</script>

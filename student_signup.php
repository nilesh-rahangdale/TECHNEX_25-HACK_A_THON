<?php 
include 'admin/db_connect.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        .masthead{
            min-height: 23vh !important;
            height: 23vh !important;
        }
        .masthead:before{
            min-height: 23vh !important;
            height: 23vh !important;
        }
        img#cimg{
            max-height: 10vh;
            max-width: 6vw;
        }
    </style>
</head>
<body>
    <header class="masthead">
        <div class="container-fluid h-100">
            <div class="row h-100 align-items-center justify-content-center text-center">
                <div class="col-lg-8 align-self-end mb-4 page-title">
                    <h3 class="text-white">Student Sign Up</h3>
                    <hr class="divider my-4" />
                </div>
            </div>
        </div>
    </header>

    <div class="container mt-3 pt-2">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="col-md-12">
                            <form action="" id="create_account_student">
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Last Name</label>
                                        <input type="text" class="form-control" name="lastname" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="control-label">First Name</label>
                                        <input type="text" class="form-control" name="firstname" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Middle Name</label>
                                        <input type="text" class="form-control" name="middlename">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Gender</label>
                                        <select class="custom-select" name="gender" required>
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Year</label>
                                        <select class="custom-select" name="year" required>
                                            <option value="1">1st</option>
                                            <option value="2">2nd</option>
                                            <option value="3">3rd</option>
                                            <option value="4">4th</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Course Pursuing</label>
                                        <select class="custom-select select2" name="course_id" required>
                                            <option></option>
                                            <?php 
                                            $course = $conn->query("SELECT * FROM courses order by course asc");
                                            while($row=$course->fetch_assoc()):
                                            ?>
                                                <option value="<?php echo $row['id'] ?>"><?php echo $row['course'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-5">
                                        <label for="" class="control-label">Currently Connected To</label>
                                        <textarea name="connected_to" id="" cols="30" rows="3" class="form-control"></textarea>
                                    </div>
                                    <div class="col-md-5">
                                        <label for="" class="control-label">Image</label>
                                        <input type="file" class="form-control" name="img" onchange="displayImg(this,$(this))">
                                        <img src="" alt="" id="cimg">
                                    </div>  
                                </div>
                                <div class="row">
                                     <div class="col-md-4">
                                        <label for="" class="control-label">Email</label>
                                        <input type="email" class="form-control" name="email" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="" class="control-label">Password</label>
                                        <input type="password" class="form-control" name="password" required>
                                    </div>
                                </div>
                                <div id="msg"></div>
                                <hr class="divider">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-primary">Create Student Account</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.select2').select2({
            placeholder: "Please Select Here",
            width: "100%"
        });

        function displayImg(input,_this) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#cimg').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#create_account_student').submit(function(e){
            e.preventDefault();
            start_load();
            $.ajax({
                url: 'admin/ajax.php?action=student_signup',
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                success: function(resp){
                    if(resp == 1){
                        location.replace('index.php');
                    }else{
                        $('#msg').html('<div class="alert alert-danger">Email already exists.</div>');
                        end_load();
                    }
                }
            });
        });
    </script>
</body>
</html>

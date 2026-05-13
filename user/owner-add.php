<?php require_once('header.php'); ?>

<?php
if(isset($_POST['form1'])) {
    $valid = 1;
    $error_message = '';
    $success_message = '';

    if(empty($_POST['owner_name'])) {
        $valid = 0;
        $error_message .= "Owner name cannot be empty<br>";
    }

    if(empty($_POST['owner_phone'])) {
        $valid = 0;
        $error_message .= "Phone number cannot be empty<br>";
    }

    if(empty($_POST['owner_area'])) {
        $valid = 0;
        $error_message .= "Area cannot be empty<br>";
    }

    if(empty($_POST['owner_location'])) {
        $valid = 0;
        $error_message .= "Location cannot be empty<br>";
    }

    // ✅ PASSWORD VALIDATION ADDED
    if(empty($_POST['owner_password'])) {
        $valid = 0;
        $error_message .= "Password cannot be empty<br>";
    }

    $path     = $_FILES['owner_photo']['name'];
    $path_tmp = $_FILES['owner_photo']['tmp_name'];
    $ext      = '';

    if($path != '') {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if(!in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) {
            $valid = 0;
            $error_message .= "Photo must be jpg, jpeg, gif or png<br>";
        }
    }

    $pet_names   = isset($_POST['pet_name'])  ? array_values(array_filter(array_map('trim', $_POST['pet_name'])))  : [];
    $pet_types   = isset($_POST['pet_type'])  ? array_values(array_filter($_POST['pet_type'],  'strlen'))          : [];
    $pet_breeds  = isset($_POST['pet_breed']) ? array_values(array_filter($_POST['pet_breed'], 'strlen'))          : [];
    $pet_ages    = isset($_POST['pet_age'])   ? $_POST['pet_age']                                                  : [];


    if($valid == 1) {

        $final_name = '';
        if($path != '') {
            $stmt = $pdo->prepare("SHOW TABLE STATUS LIKE 'tbl_owner'");
            $stmt->execute();
            $r = $stmt->fetch();
            $ai_id = $r['Auto_increment'];

            $final_name = 'owner-' . $ai_id . '.' . $ext;
            move_uploaded_file($path_tmp, '../admin/assets/uploads/owners/' . $final_name);
        }

        $no_of_pets = count($pet_names);

        // ✅ PASSWORD HASH ADDED
        $hashed_password = password_hash($_POST['owner_password'], PASSWORD_DEFAULT);

        $statement = $pdo->prepare("INSERT INTO tbl_owner (
            owner_name, owner_phone, owner_email, password,
            owner_address, owner_area, owner_location,
            owner_photo, no_of_pets, is_active
        ) VALUES (?,?,?,?,?,?,?,?,?,?)");

        $statement->execute([
            $_POST['owner_name'],
            $_POST['owner_phone'],
            $_POST['owner_email'],
            $hashed_password,
            $_POST['owner_address'],
            $_POST['owner_area'],
            $_POST['owner_location'],
            $final_name,
            $no_of_pets,
            $_POST['is_active']
        ]);

        $new_owner_id = $pdo->lastInsertId();

        for($i = 0; $i < count($pet_names); $i++) {
            if(!empty($pet_names[$i])) {
                $statement = $pdo->prepare("INSERT INTO tbl_pet (owner_id, pet_name, pet_type, pet_breed, pet_age) VALUES (?,?,?,?,?)");
                $statement->execute([
                    $new_owner_id,
                    $pet_names[$i],
                    $pet_types[$i]  ?? '',
                    $pet_breeds[$i] ?? '',
                    $pet_ages[$i]   ?? 0
                ]);
            }
        }

        $success_message = 'Owner and pets added successfully.';
    }
}
?>



<section class="content">
    <div class="row">
        <div class="col-md-12">

            <?php if(!empty($error_message)): ?>
            <div class="callout callout-danger"><p><?php echo $error_message; ?></p></div>
            <?php endif; ?>

            <?php if(!empty($success_message)): ?>
            <div class="callout callout-success"><p><?php echo $success_message; ?></p></div>
            <?php endif; ?>

            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="box box-info">
                    <div class="box-body">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Owner Name <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="owner_name" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Phone <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="owner_phone" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Email</label>
                            <div class="col-sm-4">
                                <input type="email" name="owner_email" class="form-control">
                            </div>
                        </div>

                        <!-- ✅ PASSWORD FIELD ADDED -->
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Password <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="password" name="owner_password" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Address</label>
                            <div class="col-sm-4">
                                <textarea name="owner_address" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Area <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="owner_area" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Location <span>*</span></label>
                            <div class="col-sm-4">
                                <input type="text" name="owner_location" class="form-control">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Owner Photo</label>
                            <div class="col-sm-4">
                                <input type="file" name="owner_photo">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Pets <span>*</span></label>
                            <div class="col-sm-8">
                                <table id="PetTable" class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Pet Name</th>
                                            <th>Type</th>
                                            <th>Breed</th>
                                            <th>Age</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="pet_name[]" class="form-control"></td>
                                            <td><input type="text" name="pet_type[]" class="form-control"></td>
                                            <td><input type="text" name="pet_breed[]" class="form-control"></td>
                                            <td><input type="number" name="pet_age[]" class="form-control"></td>
                                            <td><a href="javascript:void(0)" class="DeletePet btn btn-danger btn-xs">X</a></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <input type="button" id="btnAddPet" value="Add Pet" class="btn btn-warning btn-xs">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Is Active?</label>
                            <div class="col-sm-4">
                                <select name="is_active" class="form-control">
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-6">
                                <button type="submit" class="btn btn-success" name="form1">Add Owner</button>
                            </div>
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</section>

<script>
document.getElementById('btnAddPet').addEventListener('click', function() {
    var tbody = document.querySelector('#PetTable tbody');
    var row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="text" name="pet_name[]" class="form-control"></td>
        <td><input type="text" name="pet_type[]" class="form-control"></td>
        <td><input type="text" name="pet_breed[]" class="form-control"></td>
        <td><input type="number" name="pet_age[]" class="form-control"></td>
        <td><a href="javascript:void(0)" class="DeletePet btn btn-danger btn-xs">X</a></td>
    `;
    tbody.appendChild(row);
});
</script>

<?php require_once('footer.php'); ?>
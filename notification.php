<?php require_once('header.php'); ?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Notifications</h1>
    </div>
</section>

<section class="content">
<div class="box box-info">
<div class="box-body table-responsive">

<table id="example1" class="table table-bordered table-hover table-striped">
<thead>
<tr>
    <th>#</th>
    <th>Owner</th>
    <th>Pet</th>
    <th>Type</th>
    <th>Title</th>
    <th>Message</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php
$i = 0;

$statement = $pdo->prepare("
    SELECT 
        n.*, 
        p.pet_name,
        o.owner_name
    FROM notifications n
    LEFT JOIN tbl_pet p ON n.pet_id = p.pet_id
    LEFT JOIN tbl_owner o ON n.user_id = o.owner_id
    ORDER BY n.id DESC
");

$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

foreach($result as $row){
$i++;
?>
<tr>
    <td><?php echo $i; ?></td>

    <!-- OWNER -->
    <td>
        <?php echo htmlspecialchars($row['owner_name'] ?? 'N/A'); ?>
    </td>

    <!-- PET -->
    <td>
        <?php echo htmlspecialchars($row['pet_name'] ?? 'N/A'); ?>
    </td>

    <!-- TYPE -->
    <td>
        <?php
        $type = strtolower($row['type']);
        $label = 'default';

        if($type == 'alert') $label = 'danger';
        elseif($type == 'warning') $label = 'warning';
        elseif($type == 'info') $label = 'info';
        ?>

        <span class="label label-<?php echo $label; ?>">
            <?php echo ucfirst($type); ?>
        </span>
    </td>

    <!-- TITLE -->
    <td><?php echo htmlspecialchars($row['title'] ?? ''); ?></td>

    <!-- MESSAGE -->
    <td style="max-width:200px;">
        <?php echo htmlspecialchars($row['message'] ?? ''); ?>
    </td>

    <!-- STATUS -->
    <td>
        <?php if(!empty($row['is_read'])): ?>
            <span class="label label-success">Read</span>
        <?php else: ?>
            <span class="label label-danger">Unread</span>
        <?php endif; ?>
    </td>

    <!-- DATE -->
    <td>
        <?php 
        echo !empty($row['created_at']) 
            ? date('Y-m-d H:i', strtotime($row['created_at'])) 
            : '-'; 
        ?>
    </td>

    <!-- ACTION -->
    <td>

        <?php if(empty($row['is_read'])): ?>
        <a href="notification-mark-read.php?id=<?php echo $row['id']; ?>" 
           class="btn btn-success btn-xs">
           Mark Read
        </a>
        <?php endif; ?>

        <a href="notification-delete.php?id=<?php echo $row['id']; ?>" 
           class="btn btn-danger btn-xs"
           onclick="return confirm('Delete this notification?')">
           Delete
        </a>

        <?php if(!empty($row['link'])): ?>
        <a href="<?php echo $row['link']; ?>" 
           class="btn btn-info btn-xs">
           Open
        </a>
        <?php endif; ?>

    </td>

</tr>
<?php } ?>
</tbody>

</table>

</div>
</div>
</section>

<?php require_once('footer.php'); ?>
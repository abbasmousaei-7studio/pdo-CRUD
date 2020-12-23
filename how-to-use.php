<?php

include './db-class.php';
// Create a new class of db-class
$dbcon = new DB();

$tableName = 'test';

// INSERT DATA
$insertData = [
    'usrname' => 'Abbas Mousaei',
    'usrpassword' => '7studio',
    'usrdate' => date('Y-m-d'),
];
    $insert = $dbcon->insert($tableName, $insertData);
    $statusMsg = ($insert) ? 'User data has been inserted successfully.' : 'Some problem occurred, please try again.';
    echo $statusMsg;

    // UPDATE DATE
    $id = 1;
    $updateDate = [
        'usrname' => 'Abbas Mousaei',
        'usrpassword' => '7studio',
        'usrdate' => date('Y-m-d'),
    ];
    $updateCondition = ['id' => $id];
    $update = $dbcon->update($tableName, $updateDate, $updateCondition);
    $statusMsg = ($update) ? 'User data has been updated successfully.' : 'Some problem occurred, please try again.';
    echo $statusMsg;

    //DELETE DATE
    $id = 1;
    $deleteCondition = ['id' => $id];
    $delete = $dbcon->delete($tableName, $deleteCondition);
    $statusMsg = ($delete) ? 'Selected record has been deleted successfully.' : 'Some problem occurred, please try again.';
    echo $statusMsg;

    // SELECT QUERY by getRows class
    // @conditions array select, where, order_by, limit and return_type
    $selectConditions = [
        'where' => ['usrname' => 'Abbas Mousaei'],
        'order_by' => 'id ASC',
        'return_type' => 'all',
    ];

    $count = $dbcon->getRows($tableName, ['return_type' => 'count']);
    echo $count.'Item(s)<br>';

    $users = $dbcon->getRows($tableName, $selectConditions);
    if (!empty($users)) {
        $i = 0;
        foreach ($users as $user) {
            ++$i; ?>
<tr>
    <td><?php echo $i; ?>
    </td>
    <td><?php echo $user['usrname']; ?>
    </td>
    <td><?php echo $user['usrpassword']; ?>
    </td>
    <td><?php echo $user['usrdate']; ?>
    </td>
</tr>
<?php
        }
    }

    // SELECT QUERY by dynamicGetRows class
    // @conditions array select, where, order_by, limit and return_type
    $selectConditions = [
        'select' => 'usrname',
        'where' => [
            'usrname' => "LIKE '%mou%'",
        ],
        'return_type' => 'single',
    ];
    $user = $dbcon->dynamicGetRows($tableName, $selectConditions);
    if ($user) {
        echo 'Hello '.$user['usrname'];
    }

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered">
        <thead class="table-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">วันที่/เวลา</th>
                <th scope="col">เวลาออก</th>
                <th scope="col">สถานะ/เหตุผล</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Note: $db and $userData are expected to be defined by the parent file (index.php)
            $employee_id = $userData['id'];
            $detailWork = new DetailWork($db);
            $info = $detailWork->readInfo($employee_id);

            if ($info) {
                $index = 1;
                foreach ($info['attendance'] as $row) {
                    $status_badge = ($row['status'] == 'on_time') ? '<span class="badge bg-success">ปกติ</span>' : '<span class="badge bg-warning">สาย</span>';
                    echo "<tr>
                            <td>{$index}</td>
                            <td>" . htmlspecialchars($row['created_at']) . "</td>
                            <td>" . htmlspecialchars($row['departure_time'] ?? '-') . "</td>
                            <td>{$status_badge}</td>
                          </tr>";
                    $index++;
                }
                foreach ($info['leave'] as $row) {
                    echo "<tr>
                            <td>{$index}</td>
                            <td>ลา ({$row['leave_type']}): {$row['leave_date']}</td>
                            <td>-</td>
                            <td>" . htmlspecialchars($row['reason'] ?? '-') . "</td>
                          </tr>";
                    $index++;
                }
            } else {
                echo "<tr><td colspan='4' class='text-center'>ไม่พบข้อมูลการทำงาน</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

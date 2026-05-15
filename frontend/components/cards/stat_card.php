<?php
// Note: $dailyStats is expected from parent
$cards = [
    ['title' => 'เข้างานวันนี้', 'value' => $dailyStats['attendances'] ?? 0, 'color' => 'primary', 'icon' => 'fa-calendar-check'],
    ['title' => 'ออกงานวันนี้', 'value' => $dailyStats['departures'] ?? 0, 'color' => 'success', 'icon' => 'fa-sign-out-alt'],
    ['title' => 'ลาป่วยวันนี้', 'value' => $dailyStats['sick_leaves'] ?? 0, 'color' => 'danger', 'icon' => 'fa-medkit'],
    ['title' => 'ลากิจวันนี้', 'value' => $dailyStats['personal_leaves'] ?? 0, 'color' => 'warning', 'icon' => 'fa-user-clock']
];
?>

<?php foreach ($cards as $card): ?>
<div class="col-xl-3 col-md-6 mb-4">
    <div class="card h-100 p-2">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col me-2">
                    <div class="text-xs fw-bold text-muted text-uppercase mb-1"><?= $card['title'] ?></div>
                    <div class="h4 mb-0 fw-bold text-dark"><?= $card['value'] ?> <small class="text-muted fs-6">คน</small></div>
                </div>
                <div class="col-auto">
                    <div class="bg-<?= $card['color'] ?>-subtle p-3 rounded-circle">
                        <i class="fas <?= $card['icon'] ?> fa-xl text-<?= $card['color'] ?>"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

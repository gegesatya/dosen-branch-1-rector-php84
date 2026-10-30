<div class="course-description">
    <h3 class="description-head"><?php echo get_phrase('Deskripsi') ?></h3>
    <?= $rps->deskripsi_mata_kuliah; ?>
</div>

<div class="course-description">
    <h3 class="description-head"><?php echo get_phrase('Referensi') ?></h3>
    <ul class="step-down">
        <?php echo $rps->referensi; ?>
    </ul>
</div>

<div class="course-description">
    <h3 class="description-head"><?php echo get_phrase('Realisasi Pertemuan') ?></h3>


</div>
<button class="btn btn-primary" type="button" onclick="reload_table()"><i class="glyphicon glyphicon-refresh"></i> Reload</button>
<div class="table-responsive">
    <table class="table table-bordered" id="datatable_ajax">
        <thead>
            <tr role="row" class="heading">
                <th>Meet | Tgl | Jam</th>
                <th>Bahasan</th>
                <th>Realisasi</th>
                <th style="max-width: 5px !important;"></th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
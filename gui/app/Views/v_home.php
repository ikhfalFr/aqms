<?= $this->extend('layouts/layouts') ?>
<?= $this->section('content') ?>
<div class="container-md py-1">
    <div class="row mt-2 justify-content-start bg-dark">

        <div class="col-sm mx-2">
            <?php if (!$is_cems) : ?>
                <h1 class="h4 mt-2 text-light" data-intro="Gas"><?= lang('Global.Gases') ?></h1>
            <?php endif ?>
            <div id="gas-content">
                <?php foreach ($gases as $gas) : ?>
                    <div class="my-1 mx-n2 shadow px-3 rounded" style="border:5px solid RGB(124,122,243);background: RGBA(124,122,243,0.7);">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 py-0 font-weight-bold text-light"><?= $gas->caption_id ?></span>
                            <span class="py-0 small font-weight-bold sensor d-none text-light" id="svalue_<?= $gas->code ?>">0</span>
                        </div>
                        <div class="m-0 d-flex justify-content-center">
                            <div class="d-flex align-items-center">
                                <h3 class="h3 mr-1 text-light" id="value_<?= $gas->code ?>">0</h3>
                                &nbsp;&nbsp;<p <?php if ($gas->default_unit == "µg/m<sup>3") : ?> class="switch-unit" <?php endif ?> style="color:#FFFF00"><?= $gas->default_unit ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        </div>
    <div class="row">
        <div class="col-md-12 my-2">
            <div class="px-3 mb-md-0 mb-3 overflow-hidden">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-sm-items-start">
                    <div id="location">
                        <div id="aqm_voltage">
                            <?php if (!$is_cems) : ?>
                                <span class="icon" style="display:inline-block;position:relative;top:-5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <circle cx="12" cy="11" r="3"></circle>
                                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path>
                                    </svg>
                                </span>
                                <h2 class="h4 text-light" style="display:inline-block;" data-intro="<?= lang('Global.intro_aqms_location') ?>" style="cursor: pointer;" unselectable="on" onselectstart="return false;" onmousedown="return false;"><?= @$stationname ?></h2>
                            <?php endif ?>
                            <h2 class="h4 text-light" id="date"></h2>
                        </div>

                    </div>
                    <div>
                        <div id="unit" class="my-1 d-flex flex-column flex-md-row justify-content-between align-md-items-center">
                            <div class="mr-3">
                                <span class="icon" style="display:inline-block;position:relative;top:-5px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-atom" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <line x1="12" y1="12" x2="12" y2="12.01"></line>
                                        <path d="M12 2a4 10 0 0 0 -4 10a4 10 0 0 0 4 10a4 10 0 0 0 4 -10a4 10 0 0 0 -4 -10" transform="rotate(45 12 12)"></path>
                                        <path d="M12 2a4 10 0 0 0 -4 10a4 10 0 0 0 4 10a4 10 0 0 0 4 -10a4 10 0 0 0 -4 -10" transform="rotate(-45 12 12)"></path>
                                    </svg>
                                </span>
                                <h7 class="text-light" style="display:inline-block;"><b><?= lang('Global.Unit') ?></b></h7>
                            </div>
                            <div>
                                <span class="text-light" id="unit-content" style="font-weight:bolder;font-size:18px;">(µg/m3)</span>
                                <button type="button" class="btn-dark rounded border border-light btn btn-sm btn-info" id="btn-unit" data-intro="<?= lang('Global.intro_change_unit') ?>">
                                    <?= lang('Global.Switch') ?>
                                </button>
                            </div>
                        </div>
                        
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('css') ?>
<?= $this->endSection('css') ?>
<?= $this->section('js') ?>

<script>
    $(document).ready(function() {
        var begin = 1;
        var beginUnit = 1;
        setInterval(() => {
            $.ajax({
                url: '<?= base_url('measurementlog') ?>',
                dataType: 'json',
                success: function(data) {
                    if (data !== null) {
                        data?.logs.map(function(value, index) {
                            try {
                                let param_value = cleanStr(value?.value);
                                let default_unit = cleanStr(value?.default_unit);
                                let molecular_mass = cleanStr(value?.molecular_mass);
                                let p_type = value?.p_type
                                if (p_type == 'gas' && default_unit == "µg/m<sup>3") {
                                    switch (beginUnit) {
                                        case 2:
                                            param_value = calculatePpm(param_value, molecular_mass);
                                            break;
                                        case 3:
                                            param_value = calculatePpm(param_value, molecular_mass) * 1000;
                                            break;
                                        case 1:
                                        default:
                                            break;
                                    }
                                }
                                $(`#value_${value.code}`).html(param_value);
                                $(`#svalue_${value.code}`).html(cleanStr(value?.sensor_value) + " Volt");
                            } catch (err) {
                                console.error(err);
                            }

                        });
                        
                        // LOGIKA TIMER PUMP DIHAPUS AGAR TIDAK ERROR SAAT MENCARI ELEMEN DOM
                    }

                },
                error: function(xhr, status, err) {
                    console.log(err);
                }
            })
        }, 1000);
        $('#btn-unit').click(function(e) {
            beginUnit++;
            if (beginUnit > 3) {
                beginUnit = 1;
            }
            switch (beginUnit) {
                case 2: //ppm
                    $('#unit-content').html(`(ppm)`);
                    unit = `ppm`;
                    break;
                case 3: //ppb
                    $('#unit-content').html(`(ppb)`);
                    unit = `ppb`;
                    break;
                case 1: //micro
                default:
                    $('#unit-content').html(`(µg/m<sup>3</sup>)`);
                    unit = `µg/m<sup>3</sup>`;
                    break;
            }
            $('.switch-unit').html(unit)

        });

        function calculatePpm(ug, molecular_mass) {
            try {
                ug = parseFloat(ug);
                molecular_mass = parseFloat(molecular_mass);
                let value = (ug * 24.45) / (1000 * molecular_mass);
                <?php if (!$is_cems) : ?>
                    return value.toFixed(3);
                <?php else : ?>
                    return value.toFixed(1);
                <?php endif ?>
            } catch (err) {
                toastr.error(err);
                return 0;
            }
        }
    });
</script>
<script>
    function cleanStr(str) {
        try {
            if (str === undefined || str === null || str === "") {
                return `0`;
            }
        } catch (err) {
            return `0`;
        }
        return str;
    }
</script>
<script>
    var x = 1;
    var show = true;
    $('#aqm_voltage').click(function() {
        x++;
        if (x > 3) {
            if (show) {
                $('.sensor').removeClass('d-none');
            } else {
                $('.sensor').addClass('d-none');
            }
            show = !show;
            x = 1;
        }
    })
</script>
<?= $this->endSection() ?>

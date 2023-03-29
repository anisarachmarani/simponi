<!doctype html>
<html lang="en">

<head>
    <?php $this->load->view("admin/partials/head.php") ?>
    <?php $this->load->view("admin/partials/css.php") ?>
    <style>
        .apex-charts text {
            font-family: var(--bs-font-sans-serif)!important;
            fill: #000000;
        }

        #bank_stats {
            height: 355px;
        }
        
        #bank_stats div {
            height: 100%;
            overflow-y: scroll;
        }
    </style>
</head>

<body>

    <!-- <body data-layout="horizontal"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php $this->load->view("admin/partials/header.php") ?>

        <?php $this->load->view("admin/partials/menu.php") ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center
                                justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Selamat Datang, <?php echo $this->session->userdata('nama') ?> !</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item active">Dashboard</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-12 col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <div id="chart_payment" data-colors='["#5156be", "#2ab57d"]' class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6>Statistik Channel <?= date('M').' '.date('Y') ?></h6>
                                </div>
                                <div class="card-body" id="bank_stats">
                                    <div>
                                        <table>
                                            <tr>
                                                <th>Nama Channel</th>
                                                <th>Jumlah</th>
                                            </tr>

                                            <?php foreach ($bank_stats as $key => $channel) : ?>
                                                <tr>
                                                    <td><?= $channel->name; ?></td>
                                                    <td class="text-end"><?= $channel->jumlah; ?></td>
                                                </tr>x
                                            <?php endforeach ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div id="chart_payment_month" data-colors='["#2ab57d"]' class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h4 class="card-title">Daftar Billing</h4>
                                        <a href="<?php echo base_url(); ?>admin/Billing" class="btn btn-primary waves-effect waves-light">Selengkapnya</a>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Tanggal Simponi</th>
                                                    <th>Jumlah</th>
                                                    <th>Departement</th>
                                                    <th>Nomor Billing</th>
                                                    <th>Status</th>
                                                    <th>Error</th>
                                                    <th>Error Pembayaran</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($billing as $key => $item) : ?>
                                                    <tr>
                                                        <td><?php echo date('d M Y, H:i', strtotime($item->date_register)) ?></td>
                                                        <td><?php echo date('d M Y, H:i', strtotime($item->date_simponi)) ?></td>
                                                        <td class="text-end"><?php echo number_format($item->total); ?></td>
                                                        <td><?php echo $item->department_name; ?></td>
                                                        <td><?php echo $item->billing_id ?></td>
                                                        <td><?php echo $item->status_name ?></td>
                                                        <td><?php echo ($item->error != NULL) ? "$item->error" : '-' ?></td>
                                                        <td><?php echo ($item->error_pay != NULL) ? "$item->error_pay" : '-' ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end col -->

                        <!-- <div class="col-12 col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="card-title">Daftar Bank</h4>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Nama</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($bank as $key => $item) : ?>
                                                    <tr>
                                                        <td><?php echo $item->code ?></td>
                                                        <td class="text-start"><?php echo $item->name ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="<?php echo base_url(); ?>admin/Bank" class="btn btn-primary waves-effect waves-light w-50 mt-3">Selengkapnya</a>
                                </div>
                            </div>
                        </div> -->
                        <!-- end col -->

                        <!-- <div class="col-12 col-md-6">
                            <div class="card">
                                <div class="card-body text-center">
                                    <h4 class="card-title">Daftar Departemen</h4>
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Kode</th>
                                                    <th>Nama</th>
                                                    <th>User ID</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($department as $key => $item) : ?>
                                                    <tr>
                                                        <td><?php echo $item->code_unit ?></td>
                                                        <td class="text-start"><?php echo $item->name ?></td>
                                                        <td><?php echo $item->user_id ?></td>
                                                    </tr>
                                                <?php endforeach ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <a href="<?php echo base_url(); ?>admin/Department" class="btn btn-primary waves-effect waves-light w-50 mt-3">Selengkapnya</a>
                                </div>
                            </div>
                        </div> -->
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- container-fluid -->
            </div>
        </div>

        <?php $this->load->view("admin/partials/footer.php") ?>

        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?php $this->load->view("admin/partials/sidebar.php") ?>

    <?php $this->load->view("admin/partials/script.php") ?>

    <!-- apexcharts js -->
    <script src="<?= base_url() ?>assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- apexcharts init -->
    <script>
        function getChartColorsArray(chartId) {
            var colors = $(chartId).attr('data-colors');
            var colors = JSON.parse(colors);
            return colors.map(function(value) {
                var newValue = value.replace(' ', '');
                if (newValue.indexOf('--') != -1) {
                    var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                    if (color) return color;
                } else {
                    return newValue;
                }
            })
        }

        var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];;
        var date = new Date();

        var data_payment = <?php echo $grafik_payment ?>;
        let tanggalArray = [];
        let jumlahArray1 = [];

        data_payment.forEach(function(obj) {
            let tanggal = obj.tanggal.slice(8, 10);
            tanggalArray.push(tanggal);
        });

        data_payment.forEach(function(obj) {
            let jumlah = obj.jumlah;
            jumlahArray1.push(jumlah);
        });

        // console.log(data_payment);

        var lineDatalabelColors = getChartColorsArray("#chart_payment");
        var options = {
            chart: {
                height: 380,
                type: 'line',
                zoom: {
                    enabled: true
                },
                toolbar: {
                    show: true
                }
            },
            colors: lineDatalabelColors,
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: [3],
                curve: 'straight'
            },
            series: [{
                name: "Total",
                data: jumlahArray1
            }],
            title: {
                text: 'Grafik Payment ' + months[date.getMonth()] + ' ' + date.getFullYear(),
                align: 'left',
                style: {
                    fontWeight: '500',
                    color: '#263238',
                },
            },
            grid: {
                row: {
                    colors: ['transparent', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.2
                },
                borderColor: '#f1f1f1'
            },
            markers: {
                style: 'inverted',
                size: 0
            },
            xaxis: {
                categories: tanggalArray,
                title: {
                    text: months[date.getMonth()] + ' ' + date.getFullYear()
                }
            },
            // yaxis: {
            //     title: {
            //         text: 'Total Payment'
            //     }
            // },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            },
            responsive: [{
                breakpoint: 600,
                options: {
                    chart: {
                        toolbar: {
                            show: false
                        }
                    },
                    legend: {
                        show: false
                    },
                }
            }]
        }

        var chart = new ApexCharts(
            document.querySelector("#chart_payment"),
            options
        );

        chart.render();



        // grafik payment_month
        var data_payment_month = <?php echo $grafik_payment_month ?>;
        console.log(data_payment_month);

        var jumlahArray2 = [];
        var bulanArray = [];

        data_payment_month.forEach(function(obj) {
            let jumlah = obj.jumlah;
            jumlahArray2.push(jumlah);
        });

        data_payment_month.forEach(function(obj) {
            let bulan = obj.bulan - 1;
            bulanArray.push(months[bulan]);
        });

        var lineDatalabelColors2 = getChartColorsArray("#chart_payment_month");
        var options2 = {
            chart: {
                height: 380,
                type: 'bar',
                zoom: {
                    enabled: true
                },
                toolbar: {
                    show: true
                }
            },
            colors: lineDatalabelColors2,
            dataLabels: {
                enabled: true,
            },
            stroke: {
                width: [3],
                curve: 'straight'
            },
            series: [{
                name: "Total",
                data: jumlahArray2
            }],
            title: {
                text: 'Grafik Payment di Tahun ' + date.getFullYear(),
                align: 'left',
                style: {
                    fontWeight: '500',
                    color: '#263238',
                },
            },
            grid: {
                row: {
                    colors: ['transparent', 'transparent'], // takes an array which will be repeated on columns
                    opacity: 0.2
                },
                borderColor: '#f1f1f1'
            },
            markers: {
                style: 'inverted',
                size: 0
            },
            xaxis: {
                categories: bulanArray,
                title: {
                    text: date.getFullYear()
                }
            },
            // yaxis: {
            //     title: {
            //         text: 'Total Payment'
            //     }
            // },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                floating: true,
                offsetY: -25,
                offsetX: -5
            },
            responsive: [{
                breakpoint: 600,
                options: {
                    chart: {
                        toolbar: {
                            show: false
                        }
                    },
                    legend: {
                        show: false
                    },
                }
            }]
        }

        var chart2 = new ApexCharts(
            document.querySelector("#chart_payment_month"),
            options2
        );

        chart2.render();
    </script>

</body>

</html>
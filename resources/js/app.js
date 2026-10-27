import 'bootstrap/dist/css/bootstrap.min.css';
import './bootstrap';
import Alpine from 'alpinejs';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap-icons/font/bootstrap-icons.css';

import { Chart } from 'chart.js/auto';

window.Alpine = Alpine;

Alpine.start();


document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // DIAGRAM TAGIHAN DAN PENDAPATAN
    // ==========================================
    const tagihanPendapatanCanvas =
        document.getElementById('tagihanPendapatanChart');

    if (tagihanPendapatanCanvas) {
        const tagihanPendapatanData = JSON.parse(
            tagihanPendapatanCanvas.dataset.values
        );

        new Chart(tagihanPendapatanCanvas, {
            type: 'bar',
            data: {
                labels: [
                    'Total Tagihan',
                    'Pendapatan'
                ],
                datasets: [{
                    label: 'Nominal',
                    data: tagihanPendapatanData,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' +
                                    new Intl.NumberFormat('id-ID')
                                        .format(context.raw);
                            }
                        }
                    }
                }
            }
        });
    }


    // ==========================================
    // DIAGRAM TUNGGAKAN CUSTOMER OWNER
    // ==========================================
    const tunggakanCanvas =
        document.getElementById('tunggakanCustomerChart');

    if (tunggakanCanvas) {
        const tunggakanData = JSON.parse(
            tunggakanCanvas.dataset.values
        );

        new Chart(tunggakanCanvas, {
            type: 'bar',
            data: {
                labels: [
                    'Januari', 'Februari', 'Maret',
                    'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September',
                    'Oktober', 'November', 'Desember'
                ],
                datasets: [{
                    label: 'Customer Belum Bayar',
                    data: tunggakanData,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }


    // ==========================================
    // DIAGRAM DISTRIBUSI PAKET
    // ==========================================
    const paketCanvas =
        document.getElementById('paketChart');

    if (paketCanvas) {
        const paketData = JSON.parse(
            paketCanvas.dataset.values
        );

        new Chart(paketCanvas, {
            type: 'bar',
            data: {
                labels: paketData.map(item => item.nama),
                datasets: [{
                    label: 'Jumlah Pelanggan',
                    data: paketData.map(item => item.jumlah),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }


    // ==========================================
    // DIAGRAM STATUS PEMBAYARAN
    // ==========================================
    const pembayaranCanvas =
        document.getElementById('pembayaranChart');

    if (pembayaranCanvas) {
        const pembayaranData = JSON.parse(
            pembayaranCanvas.dataset.values
        );

        new Chart(pembayaranCanvas, {
            type: 'doughnut',
            data: {
                labels: [
                    'Sudah Bayar',
                    'Belum Bayar'
                ],
                datasets: [{
                    data: pembayaranData,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }


    // ==========================================
    // DIAGRAM PENDAPATAN PER BULAN
    // ==========================================
    const pendapatanCanvas =
        document.getElementById('pendapatanChart');

    if (pendapatanCanvas) {
        const pendapatanData = JSON.parse(
            pendapatanCanvas.dataset.values
        );

        new Chart(pendapatanCanvas, {
            type: 'line',
            data: {
                labels: [
                    'Januari', 'Februari', 'Maret',
                    'April', 'Mei', 'Juni',
                    'Juli', 'Agustus', 'September',
                    'Oktober', 'November', 'Desember'
                ],
                datasets: [{
                    label: 'Pendapatan',
                    data: pendapatanData,
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (value) {
                                return 'Rp ' +
                                    new Intl.NumberFormat('id-ID')
                                        .format(value);
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' +
                                    new Intl.NumberFormat('id-ID')
                                        .format(context.raw);
                            }
                        }
                    }
                }
            }
        });
    }

});

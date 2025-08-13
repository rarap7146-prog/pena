<!doctype html>
<html lang="id">
<head>
    <?php 
    require_once __DIR__ . '/../../app/helpers/site.php';
    $siteSettings = getSiteSettings();
    
    $meta = generateMetaTags(
        'Analytics Dashboard - ' . $siteSettings['site_name'],
        'Performance monitoring dan analytics data',
        '/admin/analytics'
    );
    
    include __DIR__ . '/../partials/meta-tags.php';
    ?>
    <link href="/css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50">
    <?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    ?>

    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm">
            <div class="p-6">
                <h1 class="text-xl font-bold text-gray-900"><?=htmlspecialchars($siteSettings['site_name'])?></h1>
                <p class="text-sm text-gray-600">Analytics Dashboard</p>
            </div>
            
            <?php include __DIR__ . '/partials/navigation.php'; ?>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-hidden">
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <h2 class="text-2xl font-bold text-gray-900">Analytics & Performance</h2>
                    <p class="text-gray-600">Real-time performance monitoring dan web analytics</p>
                </div>
            </header>

            <main class="p-6">
                <?php if (isset($_GET['saved']) && $_GET['saved'] === '1'): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                    <div class="flex">
                        <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Analytics configuration saved successfully!</span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Performance Overview Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">Avg Page Load</p>
                                <p class="text-2xl font-bold text-gray-900" id="avg-page-load">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">LCP Score</p>
                                <p class="text-2xl font-bold text-gray-900" id="avg-lcp">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">FID Score</p>
                                <p class="text-2xl font-bold text-gray-900" id="avg-fid">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600">CLS Score</p>
                                <p class="text-2xl font-bold text-gray-900" id="avg-cls">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Page Load Time Trend</h3>
                        <canvas id="pageLoadChart" width="400" height="200"></canvas>
                    </div>

                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Core Web Vitals</h3>
                        <canvas id="webVitalsChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <!-- Settings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Analytics Configuration</h3>
                    
                    <form action="/admin/analytics" method="POST" class="space-y-4">
                        <?php
                        // Get current analytics settings
                        $currentSettings = $siteSettings['analytics'] ?? [];
                        ?>
                        <input type="hidden" name="csrf" value="<?php 
                        if (session_status() !== PHP_SESSION_ACTIVE) {
                            session_start();
                        }
                        $_SESSION['csrf'] = bin2hex(random_bytes(16));
                        echo $_SESSION['csrf'];
                        ?>">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Google Analytics 4 Measurement ID</label>
                            <input type="text" name="ga4_measurement_id" 
                                   value="<?=htmlspecialchars($currentSettings['ga4_measurement_id'] ?? '')?>"
                                   placeholder="G-XXXXXXXXXX" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Format: G-XXXXXXXXXX</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Google Search Console Verification Code</label>
                            <input type="text" name="google_search_console_verification" 
                                   value="<?=htmlspecialchars($currentSettings['google_search_console_verification'] ?? '')?>"
                                   placeholder="verification-code-here" 
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">Kode verifikasi dari Google Search Console</p>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="enable_performance_monitoring" value="1" 
                                   <?=($currentSettings['enable_performance_monitoring'] ?? false) ? 'checked' : ''?>
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <label for="enable_performance_monitoring" class="ml-2 block text-sm text-gray-700">
                                Enable Performance Monitoring
                            </label>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                                Save Analytics Configuration
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script>
    // Load analytics data and charts
    async function loadAnalyticsData() {
        try {
            const response = await fetch('/api/performance?days=7');
            const data = await response.json();
            
            if (data.success) {
                updateMetricCards(data.data);
                updateCharts(data.data);
            }
        } catch (error) {
            console.error('Failed to load analytics data:', error);
        }
    }

    function updateMetricCards(data) {
        const metrics = {};
        data.forEach(item => {
            if (!metrics[item.metric_name]) {
                metrics[item.metric_name] = [];
            }
            metrics[item.metric_name].push(parseFloat(item.avg_value));
        });

        // Update cards
        if (metrics.page_load_time) {
            const avg = metrics.page_load_time.reduce((a, b) => a + b, 0) / metrics.page_load_time.length;
            document.getElementById('avg-page-load').textContent = Math.round(avg) + 'ms';
        }

        if (metrics.LCP) {
            const avg = metrics.LCP.reduce((a, b) => a + b, 0) / metrics.LCP.length;
            document.getElementById('avg-lcp').textContent = Math.round(avg) + 'ms';
        }

        if (metrics.FID) {
            const avg = metrics.FID.reduce((a, b) => a + b, 0) / metrics.FID.length;
            document.getElementById('avg-fid').textContent = Math.round(avg) + 'ms';
        }

        if (metrics.CLS) {
            const avg = metrics.CLS.reduce((a, b) => a + b, 0) / metrics.CLS.length;
            document.getElementById('avg-cls').textContent = avg.toFixed(3);
        }
    }

    function updateCharts(data) {
        // Page Load Time Chart
        const ctx1 = document.getElementById('pageLoadChart').getContext('2d');
        const pageLoadData = data.filter(item => item.metric_name === 'page_load_time');
        
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: pageLoadData.map(item => item.date),
                datasets: [{
                    label: 'Page Load Time (ms)',
                    data: pageLoadData.map(item => item.avg_value),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Web Vitals Chart
        const ctx2 = document.getElementById('webVitalsChart').getContext('2d');
        const vitalsData = data.filter(item => ['LCP', 'FID', 'CLS'].includes(item.metric_name));
        
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['LCP', 'FID', 'CLS'],
                datasets: [{
                    label: 'Average Score',
                    data: [
                        vitalsData.find(v => v.metric_name === 'LCP')?.avg_value || 0,
                        vitalsData.find(v => v.metric_name === 'FID')?.avg_value || 0,
                        vitalsData.find(v => v.metric_name === 'CLS')?.avg_value || 0
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(251, 191, 36, 0.8)',
                        'rgba(168, 85, 247, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Load data on page load
    document.addEventListener('DOMContentLoaded', () => {
        loadAnalyticsData();
        
        // Refresh data every 5 minutes
        setInterval(loadAnalyticsData, 5 * 60 * 1000);
    });
    </script>
</body>
</html>

<?php
/**
 * Analytics and Monitoring Helper Functions
 */

function getAnalyticsConfig() {
    $siteSettings = getSiteSettings();
    return $siteSettings['analytics'] ?? [];
}

function renderGA4Script() {
    $analytics = getAnalyticsConfig();
    $measurementId = $analytics['ga4_measurement_id'] ?? '';
    
    if (empty($measurementId)) {
        return '';
    }
    
    $config = $analytics['gtag_config'] ?? [];
    $configJson = json_encode($config);
    
    return "
<!-- Google Analytics 4 -->
<script async src=\"https://www.googletagmanager.com/gtag/js?id={$measurementId}\"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{$measurementId}', {$configJson});
</script>";
}

function renderGoogleSearchConsoleVerification() {
    $analytics = getAnalyticsConfig();
    $verificationCode = $analytics['google_search_console_verification'] ?? '';
    
    if (empty($verificationCode)) {
        return '';
    }
    
    return "<meta name=\"google-site-verification\" content=\"{$verificationCode}\">";
}

function renderPerformanceMonitoring() {
    $analytics = getAnalyticsConfig();
    $enabled = $analytics['enable_performance_monitoring'] ?? false;
    
    if (!$enabled) {
        return '';
    }
    
    return "
<!-- Performance Monitoring -->
<script>
// Core Web Vitals Monitoring
(function() {
    const vitals = {
        lcp: null,
        fid: null,
        cls: null,
        fcp: null,
        ttfb: null
    };
    
    // Largest Contentful Paint
    if ('PerformanceObserver' in window) {
        new PerformanceObserver((list) => {
            const entries = list.getEntries();
            const lastEntry = entries[entries.length - 1];
            vitals.lcp = lastEntry.startTime;
            sendVital('LCP', vitals.lcp);
        }).observe({entryTypes: ['largest-contentful-paint']});
        
        // First Input Delay
        new PerformanceObserver((list) => {
            const firstInput = list.getEntries()[0];
            if (firstInput) {
                vitals.fid = firstInput.processingStart - firstInput.startTime;
                sendVital('FID', vitals.fid);
            }
        }).observe({type: 'first-input', buffered: true});
        
        // Cumulative Layout Shift
        let clsValue = 0;
        new PerformanceObserver((list) => {
            for (const entry of list.getEntries()) {
                if (!entry.hadRecentInput) {
                    clsValue += entry.value;
                }
            }
            vitals.cls = clsValue;
            sendVital('CLS', vitals.cls);
        }).observe({type: 'layout-shift', buffered: true});
        
        // First Contentful Paint
        new PerformanceObserver((list) => {
            const entries = list.getEntries();
            entries.forEach((entry) => {
                if (entry.name === 'first-contentful-paint') {
                    vitals.fcp = entry.startTime;
                    sendVital('FCP', vitals.fcp);
                }
            });
        }).observe({type: 'paint', buffered: true});
    }
    
    // Time to First Byte
    window.addEventListener('load', () => {
        const navTiming = performance.getEntriesByType('navigation')[0];
        if (navTiming) {
            vitals.ttfb = navTiming.responseStart - navTiming.requestStart;
            sendVital('TTFB', vitals.ttfb);
        }
    });
    
    function sendVital(name, value) {
        // Send to Google Analytics if available
        if (typeof gtag !== 'undefined') {
            gtag('event', name, {
                event_category: 'Web Vitals',
                value: Math.round(value),
                non_interaction: true
            });
        }
        
        // Send to custom endpoint for detailed logging
        fetch('/api/performance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                metric: name,
                value: value,
                url: window.location.href,
                timestamp: Date.now(),
                user_agent: navigator.userAgent
            })
        }).catch(() => {}); // Silent fail
    }
    
    // Page Load Performance
    window.addEventListener('load', () => {
        setTimeout(() => {
            const perfData = performance.getEntriesByType('navigation')[0];
            if (perfData) {
                const metrics = {
                    page_load_time: perfData.loadEventEnd - perfData.navigationStart,
                    dom_content_loaded: perfData.domContentLoadedEventEnd - perfData.navigationStart,
                    dom_interactive: perfData.domInteractive - perfData.navigationStart,
                    dns_lookup: perfData.domainLookupEnd - perfData.domainLookupStart,
                    tcp_connection: perfData.connectEnd - perfData.connectStart,
                    server_response: perfData.responseEnd - perfData.requestStart
                };
                
                Object.entries(metrics).forEach(([key, value]) => {
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'timing_complete', {
                            name: key,
                            value: Math.round(value)
                        });
                    }
                });
            }
        }, 0);
    });
    
    // Error Tracking
    window.addEventListener('error', (event) => {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'exception', {
                description: event.error?.message || 'Unknown error',
                fatal: false,
                error_location: event.filename + ':' + event.lineno
            });
        }
    });
    
    // Resource Loading Errors
    window.addEventListener('error', (event) => {
        if (event.target !== window && event.target.tagName) {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'resource_error', {
                    event_category: 'Resource Loading',
                    event_label: event.target.tagName + ' - ' + (event.target.src || event.target.href),
                    non_interaction: true
                });
            }
        }
    }, true);
})();
</script>";
}

function trackEvent($eventName, $parameters = []) {
    $analytics = getAnalyticsConfig();
    $measurementId = $analytics['ga4_measurement_id'] ?? '';
    
    if (empty($measurementId)) {
        return '';
    }
    
    $params = json_encode($parameters);
    return "
<script>
if (typeof gtag !== 'undefined') {
    gtag('event', '{$eventName}', {$params});
}
</script>";
}
?>

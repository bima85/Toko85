<div
  id="asset-check"
  style="
    position: fixed;
    right: 12px;
    bottom: 12px;
    z-index: 99999;
    font-family: Arial, Helvetica, sans-serif;
  "
>
  <div
    style="
      background: #fff;
      border: 1px solid rgba(0, 0, 0, 0.1);
      padding: 8px 12px;
      border-radius: 6px;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
      min-width: 220px;
      font-size: 13px;
      color: #333;
    "
  >
    <strong style="display: block; margin-bottom: 6px">Asset check</strong>
    <div id="asset-check-list">
      <div>Checking…</div>
    </div>
  </div>
  <style>
    #asset-check .ok {
      color: green;
    }
    #asset-check .bad {
      color: #c0392b;
    }
    #asset-check .semi {
      color: #f39c12;
    }
  </style>
</div>

<script>
  (function () {
    function setList(items) {
      var container = document.getElementById('asset-check-list');
      if (!container) return;
      container.innerHTML = '';
      items.forEach(function (it) {
        var d = document.createElement('div');
        d.innerHTML =
          (it.ok ? '<span class="ok">✓</span>' : '<span class="bad">✕</span>') +
          ' ' +
          it.name +
          (it.msg ? ' — ' + it.msg : '');
        container.appendChild(d);
      });
    }

    function linkExists(substr) {
      return !!document.querySelector('link[href*="' + substr + '"]');
    }

    function runChecks() {
      var checks = [];
      // jQuery
      checks.push({ name: 'jQuery', ok: !!window.jQuery });
      // Bootstrap (check tooltip plugin)
      checks.push({
        name: 'Bootstrap JS',
        ok: !!(window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.tooltip === 'function'),
      });
      // AdminLTE (try several indicators: pushMenu or PushMenu plugin, or adminlte global)
      var adminlteOk =
        !!(
          window.jQuery &&
          window.jQuery.fn &&
          (typeof window.jQuery.fn.pushMenu === 'function' ||
            typeof window.jQuery.fn.PushMenu === 'function')
        ) || !!window.adminlte;
      checks.push({ name: 'AdminLTE JS', ok: adminlteOk });
      // OverlayScrollbars (optional enhancement). If missing, AdminLTE still works but scrollbars may differ.
      var overlayOk = !!(
        window.jQuery &&
        window.jQuery.fn &&
        typeof window.jQuery.fn.overlayScrollbars === 'function'
      );
      checks.push({
        name: 'OverlayScrollbars',
        ok: overlayOk,
        msg: overlayOk ? '' : 'optional (missing)',
      });
      // CSS files presence via link tag
      checks.push({ name: 'adminlte.min.css (link)', ok: linkExists('adminlte.min.css') });
      checks.push({
        name: 'icheck-bootstrap.min.css (link)',
        ok: linkExists('icheck-bootstrap.min.css'),
      });

      setList(checks);

      // Also log to console for developer
      if (console && console.log) {
        console.group && console.group('Asset check');
        checks.forEach(function (c) {
          console.log((c.ok ? 'OK: ' : 'FAIL: ') + c.name);
        });
        console.groupEnd && console.groupEnd();
      }
    }

    // Run checks after window 'load' to ensure other scripts executed
    if (document.readyState === 'complete') {
      // page already loaded
      setTimeout(runChecks, 50);
    } else {
      window.addEventListener('load', function () {
        setTimeout(runChecks, 50);
      });
      // fallback in case load doesn't fire quickly
      setTimeout(runChecks, 1000);
    }
  })();
</script>

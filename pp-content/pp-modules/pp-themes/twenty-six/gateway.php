<?php
    if (!defined('PipraPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if(isset($_GET['lang'])){
        if($_GET['lang'] !== ""){
            pp_set_lang($_GET['lang']);
?>
            <script>
                location.href = '<?php echo pp_checkout_address().'?gateway='.$_GET['gateway'];?>';
            </script>
<?php
            exit();
        }
    }

    if(isset($_GET['gateway'])){
        $gateway_info = pp_gateway_info($_GET['gateway'], $data);

        if($gateway_info['status'] == false){
            http_response_code(403);
            exit('Direct access not allowed');
        }
    }else{
        http_response_code(403);
        exit('Direct access not allowed');
    }

    $current_gateway_id = (string) $_GET['gateway'];
    $pp_gateways_mfs = pp_gateways('mfs', $data);
    $pp_gateways_bank = pp_gateways('bank', $data);
    $pp_gateways_global = pp_gateways('global', $data);

    $pp_switchable_count = 0;

    foreach ([$pp_gateways_mfs, $pp_gateways_bank, $pp_gateways_global] as $pp_tab_list) {
        if (($pp_tab_list['status'] ?? false) === true && !empty($pp_tab_list['gateway'])) {
            $pp_switchable_count += count($pp_tab_list['gateway']);
        }
    }

    $pp_show_method_switcher = $pp_switchable_count > 1;

    pp_gateway_apply_local_totals($current_gateway_id, $data);

    $pp_pay_amount = (float) ($data['transaction']['local_net_amount'] ?? $data['transaction']['amount'] ?? 0);

    if ($pp_pay_amount <= 0) {
        $pp_pay_amount = (float) ($data['transaction']['amount'] ?? 0);
    }

    $pp_pay_currency = trim((string) ($data['transaction']['local_currency'] ?? $data['transaction']['currency'] ?? 'BDT'));

    if ($pp_pay_currency === '' || $pp_pay_currency === '--') {
        $pp_pay_currency = 'BDT';
    }

    $pp_processing_fee = (float) ($data['transaction']['processing_fee'] ?? 0);
    $pp_discount = (float) ($data['transaction']['discount_amount'] ?? 0);
    $pp_trx_ref = trim((string) ($data['transaction']['ref'] ?? ''));
    $pp_brand_name = trim((string) ($data['brand']['name'] ?? ''));
    $pp_brand_logo = trim((string) ($data['brand']['favicon'] ?? $data['brand']['logo'] ?? ''));

    $pp_theme_accent = trim((string) ($data['options']['primary_color'] ?? '#2563eb'));

    if ($pp_theme_accent === '' || $pp_theme_accent === '--') {
        $pp_theme_accent = '#2563eb';
    }

    $pp_checkout_address = pp_checkout_address();
    $pp_all_gateways_url = $pp_checkout_address;
    $pp_subtotal = (float) ($data['transaction']['amount'] ?? $pp_pay_amount);

    if ($pp_subtotal <= 0) {
        $pp_subtotal = $pp_pay_amount;
    }

    $pp_brand_display = $pp_brand_name !== '' ? $pp_brand_name : (string) ($data['brand']['name'] ?? 'Checkout');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $data['lang']['checkout']?> - <?php echo $data['brand']['name'];?></title>
    <link rel="shortcut icon" href="<?php echo $data['brand']['favicon'];?>">
    <?php
       echo pp_assets('head');
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --pp-brand: <?php echo $gateway_info['gateway']['primary_color'];?>;
            --pp-brand-soft: <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.10)?>;
            --pp-brand-ring: <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.22)?>;
            --pp-on-brand: <?php echo $gateway_info['gateway']['text_color'];?>;
            --pp-accent: <?php echo $pp_theme_accent;?>;
            --pp-accent-soft: <?php echo pp_hexToRgba($pp_theme_accent, 0.10)?>;
            --pp-primary: var(--pp-brand);
            --pp-primary-soft: var(--pp-brand-soft);
            --pp-primary-ring: var(--pp-brand-ring);
            --pp-on-primary: var(--pp-on-brand);
            --pp-surface: #ffffff;
            --pp-muted: #6b7280;
            --pp-text: #111827;
            --pp-border: #e8ecef;
            --pp-bg: #f1f5f9;
        }

        body.pp-gateway-page {
            font-family: "Inter", system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        .btn-primary,
        .pp-submit-btn {
            --tblr-btn-border-color: transparent;
            --tblr-btn-hover-border-color: transparent;
            --tblr-btn-active-border-color: transparent;
            --tblr-btn-color: var(--pp-on-primary);
            --tblr-btn-bg: var(--pp-primary);
            --tblr-btn-hover-color: var(--pp-on-primary);
            --tblr-btn-hover-bg: <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.88)?>;
            --tblr-btn-active-color: var(--pp-on-primary);
            --tblr-btn-active-bg: <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.82)?>;
            --tblr-btn-disabled-bg: var(--pp-primary);
            --tblr-btn-disabled-color: var(--pp-on-primary);
            box-shadow: 0 10px 24px -14px <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.65)?>;
        }

        <?php echo file_get_contents(__DIR__.'/gateway-checkout.css'); ?>

    </style>

    <?php
        $seoTitle = trim($data['options']['seo_title'] ?? '');
        $seoDesc  = trim($data['options']['seo_description'] ?? '');
        $seoKey   = trim($data['options']['seo_keywords'] ?? '');
        $analyticsCode = trim($data['options']['analytics_code'] ?? '');

        if ($seoTitle !== '' && $seoTitle !== '--') {
            echo '<title>' . htmlspecialchars($seoTitle) . '</title>' . PHP_EOL;
            echo '<meta name="title" content="' . htmlspecialchars($seoTitle) . '">' . PHP_EOL;
            echo '<meta property="og:title" content="' . htmlspecialchars($seoTitle) . '">' . PHP_EOL;
        }

        if ($seoDesc !== '' && $seoDesc !== '--') {
            echo '<meta name="description" content="' . htmlspecialchars($seoDesc) . '">' . PHP_EOL;
            echo '<meta property="og:description" content="' . htmlspecialchars($seoDesc) . '">' . PHP_EOL;
        }

        if ($seoKey !== '' && $seoKey !== '--') {
            echo '<meta name="keywords" content="' . htmlspecialchars($seoKey) . '">' . PHP_EOL;
        }

        if ($analyticsCode !== '' && $analyticsCode !== '--') {
            echo $analyticsCode;
        }

        $bgStyle = 'background: linear-gradient(160deg, #f3f4f6 0%, #eef2f7 45%, #f8fafc 100%);';
        if (!empty($data['options']['enable_bg_image']) &&$data['options']['enable_bg_image'] === 'enabled' &&!empty($data['options']['background_image'])) {
            $bgImage = $data['options']['background_image'];
            $bgStyle = "
                background-image: url('{$bgImage}');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                background-attachment: fixed;
            ";
        }
    ?>
</head>
<body class="pp-gateway-page" style="<?= $bgStyle ?>" loading="lazy">
    <div class="pp-shell">
        <div class="pp-checkout">
            <aside class="pp-checkout__aside" aria-label="<?php echo htmlspecialchars($data['lang']['order_summary'], ENT_QUOTES); ?>">
                <div>
                    <div class="pp-summary__brand-row">
                        <?php if ($pp_brand_logo !== ''): ?>
                            <img src="<?php echo htmlspecialchars($pp_brand_logo, ENT_QUOTES); ?>" alt="" class="pp-summary__logo" width="40" height="40">
                        <?php endif; ?>
                        <div>
                            <p class="pp-summary__brand"><?php echo htmlspecialchars($pp_brand_display, ENT_QUOTES); ?></p>
                            <p class="pp-summary__secure">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z"/><path d="M12 3a12 12 0 0 0 8.5 3.5"/><path d="M17 11v1a5 5 0 0 1 -10 0v-1"/><path d="M12 19v4"/></svg>
                                <?php echo $data['lang']['secured_checkout']; ?>
                            </p>
                        </div>
                    </div>

                    <h2 class="pp-summary__heading"><?php echo $data['lang']['order_summary']; ?></h2>

                    <ul class="pp-summary__meta">
                        <?php if ($pp_trx_ref !== ''): ?>
                            <li>
                                <span class="pp-summary__meta-label"><?php echo $data['lang']['transaction_ref']; ?></span>
                                <span class="pp-summary__meta-value"><?php echo htmlspecialchars($pp_trx_ref, ENT_QUOTES); ?></span>
                            </li>
                        <?php endif; ?>
                        <li>
                            <span class="pp-summary__meta-label"><?php echo $data['lang']['total']; ?></span>
                            <span class="pp-summary__meta-value"><?php echo number_format($pp_subtotal, 2).' '.htmlspecialchars($pp_pay_currency, ENT_QUOTES); ?></span>
                        </li>
                        <?php if ($pp_processing_fee > 0): ?>
                            <li>
                                <span class="pp-summary__meta-label"><?php echo $data['lang']['processing_fee']; ?></span>
                                <span class="pp-summary__meta-value"><?php echo number_format($pp_processing_fee, 2).' '.htmlspecialchars($pp_pay_currency, ENT_QUOTES); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if ($pp_discount > 0): ?>
                            <li>
                                <span class="pp-summary__meta-label"><?php echo $data['lang']['discount']; ?></span>
                                <span class="pp-summary__meta-value">−<?php echo number_format($pp_discount, 2).' '.htmlspecialchars($pp_pay_currency, ENT_QUOTES); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="pp-summary__total">
                    <span class="pp-summary__total-label"><?php echo $data['lang']['total_due']; ?></span>
                    <span class="pp-summary__total-value">
                        <?php echo number_format($pp_pay_amount, 2); ?>
                        <span class="pp-summary__total-currency"><?php echo htmlspecialchars($pp_pay_currency, ENT_QUOTES); ?></span>
                    </span>
                </div>
            </aside>

            <div class="pp-checkout__main">
                <div class="pp-main__toolbar">
                    <button type="button" class="pp-icon-btn" onclick="location.href='<?php echo htmlspecialchars($pp_checkout_address, ENT_QUOTES); ?>'" aria-label="Back">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z"/><path d="M5 12h14"/><path d="M5 12l6 6"/><path d="M5 12l6 -6"/></svg>
                    </button>
                    <button type="button" class="pp-icon-btn" data-bs-target="#modal-language" data-bs-toggle="modal" aria-label="Language">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path stroke="none" d="M0 0h24v24H0z"/><path d="M9 6.371c0 4.418 -2.239 6.629 -5 6.629"/><path d="M4 6.371h7"/><path d="M5 9c0 2.144 2.252 3.908 6 4"/><path d="M12 20l4 -9l4 9"/><path d="M19.1 18h-6.2"/><path d="M6.694 3l.793 .582"/></svg>
                    </button>
                </div>

                <div class="pp-main__amount-mobile" aria-live="polite">
                    <p class="pp-main__amount-label"><?php echo $data['lang']['complete_payment']; ?></p>
                    <p class="pp-main__amount-value">
                        <?php echo number_format($pp_pay_amount, 2); ?>
                        <span><?php echo htmlspecialchars($pp_pay_currency, ENT_QUOTES); ?></span>
                    </p>
                </div>

                <?php if (! $pp_show_method_switcher): ?>
                    <div class="pp-main__gateway-badge">
                        <img src="<?php echo htmlspecialchars($gateway_info['gateway']['logo'], ENT_QUOTES); ?>" alt="">
                    </div>
                <?php endif; ?>

                <?php include __DIR__.'/gateway-switcher.php'; ?>

                <section class="pp-pay-steps">
                    <h3 class="pp-pay-steps__title"><?php echo $data['lang']['how_to_pay']; ?></h3>
                    <?php pp_gateway_render($_GET['gateway'] ?? '', $data); ?>
                </section>
            </div>
        </div>

        <?php if (trim((string) ($data['options']['watermark_text'] ?? '')) !== ''): ?>
            <p class="pp-footer-note footer-branding"><?php echo $data['options']['watermark_text']; ?></p>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="modal-language" data-bs-keyboard="false" tabindex="-1" aria-labelledby="scrollableLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scrollableLabel"><?php echo $data['lang']['select_language']?></h5> 
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body"> 
                    <div class="form-group mt-1">
                        <label for="" class="form-label"><?php echo $data['lang']['language']?> <span class="text-danger">*</span></label>
                        <div class="form-control-wrap">
                            <select class="form-select" id="model-languages" onchange="hitLanguage()">
                                <option value="" selected><?php echo $data['lang']['select_a_language']?></option>
                                <?php
                                    foreach ($gateway_info['supported_languages'] ?? [] as $code => $language) {
                                ?>
                                            <option value="<?= htmlspecialchars($code) ?>">
                                                <?= htmlspecialchars($language) ?>
                                            </option>
                                <?php
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal"><?php echo $data['lang']['close']?></button>
                </div>
            </div>
        </div>
    </div>

    <?php
       echo pp_assets('footer');
    ?>

    <script data-cfasync="false">
        var ppLang = {
            copied:        '<?php echo addslashes($data['lang']['copied_successfully'])?>',
            copiedDesc:    '<?php echo addslashes($data['lang']['copy_content_copied'])?>',
            copyFailed:    '<?php echo addslashes($data['lang']['copy_failed'])?>',
            copyFailedDesc:'<?php echo addslashes($data['lang']['copy_failed_text'])?>',
            noContent:     '<?php echo addslashes($data['lang']['copy_no_content'])?>',
            somethingWrong:'<?php echo addslashes($data['lang']['something_wrong'])?>',
            supportText:   '<?php echo addslashes($data['lang']['support_contact_text'])?>',
        };

        function copy_value(content){
            if (!content) {
                createToast({
                    title: ppLang.somethingWrong,
                    description: ppLang.noContent,
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 20
                });
                return;
            }

            navigator.clipboard.writeText(content).then(() => {
                createToast({
                    title: ppLang.copied,
                    description: ppLang.copiedDesc,
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#5f38f9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 12l2 2l4 -4" /></svg>`,
                    timeout: 4000,
                    top: 20
                });
            }).catch((err) => {
                createToast({
                    title: ppLang.copyFailed,
                    description: ppLang.copyFailedDesc,
                    svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                    timeout: 6000,
                    top: 20
                });
                console.error('Clipboard error:', err);
            });
        }

        function failed(title, message){
            createToast({
                title: title,
                description: message,
                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                timeout: 6000,
                top: 20
            });
        }

        function success(){
            location.href = "<?php echo pp_checkout_address();?>";
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Only buttons that should auto-activate
            const autoButtons = document.querySelectorAll('.btn-group .btn');

            // All buttons for click handling
            const allButtons = document.querySelectorAll('.btn-group .btn, .btns-group .btns');

            const rows = {};

            // Attach click events to all buttons
            allButtons.forEach(btn => {
                const tab = btn.dataset.tab;
                if (!tab) return; // skip buttons without data-tab

                // Store row element if exists
                const row = document.getElementById('gateways-' + tab);
                if (row) rows[tab] = row;

                btn.addEventListener('click', function() {
                    // Remove active from all buttons
                    allButtons.forEach(b => b.classList.remove('active'));

                    // Add active only to clicked button
                    this.classList.add('active');

                    // Hide all rows
                    Object.values(rows).forEach(r => r.style.display = 'none');

                    // Show selected row if it exists
                    if (rows[tab]) rows[tab].style.display = rows[tab].classList.contains('row') ? 'flex' : 'block';
                });
            });

            // ✅ Auto-enable first available tab ONLY from .btn-group .btn
            if (autoButtons.length > 0) {
                autoButtons[0].click();
            }
        });

        function hitLanguage(){
            var language = document.querySelector("#model-languages").value;

            if(language !== ""){
                location.href = '<?php echo pp_checkout_address().'?gateway='.$_GET['gateway'];?>&lang='+language;
            }
        }

        $(document).ready(function() {
            $('#form').on('submit', function(e) {
                e.preventDefault(); // prevent default form submission

                var formData = $(this).serialize(); // serialize all form inputs

                document.querySelector("#payButton").innerHTML = '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>';

                $.ajax({
                    url: '<?php echo pp_site_address(); ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: formData, // send all form data
                    success: function(data) {
                        document.querySelector("#payButton").innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-credit-card"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M3 8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3l0 -8" /><path d="M3 10l18 0" /><path d="M7 15l.01 0" /><path d="M11 15l2 0" /></svg> <?php echo $data['lang']['pay_now']?>';

                        if (data.status == "true") {
                            location.href = data.redirect;
                        } else {
                            createToast({
                                title: data.title,
                                description: data.message,
                                svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                                timeout: 6000
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        createToast({
                            title: ppLang.somethingWrong,
                            description: ppLang.supportText,
                            svg: `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#d63939" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-exclamation-circle"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 9v4" /><path d="M12 16v.01" /></svg>`,
                            timeout: 6000
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>

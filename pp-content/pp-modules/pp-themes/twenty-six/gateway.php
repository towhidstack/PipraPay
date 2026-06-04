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

    $pp_pay_amount = (float) ($data['transaction']['local_net_amount'] ?? $data['transaction']['amount'] ?? 0);
    $pp_pay_currency = trim((string) ($data['transaction']['local_currency'] ?? $data['transaction']['currency'] ?? 'BDT'));

    if ($pp_pay_currency === '' || $pp_pay_currency === '--') {
        $pp_pay_currency = 'BDT';
    }

    $pp_theme_accent = trim((string) ($data['options']['primary_color'] ?? '#2563eb'));

    if ($pp_theme_accent === '' || $pp_theme_accent === '--') {
        $pp_theme_accent = '#2563eb';
    }
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

        .pp-gateway-wrap {
            max-width: 440px;
            width: 100%;
            margin: 0 auto;
            padding: 1.25rem 1rem 2rem;
        }

        .pp-gateway-card {
            background: var(--pp-surface);
            border: 1px solid var(--pp-border);
            border-radius: 1.25rem;
            box-shadow: 0 18px 48px -28px rgba(15, 23, 42, 0.28);
            overflow: hidden;
        }

        .pp-gateway-card__body {
            padding: 1.25rem 1.25rem 1.5rem;
        }

        .pp-gateway-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .pp-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border: 1px solid var(--pp-border);
            border-radius: 9999px;
            background: #fff;
            color: var(--pp-primary);
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease, transform 0.12s ease;
        }

        .pp-icon-btn:hover {
            background: var(--pp-primary-soft);
            border-color: var(--pp-primary-ring);
        }

        .pp-icon-btn:active {
            transform: scale(0.97);
        }

        .pp-icon-btn svg {
            width: 1.25rem;
            height: 1.25rem;
        }

        .pp-pay-header {
            text-align: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--pp-border);
        }

        .pp-pay-header--solo {
            border-bottom: none;
            padding-bottom: 0;
        }

        .pp-gateway-logo {
            height: 2.75rem;
            width: auto;
            max-width: 160px;
            object-fit: contain;
            margin: 0 auto 0.75rem;
        }

        .pp-pay-header__label {
            margin: 0 0 0.35rem;
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--pp-muted);
            letter-spacing: 0.02em;
        }

        .pp-pay-header__amount {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.2;
            color: var(--pp-text);
            letter-spacing: -0.02em;
        }

        .pp-pay-header__currency {
            font-size: 1rem;
            font-weight: 700;
            color: var(--pp-muted);
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
            min-height: 3rem;
            border-radius: 0.875rem !important;
            font-weight: 700;
            letter-spacing: 0.01em;
            box-shadow: 0 10px 24px -14px <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.65)?>;
        }

        .pp-input,
        .pp-verify-form .form-control {
            min-height: 3rem;
            border-radius: 0.75rem;
            border-color: var(--pp-border);
            padding-left: 0.9rem;
            padding-right: 0.9rem;
            font-size: 1rem;
        }

        .pp-input:focus,
        .pp-verify-form .form-control:focus {
            border-color: var(--pp-primary);
            box-shadow: 0 0 0 3px var(--pp-primary-ring);
        }

        .pp-verify-section {
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--pp-border);
        }

        .pp-verify-form .form-label {
            font-size: 0.8125rem;
            font-weight: 600;
            color: var(--pp-muted);
            margin-bottom: 0.4rem;
        }

        .pp-form-group {
            margin-bottom: 0.25rem;
        }

        /* Step-by-step instructions (modern light card) */
        .payment-instructions.payment-steps {
            list-style: none;
            counter-reset: pp-step;
            margin: 0 0 0;
            padding: 0;
            background: #f9fafb;
            border: 1px solid var(--pp-border);
            border-radius: 1rem;
            overflow: hidden;
            color: var(--pp-text);
        }

        .payment-instructions.payment-steps li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 0.95rem 1rem;
            word-break: break-word;
            border-bottom: 1px solid var(--pp-border);
            counter-increment: pp-step;
        }

        .payment-instructions.payment-steps li:last-child {
            border-bottom: none;
        }

        .payment-instructions.payment-steps li .dot {
            display: none;
        }

        .payment-instructions.payment-steps li::before {
            content: counter(pp-step);
            flex-shrink: 0;
            width: 1.75rem;
            height: 1.75rem;
            margin-top: 0.1rem;
            border-radius: 9999px;
            background: var(--pp-primary);
            color: var(--pp-on-primary);
            font-size: 0.75rem;
            font-weight: 700;
            line-height: 1.75rem;
            text-align: center;
        }

        .payment-instructions.payment-steps li p {
            margin: 0;
            flex: 1;
            font-size: 0.9375rem;
            line-height: 1.55;
            color: #374151;
        }

        .payment-instructions.payment-steps li .dynamic-value {
            display: inline-block;
            margin-top: 0.15rem;
            padding: 0.15rem 0.5rem;
            border-radius: 0.5rem;
            background: var(--pp-primary-soft);
            color: var(--pp-primary);
            font-weight: 700;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.9em;
            word-break: break-all;
        }

        .payment-instructions.payment-steps li svg {
            width: 1rem;
            height: 1rem;
        }

        .payment-instructions.payment-steps li .button-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            vertical-align: middle;
            padding: 0.35rem;
            margin-left: 0.35rem;
            background: #fff;
            color: var(--pp-primary);
            border: 1px solid var(--pp-primary-ring);
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background 0.15s ease, transform 0.12s ease;
        }

        .payment-instructions.payment-steps li .button-icon:hover {
            background: var(--pp-primary-soft);
            transform: translateY(-1px);
        }

        .bp-modal {
            position: fixed;
            inset: 0;
            background: rgb(86 85 85 / 13%);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 15px;
        }

        .bp-modal-content {
            position: relative;
            background: #FFFFFF;
            border-radius: 5px;
            padding: 10px;
            max-width: 95vw;
            max-height: 95vh;
            box-shadow: 0 00px 5px rgb(157 145 145 / 60%);
            animation: bpZoomIn 0.25s ease-out;
        }

        .bp-model-image-b{
            margin: 20px;
        }

        #bp-modal-image {
            display: block;
            max-width: 300px;
            border-radius: 10px;
            width: 100%;
        }

        .bp-close {
            position: absolute;
            top: -12px;
            right: -12px;
            width: 36px;
            height: 36px;
            background: #ff4d4f;
            color: #fff;
            font-size: 22px;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 0px 5px rgba(0, 0, 0, 0.4);
            transition: transform 0.2s ease, background 0.2s ease;
        }

        .bp-close:hover {
            background: #ff1f1f;
            transform: scale(1.1);
        }

        @keyframes bpZoomIn {
            from {
                transform: scale(0.92);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @media (max-width: 576px) {
            .bp-close {
                top: -10px;
                right: -10px;
                width: 32px;
                height: 32px;
                font-size: 20px;
            }
        }

        /* Payment method picker */
        .pp-pay-methods {
            margin-bottom: 1.25rem;
            padding: 1rem;
            border-radius: 1rem;
            background: var(--pp-bg);
            border: 1px solid var(--pp-border);
        }

        .pp-pay-methods__head {
            margin-bottom: 0.75rem;
        }

        .pp-pay-methods__title {
            margin: 0;
            font-size: 0.8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pp-muted);
        }

        .pp-pay-methods__hint {
            margin: 0.25rem 0 0;
            font-size: 0.75rem;
            line-height: 1.4;
            color: #9ca3af;
        }

        .pp-pay-methods__segments {
            display: flex;
            gap: 0.35rem;
            padding: 0.25rem;
            margin-bottom: 0.75rem;
            border-radius: 0.75rem;
            background: #e2e8f0;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .pp-pay-methods__segment {
            flex: 1 1 auto;
            min-width: 0;
            border: none;
            border-radius: 0.5rem;
            background: transparent;
            color: #64748b;
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 0.5rem 0.55rem;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.15s ease, color 0.15s ease, box-shadow 0.15s ease;
        }

        .pp-pay-methods__segment.is-active {
            background: #fff;
            color: var(--pp-text);
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08);
        }

        .pp-pay-methods__grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.5rem;
        }

        @media (max-width: 380px) {
            .pp-pay-methods__grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .pp-pay-tile {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 3.5rem;
            padding: 0.5rem;
            border: 2px solid #fff;
            border-radius: 0.75rem;
            background: #fff;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.05);
            text-decoration: none;
            transition: border-color 0.15s ease, box-shadow 0.15s ease, transform 0.12s ease;
        }

        .pp-pay-tile:hover {
            border-color: var(--pp-border);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        }

        .pp-pay-tile.is-active {
            border-color: var(--pp-brand);
            box-shadow: 0 0 0 1px var(--pp-brand), 0 4px 14px var(--pp-brand-soft);
            pointer-events: none;
        }

        .pp-pay-tile img {
            max-height: 2.125rem;
            max-width: 100%;
            object-fit: contain;
        }

        .pp-pay-tile__check {
            position: absolute;
            top: 0.25rem;
            right: 0.25rem;
            width: 1rem;
            height: 1rem;
            color: var(--pp-brand);
        }

        .pp-pay-tile__check svg {
            width: 100%;
            height: 100%;
        }

        .pp-pay-steps__title {
            margin: 0 0 0.75rem;
            font-size: 0.8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--pp-muted);
        }

        .pp-pay-steps {
            margin-top: 0.25rem;
        }
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
    <div class="pp-gateway-wrap">
        <div class="pp-gateway-card">
          <div class="pp-gateway-card__body">
              <div class="pp-gateway-toolbar">
                  <button type="button" class="pp-icon-btn" onclick="location.href='<?php echo pp_checkout_address();?>'" aria-label="Back">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0"/><path d="M5 12l6 6"/><path d="M5 12l6 -6"/></svg>
                  </button>
                  <button type="button" class="pp-icon-btn" data-bs-target="#modal-language" data-bs-toggle="modal" aria-label="Language">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 6.371c0 4.418 -2.239 6.629 -5 6.629"/><path d="M4 6.371h7"/><path d="M5 9c0 2.144 2.252 3.908 6 4"/><path d="M12 20l4 -9l4 9"/><path d="M19.1 18h-6.2"/><path d="M6.694 3l.793 .582"/></svg>
                  </button>
              </div>

              <div class="pp-pay-header<?php echo $pp_show_method_switcher ? '' : ' pp-pay-header--solo'; ?>">
                  <?php if (! $pp_show_method_switcher): ?>
                      <img src="<?php echo htmlspecialchars($gateway_info['gateway']['logo'], ENT_QUOTES);?>" alt="" class="pp-gateway-logo">
                  <?php endif; ?>
                  <p class="pp-pay-header__label"><?php echo $data['lang']['complete_payment']; ?></p>
                  <p class="pp-pay-header__amount">
                      <?php echo number_format($pp_pay_amount, 2); ?>
                      <span class="pp-pay-header__currency"><?php echo htmlspecialchars($pp_pay_currency, ENT_QUOTES); ?></span>
                  </p>
              </div>

              <?php include __DIR__.'/gateway-switcher.php'; ?>

              <div class="pp-pay-steps">
                  <h3 class="pp-pay-steps__title"><?php echo $data['lang']['how_to_pay']; ?></h3>
                  <?php pp_gateway_render($_GET['gateway'] ?? '', $data); ?>
              </div>
          </div>
        </div>

        <p class="footer-branding text-center text-muted" style="margin-top: 1.25rem; font-size: 0.8125rem;"><?php echo $data['options']['watermark_text'];?></p>
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

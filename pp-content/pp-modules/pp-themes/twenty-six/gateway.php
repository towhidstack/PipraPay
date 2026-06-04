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
            --pp-primary: <?php echo $gateway_info['gateway']['primary_color'];?>;
            --pp-primary-soft: <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.10)?>;
            --pp-primary-ring: <?php echo pp_hexToRgba($gateway_info['gateway']['primary_color'], 0.22)?>;
            --pp-on-primary: <?php echo $gateway_info['gateway']['text_color'];?>;
            --pp-surface: #ffffff;
            --pp-muted: #6b7280;
            --pp-text: #111827;
            --pp-border: #e5e7eb;
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

        .pp-gateway-hero {
            text-align: center;
            margin-bottom: 1.25rem;
        }

        .pp-gateway-logo {
            height: 3.25rem;
            width: auto;
            max-width: 180px;
            object-fit: contain;
            margin-bottom: 0.75rem;
        }

        .pp-gateway-amount {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.4rem 0.85rem;
            border-radius: 9999px;
            background: var(--pp-primary-soft);
            color: var(--pp-primary);
            font-size: 0.875rem;
            font-weight: 700;
            letter-spacing: 0.01em;
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

        /* Payment method switcher (bKash ↔ Nagad ↔ …) */
        .pp-gateway-switcher {
            margin-bottom: 1.25rem;
        }

        .pp-gateway-switcher__toggle {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.75rem 0.9rem;
            border: 1px solid var(--pp-border);
            border-radius: 0.875rem;
            background: #fff;
            color: var(--pp-text);
            cursor: pointer;
            text-align: left;
            transition: border-color 0.15s ease, background 0.15s ease;
        }

        .pp-gateway-switcher__toggle:hover,
        .pp-gateway-switcher__toggle.is-open {
            border-color: var(--pp-primary-ring);
            background: var(--pp-primary-soft);
        }

        .pp-gateway-switcher__toggle-text {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            min-width: 0;
        }

        .pp-gateway-switcher__toggle-title {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--pp-text);
        }

        .pp-gateway-switcher__toggle-hint {
            font-size: 0.75rem;
            color: var(--pp-muted);
            line-height: 1.35;
        }

        .pp-gateway-switcher__toggle-icon {
            flex-shrink: 0;
            width: 1.5rem;
            height: 1.5rem;
            color: var(--pp-primary);
            transition: transform 0.2s ease;
        }

        .pp-gateway-switcher__toggle-icon svg {
            width: 100%;
            height: 100%;
        }

        .pp-gateway-switcher__toggle.is-open .pp-gateway-switcher__toggle-icon {
            transform: rotate(180deg);
        }

        .pp-gateway-switcher__quick {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.65rem;
            padding-bottom: 0.15rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scroll-snap-type: x proximity;
        }

        .pp-gateway-switcher__quick::-webkit-scrollbar {
            height: 4px;
        }

        .pp-gateway-switcher__quick-item {
            flex: 0 0 auto;
            scroll-snap-align: start;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 4.25rem;
            height: 3rem;
            border: 2px solid var(--pp-border);
            border-radius: 0.75rem;
            background: #fff;
            padding: 0.35rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .pp-gateway-switcher__quick-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .pp-gateway-switcher__quick-item.is-active {
            border-color: var(--pp-primary);
            box-shadow: 0 0 0 1px var(--pp-primary);
            pointer-events: none;
        }

        .pp-gateway-switcher__panel {
            margin-top: 0.75rem;
            padding: 0.75rem;
            border: 1px solid var(--pp-border);
            border-radius: 1rem;
            background: #f9fafb;
        }

        .pp-gateway-switcher__tabs {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 0.35rem;
            margin-bottom: 0.75rem;
        }

        .pp-gateway-switcher__tab {
            flex: 0 0 auto;
            border: 1px solid var(--pp-border);
            border-radius: 9999px;
            background: #fff;
            color: var(--pp-muted);
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.4rem 0.85rem;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.15s ease;
        }

        .pp-gateway-switcher__tab.is-active {
            border-color: var(--pp-primary);
            background: var(--pp-primary);
            color: var(--pp-on-primary, #fff);
        }

        .pp-gateway-switcher__grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.5rem;
        }

        @media (min-width: 480px) {
            .pp-gateway-switcher__grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }

        .pp-gateway-switcher__item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.35rem;
            padding: 0.5rem 0.35rem;
            border: 2px solid var(--pp-border);
            border-radius: 0.75rem;
            background: #fff;
            text-decoration: none;
            color: inherit;
            transition: border-color 0.15s ease, transform 0.12s ease, box-shadow 0.15s ease;
        }

        .pp-gateway-switcher__item:hover {
            border-color: var(--pp-primary-ring);
            transform: translateY(-1px);
        }

        .pp-gateway-switcher__item.is-active {
            border-color: var(--pp-primary);
            box-shadow: 0 0 0 1px var(--pp-primary);
            pointer-events: none;
        }

        .pp-gateway-switcher__item-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 2.25rem;
            width: 100%;
        }

        .pp-gateway-switcher__item-logo img {
            max-height: 2rem;
            max-width: 100%;
            object-fit: contain;
        }

        .pp-gateway-switcher__item-name {
            font-size: 0.625rem;
            font-weight: 600;
            line-height: 1.2;
            text-align: center;
            color: #4b5563;
            word-break: break-word;
        }

        @media (min-width: 640px) {
            .pp-gateway-switcher__quick {
                display: none;
            }

            .pp-gateway-switcher__toggle {
                display: none;
            }

            .pp-gateway-switcher__panel {
                display: block !important;
                margin-top: 0;
            }

            .pp-gateway-switcher__panel[hidden] {
                display: block !important;
            }
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

              <div class="pp-gateway-hero">
                  <img src="<?php echo htmlspecialchars($gateway_info['gateway']['logo'], ENT_QUOTES);?>" alt="" class="pp-gateway-logo">
                  <?php if (!empty($data['transaction']['local_net_amount'])): ?>
                      <div class="pp-gateway-amount">
                          <?php echo number_format((float) $data['transaction']['local_net_amount'], 2); ?>
                          <?php echo htmlspecialchars((string) ($data['transaction']['local_currency'] ?? 'BDT'), ENT_QUOTES); ?>
                      </div>
                  <?php endif; ?>
              </div>

              <?php include __DIR__.'/gateway-switcher.php'; ?>

              <?php
                 pp_gateway_render($_GET['gateway'] ?? '', $data);
              ?>
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

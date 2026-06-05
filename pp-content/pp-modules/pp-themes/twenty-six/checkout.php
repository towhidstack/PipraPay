<?php
    if (!defined('PipraPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    if (isset($_GET['lang']) && $_GET['lang'] !== '') {
        pp_set_lang($_GET['lang']);
?>
        <script>location.href = '?lang=';</script>
<?php
        exit();
    }

    if (isset($_GET['cancel'])) {
        pp_set_transaction_status($data['transaction']['ref'], 'canceled');
        $pp_cancel_redirect = pp_merchant_cancel_redirect($data['transaction']);
?>
        <script>location.href = '<?php echo htmlspecialchars($pp_cancel_redirect, ENT_QUOTES); ?>';</script>
<?php
        exit();
    }

    $pp_gateways_mfs = pp_gateways('mfs', $data);
    $pp_gateways_bank = pp_gateways('bank', $data);
    $pp_gateways_global = pp_gateways('global', $data);

    $pp_checkout_base = pp_checkout_address();
    $pp_amount = money_round($data['transaction']['amount'] ?? 0, 2);
    $pp_currency = htmlspecialchars((string) ($data['transaction']['currency'] ?? 'BDT'), ENT_QUOTES);
    $pp_brand_name = htmlspecialchars((string) ($data['brand']['name'] ?? ''), ENT_QUOTES);
    $pp_brand_logo = pp_resolve_media_url(
        (string) ($data['brand']['favicon'] ?? ''),
        pp_resolve_media_url((string) ($data['brand']['logo'] ?? ''))
    );
    $pp_accent = trim((string) ($data['options']['primary_color'] ?? '#2b63d9'));
    $pp_on_accent = trim((string) ($data['options']['text_color'] ?? '#ffffff'));

    if ($pp_accent === '' || $pp_accent === '--') {
        $pp_accent = '#2b63d9';
    }

    if ($pp_on_accent === '' || $pp_on_accent === '--') {
        $pp_on_accent = '#ffffff';
    }

    $pp_pay_label = str_replace(
        ['{amount}', '{currency}'],
        [$pp_amount, $pp_currency],
        (string) ($data['lang']['pay_with_amount'] ?? 'Pay {amount} {currency}')
    );

    $pp_secured_label = str_replace(
        '{brand}',
        $pp_brand_name,
        (string) ($data['lang']['secured_by_brand'] ?? 'Secured by {brand}')
    );

    $pp_terms_link = '<a href="#" data-bs-toggle="modal" data-bs-target="#modal-terms">'.htmlspecialchars((string) ($data['lang']['terms_of_service'] ?? 'Terms of Service'), ENT_QUOTES).'</a>';
    $pp_terms_notice = str_replace(
        ['{terms}', '{brand}'],
        [$pp_terms_link, $pp_brand_name],
        (string) ($data['lang']['terms_notice'] ?? '')
    );

    $pp_support = $data['brand']['support'] ?? [];

    $pp_render_gateway_grid = static function (array $tabData, string $panelKey) use ($pp_checkout_base): void {
        if (($tabData['status'] ?? false) !== true || empty($tabData['gateway'])) {
            return;
        }

        echo '<div id="gateways-'.$panelKey.'" class="pp-picker__grid pp-picker__panel" data-panel="'.$panelKey.'" role="tabpanel" hidden>';

        foreach ($tabData['gateway'] as $row) {
            $gatewayId = htmlspecialchars((string) ($row['gateway_id'] ?? ''), ENT_QUOTES);
            $display = htmlspecialchars((string) ($row['display'] ?? ''), ENT_QUOTES);
            $logo = htmlspecialchars((string) ($row['logo'] ?? ''), ENT_QUOTES);
            $href = $pp_checkout_base.'?gateway='.$gatewayId;

            echo '<button type="button" class="pp-picker__card" data-gateway-id="'.$gatewayId.'" data-gateway-url="'.$href.'" data-gateway-label="'.$display.'">';
            echo '<img src="'.$logo.'" alt="">';
            echo '<span class="pp-picker__card-label">'.$display.'</span>';
            echo '</button>';
        }

        echo '</div>';
    };

    $pp_tabs = [];

    if (($pp_gateways_mfs['status'] ?? false) === true && !empty($pp_gateways_mfs['gateway'])) {
        $pp_tabs['mfs'] = ['label' => $data['lang']['mobile_banking'] ?? 'Mobile Banking', 'data' => $pp_gateways_mfs];
    }

    if (($pp_gateways_bank['status'] ?? false) === true && !empty($pp_gateways_bank['gateway'])) {
        $pp_tabs['bank'] = ['label' => $data['lang']['bank_transfer'] ?? 'Bank Transfer', 'data' => $pp_gateways_bank];
    }

    if (($pp_gateways_global['status'] ?? false) === true && !empty($pp_gateways_global['gateway'])) {
        $pp_tabs['global'] = ['label' => $data['lang']['global'] ?? 'Global', 'data' => $pp_gateways_global];
    }

    $pp_first_tab = array_key_first($pp_tabs) ?? 'mfs';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo $data['lang']['checkout']; ?> - <?php echo $pp_brand_name; ?></title>
    <link rel="shortcut icon" href="<?php echo htmlspecialchars($pp_brand_logo, ENT_QUOTES); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php echo pp_assets('head'); ?>
    <style>
        :root {
            --pp-accent: <?php echo $pp_accent; ?>;
            --pp-accent-soft: <?php echo pp_hexToRgba($pp_accent, 0.10); ?>;
            --pp-accent-ring: <?php echo pp_hexToRgba($pp_accent, 0.28); ?>;
            --pp-on-accent: <?php echo $pp_on_accent; ?>;
        }
        <?php echo file_get_contents(__DIR__.'/checkout-picker.css'); ?>
    </style>
    <?php
        $bgStyle = 'background:#f3f4f8;';
        if (!empty($data['options']['enable_bg_image']) && $data['options']['enable_bg_image'] === 'enabled' && !empty($data['options']['background_image'])) {
            $bgImage = $data['options']['background_image'];
            $bgStyle = "background-image:url('{$bgImage}');background-size:cover;background-position:center;background-repeat:no-repeat;background-attachment:fixed;";
        }
    ?>
</head>
<body class="pp-picker-page" style="<?php echo $bgStyle; ?>">
    <div class="pp-picker">
        <header class="pp-picker__top">
            <button type="button" class="pp-picker__icon-btn" onclick="location.href='<?php echo htmlspecialchars($pp_checkout_base, ENT_QUOTES); ?>?cancel'" aria-label="Cancel">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 6l-12 12"/><path d="M6 6l12 12"/></svg>
            </button>
            <div class="pp-picker__top-actions">
                <button type="button" class="pp-picker__icon-btn" data-bs-toggle="modal" data-bs-target="#modal-language" aria-label="Language">বাং</button>
            </div>
        </header>

        <div class="pp-picker__brand">
            <?php if ($pp_brand_logo !== ''): ?>
                <img src="<?php echo htmlspecialchars($pp_brand_logo, ENT_QUOTES); ?>" alt="" class="pp-picker__logo" width="72" height="72">
            <?php endif; ?>
            <h1 class="pp-picker__name"><?php echo $pp_brand_name; ?></h1>
            <div class="pp-picker__utils" role="toolbar" aria-label="Help">
                <button type="button" class="pp-picker__util" data-pp-view="support" aria-label="<?php echo htmlspecialchars($data['lang']['contact_support'] ?? 'Support', ENT_QUOTES); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3"/><path d="M15 15a2 2 0 0 1 2-2h1a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3"/><path d="M4 15v-3a8 8 0 0 1 16 0v3"/></svg>
                </button>
                <button type="button" class="pp-picker__util" data-pp-view="faq" aria-label="FAQ">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 16v.01"/><path d="M12 13a2 2 0 0 0 .914-3.782 1.98 1.98 0 0 0-2.414.483"/><path d="M19.875 6.27A9 9 0 0 0 3.125 6.27"/></svg>
                </button>
                <button type="button" class="pp-picker__util" data-pp-view="details" aria-label="Details">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 9h.01"/><path d="M11 12h1v4h1"/></svg>
                </button>
            </div>
        </div>

        <?php if (count($pp_tabs) > 0): ?>
            <div class="pp-picker__tabs" role="tablist" aria-label="<?php echo htmlspecialchars($data['lang']['choose_payment_method'] ?? 'Payment', ENT_QUOTES); ?>">
                <?php foreach ($pp_tabs as $tabKey => $tab): ?>
                    <button type="button" class="pp-picker__tab<?php echo $tabKey === $pp_first_tab ? ' is-active' : ''; ?>" role="tab" data-pp-tab="<?php echo htmlspecialchars($tabKey, ENT_QUOTES); ?>" aria-selected="<?php echo $tabKey === $pp_first_tab ? 'true' : 'false'; ?>">
                        <?php echo htmlspecialchars($tab['label'], ENT_QUOTES); ?>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="pp-picker__body">
            <?php
                foreach ($pp_tabs as $tabKey => $tab) {
                    $pp_render_gateway_grid($tab['data'], $tabKey);
                }
            ?>

            <div id="gateways-support" class="pp-picker__panel" data-panel="support" hidden>
                <div class="pp-picker__support-grid">
                    <?php if (!empty($pp_support['whatsapp']) && $pp_support['whatsapp'] !== '--'): ?>
                        <a class="pp-picker__support-card pp-picker__support-card--wa" href="https://wa.me/<?php echo preg_replace('/\D+/', '', (string) $pp_support['whatsapp']); ?>" target="_blank" rel="noopener">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L5.05.9"/></svg>
                            WhatsApp
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($pp_support['messenger']) && $pp_support['messenger'] !== '--'): ?>
                        <a class="pp-picker__support-card pp-picker__support-card--fb" href="<?php echo htmlspecialchars((string) $pp_support['messenger'], ENT_QUOTES); ?>" target="_blank" rel="noopener">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 20l1.3-3.9a9 8 0 1 1 3.4 2.9l-4.7 1"/></svg>
                            Messenger
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($pp_support['website']) && $pp_support['website'] !== '--'): ?>
                        <a class="pp-picker__support-card pp-picker__support-card--web" href="<?php echo htmlspecialchars((string) $pp_support['website'], ENT_QUOTES); ?>" target="_blank" rel="noopener">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3.6 9h16.8"/><path d="M12 3a17 17 0 0 0 0 18"/><path d="M12 3a17 17 0 0 1 0 18"/></svg>
                            <?php echo $data['lang']['contact_website']; ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($pp_support['email']) && $pp_support['email'] !== '--'): ?>
                        <a class="pp-picker__support-card pp-picker__support-card--mail" href="mailto:<?php echo htmlspecialchars((string) $pp_support['email'], ENT_QUOTES); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 7a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7"/><path d="M3 7l9 6 9-6"/></svg>
                            <?php echo $data['lang']['contact_email']; ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($pp_support['phone']) && $pp_support['phone'] !== '--'): ?>
                        <a class="pp-picker__support-card pp-picker__support-card--phone" href="tel:<?php echo htmlspecialchars((string) $pp_support['phone'], ENT_QUOTES); ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 15l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2"/></svg>
                            <?php echo $data['lang']['contact_phone']; ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div id="gateways-details" class="pp-picker__panel" data-panel="details" hidden>
                <ul class="pp-picker__details">
                    <li><span><?php echo $data['lang']['currency']; ?></span><span><?php echo $pp_currency; ?></span></li>
                    <li><span><?php echo $data['lang']['subtotal']; ?></span><span><?php echo money_round(($data['transaction']['amount'] ?? 0) - ($data['transaction']['discount_amount'] ?? 0), 2).$pp_currency; ?></span></li>
                    <li><span><?php echo $data['lang']['discount']; ?></span><span><?php echo money_round($data['transaction']['discount_amount'] ?? 0, 2).$pp_currency; ?></span></li>
                    <li><span><?php echo $data['lang']['total']; ?></span><span><?php echo $pp_amount.' '.$pp_currency; ?></span></li>
                </ul>
            </div>

            <div id="gateways-faq" class="pp-picker__panel" data-panel="faq" hidden>
                <div class="pp-picker__faq">
                    <div class="accordion" id="accordion-checkout-faq">
                        <?php $faqCount = 0; foreach ($data['faqs'] ?? [] as $faq): $faqCount++; ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button<?php echo $faqCount > 1 ? ' collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq-<?php echo $faqCount; ?>">
                                        <?php echo htmlspecialchars((string) ($faq['title'] ?? ''), ENT_QUOTES); ?>
                                    </button>
                                </h2>
                                <div id="faq-<?php echo $faqCount; ?>" class="accordion-collapse collapse<?php echo $faqCount === 1 ? ' show' : ''; ?>" data-bs-parent="#accordion-checkout-faq">
                                    <div class="accordion-body"><?php echo $faq['description'] ?? ''; ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="pp-picker__secure">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 3l8 4v5c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V7l8-4z"/><path d="M9 12l2 2 4-4"/></svg>
            <?php echo $pp_secured_label; ?>
        </div>

        <?php if ($pp_terms_notice !== ''): ?>
            <p class="pp-picker__terms"><?php echo $pp_terms_notice; ?></p>
        <?php endif; ?>

        <div class="pp-picker__dock">
            <div class="pp-picker__dock-inner">
                <button type="button" id="ppPayBtn" class="pp-picker__pay-btn" disabled><?php echo htmlspecialchars($pp_pay_label, ENT_QUOTES); ?></button>
            </div>
        </div>

        <?php if (trim((string) ($data['options']['watermark_text'] ?? '')) !== ''): ?>
            <p class="pp-picker__footer footer-branding"><?php echo $data['options']['watermark_text']; ?></p>
        <?php endif; ?>
    </div>

    <div class="pp-sheet" id="ppGatewaySheet" aria-hidden="true">
        <div class="pp-sheet__backdrop" data-pp-sheet-close></div>
        <div class="pp-sheet__panel" role="dialog" aria-labelledby="ppSheetTitle">
            <div class="pp-sheet__head">
                <h2 class="pp-sheet__title" id="ppSheetTitle"><?php echo $data['lang']['select_payment_option']; ?></h2>
                <button type="button" class="pp-sheet__close" data-pp-sheet-close aria-label="<?php echo $data['lang']['close']; ?>">&times;</button>
            </div>
            <div class="pp-sheet__grid" id="ppSheetGrid"></div>
            <div class="pp-sheet__actions">
                <button type="button" class="pp-sheet__btn" data-pp-sheet-close><?php echo $data['lang']['close']; ?></button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-language" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-top">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?php echo $data['lang']['select_language']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <select class="form-select" id="model-languages" onchange="hitLanguage()">
                        <option value="" selected><?php echo $data['lang']['select_a_language']; ?></option>
                        <?php foreach ($data['supported_languages'] ?? [] as $code => $language): ?>
                            <option value="<?= htmlspecialchars($code) ?>"><?= htmlspecialchars($language) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-terms" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-bottom">
            <div class="modal-content" style="border-radius:1rem 1rem 0 0;">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100 text-center"><?php echo $data['lang']['terms_of_service']; ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small text-center"><?php echo $pp_brand_name; ?></p>
                    <p class="small"><?php echo $pp_terms_notice !== '' ? strip_tags($pp_terms_notice) : ''; ?></p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal" style="background:var(--pp-accent);border-color:var(--pp-accent);"><?php echo $data['lang']['got_it']; ?></button>
                </div>
            </div>
        </div>
    </div>

    <?php echo pp_assets('footer'); ?>

    <script data-cfasync="false">
        (function () {
            const payBtn = document.getElementById('ppPayBtn');
            const sheet = document.getElementById('ppGatewaySheet');
            const sheetGrid = document.getElementById('ppSheetGrid');
            let selectedUrl = '';
            let selectedLabel = '';
            const tabs = document.querySelectorAll('.pp-picker__tab');
            const panels = document.querySelectorAll('.pp-picker__panel[data-panel]');
            const utilBtns = document.querySelectorAll('[data-pp-view]');
            const paymentPanels = ['mfs', 'bank', 'global'];

            function showPanel(name) {
                panels.forEach(function (p) {
                    p.hidden = p.getAttribute('data-panel') !== name;
                });
                utilBtns.forEach(function (b) {
                    b.classList.toggle('is-active', b.getAttribute('data-pp-view') === name && !paymentPanels.includes(name));
                });
            }

            function showTab(tab) {
                tabs.forEach(function (btn) {
                    const on = btn.getAttribute('data-pp-tab') === tab;
                    btn.classList.toggle('is-active', on);
                    btn.setAttribute('aria-selected', on ? 'true' : 'false');
                });
                showPanel(tab);
                bindCards(document.getElementById('gateways-' + tab));
                autoSelectSingle();
            }

            function bindCards(root) {
                if (!root) return;
                root.querySelectorAll('.pp-picker__card').forEach(function (card) {
                    card.onclick = function () {
                        root.querySelectorAll('.pp-picker__card').forEach(function (c) {
                            c.classList.remove('is-selected');
                        });
                        card.classList.add('is-selected');
                        selectedUrl = card.getAttribute('data-gateway-url') || '';
                        selectedLabel = card.getAttribute('data-gateway-label') || '';
                        payBtn.disabled = !selectedUrl;
                    };
                });
            }

            function autoSelectSingle() {
                const visible = document.querySelector('.pp-picker__panel:not([hidden])');
                if (!visible || !visible.classList.contains('pp-picker__grid')) return;
                const cards = visible.querySelectorAll('.pp-picker__card');
                if (cards.length === 1) {
                    cards[0].click();
                }
            }

            tabs.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    showTab(btn.getAttribute('data-pp-tab'));
                });
            });

            utilBtns.forEach(function (btn) {
                btn.addEventListener('click', function () {
                    showPanel(btn.getAttribute('data-pp-view'));
                    tabs.forEach(function (t) {
                        t.classList.remove('is-active');
                        t.setAttribute('aria-selected', 'false');
                    });
                });
            });

            payBtn.addEventListener('click', function () {
                if (!selectedUrl) return;

                const activePanel = document.querySelector('.pp-picker__grid.pp-picker__panel:not([hidden])');
                if (!activePanel) {
                    location.href = selectedUrl;
                    return;
                }

                const selected = activePanel.querySelectorAll('.pp-picker__card.is-selected');
                const sameBrand = [];
                const baseLabel = (selectedLabel || '').replace(/\s+(Personal|Merchant|Agent)$/i, '').trim();

                activePanel.querySelectorAll('.pp-picker__card').forEach(function (card) {
                    const label = card.getAttribute('data-gateway-label') || '';
                    if (baseLabel && label.toLowerCase().indexOf(baseLabel.toLowerCase()) === 0 && activePanel.querySelectorAll('.pp-picker__card').length > 1) {
                        sameBrand.push(card);
                    }
                });

                if (sameBrand.length > 1 && selected.length <= 1) {
                    sheetGrid.innerHTML = '';
                    sameBrand.forEach(function (card) {
                        const clone = card.cloneNode(true);
                        clone.classList.remove('is-selected');
                        clone.onclick = function () {
                            location.href = card.getAttribute('data-gateway-url');
                        };
                        sheetGrid.appendChild(clone);
                    });
                    sheet.classList.add('is-open');
                    sheet.setAttribute('aria-hidden', 'false');
                    return;
                }

                location.href = selectedUrl;
            });

            document.querySelectorAll('[data-pp-sheet-close]').forEach(function (el) {
                el.addEventListener('click', function () {
                    sheet.classList.remove('is-open');
                    sheet.setAttribute('aria-hidden', 'true');
                });
            });

            const firstTab = document.querySelector('.pp-picker__tab.is-active');
            if (firstTab) {
                showTab(firstTab.getAttribute('data-pp-tab'));
            } else if (panels.length) {
                showPanel(panels[0].getAttribute('data-panel'));
            }
        })();

        function hitLanguage() {
            const language = document.querySelector('#model-languages').value;
            if (language !== '') {
                location.href = '?lang=' + language;
            }
        }
    </script>
</body>
</html>

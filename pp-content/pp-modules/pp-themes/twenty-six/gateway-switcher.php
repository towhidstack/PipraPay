<?php
    if (!defined('PipraPay_INIT')) {
        http_response_code(403);
        exit('Direct access not allowed');
    }

    $pp_gateway_switch_url = static function (string $gatewayId): string {
        $url = pp_checkout_address().'?gateway='.rawurlencode($gatewayId);

        if (!empty($_GET['lang'])) {
            $url .= '&lang='.rawurlencode((string) $_GET['lang']);
        }

        return $url;
    };

    $pp_gateway_tabs = [
        'mfs' => $pp_gateways_mfs ?? ['status' => false, 'gateway' => []],
        'bank' => $pp_gateways_bank ?? ['status' => false, 'gateway' => []],
        'global' => $pp_gateways_global ?? ['status' => false, 'gateway' => []],
    ];

    $pp_active_tabs = [];

    foreach ($pp_gateway_tabs as $tabKey => $tabData) {
        if (($tabData['status'] ?? false) === true && !empty($tabData['gateway'])) {
            $pp_active_tabs[$tabKey] = $tabData;
        }
    }

    $pp_gateway_count = 0;

    foreach ($pp_active_tabs as $tabData) {
        $pp_gateway_count += count($tabData['gateway']);
    }

    if ($pp_gateway_count <= 1) {
        return;
    }

    $pp_current_gateway_id = (string) ($current_gateway_id ?? ($_GET['gateway'] ?? ''));
    $pp_current_tab = (string) ($gateway_info['gateway']['tab'] ?? 'mfs');

    if (!array_key_exists($pp_current_tab, $pp_active_tabs)) {
        $pp_current_tab = array_key_first($pp_active_tabs) ?? 'mfs';
    }

    $pp_show_tab_bar = count($pp_active_tabs) > 1;
?>

<section class="pp-pay-methods" data-initial-tab="<?php echo htmlspecialchars($pp_current_tab, ENT_QUOTES); ?>">
    <div class="pp-pay-methods__head">
        <h2 class="pp-pay-methods__title"><?php echo $data['lang']['choose_payment_method']; ?></h2>
        <p class="pp-pay-methods__hint"><?php echo $data['lang']['switch_payment_hint']; ?></p>
    </div>

    <?php if ($pp_show_tab_bar): ?>
        <div class="pp-pay-methods__segments" role="tablist" aria-label="<?php echo htmlspecialchars($data['lang']['choose_payment_method'], ENT_QUOTES); ?>">
            <?php if (isset($pp_active_tabs['mfs'])): ?>
                <button type="button" class="pp-pay-methods__segment" data-pp-tab="mfs" role="tab">
                    <?php echo $data['lang']['mobile_banking']; ?>
                </button>
            <?php endif; ?>
            <?php if (isset($pp_active_tabs['bank'])): ?>
                <button type="button" class="pp-pay-methods__segment" data-pp-tab="bank" role="tab">
                    <?php echo $data['lang']['net_banking']; ?>
                </button>
            <?php endif; ?>
            <?php if (isset($pp_active_tabs['global'])): ?>
                <button type="button" class="pp-pay-methods__segment" data-pp-tab="global" role="tab">
                    <?php echo $data['lang']['global']; ?>
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php foreach ($pp_active_tabs as $tabKey => $tabData): ?>
        <div class="pp-pay-methods__grid" data-pp-panel="<?php echo htmlspecialchars($tabKey, ENT_QUOTES); ?>" role="tabpanel" hidden>
            <?php foreach ($tabData['gateway'] as $row):
                $gatewayId = (string) ($row['gateway_id'] ?? '');
                $isActive = $gatewayId !== '' && $gatewayId === $pp_current_gateway_id;
                $href = $isActive ? '#' : $pp_gateway_switch_url($gatewayId);
                $label = (string) ($row['display'] ?? $row['name'] ?? '');
                ?>
                <a
                    href="<?php echo htmlspecialchars($href, ENT_QUOTES); ?>"
                    class="pp-pay-tile<?php echo $isActive ? ' is-active' : ''; ?>"
                    <?php echo $isActive ? 'aria-current="true"' : ''; ?>
                    aria-label="<?php echo htmlspecialchars($label, ENT_QUOTES); ?>"
                >
                    <?php if ($isActive): ?>
                        <span class="pp-pay-tile__check" aria-hidden="true">
                            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                        </span>
                    <?php endif; ?>
                    <img
                        src="<?php echo htmlspecialchars((string) ($row['logo'] ?? ''), ENT_QUOTES); ?>"
                        alt=""
                        loading="lazy"
                    >
                </a>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</section>

<script data-cfasync="false">
    (function () {
        const root = document.querySelector('.pp-pay-methods');
        if (!root) return;

        const segments = root.querySelectorAll('.pp-pay-methods__segment');
        const grids = root.querySelectorAll('.pp-pay-methods__grid');
        const initialTab = root.getAttribute('data-initial-tab') || 'mfs';

        function showTab(tab) {
            segments.forEach(function (btn) {
                const active = btn.getAttribute('data-pp-tab') === tab;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            grids.forEach(function (grid) {
                grid.hidden = grid.getAttribute('data-pp-panel') !== tab;
            });
        }

        segments.forEach(function (btn) {
            btn.addEventListener('click', function () {
                showTab(btn.getAttribute('data-pp-tab'));
            });
        });

        showTab(initialTab);
    })();
</script>

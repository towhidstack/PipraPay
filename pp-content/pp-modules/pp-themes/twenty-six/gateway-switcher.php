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

    $pp_gateway_count = 0;

    foreach ($pp_gateway_tabs as $tabData) {
        if (($tabData['status'] ?? false) === true && !empty($tabData['gateway'])) {
            $pp_gateway_count += count($tabData['gateway']);
        }
    }

    if ($pp_gateway_count <= 1) {
        return;
    }

    $pp_theme_primary = $data['options']['primary_color'] ?? '#5f38f9';
    $pp_current_gateway_id = (string) ($current_gateway_id ?? ($_GET['gateway'] ?? ''));
    $pp_current_tab = (string) ($gateway_info['gateway']['tab'] ?? 'mfs');

    if (!array_key_exists($pp_current_tab, $pp_gateway_tabs)) {
        $pp_current_tab = 'mfs';
    }

    $pp_render_switch_grid = static function (array $tabData, string $currentGatewayId, callable $switchUrl): void {
        if (($tabData['status'] ?? false) !== true || empty($tabData['gateway'])) {
            return;
        }

        foreach ($tabData['gateway'] as $row) {
            $gatewayId = (string) ($row['gateway_id'] ?? '');
            $isActive = $gatewayId !== '' && $gatewayId === $currentGatewayId;
            $href = $isActive ? '#' : $switchUrl($gatewayId);
            ?>
            <a
                href="<?php echo htmlspecialchars($href, ENT_QUOTES); ?>"
                class="pp-gateway-switcher__item<?php echo $isActive ? ' is-active' : ''; ?>"
                <?php echo $isActive ? 'aria-current="true"' : ''; ?>
                title="<?php echo htmlspecialchars((string) ($row['display'] ?? ''), ENT_QUOTES); ?>"
            >
                <span class="pp-gateway-switcher__item-logo">
                    <img src="<?php echo htmlspecialchars((string) ($row['logo'] ?? ''), ENT_QUOTES); ?>" alt="">
                </span>
                <span class="pp-gateway-switcher__item-name"><?php echo htmlspecialchars((string) ($row['display'] ?? ''), ENT_QUOTES); ?></span>
            </a>
            <?php
        }
    };
?>

<div class="pp-gateway-switcher" data-initial-tab="<?php echo htmlspecialchars($pp_current_tab, ENT_QUOTES); ?>">
    <button
        type="button"
        class="pp-gateway-switcher__toggle"
        aria-expanded="false"
        aria-controls="pp-gateway-switcher-panel"
        id="pp-gateway-switcher-toggle"
    >
        <span class="pp-gateway-switcher__toggle-text">
            <span class="pp-gateway-switcher__toggle-title"><?php echo $data['lang']['switch_payment_method']; ?></span>
            <span class="pp-gateway-switcher__toggle-hint"><?php echo $data['lang']['switch_payment_hint']; ?></span>
        </span>
        <span class="pp-gateway-switcher__toggle-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg>
        </span>
    </button>

    <div class="pp-gateway-switcher__panel" id="pp-gateway-switcher-panel" hidden>
        <div class="pp-gateway-switcher__tabs" role="tablist" aria-label="<?php echo htmlspecialchars($data['lang']['switch_payment_method'], ENT_QUOTES); ?>">
            <?php if (($pp_gateway_tabs['mfs']['status'] ?? false) === true && !empty($pp_gateway_tabs['mfs']['gateway'])): ?>
                <button type="button" class="pp-gateway-switcher__tab" data-pp-tab="mfs" role="tab" aria-selected="false">
                    <?php echo $data['lang']['mobile_banking']; ?>
                </button>
            <?php endif; ?>
            <?php if (($pp_gateway_tabs['bank']['status'] ?? false) === true && !empty($pp_gateway_tabs['bank']['gateway'])): ?>
                <button type="button" class="pp-gateway-switcher__tab" data-pp-tab="bank" role="tab" aria-selected="false">
                    <?php echo $data['lang']['net_banking']; ?>
                </button>
            <?php endif; ?>
            <?php if (($pp_gateway_tabs['global']['status'] ?? false) === true && !empty($pp_gateway_tabs['global']['gateway'])): ?>
                <button type="button" class="pp-gateway-switcher__tab" data-pp-tab="global" role="tab" aria-selected="false">
                    <?php echo $data['lang']['global']; ?>
                </button>
            <?php endif; ?>
        </div>

        <?php foreach ($pp_gateway_tabs as $tabKey => $tabData): ?>
            <?php if (($tabData['status'] ?? false) !== true || empty($tabData['gateway'])) { continue; } ?>
            <div class="pp-gateway-switcher__grid" data-pp-panel="<?php echo htmlspecialchars($tabKey, ENT_QUOTES); ?>" role="tabpanel" hidden>
                <?php $pp_render_switch_grid($tabData, $pp_current_gateway_id, $pp_gateway_switch_url); ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pp-gateway-switcher__quick" aria-label="<?php echo htmlspecialchars($data['lang']['switch_payment_method'], ENT_QUOTES); ?>">
        <?php
            $quickTab = $pp_gateway_tabs[$pp_current_tab] ?? $pp_gateway_tabs['mfs'];
            if (($quickTab['status'] ?? false) === true && !empty($quickTab['gateway'])) {
                foreach ($quickTab['gateway'] as $row) {
                    $gatewayId = (string) ($row['gateway_id'] ?? '');
                    $isActive = $gatewayId === $pp_current_gateway_id;
                    $href = $isActive ? '#' : $pp_gateway_switch_url($gatewayId);
                    ?>
                    <a
                        href="<?php echo htmlspecialchars($href, ENT_QUOTES); ?>"
                        class="pp-gateway-switcher__quick-item<?php echo $isActive ? ' is-active' : ''; ?>"
                        <?php echo $isActive ? 'aria-current="true"' : ''; ?>
                    >
                        <img src="<?php echo htmlspecialchars((string) ($row['logo'] ?? ''), ENT_QUOTES); ?>" alt="<?php echo htmlspecialchars((string) ($row['display'] ?? ''), ENT_QUOTES); ?>">
                    </a>
                    <?php
                }
            }
        ?>
    </div>
</div>

<script data-cfasync="false">
    (function () {
        const root = document.querySelector('.pp-gateway-switcher');
        if (!root) return;

        const toggle = root.querySelector('.pp-gateway-switcher__toggle');
        const panel = root.querySelector('.pp-gateway-switcher__panel');
        const tabs = root.querySelectorAll('.pp-gateway-switcher__tab');
        const grids = root.querySelectorAll('.pp-gateway-switcher__grid');
        const initialTab = root.getAttribute('data-initial-tab') || 'mfs';

        function showTab(tab) {
            tabs.forEach(function (btn) {
                const active = btn.getAttribute('data-pp-tab') === tab;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            grids.forEach(function (grid) {
                const active = grid.getAttribute('data-pp-panel') === tab;
                grid.hidden = !active;
            });
        }

        tabs.forEach(function (btn) {
            btn.addEventListener('click', function () {
                showTab(btn.getAttribute('data-pp-tab'));
            });
        });

        showTab(initialTab);

        if (toggle && panel) {
            toggle.addEventListener('click', function () {
                const open = panel.hidden;
                panel.hidden = !open;
                toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
                toggle.classList.toggle('is-open', open);
            });

            if (window.matchMedia('(min-width: 640px)').matches) {
                panel.hidden = false;
                toggle.setAttribute('aria-expanded', 'true');
                toggle.classList.add('is-open');
            }
        }
    })();
</script>

document.addEventListener('DOMContentLoaded', () => {
    initSidebar();
    initAlerts();
    initQuoteForm();
    initDashboardCharts();
});

function initSidebar() {
    const sidebar = document.querySelector('#app-sidebar');
    const overlay = document.querySelector('[data-sidebar-overlay]');
    const toggle = document.querySelector('[data-sidebar-toggle]');

    if (!sidebar || !overlay || !toggle) {
        return;
    }

    const open = () => {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    };

    const close = () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    };

    toggle.addEventListener('click', open);
    overlay.addEventListener('click', close);
}

function initAlerts() {
    document.querySelectorAll('[data-dismiss]').forEach((button) => {
        button.addEventListener('click', () => {
            const alert = button.closest('div.mb-6, div.mt-6');
            if (alert) {
                alert.style.display = 'none';
            }
        });
    });
}

function initQuoteForm() {
    const form = document.querySelector('[data-quote-form]');
    if (!form) {
        return;
    }

    const body = document.querySelector('#quote-items-body');
    const template = document.querySelector('#quote-item-template');
    const totalElement = document.querySelector('#quote-grand-total');
    const addButton = document.querySelector('[data-add-quote-item]');
    const productsScript = document.querySelector('#quote-products-data');

    if (!body || !template || !totalElement || !addButton || !productsScript) {
        return;
    }

    const products = JSON.parse(productsScript.textContent || '[]');
    let index = 0;

    const currency = (value) => new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(Number(value || 0));

    const productMap = new Map(products.map((product) => [String(product.id), product]));

    const updateGrandTotal = () => {
        const total = Array.from(body.querySelectorAll('.quote-item-row')).reduce((sum, row) => {
            const price = Number(row.querySelector('.quote-unit-price')?.value || 0);
            const quantity = Number(row.querySelector('.quote-quantity')?.value || 0);
            return sum + (price * quantity);
        }, 0);

        totalElement.textContent = currency(total);
    };

    const updateRow = (row) => {
        const price = Number(row.querySelector('.quote-unit-price')?.value || 0);
        const quantity = Number(row.querySelector('.quote-quantity')?.value || 0);
        row.querySelector('.quote-line-total').textContent = currency(price * quantity);
        updateGrandTotal();
    };

    const bindRow = (row) => {
        const select = row.querySelector('.quote-product-select');
        const description = row.querySelector('.quote-description-input');
        const unitPrice = row.querySelector('.quote-unit-price');
        const quantity = row.querySelector('.quote-quantity');
        const remove = row.querySelector('.quote-remove-item');

        select.addEventListener('change', () => {
            const product = productMap.get(select.value);
            if (!product) {
                description.value = '';
                unitPrice.value = '';
                updateRow(row);
                return;
            }

            description.value = product.description || '';
            unitPrice.value = product.unit_price;
            updateRow(row);
        });

        [description, unitPrice, quantity].forEach((field) => {
            field.addEventListener('input', () => updateRow(row));
        });

        remove.addEventListener('click', () => {
            row.remove();
            updateGrandTotal();
        });

        updateRow(row);
    };

    const addRow = () => {
        const html = template.innerHTML.replaceAll('__INDEX__', index);
        index += 1;
        const wrapper = document.createElement('tbody');
        wrapper.innerHTML = html.trim();
        const row = wrapper.firstElementChild;
        body.appendChild(row);
        bindRow(row);
    };

    addButton.addEventListener('click', addRow);

    if (!body.children.length) {
        addRow();
    }
}

function initDashboardCharts() {
    document.querySelectorAll('[data-chart]').forEach((element) => {
        const type = element.dataset.chart;
        const payload = JSON.parse(element.dataset.payload || '{}');

        if (type === 'sales') {
            renderSalesChart(element, payload);
        }

        if (type === 'bars') {
            renderBarsChart(element, payload);
        }

        if (type === 'donut') {
            renderDonutChart(element, payload);
        }
    });
}

function renderSalesChart(element, payload) {
    const labels = payload.labels || [];
    const revenue = payload.revenue || [];
    const profit = payload.profit || [];
    const quotes = payload.quotes || [];
    const width = 860;
    const height = 320;
    const padding = { top: 20, right: 20, bottom: 55, left: 55 };
    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;
    const maxValue = Math.max(...revenue, ...profit, 1);
    const barMax = Math.max(...quotes, 1);
    const points = labels.map((_, index) => ({
        x: padding.left + (chartWidth / Math.max(labels.length - 1, 1)) * index,
        revenueY: padding.top + chartHeight - ((Number(revenue[index] || 0) / maxValue) * chartHeight),
        profitY: padding.top + chartHeight - ((Number(profit[index] || 0) / maxValue) * chartHeight),
        quoteHeight: (Number(quotes[index] || 0) / barMax) * (chartHeight * 0.4)
    }));

    const revenuePath = buildLinePath(points.map((point) => [point.x, point.revenueY]));
    const profitPath = buildLinePath(points.map((point) => [point.x, point.profitY]));

    const bars = points.map((point, index) => {
        const barWidth = Math.max(18, chartWidth / Math.max(labels.length * 3, 6));
        const x = point.x - (barWidth / 2);
        const y = padding.top + chartHeight - point.quoteHeight;
        return `
            <rect x="${x}" y="${y}" width="${barWidth}" height="${point.quoteHeight}" rx="8" fill="rgba(251,191,36,0.22)"></rect>
            <text x="${point.x}" y="${padding.top + chartHeight + 24}" fill="#94a3b8" font-size="11" text-anchor="middle">${labels[index]}</text>
            <text x="${point.x}" y="${padding.top + chartHeight + 40}" fill="#fbbf24" font-size="10" text-anchor="middle">${quotes[index] || 0} orc.</text>
        `;
    }).join('');

    const pointsMarkup = points.map((point, index) => `
        <circle cx="${point.x}" cy="${point.revenueY}" r="4" fill="#22d3ee"></circle>
        <circle cx="${point.x}" cy="${point.profitY}" r="4" fill="#34d399"></circle>
        <text x="${point.x}" y="${Math.min(point.revenueY, point.profitY) - 10}" fill="#e2e8f0" font-size="10" text-anchor="middle">${formatCompactCurrency(revenue[index] || 0)}</text>
    `).join('');

    element.innerHTML = `
        <svg viewBox="0 0 ${width} ${height}" class="h-full w-full" role="img" aria-label="Grafico de faturamento e lucro">
            <line x1="${padding.left}" y1="${padding.top + chartHeight}" x2="${padding.left + chartWidth}" y2="${padding.top + chartHeight}" stroke="rgba(148,163,184,0.25)"></line>
            <line x1="${padding.left}" y1="${padding.top}" x2="${padding.left}" y2="${padding.top + chartHeight}" stroke="rgba(148,163,184,0.25)"></line>
            ${bars}
            <path d="${revenuePath}" fill="none" stroke="#22d3ee" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path>
            <path d="${profitPath}" fill="none" stroke="#34d399" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"></path>
            ${pointsMarkup}
        </svg>
    `;
}

function renderBarsChart(element, payload) {
    const labels = payload.labels || [];
    const values = payload.values || [];
    const width = 860;
    const height = 280;
    const padding = { top: 20, right: 20, bottom: 48, left: 32 };
    const chartWidth = width - padding.left - padding.right;
    const chartHeight = height - padding.top - padding.bottom;
    const maxValue = Math.max(...values, 1);
    const color = payload.color || '#38bdf8';
    const barWidth = chartWidth / Math.max(labels.length * 1.6, 2);

    const bars = labels.map((label, index) => {
        const x = padding.left + (chartWidth / Math.max(labels.length, 1)) * index + (barWidth * 0.2);
        const value = Number(values[index] || 0);
        const barHeight = (value / maxValue) * chartHeight;
        const y = padding.top + chartHeight - barHeight;

        return `
            <rect x="${x}" y="${y}" width="${barWidth}" height="${barHeight}" rx="12" fill="${color}"></rect>
            <text x="${x + (barWidth / 2)}" y="${y - 8}" fill="#e2e8f0" font-size="11" text-anchor="middle">${value}</text>
            <text x="${x + (barWidth / 2)}" y="${padding.top + chartHeight + 22}" fill="#94a3b8" font-size="11" text-anchor="middle">${label}</text>
        `;
    }).join('');

    element.innerHTML = `
        <svg viewBox="0 0 ${width} ${height}" class="h-full w-full" role="img" aria-label="${payload.seriesLabel || 'Grafico de barras'}">
            <line x1="${padding.left}" y1="${padding.top + chartHeight}" x2="${padding.left + chartWidth}" y2="${padding.top + chartHeight}" stroke="rgba(148,163,184,0.25)"></line>
            ${bars}
        </svg>
    `;
}

function renderDonutChart(element, payload) {
    const labels = payload.labels || [];
    const values = payload.values || [];
    const colors = payload.colors || ['#38bdf8', '#34d399', '#fb7185'];
    const total = values.reduce((sum, value) => sum + Number(value || 0), 0);
    const gradientParts = [];
    let start = 0;

    values.forEach((value, index) => {
        const slice = total > 0 ? (Number(value || 0) / total) * 100 : 0;
        const end = start + slice;
        gradientParts.push(`${colors[index % colors.length]} ${start}% ${end}%`);
        start = end;
    });

    const legend = labels.map((label, index) => `
        <div class="flex items-center justify-between gap-3 rounded-2xl border border-white/10 bg-white/5 px-4 py-3">
            <div class="flex items-center gap-3">
                <span class="h-3.5 w-3.5 rounded-full" style="background:${colors[index % colors.length]}"></span>
                <span class="text-sm text-slate-200">${label}</span>
            </div>
            <span class="text-sm font-semibold text-white">${values[index] || 0}</span>
        </div>
    `).join('');

    element.innerHTML = `
        <div class="flex h-full flex-col items-center justify-center gap-6 lg:flex-row lg:items-start">
            <div class="relative flex h-52 w-52 items-center justify-center rounded-full" style="background: conic-gradient(${gradientParts.join(', ')})">
                <div class="flex h-32 w-32 flex-col items-center justify-center rounded-full bg-slate-950 text-center">
                    <span class="text-xs uppercase tracking-[0.2em] text-slate-500">Total</span>
                    <span class="mt-2 text-3xl font-bold text-white">${total}</span>
                </div>
            </div>
            <div class="w-full space-y-3">${legend}</div>
        </div>
    `;
}

function buildLinePath(points) {
    return points.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point[0]} ${point[1]}`).join(' ');
}

function formatCompactCurrency(value) {
    const number = Number(value || 0);

    if (number >= 1000000) {
        return `R$ ${(number / 1000000).toFixed(1)}M`;
    }

    if (number >= 1000) {
        return `R$ ${(number / 1000).toFixed(1)}k`;
    }

    return `R$ ${number.toFixed(0)}`;
}

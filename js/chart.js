/* ============================================
   chart.js - Simple Canvas Chart (No Libraries)
   ============================================ */

// Simple line chart using Canvas API
class SimpleChart {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;

        this.ctx = this.canvas.getContext('2d');
        this.data = options.data || [];
        this.labels = options.labels || [];
        this.title = options.title || '';
        this.datasets = options.datasets || [];
        this.height = options.height || 300;

        this.resize();
        window.addEventListener('resize', () => this.resize());
    }

    resize() {
        const container = this.canvas.parentElement;
        this.canvas.width = container.clientWidth - 50;
        this.canvas.height = this.height;
        this.draw();
    }

    draw() {
        if (!this.ctx || this.datasets.length === 0) return;

        const ctx = this.ctx;
        const width = this.canvas.width;
        const height = this.canvas.height;
        const padding = { top: 30, right: 30, bottom: 50, left: 60 };
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;

        // Clear canvas
        ctx.clearRect(0, 0, width, height);

        // Get dark mode colors
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#94a3b8' : '#666';
        const gridColor = isDark ? '#334155' : '#e0e0e0';

        // Find min and max values across all datasets
        let allValues = [];
        this.datasets.forEach(ds => allValues = allValues.concat(ds.data));
        let minVal = Math.min(...allValues);
        let maxVal = Math.max(...allValues);

        // Add some padding to min/max
        const range = maxVal - minVal;
        minVal = Math.max(0, minVal - range * 0.1);
        maxVal = maxVal + range * 0.1;

        // Draw grid lines
        ctx.strokeStyle = gridColor;
        ctx.lineWidth = 1;
        const gridLines = 5;
        for (let i = 0; i <= gridLines; i++) {
            const y = padding.top + (chartHeight / gridLines) * i;
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(width - padding.right, y);
            ctx.stroke();

            // Y-axis labels
            const value = maxVal - ((maxVal - minVal) / gridLines) * i;
            ctx.fillStyle = textColor;
            ctx.font = '12px Segoe UI';
            ctx.textAlign = 'right';
            ctx.fillText(value.toFixed(1), padding.left - 10, y + 4);
        }

        // Draw X-axis labels
        const step = Math.ceil(this.labels.length / 10);
        ctx.textAlign = 'center';
        for (let i = 0; i < this.labels.length; i += step) {
            const x = padding.left + (chartWidth / (this.labels.length - 1 || 1)) * i;
            ctx.fillStyle = textColor;
            ctx.fillText(this.labels[i], x, height - padding.bottom + 20);
        }

        // Draw datasets
        this.datasets.forEach(dataset => {
            ctx.strokeStyle = dataset.color || '#4361ee';
            ctx.lineWidth = 2;
            ctx.beginPath();

            dataset.data.forEach((value, index) => {
                const x = padding.left + (chartWidth / (dataset.data.length - 1 || 1)) * index;
                const y = padding.top + chartHeight - ((value - minVal) / (maxVal - minVal)) * chartHeight;

                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });

            ctx.stroke();

            // Draw fill under line
            if (dataset.fill) {
                ctx.lineTo(padding.left + chartWidth, padding.top + chartHeight);
                ctx.lineTo(padding.left, padding.top + chartHeight);
                ctx.closePath();
                ctx.fillStyle = dataset.fillColor || 'rgba(67, 97, 238, 0.1)';
                ctx.fill();
            }

            // Draw points
            dataset.data.forEach((value, index) => {
                const x = padding.left + (chartWidth / (dataset.data.length - 1 || 1)) * index;
                const y = padding.top + chartHeight - ((value - minVal) / (maxVal - minVal)) * chartHeight;

                ctx.beginPath();
                ctx.arc(x, y, 3, 0, Math.PI * 2);
                ctx.fillStyle = dataset.color || '#4361ee';
                ctx.fill();
            });
        });

        // Draw legend
        let legendX = padding.left;
        this.datasets.forEach(dataset => {
            ctx.fillStyle = dataset.color;
            ctx.fillRect(legendX, height - 15, 15, 3);
            ctx.fillStyle = textColor;
            ctx.font = '11px Segoe UI';
            ctx.textAlign = 'left';
            ctx.fillText(dataset.label, legendX + 20, height - 10);
            legendX += ctx.measureText(dataset.label).width + 40;
        });
    }

    update(datasets, labels) {
        this.datasets = datasets;
        this.labels = labels;
        this.draw();
    }
}

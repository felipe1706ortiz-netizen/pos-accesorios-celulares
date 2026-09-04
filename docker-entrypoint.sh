#!/bin/bash
set -e

# ------------------------------------------------------------------------------
# 1. PREVENIR ERROR AH00534 (More than one MPM loaded)
# ------------------------------------------------------------------------------
a2dismod mpm_event mpm_worker 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# ------------------------------------------------------------------------------
# 2. CONFIGURAR PUERTO DINÁMICO DE RAILWAY / RENDER ($PORT)
# ------------------------------------------------------------------------------
PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/Listen [0-9]*/Listen ${PORT}/g" /etc/apache2/ports.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/*.conf 2>/dev/null || true
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:${PORT}>/g" /etc/apache2/sites-available/*.conf 2>/dev/null || true

# ------------------------------------------------------------------------------
# 3. INICIAR APACHE EN PRIMER PLANO
# ------------------------------------------------------------------------------
exec apache2-foreground

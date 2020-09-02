<?php

namespace app\system;

class Config
{

    /*
     * MySQL
     */
    const DBHOST = '';
    const DBUSER = '';
    const DBPASS = '';
    const DBNAME = '';

    /**
     * Gateways
     * MercadoPago, PagSeguro e PayPal
     */
    const MERCADOPAGO_CLIENT_ID = "";
    const MERCADOPAGO_CLIENT_SECRET = "";

    const PAGSEGURO_EMAIL = "";
    const PAGSEGURO_TOKEN = "";

    const PAYPAL_EMAIL = "";

    const SANDBOX = false;

    /*
     * HTTPS
     */
    const HTTPS_FORCE = true;

    /*
     * Domain
     */
    const DOMAIN = "premiumhosting.com.br";

    /*
     * HTML Minify
     */
    const HTML_MINIFY = false;

    /*
     * Show errors
     */
    const SHOW_ERRORS = false;

    /*
     * Session Name
     */
    const SESSION_NAME = "PremiumHosting";
}
